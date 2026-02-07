<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Student;
use App\Grade;
use Illuminate\Support\Facades\DB;

class RollbackStudentUpgrade extends Command
{
    protected $signature = 'students:rollback-upgrade {--steps=3 : Number of upgrade steps to rollback}';
    protected $description = 'Rollback student upgrades by moving students back to previous grades';

    public function handle()
    {
        $steps = (int) $this->option('steps');
        
        $this->info("Rolling back $steps upgrade steps...");
        
        // Get all grades ordered by class_numeric
        $grades = Grade::orderBy('class_numeric', 'asc')->get();
        
        $this->info("\nCurrent distribution:");
        foreach ($grades as $grade) {
            $count = Student::where('class_id', $grade->id)->where('is_transferred', false)->count();
            $this->line("  {$grade->class_name}: $count students");
        }
        $transferred = Student::where('is_transferred', true)->count();
        $this->line("  Transferred/Graduated: $transferred students");
        
        if (!$this->confirm("\nThis will rollback $steps upgrade steps. Continue?")) {
            return 0;
        }

        try {
            DB::beginTransaction();

            for ($step = 1; $step <= $steps; $step++) {
                $this->info("\n--- Rollback Step $step ---");
                
                // First, un-graduate students from the highest grade
                $highestGrade = Grade::orderBy('class_numeric', 'desc')->first();
                $graduatedStudents = Student::where('is_transferred', true)
                    ->orderBy('updated_at', 'desc')
                    ->limit(100) // Limit per step
                    ->get();
                
                // Move students down one grade (in reverse order - lowest first)
                $gradesDesc = Grade::orderBy('class_numeric', 'desc')->get();
                
                foreach ($gradesDesc as $grade) {
                    // Find the previous grade (lower class_numeric)
                    $prevGrade = Grade::where('class_numeric', $grade->class_numeric - 1)->first();
                    
                    if (!$prevGrade) {
                        continue; // This is the lowest grade
                    }
                    
                    // Get students in this grade and move them to previous grade
                    $studentsInGrade = Student::where('class_id', $grade->id)
                        ->where('is_transferred', false)
                        ->get();
                    
                    foreach ($studentsInGrade as $student) {
                        $student->class_id = $prevGrade->id;
                        $student->save();
                    }
                    
                    if ($studentsInGrade->count() > 0) {
                        $this->line("  Moved {$studentsInGrade->count()} from {$grade->class_name} to {$prevGrade->class_name}");
                    }
                }
                
                // Un-graduate some students back to highest grade
                $toUngraduate = min(50, $graduatedStudents->count());
                $ungraduated = 0;
                foreach ($graduatedStudents->take($toUngraduate) as $student) {
                    $student->is_transferred = false;
                    $student->class_id = $highestGrade->id;
                    $student->save();
                    $ungraduated++;
                }
                if ($ungraduated > 0) {
                    $this->line("  Un-graduated $ungraduated students back to {$highestGrade->class_name}");
                }
            }

            DB::commit();
            
            $this->info("\n=== After Rollback ===");
            foreach ($grades as $grade) {
                $count = Student::where('class_id', $grade->id)->where('is_transferred', false)->count();
                $this->line("  {$grade->class_name}: $count students");
            }
            $transferred = Student::where('is_transferred', true)->count();
            $this->line("  Transferred/Graduated: $transferred students");
            
            $this->info("\nRollback complete!");
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Rollback failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
