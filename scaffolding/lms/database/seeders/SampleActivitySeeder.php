<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activity;

class SampleActivitySeeder extends Seeder
{
    public function run(): void
    {
        $activities = [
            // 0–1 year
            ['id'=>1, 'title'=>'Guided tummy‑time videos', 'type'=>'video', 'description'=>'Video guidance for sensory and motor development.', 'age_min'=>0, 'age_max'=>1, 'skill'=>'Sensory & Motor Development', 'language'=>'en', 'media_url'=>'/media/tummytime.mp4'],
            ['id'=>2, 'title'=>'Soothing songs (multi-language)', 'type'=>'video', 'description'=>'Lullabies and soothing music in multiple languages.', 'age_min'=>0, 'age_max'=>1, 'skill'=>'Early Language Exposure', 'language'=>'multi', 'media_url'=>'/media/soothing_songs.mp3'],
            ['id'=>3, 'title'=>'Baby sign language sessions', 'type'=>'video', 'description'=>'Sign language basics for infants and parents.', 'age_min'=>0, 'age_max'=>1, 'skill'=>'Early Language Exposure', 'language'=>'en', 'media_url'=>'/media/sign_language.mp4'],
            ['id'=>4, 'title'=>'Parent modules on reading cues', 'type'=>'video', 'description'=>'Learn to read and respond to infant cues.', 'age_min'=>0, 'age_max'=>1, 'skill'=>'Parent–Infant Bonding', 'language'=>'en', 'media_url'=>'/media/parent_cues.mp4'],
            ['id'=>5, 'title'=>'Gentle lullabies (EN, Mandarin, Spanish)', 'type'=>'video', 'description'=>'Lullabies in English, Mandarin, and Spanish.', 'age_min'=>0, 'age_max'=>1, 'skill'=>'Parent–Infant Bonding', 'language'=>'multi', 'media_url'=>'/media/lullabies.mp3'],
            // 1–2 years
            ['id'=>6, 'title'=>'Interactive videos: first words', 'type'=>'video', 'description'=>'Videos encouraging first words and phrases.', 'age_min'=>1, 'age_max'=>2, 'skill'=>'Language Development', 'language'=>'en', 'media_url'=>'/media/first_words.mp4'],
            ['id'=>7, 'title'=>'Tracing simple shapes/lines', 'type'=>'tracing', 'description'=>'Trace basic shapes and lines for motor skills.', 'age_min'=>1, 'age_max'=>2, 'skill'=>'Fine Motor Skills', 'language'=>'en', 'media_url'=>'/media/tracing_shapes.mp4'],
            ['id'=>8, 'title'=>'Music‑and‑movement games', 'type'=>'video', 'description'=>'Games to encourage movement and rhythm.', 'age_min'=>1, 'age_max'=>2, 'skill'=>'Physical Development', 'language'=>'en', 'media_url'=>'/media/music_movement.mp4'],
            ['id'=>9, 'title'=>'Social play tips for parents', 'type'=>'video', 'description'=>'Tips for sharing and turn-taking.', 'age_min'=>1, 'age_max'=>2, 'skill'=>'Social Skills', 'language'=>'en', 'media_url'=>'/media/social_play.mp4'],
            // 2–3 years
            ['id'=>10, 'title'=>'Storytelling sessions', 'type'=>'video', 'description'=>'AI-narrated cross-cultural stories.', 'age_min'=>2, 'age_max'=>3, 'skill'=>'Language and Literacy', 'language'=>'multi', 'media_url'=>'/media/storytelling.mp4'],
            ['id'=>11, 'title'=>'Songs/dances (Japan, China, Scandinavia)', 'type'=>'video', 'description'=>'Songs and dances from around the world.', 'age_min'=>2, 'age_max'=>3, 'skill'=>'Cultural Awareness', 'language'=>'multi', 'media_url'=>'/media/songs_dances.mp4'],
            ['id'=>12, 'title'=>'Tracing numbers/letters', 'type'=>'tracing', 'description'=>'Trace numbers and letters for fine motor skills.', 'age_min'=>2, 'age_max'=>3, 'skill'=>'Literacy and Numeracy', 'language'=>'en', 'media_url'=>'/media/tracing_numbers_letters.mp4'],
            ['id'=>13, 'title'=>'Simple art projects', 'type'=>'drawing', 'description'=>'Easy art projects for creativity.', 'age_min'=>2, 'age_max'=>3, 'skill'=>'Creativity', 'language'=>'en', 'media_url'=>'/media/art_projects.mp4'],
            ['id'=>14, 'title'=>'Videos: polite greetings', 'type'=>'video', 'description'=>'Short videos modeling polite greetings.', 'age_min'=>2, 'age_max'=>3, 'skill'=>'Social Skills', 'language'=>'multi', 'media_url'=>'/media/polite_greetings.mp4'],
            // 2–3 years (Basic Etiquette)
            ['id'=>40, 'title'=>'Polite Greetings for Toddlers', 'type'=>'video', 'description'=>'Short videos modeling polite greetings (please, thank you).', 'age_min'=>2, 'age_max'=>3, 'skill'=>'Basic Etiquette', 'language'=>'multi', 'media_url'=>'/media/polite_greetings_toddlers.mp4'],
            ['id'=>44, 'title'=>'Polite Words Tracing', 'type'=>'tracing', 'description'=>'Trace the words "please" and "thank you" in multiple scripts.', 'age_min'=>2, 'age_max'=>3, 'skill'=>'Basic Etiquette', 'language'=>'multi', 'media_url'=>'/media/tracing_polite_words.png'],
            ['id'=>45, 'title'=>'Polite Greetings Quiz', 'type'=>'quiz', 'description'=>'Simple quiz: When do you say please/thank you?', 'age_min'=>2, 'age_max'=>3, 'skill'=>'Basic Etiquette', 'language'=>'multi', 'media_url'=>''],
            // 3–4 years
            ['id'=>15, 'title'=>'Tracing alphabets (multi-script)', 'type'=>'tracing', 'description'=>'Trace alphabets in multiple scripts.', 'age_min'=>3, 'age_max'=>4, 'skill'=>'Literacy', 'language'=>'multi', 'media_url'=>'/media/tracing_alphabets.mp4'],
            ['id'=>16, 'title'=>'Basic counting games', 'type'=>'puzzle', 'description'=>'Games to practice counting and numeracy.', 'age_min'=>3, 'age_max'=>4, 'skill'=>'Numeracy', 'language'=>'en', 'media_url'=>'/media/counting_games.mp4'],
            ['id'=>17, 'title'=>'Parent modules: emotional coaching', 'type'=>'video', 'description'=>'Coaching for naming feelings and empathy.', 'age_min'=>3, 'age_max'=>4, 'skill'=>'Emotional Development', 'language'=>'en', 'media_url'=>'/media/emotional_coaching.mp4'],
            ['id'=>18, 'title'=>'Role-play: table manners, sharing', 'type'=>'video', 'description'=>'Role-play videos for manners and sharing.', 'age_min'=>3, 'age_max'=>4, 'skill'=>'Social Skills', 'language'=>'multi', 'media_url'=>'/media/role_play_manners.mp4'],
            // 3–4 years (Manners)
            ['id'=>41, 'title'=>'Table Manners Role-play', 'type'=>'video', 'description'=>'Role-play videos for table manners and sharing.', 'age_min'=>3, 'age_max'=>4, 'skill'=>'Manners', 'language'=>'multi', 'media_url'=>'/media/table_manners.mp4'],
            ['id'=>46, 'title'=>'Table Setting Puzzle', 'type'=>'puzzle', 'description'=>'Drag and drop utensils to set the table correctly.', 'age_min'=>3, 'age_max'=>4, 'skill'=>'Manners', 'language'=>'multi', 'media_url'=>'/media/table_setting_puzzle.png'],
            ['id'=>47, 'title'=>'Manners Matching Game', 'type'=>'quiz', 'description'=>'Match the situation to the correct polite response.', 'age_min'=>3, 'age_max'=>4, 'skill'=>'Manners', 'language'=>'multi', 'media_url'=>''],
            // 4–5 years
            ['id'=>19, 'title'=>'Tracing complex letters', 'type'=>'tracing', 'description'=>'Trace complex letters for advanced literacy.', 'age_min'=>4, 'age_max'=>5, 'skill'=>'Literacy', 'language'=>'en', 'media_url'=>'/media/tracing_complex_letters.mp4'],
            ['id'=>20, 'title'=>'Simple words/phrases (FR, RU, KO)', 'type'=>'video', 'description'=>'Learn simple words in French, Russian, Korean.', 'age_min'=>4, 'age_max'=>5, 'skill'=>'Language Development', 'language'=>'multi', 'media_url'=>'/media/simple_words_phrases.mp4'],
            ['id'=>21, 'title'=>'Puzzles and sorting games', 'type'=>'puzzle', 'description'=>'Puzzles and sorting for problem solving.', 'age_min'=>4, 'age_max'=>5, 'skill'=>'Problem Solving', 'language'=>'en', 'media_url'=>'/media/puzzles_sorting.mp4'],
            ['id'=>22, 'title'=>'Animated scenarios: kindness, respect', 'type'=>'video', 'description'=>'Animated stories teaching kindness and respect.', 'age_min'=>4, 'age_max'=>5, 'skill'=>'Social Skills', 'language'=>'multi', 'media_url'=>'/media/animated_scenarios.mp4'],
            ['id'=>23, 'title'=>'Basic mindfulness exercises', 'type'=>'video', 'description'=>'Mindfulness and self-regulation for kids.', 'age_min'=>4, 'age_max'=>5, 'skill'=>'Mindfulness', 'language'=>'en', 'media_url'=>'/media/mindfulness_exercises.mp4'],
            // 4–5 years (Chivalry & Etiquette)
            ['id'=>42, 'title'=>'Chivalry and Kindness', 'type'=>'video', 'description'=>'Animated scenarios teaching chivalry, kindness, and respect for elders.', 'age_min'=>4, 'age_max'=>5, 'skill'=>'Chivalry & Etiquette', 'language'=>'multi', 'media_url'=>'/media/chivalry_kindness.mp4'],
            ['id'=>48, 'title'=>'Chivalry Choices Quiz', 'type'=>'quiz', 'description'=>'Choose the chivalrous action in different scenarios.', 'age_min'=>4, 'age_max'=>5, 'skill'=>'Chivalry & Etiquette', 'language'=>'multi', 'media_url'=>''],
            ['id'=>49, 'title'=>'Kindness Drawing', 'type'=>'drawing', 'description'=>'Draw a picture of a kind action you can do for others.', 'age_min'=>4, 'age_max'=>5, 'skill'=>'Chivalry & Etiquette', 'language'=>'multi', 'media_url'=>''],
            // 5–6 years
            ['id'=>24, 'title'=>'Storytelling, science experiments, arithmetic', 'type'=>'video', 'description'=>'Storytelling, science, and math for pre-academics.', 'age_min'=>5, 'age_max'=>6, 'skill'=>'Language, Science, Math', 'language'=>'multi', 'media_url'=>'/media/storytelling_science_arithmetic.mp4'],
            ['id'=>25, 'title'=>'Art styles: origami, calligraphy, weaving', 'type'=>'drawing', 'description'=>'Explore art styles from around the world.', 'age_min'=>5, 'age_max'=>6, 'skill'=>'Creativity', 'language'=>'multi', 'media_url'=>'/media/art_styles.mp4'],
            ['id'=>26, 'title'=>'Etiquette: dining, courtesy, sportsmanship', 'type'=>'video', 'description'=>'Etiquette for dining, courtesy, and sports.', 'age_min'=>5, 'age_max'=>6, 'skill'=>'Social Skills', 'language'=>'en', 'media_url'=>'/media/etiquette.mp4'],
            ['id'=>27, 'title'=>'Family tree, cultural scrapbook', 'type'=>'drawing', 'description'=>'Create a family tree or cultural scrapbook.', 'age_min'=>5, 'age_max'=>6, 'skill'=>'Cultural Awareness', 'language'=>'en', 'media_url'=>'/media/family_tree_scrapbook.mp4'],
            // 5–6 years (Royal Etiquette)
            ['id'=>43, 'title'=>'Royal Etiquette for Kids', 'type'=>'video', 'description'=>'Etiquette modules covering formal dining, royal courtesy, and sportsmanship.', 'age_min'=>5, 'age_max'=>6, 'skill'=>'Royal Etiquette', 'language'=>'en', 'media_url'=>'/media/royal_etiquette.mp4'],
            ['id'=>50, 'title'=>'Royal Table Setting Puzzle', 'type'=>'puzzle', 'description'=>'Arrange a royal table setting with correct utensils and plates.', 'age_min'=>5, 'age_max'=>6, 'skill'=>'Royal Etiquette', 'language'=>'en', 'media_url'=>'/media/royal_table_setting.png'],
            ['id'=>51, 'title'=>'Royal Etiquette Quiz', 'type'=>'quiz', 'description'=>'Quiz: What is the correct way to greet a royal guest?', 'age_min'=>5, 'age_max'=>6, 'skill'=>'Royal Etiquette', 'language'=>'en', 'media_url'=>''],
            ['id'=>52, 'title'=>'Royal Bow Drawing', 'type'=>'drawing', 'description'=>'Draw yourself doing a royal bow or curtsy.', 'age_min'=>5, 'age_max'=>6, 'skill'=>'Royal Etiquette', 'language'=>'en', 'media_url'=>''],
            // 7–8 years
            ['id'=>28, 'title'=>'Block-based programming', 'type'=>'video', 'description'=>'Intro to block-based coding (Scratch/Blockly).', 'age_min'=>7, 'age_max'=>8, 'skill'=>'Programming', 'language'=>'en', 'media_url'=>'/media/block_based_programming.mp4'],
            ['id'=>29, 'title'=>'DIY robot kits', 'type'=>'video', 'description'=>'Build simple robots with DIY kits.', 'age_min'=>7, 'age_max'=>8, 'skill'=>'Engineering', 'language'=>'en', 'media_url'=>'/media/diy_robot_kits.mp4'],
            ['id'=>30, 'title'=>'Videos: mechanical concepts', 'type'=>'video', 'description'=>'Videos explaining mechanical concepts.', 'age_min'=>7, 'age_max'=>8, 'skill'=>'Science', 'language'=>'en', 'media_url'=>'/media/mechanical_concepts.mp4'],
            ['id'=>31, 'title'=>'Plant/insect experiments', 'type'=>'video', 'description'=>'Science experiments with plants and insects.', 'age_min'=>7, 'age_max'=>8, 'skill'=>'Science', 'language'=>'en', 'media_url'=>'/media/plant_insect_experiments.mp4'],
            // 8–9 years
            ['id'=>32, 'title'=>'Python/JS game challenges', 'type'=>'quiz', 'description'=>'Game-like coding challenges in Python/JS.', 'age_min'=>8, 'age_max'=>9, 'skill'=>'Programming', 'language'=>'en', 'media_url'=>'/media/python_js_game_challenges.mp4'],
            ['id'=>33, 'title'=>'3D printing simple objects', 'type'=>'video', 'description'=>'Design and print simple 3D objects.', 'age_min'=>8, 'age_max'=>9, 'skill'=>'Engineering', 'language'=>'en', 'media_url'=>'/media/3d_printing_simple_objects.mp4'],
            ['id'=>34, 'title'=>'Fractions/geometry puzzles', 'type'=>'puzzle', 'description'=>'Puzzles for fractions and geometry.', 'age_min'=>8, 'age_max'=>9, 'skill'=>'Math', 'language'=>'en', 'media_url'=>'/media/fractions_geometry_puzzles.mp4'],
            ['id'=>35, 'title'=>'Team coding projects', 'type'=>'video', 'description'=>'Collaborative coding projects for kids.', 'age_min'=>8, 'age_max'=>9, 'skill'=>'Programming', 'language'=>'en', 'media_url'=>'/media/team_coding_projects.mp4'],
            // 9–10 years
            ['id'=>36, 'title'=>'Programmable robots with sensors', 'type'=>'video', 'description'=>'Build and program robots with sensors.', 'age_min'=>9, 'age_max'=>10, 'skill'=>'Engineering', 'language'=>'en', 'media_url'=>'/media/programmable_robots_sensors.mp4'],
            ['id'=>37, 'title'=>'Advanced Python/JS projects', 'type'=>'video', 'description'=>'Create advanced projects in Python/JS.', 'age_min'=>9, 'age_max'=>10, 'skill'=>'Programming', 'language'=>'en', 'media_url'=>'/media/advanced_python_js_projects.mp4'],
            ['id'=>38, 'title'=>'3D modeling and animation', 'type'=>'video', 'description'=>'Design and animate 3D models.', 'age_min'=>9, 'age_max'=>10, 'skill'=>'Art & Technology', 'language'=>'en', 'media_url'=>'/media/3d_modeling_animation.mp4'],
            ['id'=>39, 'title'=>'Science experiments: physics, chemistry', 'type'=>'video', 'description'=>'Hands-on experiments in physics and chemistry.', 'age_min'=>9, 'age_max'=>10, 'skill'=>'Science', 'language'=>'en', 'media_url'=>'/media/science_experiments_physics_chemistry.mp4'],
        ];

        foreach ($activities as $activity) {
            Activity::updateOrCreate(['id' => $activity['id']], $activity);
        }
    }
}
