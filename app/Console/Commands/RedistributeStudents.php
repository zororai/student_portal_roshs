<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Student;
use App\Grade;
use Illuminate\Support\Facades\DB;

class RedistributeStudents extends Command
{
    protected $signature = 'students:redistribute';
    protected $description = 'Redistribute students evenly across all grades';

    public function handle()
    {
        $this->info("=== Redistributing Students ===\n");
        
        // Get all active students
        $students = Student::where('is_transferred', false)->get()->shuffle();
        $totalStudents = $students->count();
        
        // Get all grades grouped by class_numeric
        $gradesByLevel = Grade::orderBy('class_numeric')->get()->groupBy('class_numeric');
        $numLevels = $gradesByLevel->count();
        
        $this->info("Total active students: $totalStudents");
        $this->info("Grade levels: $numLevels\n");
        
        if (!$this->confirm("Redistribute $totalStudents students across $numLevels grade levels?")) {
            return 0;
        }

        try {
            DB::beginTransaction();
            
            // Calculate students per level
            $studentsPerLevel = (int) ceil($totalStudents / $numLevels);
            
            $studentIndex = 0;
            $distribution = [];
            
            foreach ($gradesByLevel as $level => $gradesAtLevel) {
                $gradesArray = $gradesAtLevel->values();
                $numClasses = $gradesArray->count();
                $studentsForThisLevel = min($studentsPerLevel, $totalStudents - $studentIndex);
                $studentsPerClass = (int) ceil($studentsForThisLevel / $numClasses);
                
                foreach ($gradesArray as $classIndex => $grade) {
                    $classStudentCount = 0;
                    
                    // Assign students to this class
                    while ($studentIndex < $totalStudents && $classStudentCount < $studentsPerClass) {
                        // Check if we've already assigned enough to this level
                        $totalAssignedToLevel = collect($distribution)
                            ->filter(fn($d) => $gradesAtLevel->pluck('id')->contains($d['grade_id']))
                            ->sum('count');
                        
                        if ($totalAssignedToLevel >= $studentsForThisLevel && $classIndex < $numClasses - 1) {
                            break;
                        }
                        
                        $student = $students[$studentIndex];
                        $student->class_id = $grade->id;
                        $student->save();
                        
                        $studentIndex++;
                        $classStudentCount++;
                    }
                    
                    if ($classStudentCount > 0) {
                        $distribution[] = [
                            'grade_id' => $grade->id,
                            'grade_name' => $grade->class_name,
                            'count' => $classStudentCount,
                        ];
                        $this->line("  {$grade->class_name}: $classStudentCount students");
                    }
                }
            }
            
            DB::commit();
            
            $this->info("\n=== Final Distribution ===");
            foreach (Grade::orderBy('class_numeric')->get() as $grade) {
                $count = Student::where('class_id', $grade->id)->where('is_transferred', false)->count();
                $this->line("  {$grade->class_name}: $count students");
            }
            
            $this->info("\nRedistribution complete!");
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Redistribution failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
