<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Module;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BasicCourseSeeder extends Seeder
{
    public function run(): void
    {
        // Temporarily disable FK checks to allow truncation (db-portable).
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
        }
        Module::truncate();
        Course::truncate();
        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON');
        }

        $courses = [
            [
                'title' => 'Newborn Wonder (0–1 yr)',
                'slug' => 'newborn-wonder',
                'description' => 'Sensory play, tummy time, black-and-white visual stimulation, and early bonding routines for babies aged 0–12 months.',
                'age_min' => 0,
                'age_max' => 1,
                'color' => '#FFB5C8',
                'emoji' => '👶',
                'modules' => [
                    'Sensory & Touch',
                    'Visual Tracking',
                    'Sound & Music',
                    'Tummy Time & Movement',
                    'Bonding & Parent Connection',
                    'Motor Skills & Grasp',
                    'Royal Etiquette & Personality',
                    'Quran & Islamic Studies',
                    'Arabic Introduction',
                    'World Cultures & Stories',
                    'Life Skills & Routines',
                    'Creative Expression',
                ],
            ],
            [
                'title' => 'Explorer at 1 (1–2 yrs)',
                'slug' => 'explorer-at-1',
                'description' => 'First words, walking milestones, cause-and-effect play, and simple sorting activities.',
                'age_min' => 1,
                'age_max' => 2,
                'color' => '#FFD54F',
                'emoji' => '🐣',
                'modules' => [
                    'First Words & Communication',
                    'Walking & Balance',
                    'Cause & Effect Play',
                    'Sorting & Shapes',
                    'Imitation & Social Play',
                    'Sensory Exploration',
                    'Royal Etiquette & Personality',
                    'Quran & Islamic Studies',
                    'Arabic Introduction',
                    'World Cultures & Stories',
                    'Life Skills & Routines',
                    'Creative Expression',
                ],
            ],
            [
                'title' => 'Curious Sprout (2–3 yrs)',
                'slug' => 'curious-sprout',
                'description' => 'Big emotions, potty training readiness, imaginative play, counting to 5, and arts & crafts.',
                'age_min' => 2,
                'age_max' => 3,
                'color' => '#AED581',
                'emoji' => '🌱',
                'modules' => [
                    'Emotions & Self-Regulation',
                    'Counting 1–5',
                    'Imaginative Play',
                    'Arts & Crafts Basics',
                    'Nature Exploration',
                    'Motor Skills & Balance',
                    'Royal Etiquette & Personality',
                    'Quran & Islamic Studies',
                    'Arabic Introduction',
                    'World Cultures & Stories',
                    'Life Skills & Routines',
                    'Creative Expression',
                ],
            ],
            [
                'title' => 'Little Learner (3–4 yrs)',
                'slug' => 'little-learner',
                'description' => 'Alphabet introduction, phonics foundations, numbers 1–10, storytelling, and social skills.',
                'age_min' => 3,
                'age_max' => 4,
                'color' => '#64B5F6',
                'emoji' => '📚',
                'modules' => [
                    'Alphabet & Phonics',
                    'Numbers 1–10',
                    'Storytelling & Listening',
                    'Sharing & Social Skills',
                    'Colours & Patterns',
                    'STEM Basics',
                    'Royal Etiquette & Personality',
                    'Quran & Islamic Studies',
                    'Arabic Introduction',
                    'World Cultures & Stories',
                    'Life Skills & Routines',
                    'Creative Expression',
                ],
            ],
            [
                'title' => 'Creative Mind (4–5 yrs)',
                'slug' => 'creative-mind',
                'description' => 'Drawing, painting, early writing, rhymes, counting to 20, and basic science experiments.',
                'age_min' => 4,
                'age_max' => 5,
                'color' => '#CE93D8',
                'emoji' => '🎨',
                'modules' => [
                    'Drawing & Painting',
                    'Early Writing',
                    'Rhymes & Poetry',
                    'Counting 11–20',
                    'Simple Science',
                    'Social-Emotional Learning',
                    'Royal Etiquette & Personality',
                    'Quran & Islamic Studies',
                    'Arabic Introduction',
                    'World Cultures & Stories',
                    'Life Skills & Routines',
                    'Creative Expression',
                ],
            ],
            [
                'title' => 'World Explorer (5–6 yrs)',
                'slug' => 'world-explorer',
                'description' => 'Reading readiness, global cultures, multilingual basics, geography, and collaborative projects.',
                'age_min' => 5,
                'age_max' => 6,
                'color' => '#FFB74D',
                'emoji' => '🌍',
                'modules' => [
                    'Reading Readiness',
                    'World Cultures',
                    'Multilingual Basics',
                    'Maps & Geography',
                    'Team Projects',
                    'STEM Discovery',
                    'Royal Etiquette & Personality',
                    'Quran & Islamic Studies',
                    'Arabic Introduction',
                    'Sensory & Motor Skills',
                    'Life Skills & Routines',
                    'Creative Expression',
                ],
            ],
            [
                'title' => 'Young Scientist (6–7 yrs)',
                'slug' => 'young-scientist',
                'description' => 'Scientific method, plant life cycles, simple machines, weather, and kitchen chemistry.',
                'age_min' => 6,
                'age_max' => 7,
                'color' => '#4DD0E1',
                'emoji' => '🔬',
                'modules' => [
                    'Scientific Method',
                    'Plant Life Cycles',
                    'Simple Machines',
                    'Weather & Climate',
                    'Kitchen Chemistry',
                    'Social-Emotional Learning',
                    'Royal Etiquette & Personality',
                    'Quran & Islamic Studies',
                    'Arabic Language',
                    'World Cultures & Stories',
                    'Life Skills & Independence',
                    'Creative Expression',
                ],
            ],
            [
                'title' => 'Tech Trailblazer (7–8 yrs)',
                'slug' => 'tech-trailblazer',
                'description' => 'Intro to robotics, block-based coding (Scratch), digital safety, and creative tech projects.',
                'age_min' => 7,
                'age_max' => 8,
                'color' => '#7986CB',
                'emoji' => '🤖',
                'modules' => [
                    'Block-Based Coding',
                    'Intro to Robotics',
                    'Digital Safety',
                    'Creative Tech Projects',
                    'Problem Solving with Code',
                    'Social-Emotional Learning',
                    'Royal Etiquette & Personality',
                    'Quran & Islamic Studies',
                    'Arabic Language',
                    'World Cultures & Stories',
                    'Life Skills & Independence',
                    'Creative Expression',
                ],
            ],
            [
                'title' => 'Code Creator (8–9 yrs)',
                'slug' => 'code-creator',
                'description' => 'Python fundamentals, game design basics, web literacy, and computational thinking challenges.',
                'age_min' => 8,
                'age_max' => 9,
                'color' => '#1565C0',
                'emoji' => '💻',
                'modules' => [
                    'Python Basics',
                    'Game Design',
                    'Web Literacy',
                    'Computational Thinking',
                    'Mini Hackathon',
                    'Social-Emotional Learning',
                    'Royal Etiquette & Personality',
                    'Quran & Islamic Studies',
                    'Arabic Language',
                    'World Cultures & Stories',
                    'Life Skills & Independence',
                    'Creative Expression',
                ],
            ],
            [
                'title' => 'Deep Thinker (9–10 yrs)',
                'slug' => 'deep-thinker',
                'description' => 'Logic & critical thinking, philosophy for kids, advanced maths puzzles, debate, and research skills.',
                'age_min' => 9,
                'age_max' => 10,
                'color' => '#7E57C2',
                'emoji' => '🧠',
                'modules' => [
                    'Logic & Reasoning',
                    'Philosophy for Kids',
                    'Advanced Maths Puzzles',
                    'Debate & Argumentation',
                    'Research Skills',
                    'Social-Emotional Learning',
                    'Royal Etiquette & Personality',
                    'Quran & Islamic Studies',
                    'Arabic Language',
                    'World Cultures & Stories',
                    'Life Skills & Independence',
                    'Creative Expression',
                ],
            ],
            [
                'title' => 'Future Leader (10 yrs)',
                'slug' => 'future-leader',
                'description' => 'Leadership, entrepreneurship, public speaking, global citizenship, and STEM project capstone.',
                'age_min' => 10,
                'age_max' => 10,
                'color' => '#3949AB',
                'emoji' => '🚀',
                'modules' => [
                    'Leadership Foundations',
                    'Entrepreneurship Basics',
                    'Public Speaking',
                    'Global Citizenship',
                    'STEM Capstone Project',
                    'Social-Emotional Learning',
                    'Royal Etiquette & Personality',
                    'Quran & Islamic Studies',
                    'Arabic Language',
                    'World Cultures & Stories',
                    'Life Skills & Independence',
                    'Creative Expression',
                ],
            ],
        ];

        foreach ($courses as $courseData) {
            $modules = $courseData['modules'];
            unset($courseData['modules']);

            $course = Course::create($courseData);

            foreach ($modules as $i => $moduleName) {
                Module::create([
                    'course_id' => $course->id,
                    'title' => $moduleName,
                    'order' => $i + 1,
                ]);
            }
        }
    }
}
