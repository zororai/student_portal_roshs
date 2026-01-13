<?php

use Illuminate\Database\Seeder;
use App\SyllabusTopic;
use App\Subject;

class SyllabusTopicsSeeder extends Seeder
{
    public function run()
    {
        // Get some subjects (adjust IDs based on your database)
        $mathematics = Subject::where('name', 'LIKE', '%Mathematics%')->first();
        $english = Subject::where('name', 'LIKE', '%English%')->first();
        $science = Subject::where('name', 'LIKE', '%Science%')->first();

        if ($mathematics) {
            $mathTopics = [
                ['name' => 'Algebra - Linear Equations', 'description' => 'Solving linear equations and inequalities', 'difficulty_level' => 'medium', 'suggested_periods' => 4, 'order_index' => 1],
                ['name' => 'Algebra - Quadratic Equations', 'description' => 'Solving quadratic equations using various methods', 'difficulty_level' => 'hard', 'suggested_periods' => 6, 'order_index' => 2],
                ['name' => 'Geometry - Triangles', 'description' => 'Properties and theorems of triangles', 'difficulty_level' => 'medium', 'suggested_periods' => 5, 'order_index' => 3],
                ['name' => 'Geometry - Circles', 'description' => 'Circle theorems and properties', 'difficulty_level' => 'hard', 'suggested_periods' => 5, 'order_index' => 4],
                ['name' => 'Statistics - Data Representation', 'description' => 'Bar charts, pie charts, histograms', 'difficulty_level' => 'easy', 'suggested_periods' => 3, 'order_index' => 5],
                ['name' => 'Probability - Basic Concepts', 'description' => 'Introduction to probability and simple events', 'difficulty_level' => 'medium', 'suggested_periods' => 4, 'order_index' => 6],
            ];

            foreach ($mathTopics as $topic) {
                SyllabusTopic::create(array_merge($topic, [
                    'subject_id' => $mathematics->id,
                    'term' => 'Term 1',
                    'is_active' => true
                ]));
            }
        }

        if ($english) {
            $englishTopics = [
                ['name' => 'Grammar - Parts of Speech', 'description' => 'Nouns, verbs, adjectives, adverbs', 'difficulty_level' => 'easy', 'suggested_periods' => 3, 'order_index' => 1],
                ['name' => 'Grammar - Tenses', 'description' => 'Present, past, and future tenses', 'difficulty_level' => 'medium', 'suggested_periods' => 4, 'order_index' => 2],
                ['name' => 'Writing - Essay Structure', 'description' => 'Introduction, body, conclusion', 'difficulty_level' => 'medium', 'suggested_periods' => 5, 'order_index' => 3],
                ['name' => 'Literature - Poetry Analysis', 'description' => 'Analyzing themes, metaphors, and literary devices', 'difficulty_level' => 'hard', 'suggested_periods' => 6, 'order_index' => 4],
                ['name' => 'Comprehension - Reading Skills', 'description' => 'Understanding and interpreting texts', 'difficulty_level' => 'medium', 'suggested_periods' => 4, 'order_index' => 5],
            ];

            foreach ($englishTopics as $topic) {
                SyllabusTopic::create(array_merge($topic, [
                    'subject_id' => $english->id,
                    'term' => 'Term 1',
                    'is_active' => true
                ]));
            }
        }

        if ($science) {
            $scienceTopics = [
                ['name' => 'Biology - Cell Structure', 'description' => 'Plant and animal cells, organelles', 'difficulty_level' => 'medium', 'suggested_periods' => 4, 'order_index' => 1],
                ['name' => 'Biology - Photosynthesis', 'description' => 'Process of photosynthesis in plants', 'difficulty_level' => 'hard', 'suggested_periods' => 5, 'order_index' => 2],
                ['name' => 'Chemistry - Atomic Structure', 'description' => 'Protons, neutrons, electrons', 'difficulty_level' => 'medium', 'suggested_periods' => 4, 'order_index' => 3],
                ['name' => 'Chemistry - Chemical Reactions', 'description' => 'Types of chemical reactions', 'difficulty_level' => 'hard', 'suggested_periods' => 6, 'order_index' => 4],
                ['name' => 'Physics - Forces and Motion', 'description' => 'Newton\'s laws of motion', 'difficulty_level' => 'medium', 'suggested_periods' => 5, 'order_index' => 5],
                ['name' => 'Physics - Energy', 'description' => 'Forms of energy and energy transfer', 'difficulty_level' => 'medium', 'suggested_periods' => 4, 'order_index' => 6],
            ];

            foreach ($scienceTopics as $topic) {
                SyllabusTopic::create(array_merge($topic, [
                    'subject_id' => $science->id,
                    'term' => 'Term 1',
                    'is_active' => true
                ]));
            }
        }

        $this->command->info('Syllabus topics seeded successfully!');
    }
}
