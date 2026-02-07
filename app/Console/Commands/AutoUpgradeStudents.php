<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Student;
use App\Grade;
use App\User;
use App\AuditTrail;
use App\SchoolNotification;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AutoUpgradeStudents extends Command
{
    protected $signature = 'students:auto-upgrade {--force : Force upgrade without year-end check}';
    protected $description = 'Automatically upgrade all students to the next grade at year end';

    public function handle()
    {
        $now = Carbon::now();
        
        // Only run at year end (December) unless forced
        if (!$this->option('force') && $now->month !== 12) {
            $this->info('Auto upgrade only runs in December. Use --force to override.');
            return 0;
        }

        $this->info('Starting automatic student upgrade process...');

        try {
            DB::beginTransaction();

            $classes = Grade::orderBy('class_numeric', 'desc')->get();
            $upgradedCount = 0;
            $graduatedCount = 0;
            $upgradeDetails = [];

            foreach ($classes as $class) {
                // Find all classes at next level
                $nextClasses = Grade::where('class_numeric', $class->class_numeric + 1)->get();
                
                $studentsToUpgrade = Student::where('class_id', $class->id)
                    ->where('is_transferred', false)
                    ->get();

                if ($studentsToUpgrade->isEmpty()) {
                    continue;
                }

                if ($nextClasses->isNotEmpty()) {
                    // Upgrade students to next class level, distributing across available classes
                    $totalNextClasses = $nextClasses->count();
                    $studentsPerClass = floor($studentsToUpgrade->count() / $totalNextClasses);
                    $remainder = $studentsToUpgrade->count() % $totalNextClasses;
                    
                    $studentIndex = 0;
                    $classDistribution = [];
                    
                    foreach ($nextClasses as $index => $nextClass) {
                        // Calculate how many students for this class (add 1 extra for remainder distribution)
                        $studentsForThisClass = $studentsPerClass + ($index < $remainder ? 1 : 0);
                        
                        for ($i = 0; $i < $studentsForThisClass && $studentIndex < $studentsToUpgrade->count(); $i++) {
                            $student = $studentsToUpgrade[$studentIndex];
                            $student->class_id = $nextClass->id;
                            $student->save();
                            $upgradedCount++;
                            $studentIndex++;
                        }
                        
                        if ($studentsForThisClass > 0) {
                            $classDistribution[] = "{$nextClass->class_name} ({$studentsForThisClass})";
                        }
                    }

                    $upgradeDetails[] = [
                        'from' => $class->class_name,
                        'to' => implode(', ', $classDistribution),
                        'count' => $studentsToUpgrade->count(),
                    ];

                    $this->info("Upgraded {$studentsToUpgrade->count()} students from {$class->class_name} to " . implode(', ', $classDistribution));
                } else {
                    // Final class - mark students as graduated
                    foreach ($studentsToUpgrade as $student) {
                        $student->is_transferred = true;
                        $student->save();
                        $graduatedCount++;
                    }

                    $upgradeDetails[] = [
                        'from' => $class->class_name,
                        'to' => 'Graduated',
                        'count' => $studentsToUpgrade->count(),
                    ];

                    $this->info("Graduated {$studentsToUpgrade->count()} students from {$class->class_name}");
                }
            }

            DB::commit();

            // Log the upgrade action
            AuditTrail::create([
                'user_id' => null,
                'user_name' => 'System',
                'user_role' => 'System',
                'action' => 'update',
                'description' => "[AUTO] Upgraded $upgradedCount students to next class level. $graduatedCount students graduated.",
                'old_values' => json_encode(['action' => 'automatic_student_class_upgrade', 'year' => $now->year]),
                'new_values' => json_encode($upgradeDetails),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Scheduler',
            ]);

            // Notify all admins
            $this->notifyAdmins($upgradedCount, $graduatedCount, $upgradeDetails, $now->year);

            $this->info("✓ Automatic upgrade complete: $upgradedCount students upgraded, $graduatedCount students graduated.");

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Failed to upgrade students: ' . $e->getMessage());
            
            // Notify admins about the failure with detailed error info
            $errorDetails = [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ];
            $this->notifyAdminsAboutFailure($errorDetails, $now->year);
            
            return 1;
        }
    }

    private function notifyAdmins($upgradedCount, $graduatedCount, $upgradeDetails, $year)
    {
        $admins = User::role('Admin')->get();
        
        // Get a system admin user for sent_by field (use first admin)
        $systemAdmin = $admins->first();
        
        if (!$systemAdmin) {
            $this->warn('No admin users found to send notification.');
            return;
        }

        // Create a single notification for all admins
        SchoolNotification::create([
            'title' => 'Annual Student Upgrade Complete',
            'message' => "The automatic year-end student upgrade for {$year} has been completed successfully.\n\n" .
                        "- Students upgraded: {$upgradedCount}\n" .
                        "- Students graduated: {$graduatedCount}\n\n" .
                        "Please review the changes in the Student Upgrade section.",
            'recipient_type' => 'individual',
            'recipient_id' => null,
            'sent_by' => $systemAdmin->id,
            'priority' => 'high',
        ]);

        $this->info("Created upgrade notification for admin review.");
    }

    private function notifyAdminsAboutFailure($errorDetails, $year)
    {
        $admins = User::role('Admin')->get();
        
        $systemAdmin = $admins->first();
        
        if (!$systemAdmin) {
            return;
        }

        $errorMessage = is_array($errorDetails) 
            ? $errorDetails['message'] 
            : $errorDetails;
        
        $errorLocation = is_array($errorDetails) 
            ? "\nLocation: {$errorDetails['file']} (Line {$errorDetails['line']})" 
            : '';

        SchoolNotification::create([
            'title' => 'Student Upgrade Failed',
            'message' => "The automatic year-end student upgrade for {$year} has failed.\n\n" .
                        "Error: {$errorMessage}{$errorLocation}\n\n" .
                        "Please perform the upgrade manually from the Student Upgrade section at /admin/student-upgrade",
            'recipient_type' => 'individual',
            'recipient_id' => null,
            'sent_by' => $systemAdmin->id,
            'priority' => 'urgent',
        ]);
    }

    private function findNextClass($currentClass)
    {
        $nextNumeric = $currentClass->class_numeric + 1;
        
        // Get all classes at the next level
        $nextLevelClasses = Grade::where('class_numeric', $nextNumeric)->get();
        
        if ($nextLevelClasses->isEmpty()) {
            return null; // No next level - student graduates
        }
        
        // If only one class at next level, use it
        if ($nextLevelClasses->count() === 1) {
            return $nextLevelClasses->first();
        }
        
        // Try to match stream name (e.g., "Form 5 Sciences" -> "Form 6 Sciences")
        $currentStream = $this->extractStream($currentClass->class_name);
        
        foreach ($nextLevelClasses as $nextClass) {
            $nextStream = $this->extractStream($nextClass->class_name);
            if ($currentStream && $nextStream && strtolower($currentStream) === strtolower($nextStream)) {
                return $nextClass;
            }
        }
        
        // If no matching stream, return the first available class at next level
        return $nextLevelClasses->first();
    }

    private function extractStream($className)
    {
        // Extract stream from class name like "Form 5 Sciences" -> "Sciences"
        // or "Form 4 Red" -> "Red"
        $parts = explode(' ', $className);
        if (count($parts) >= 3) {
            return end($parts); // Return last word as stream
        }
        return null;
    }
}
