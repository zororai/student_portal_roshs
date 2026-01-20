<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Grade;
use App\Student;
use App\Parents;
use App\User;

class CleanupGeneratedStudents extends Command
{
    protected $signature = 'students:cleanup-generated';

    protected $description = 'Delete students (and their parent/user accounts) generated within last 24 hours for Form 6 streams';

    public function handle()
    {
        $classes = ['Form 6 Arts','Form 6 Sciences','Form 6 Commercial'];
        $gradeIds = Grade::whereIn('class_name', $classes)->pluck('id')->toArray();

        $deleted = DB::transaction(function () use ($gradeIds) {
            $students = Student::whereIn('class_id', $gradeIds)->where('created_at', '>=', now()->subDay())->get();
            $studentIds = $students->pluck('id')->toArray();
            $studentUserIds = $students->pluck('user_id')->filter()->unique()->toArray();
            $parentIds = $students->pluck('parent_id')->filter()->unique()->toArray();

            $parents = Parents::whereIn('id', $parentIds)->where('created_at', '>=', now()->subDay())->get();
            $parentIdsToDelete = $parents->pluck('id')->toArray();
            $parentUserIds = $parents->pluck('user_id')->filter()->unique()->toArray();

            $userIds = array_values(array_unique(array_merge($studentUserIds, $parentUserIds)));

            $counts = [
                'students' => count($studentIds),
                'parents' => count($parentIdsToDelete),
                'users' => count($userIds),
            ];

            if (count($studentIds)) {
                Student::whereIn('id', $studentIds)->delete();
            }

            if (count($parentIdsToDelete)) {
                Parents::whereIn('id', $parentIdsToDelete)->delete();
            }

            if (count($userIds)) {
                User::whereIn('id', $userIds)->delete();
            }

            return $counts;
        });

        $this->info("Deleted {$deleted['students']} students, {$deleted['parents']} parents, {$deleted['users']} users.");
        return 0;
    }
}
