<?php

namespace App\Services;

use App\Assessment;
use App\AssessmentMark;
use App\SyllabusTopic;
use App\SchemeTopic;
use App\SchemeOfWork;
use App\TopicPerformanceSnapshot;
use App\RemedialLesson;
use App\Student;
use App\Grade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TopicPerformanceService
{
    const MASTERY_THRESHOLD = 75;
    const PARTIAL_THRESHOLD = 50;
    const REMEDIAL_THRESHOLD = 50;

    /**
     * Calculate performance for a specific syllabus topic in a class
     */
    public function calculateTopicPerformance(
        int $syllabusTopicId,
        int $classId,
        ?string $term = null,
        ?string $academicYear = null
    ): array {
        $topic = SyllabusTopic::find($syllabusTopicId);
        if (!$topic) {
            return ['success' => false, 'message' => 'Topic not found'];
        }

        $class = Grade::find($classId);
        if (!$class) {
            return ['success' => false, 'message' => 'Class not found'];
        }

        // Get all assessments for this topic and class
        $assessmentsQuery = Assessment::where('syllabus_topic_id', $syllabusTopicId)
            ->where('class_id', $classId);

        if ($term) {
            $assessmentsQuery->where('term', $term);
        }
        if ($academicYear) {
            $assessmentsQuery->where('academic_year', $academicYear);
        }

        $assessments = $assessmentsQuery->get();

        if ($assessments->isEmpty()) {
            return [
                'success' => true,
                'data' => [
                    'average_score' => null,
                    'mastery_level' => 'not_assessed',
                    'students_assessed' => 0,
                    'assessments_count' => 0
                ]
            ];
        }

        // Get all marks for these assessments
        $assessmentIds = $assessments->pluck('id');
        $marks = AssessmentMark::whereIn('assessment_id', $assessmentIds)
            ->whereNotNull('mark')
            ->get();

        if ($marks->isEmpty()) {
            return [
                'success' => true,
                'data' => [
                    'average_score' => null,
                    'mastery_level' => 'not_assessed',
                    'students_assessed' => 0,
                    'assessments_count' => $assessments->count()
                ]
            ];
        }

        // Calculate performance metrics
        $totalStudents = $class->students()->count();
        $studentsAssessed = $marks->pluck('student_id')->unique()->count();

        // Calculate weighted average per student, then overall average
        $studentScores = [];
        foreach ($marks as $mark) {
            $studentId = $mark->student_id;
            if (!isset($studentScores[$studentId])) {
                $studentScores[$studentId] = ['total' => 0, 'max' => 0];
            }
            $studentScores[$studentId]['total'] += $mark->mark;
            $studentScores[$studentId]['max'] += $mark->total_marks;
        }

        $percentages = [];
        foreach ($studentScores as $scores) {
            if ($scores['max'] > 0) {
                $percentages[] = ($scores['total'] / $scores['max']) * 100;
            }
        }

        $averageScore = count($percentages) > 0 ? array_sum($percentages) / count($percentages) : 0;
        $highestScore = count($percentages) > 0 ? max($percentages) : null;
        $lowestScore = count($percentages) > 0 ? min($percentages) : null;
        
        // Calculate pass rate (students >= 50%)
        $passCount = count(array_filter($percentages, fn($p) => $p >= self::PARTIAL_THRESHOLD));
        $passRate = count($percentages) > 0 ? ($passCount / count($percentages)) * 100 : 0;

        // Determine mastery level
        $masteryLevel = $this->determineMasteryLevel($averageScore);

        return [
            'success' => true,
            'data' => [
                'average_score' => round($averageScore, 2),
                'highest_score' => $highestScore ? round($highestScore, 2) : null,
                'lowest_score' => $lowestScore ? round($lowestScore, 2) : null,
                'pass_rate' => round($passRate, 2),
                'mastery_level' => $masteryLevel,
                'students_assessed' => $studentsAssessed,
                'total_students' => $totalStudents,
                'assessments_count' => $assessments->count()
            ]
        ];
    }

    /**
     * Determine mastery level based on average score
     */
    public function determineMasteryLevel(float $averageScore): string
    {
        if ($averageScore >= self::MASTERY_THRESHOLD) {
            return 'mastered';
        } elseif ($averageScore >= self::PARTIAL_THRESHOLD) {
            return 'partial';
        } else {
            return 'weak';
        }
    }

    /**
     * Create or update a topic performance snapshot
     */
    public function createPerformanceSnapshot(
        int $syllabusTopicId,
        int $classId,
        string $term,
        string $academicYear,
        ?int $teacherId = null
    ): ?TopicPerformanceSnapshot {
        $result = $this->calculateTopicPerformance($syllabusTopicId, $classId, $term, $academicYear);

        if (!$result['success'] || $result['data']['average_score'] === null) {
            return null;
        }

        $topic = SyllabusTopic::find($syllabusTopicId);
        $data = $result['data'];

        return TopicPerformanceSnapshot::updateOrCreate(
            [
                'syllabus_topic_id' => $syllabusTopicId,
                'class_id' => $classId,
                'term' => $term,
                'academic_year' => $academicYear
            ],
            [
                'subject_id' => $topic->subject_id,
                'teacher_id' => $teacherId,
                'students_assessed' => $data['students_assessed'],
                'total_students' => $data['total_students'],
                'average_score' => $data['average_score'],
                'highest_score' => $data['highest_score'],
                'lowest_score' => $data['lowest_score'],
                'pass_rate' => $data['pass_rate'],
                'mastery_level' => $data['mastery_level'],
                'assessments_count' => $data['assessments_count'],
                'calculated_at' => now()
            ]
        );
    }

    /**
     * Update scheme topic performance from assessment data
     */
    public function updateSchemeTopicPerformance(SchemeTopic $schemeTopic): void
    {
        $scheme = $schemeTopic->scheme;
        
        $result = $this->calculateTopicPerformance(
            $schemeTopic->syllabus_topic_id,
            $scheme->class_id,
            $scheme->term,
            $scheme->academic_year
        );

        if ($result['success'] && $result['data']['average_score'] !== null) {
            $schemeTopic->actual_performance = $result['data']['average_score'];
            $schemeTopic->updateMasteryLevel();
        }
    }

    /**
     * Get historical performance data for a topic
     */
    public function getTopicHistoricalPerformance(int $syllabusTopicId, int $classId = null): array
    {
        $query = TopicPerformanceSnapshot::where('syllabus_topic_id', $syllabusTopicId)
            ->orderBy('academic_year', 'desc')
            ->orderBy('term', 'desc');

        if ($classId) {
            $query->where('class_id', $classId);
        }

        $snapshots = $query->get();

        return $snapshots->map(function ($snapshot) {
            return [
                'term' => $snapshot->term,
                'academic_year' => $snapshot->academic_year,
                'average_score' => $snapshot->average_score,
                'mastery_level' => $snapshot->mastery_level,
                'pass_rate' => $snapshot->pass_rate,
                'class_name' => $snapshot->class->class_name ?? 'N/A'
            ];
        })->toArray();
    }

    /**
     * Get suggested periods based on historical performance
     */
    public function getSuggestedPeriods(int $syllabusTopicId, int $classId = null): int
    {
        $topic = SyllabusTopic::find($syllabusTopicId);
        if (!$topic) {
            return 1;
        }

        $basePeriods = $topic->suggested_periods;

        // Get latest performance snapshot
        $query = TopicPerformanceSnapshot::where('syllabus_topic_id', $syllabusTopicId)
            ->orderBy('calculated_at', 'desc');

        if ($classId) {
            $query->where('class_id', $classId);
        }

        $latestSnapshot = $query->first();

        if (!$latestSnapshot) {
            return $basePeriods;
        }

        // Adjust periods based on mastery level
        switch ($latestSnapshot->mastery_level) {
            case 'weak': return (int) ceil($basePeriods * 1.5); // 50% more time for weak topics
            case 'partial': return (int) ceil($basePeriods * 1.2); // 20% more time for partial mastery
            case 'mastered': return $basePeriods; // Keep standard time for mastered topics
            default: return $basePeriods;
        }
    }

    /**
     * Get class-wide topic performance heatmap data
     */
    public function getClassTopicHeatmap(int $classId, int $subjectId, string $term, string $academicYear): array
    {
        $topics = SyllabusTopic::where('subject_id', $subjectId)
            ->active()
            ->ordered()
            ->get();

        $heatmapData = [];

        foreach ($topics as $topic) {
            $snapshot = TopicPerformanceSnapshot::where('syllabus_topic_id', $topic->id)
                ->where('class_id', $classId)
                ->where('term', $term)
                ->where('academic_year', $academicYear)
                ->first();

            $heatmapData[] = [
                'topic_id' => $topic->id,
                'topic_name' => $topic->name,
                'average_score' => $snapshot ? $snapshot->average_score : null,
                'mastery_level' => $snapshot ? $snapshot->mastery_level : 'not_assessed',
                'pass_rate' => $snapshot ? $snapshot->pass_rate : null,
                'students_assessed' => $snapshot ? $snapshot->students_assessed : 0,
                'color' => $this->getMasteryColor($snapshot ? $snapshot->mastery_level : 'not_assessed')
            ];
        }

        return $heatmapData;
    }

    /**
     * Get mastery color for UI display
     */
    public function getMasteryColor(string $masteryLevel): string
    {
        switch ($masteryLevel) {
            case 'mastered': return 'green';
            case 'partial': return 'yellow';
            case 'weak': return 'red';
            default: return 'gray';
        }
    }

    /**
     * Check if remedial is required and trigger it
     */
    public function checkAndTriggerRemedial(SchemeTopic $schemeTopic): ?RemedialLesson
    {
        if (!$schemeTopic->remedial_required) {
            return null;
        }

        // Check if remedial already exists for this scheme topic
        $existingRemedial = RemedialLesson::where('scheme_topic_id', $schemeTopic->id)
            ->whereIn('status', ['pending', 'scheduled', 'in_progress'])
            ->first();

        if ($existingRemedial) {
            return $existingRemedial;
        }

        $scheme = $schemeTopic->scheme;
        $syllabusTopic = $schemeTopic->syllabusTopic;

        // Create remedial lesson
        $remedialLesson = RemedialLesson::create([
            'syllabus_topic_id' => $schemeTopic->syllabus_topic_id,
            'scheme_topic_id' => $schemeTopic->id,
            'class_id' => $scheme->class_id,
            'subject_id' => $scheme->subject_id,
            'teacher_id' => $scheme->teacher_id,
            'title' => 'Remedial: ' . $syllabusTopic->name,
            'description' => 'Auto-generated remedial lesson for topic with low performance',
            'objectives' => $syllabusTopic->learning_objectives,
            'status' => 'pending',
            'trigger_type' => 'auto',
            'trigger_score' => $schemeTopic->actual_performance,
            'pre_remedial_avg' => $schemeTopic->actual_performance
        ]);

        // Add students who scored below threshold
        $this->assignStudentsToRemedial($remedialLesson, $schemeTopic);

        Log::info('Auto-triggered remedial lesson', [
            'remedial_id' => $remedialLesson->id,
            'topic' => $syllabusTopic->name,
            'trigger_score' => $schemeTopic->actual_performance
        ]);

        return $remedialLesson;
    }

    /**
     * Assign students who need remedial to the lesson
     */
    protected function assignStudentsToRemedial(RemedialLesson $remedialLesson, SchemeTopic $schemeTopic): void
    {
        $scheme = $schemeTopic->scheme;

        // Get assessment marks for this topic
        $assessments = Assessment::where('syllabus_topic_id', $schemeTopic->syllabus_topic_id)
            ->where('class_id', $scheme->class_id)
            ->where('term', $scheme->term)
            ->where('academic_year', $scheme->academic_year)
            ->pluck('id');

        if ($assessments->isEmpty()) {
            return;
        }

        // Get students with marks below threshold
        $studentScores = AssessmentMark::whereIn('assessment_id', $assessments)
            ->whereNotNull('mark')
            ->select('student_id', DB::raw('SUM(mark) as total_marks'), DB::raw('SUM(total_marks) as max_marks'))
            ->groupBy('student_id')
            ->get();

        foreach ($studentScores as $score) {
            if ($score->max_marks > 0) {
                $percentage = ($score->total_marks / $score->max_marks) * 100;
                
                if ($percentage < self::REMEDIAL_THRESHOLD) {
                    $remedialLesson->students()->attach($score->student_id, [
                        'pre_score' => round($percentage, 2)
                    ]);
                }
            }
        }
    }

    /**
     * Recalculate all topic performances for a scheme
     */
    public function recalculateSchemePerformance(SchemeOfWork $scheme): void
    {
        foreach ($scheme->schemeTopics as $schemeTopic) {
            $this->updateSchemeTopicPerformance($schemeTopic);
            
            // Check for remedial triggers
            if ($schemeTopic->remedial_required) {
                $this->checkAndTriggerRemedial($schemeTopic);
            }
        }

        // Recalculate scheme totals
        $scheme->recalculateTotals();
    }

    /**
     * Get weak topics for a teacher across all their classes
     */
    public function getWeakTopicsForTeacher(int $teacherId, string $term, string $academicYear): array
    {
        return TopicPerformanceSnapshot::where('teacher_id', $teacherId)
            ->where('term', $term)
            ->where('academic_year', $academicYear)
            ->where('mastery_level', 'weak')
            ->with(['syllabusTopic', 'class', 'subject'])
            ->orderBy('average_score', 'asc')
            ->get()
            ->map(function ($snapshot) {
                return [
                    'topic_name' => $snapshot->syllabusTopic->name ?? 'Unknown',
                    'class_name' => $snapshot->class->class_name ?? 'Unknown',
                    'subject_name' => $snapshot->subject->name ?? 'Unknown',
                    'average_score' => $snapshot->average_score,
                    'pass_rate' => $snapshot->pass_rate,
                    'students_assessed' => $snapshot->students_assessed
                ];
            })
            ->toArray();
    }

    /**
     * Compare topic performance across classes
     */
    public function compareTopicAcrossClasses(int $syllabusTopicId, string $term, string $academicYear): array
    {
        return TopicPerformanceSnapshot::where('syllabus_topic_id', $syllabusTopicId)
            ->where('term', $term)
            ->where('academic_year', $academicYear)
            ->with(['class', 'teacher.user'])
            ->orderBy('average_score', 'desc')
            ->get()
            ->map(function ($snapshot) {
                return [
                    'class_name' => $snapshot->class->class_name ?? 'Unknown',
                    'teacher_name' => $snapshot->teacher->user->name ?? 'Unknown',
                    'average_score' => $snapshot->average_score,
                    'pass_rate' => $snapshot->pass_rate,
                    'mastery_level' => $snapshot->mastery_level
                ];
            })
            ->toArray();
    }
}
