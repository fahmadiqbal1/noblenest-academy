<?php

namespace Database\Seeders;

use App\Models\MaternalExercisePlan;
use Illuminate\Database\Seeder;

/**
 * Seeds exercise plans by trimester with Chinese, Japanese, and Ayurvedic
 * cultural origins plus general evidence-based routines.
 */
class MaternalExercisePlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            // ====================== TRIMESTER 1 ======================
            [
                'stage'                  => 'trimester_1',
                'week_number'            => 6,
                'day_of_week'            => 'Monday',
                'routine_name'           => 'Gentle Morning Qi Flow',
                'cultural_origin'        => 'chinese',
                'intensity'              => 'low',
                'total_duration_minutes' => 15,
                'benefit_explanation'    => 'Gentle Qi movements early in the day combat nausea, increase energy without overstimulation, and set a calm tone for the entire day.',
                'warmup_instructions'    => 'Stand in Wuji pose for 2 minutes: feet shoulder-width, arms relaxed, gentle breathing.',
                'cooldown_instructions'  => 'Place hands on lower belly (dantian). Breathe deeply for 2 minutes, sending warmth to your baby.',
                'exercises'              => [
                    ['name' => 'Standing Qi Circles', 'duration' => '3 min', 'reps' => null, 'notes' => 'Circle arms slowly in front of body, coordinating with breath'],
                    ['name' => 'Gentle Neck Rolls', 'duration' => '2 min', 'reps' => '5 each direction', 'notes' => 'Slow, smooth circles to release tension'],
                    ['name' => 'Dantian Breathing', 'duration' => '5 min', 'reps' => null, 'notes' => 'Deep belly breathing, hands on lower abdomen'],
                    ['name' => 'Ankle Circles', 'duration' => '2 min', 'reps' => '10 each foot', 'notes' => 'Promotes circulation, prevents early swelling'],
                ],
                'safety_notes'           => 'Stop immediately if dizzy or nauseous. Morning sickness may limit exercise — do what feels right.',
                'language'               => 'en',
            ],
            [
                'stage'                  => 'trimester_1',
                'week_number'            => 8,
                'day_of_week'            => 'Wednesday',
                'routine_name'           => 'First Trimester Yoga Basics',
                'cultural_origin'        => 'ayurvedic',
                'intensity'              => 'low',
                'total_duration_minutes' => 20,
                'benefit_explanation'    => 'These foundational yoga poses ease first-trimester fatigue, gentle stretching relieves the bloating and constipation common in weeks 6-12, and pranayama breathing anchors your mind during hormonal surges.',
                'warmup_instructions'    => 'Sit cross-legged. Practice 3 minutes of alternate nostril breathing (Nadi Shodhana) to balance left and right energy channels.',
                'cooldown_instructions'  => 'Side-lying Savasana on your left side, pillow between knees. Rest for 5 minutes.',
                'exercises'              => [
                    ['name' => 'Cat-Cow', 'duration' => '3 min', 'reps' => '10 cycles', 'notes' => 'Relieves lower back tension, safe in all trimesters'],
                    ['name' => 'Seated Side Stretch', 'duration' => '2 min', 'reps' => '5 each side', 'notes' => 'Opens ribcage for easier breathing'],
                    ['name' => 'Standing Mountain Pose', 'duration' => '2 min', 'reps' => null, 'notes' => 'Grounding posture, improves posture awareness'],
                    ['name' => 'Gentle Spinal Twist (seated)', 'duration' => '3 min', 'reps' => '5 each side', 'notes' => 'Aids digestion, relieves bloating. Keep twist gentle — do not compress belly.'],
                    ['name' => 'Legs Up The Wall', 'duration' => '5 min', 'reps' => null, 'notes' => 'Reduces leg heaviness and early swelling'],
                ],
                'safety_notes'           => 'No deep twists or inversions. Keep movements gentle. Hydrate before and after.',
                'language'               => 'en',
            ],

            // ====================== TRIMESTER 2 ======================
            [
                'stage'                  => 'trimester_2',
                'week_number'            => 16,
                'day_of_week'            => 'Tuesday',
                'routine_name'           => 'Prenatal Tai Chi — Cloud Hands Sequence',
                'cultural_origin'        => 'chinese',
                'intensity'              => 'low',
                'total_duration_minutes' => 25,
                'benefit_explanation'    => 'Tai Chi improves proprioception (body awareness in space), which is critical as your center of gravity shifts in the second trimester. The slow, weight-shifting movements strengthen leg muscles and improve ankle stability — reducing fall risk by 40%.',
                'warmup_instructions'    => 'Stand in Wuji for 3 minutes. Shift weight slowly side to side, feeling the earth beneath each foot.',
                'cooldown_instructions'  => 'Return to Wuji. Hands on belly, 3 minutes of connected breathing.',
                'exercises'              => [
                    ['name' => 'Cloud Hands', 'duration' => '5 min', 'reps' => '10 each side', 'notes' => 'Hands float in circles at chest level while shifting weight'],
                    ['name' => 'Parting Wild Horses Mane', 'duration' => '5 min', 'reps' => '8 each side', 'notes' => 'Gentle stepping with flowing arm movements'],
                    ['name' => 'Brush Knee and Push', 'duration' => '5 min', 'reps' => '8 each side', 'notes' => 'Do not lunge deep — keep knees above ankles'],
                    ['name' => 'Golden Rooster Stands', 'duration' => '4 min', 'reps' => '5 each leg (10 sec hold)', 'notes' => 'Hold a chair for support. Excellent balance training.'],
                ],
                'safety_notes'           => 'Stay near a wall or chair for balance support. Do not lunge deeply. Stop if any pelvic pressure.',
                'language'               => 'en',
            ],
            [
                'stage'                  => 'trimester_2',
                'week_number'            => 20,
                'day_of_week'            => 'Thursday',
                'routine_name'           => 'Hip-Opening Yoga for Birth Preparation',
                'cultural_origin'        => 'ayurvedic',
                'intensity'              => 'moderate',
                'total_duration_minutes' => 30,
                'benefit_explanation'    => 'Open hips create more space in the pelvis for baby to descend during labor. Strong hip muscles also support the weight of your growing baby, reducing pelvic girdle pain — the most common complaint of the second trimester.',
                'warmup_instructions'    => 'Cat-Cow for 3 minutes, then 2 minutes of gentle hip circles on all fours.',
                'cooldown_instructions'  => 'Butterfly pose for 3 minutes, then left-side Savasana for 5 minutes.',
                'exercises'              => [
                    ['name' => 'Goddess Pose (Utkata Konasana)', 'duration' => '3 min', 'reps' => '5 holds of 30 sec', 'notes' => 'Wide stance, deep bend — strengthens thighs and opens hips'],
                    ['name' => 'Pigeon Pose (modified)', 'duration' => '4 min', 'reps' => '2 min each side', 'notes' => 'Use a pillow under the hip. Deep hip flexor release.'],
                    ['name' => 'Squat with Wall Support', 'duration' => '3 min', 'reps' => '5 holds of 30 sec', 'notes' => 'Back against wall, feet wide. Opens pelvis.'],
                    ['name' => 'Butterfly Pulses', 'duration' => '3 min', 'reps' => '20 pulses', 'notes' => 'Seated, soles together, gently press knees down and release'],
                    ['name' => 'Pelvic Floor Kegels', 'duration' => '5 min', 'reps' => '3 sets of 10', 'notes' => 'Squeeze, hold 5 sec, release. Essential for labor and recovery.'],
                ],
                'safety_notes'           => 'If you feel sharp pain in the pubic bone (SPD), reduce depth of squat/goddess. Do not push through pain.',
                'language'               => 'en',
            ],
            [
                'stage'                  => 'trimester_2',
                'week_number'            => 18,
                'day_of_week'            => 'Saturday',
                'routine_name'           => 'Japanese Forest Bathing Walk (Shinrin-yoku)',
                'cultural_origin'        => 'japanese',
                'intensity'              => 'low',
                'total_duration_minutes' => 40,
                'benefit_explanation'    => 'Shinrin-yoku (森林浴) — forest bathing — is a Japanese practice of immersing in nature for health. Walking in green spaces reduces cortisol by 16%, blood pressure by 6%, and heart rate by 4%. For pregnant women, it also reduces pregnancy-specific anxiety and improves mood.',
                'warmup_instructions'    => 'Stand at the trailhead. Close your eyes. Take 5 deep breaths, focusing on the smells around you.',
                'cooldown_instructions'  => 'Find a bench or comfortable spot. Sit for 5 minutes, eyes closed, listening to nature.',
                'exercises'              => [
                    ['name' => 'Mindful Walk', 'duration' => '20 min', 'reps' => null, 'notes' => 'Walk slowly. No phone. Focus on sounds, smells, textures of trees and leaves.'],
                    ['name' => 'Touch Meditation', 'duration' => '5 min', 'reps' => null, 'notes' => 'Touch different tree barks, leaves, and stones. Focus on the sensation.'],
                    ['name' => 'Standing Tree Breath', 'duration' => '5 min', 'reps' => null, 'notes' => 'Stand near a tree. Breathe with the tree. Imagine exchanging CO2 for O2 in partnership.'],
                    ['name' => 'Gentle Stretching Outdoors', 'duration' => '5 min', 'reps' => null, 'notes' => 'Side stretches, shoulder rolls, and calf raises using a fallen log for balance.'],
                ],
                'safety_notes'           => 'Wear comfortable shoes with good grip. Stay on paths. Bring water. Avoid slippery or uneven terrain.',
                'language'               => 'en',
            ],

            // ====================== TRIMESTER 3 ======================
            [
                'stage'                  => 'trimester_3',
                'week_number'            => 32,
                'day_of_week'            => 'Monday',
                'routine_name'           => 'Birth Preparation — Qigong for Labor',
                'cultural_origin'        => 'chinese',
                'intensity'              => 'low',
                'total_duration_minutes' => 20,
                'benefit_explanation'    => 'Qigong breathing and gentle movements prepare the body for the stamina required during labor. These practices teach you to direct energy and breath to areas of tension — a skill that directly translates to coping with contractions.',
                'warmup_instructions'    => 'Seated on a birth ball, gentle hip circles for 3 minutes.',
                'cooldown_instructions'  => 'Lie on your left side, pillow between knees. Practice ocean breath for 5 minutes.',
                'exercises'              => [
                    ['name' => 'Birth Ball Hip Circles', 'duration' => '5 min', 'reps' => null, 'notes' => 'Encourages optimal fetal positioning (head-down)'],
                    ['name' => 'Standing Figure-8 Hips', 'duration' => '3 min', 'reps' => null, 'notes' => 'Draw figure-8 with your hips. Opens pelvis, encourages engagement.'],
                    ['name' => 'Wall Squat with Breath', 'duration' => '4 min', 'reps' => '5 holds of 30 sec', 'notes' => 'Squat against wall, breathing through the hold — simulates contraction coping'],
                    ['name' => 'Rocking on All Fours', 'duration' => '3 min', 'reps' => null, 'notes' => 'Gently rock hips back and forth. Relieves back labor pressure.'],
                ],
                'safety_notes'           => 'If you experience regular contractions (not Braxton Hicks), stop exercising and contact your healthcare provider.',
                'contraindications'      => ['preterm_labor_risk', 'placenta_previa'],
                'language'               => 'en',
            ],
            [
                'stage'                  => 'trimester_3',
                'week_number'            => 35,
                'day_of_week'            => 'Wednesday',
                'routine_name'           => 'Ayurvedic Birth-Ready Yoga',
                'cultural_origin'        => 'ayurvedic',
                'intensity'              => 'low',
                'total_duration_minutes' => 25,
                'benefit_explanation'    => 'These specific Ayurvedic yoga poses encourage optimal fetal positioning, strengthen the muscles used during pushing, and teach controlled exhale breathing — the foundation of effective pushing technique.',
                'warmup_instructions'    => 'Seated, practice Ujjayi (ocean) breathing for 3 minutes — inhale through nose, exhale through slightly constricted throat.',
                'cooldown_instructions'  => 'Yoga Nidra (sleep yoga) in left-side position for 10 minutes.',
                'exercises'              => [
                    ['name' => 'Supported Deep Squat (Malasana)', 'duration' => '5 min', 'reps' => '3 holds of 1 min', 'notes' => 'Use blocks or partner support. Opens pelvic outlet by 28%.'],
                    ['name' => 'Cat-Cow with Breath of Fire', 'duration' => '3 min', 'reps' => '8 slow cycles', 'notes' => 'Emphasize long exhales through mouth — practice pushing breath'],
                    ['name' => 'Wall Push-Ups', 'duration' => '3 min', 'reps' => '3 sets of 10', 'notes' => 'Maintains upper body strength for holding baby postpartum'],
                    ['name' => 'Perineal Massage Guidance', 'duration' => '5 min', 'reps' => null, 'notes' => 'Explanation and guided practice. Reduces tearing risk by up to 10%.'],
                ],
                'safety_notes'           => 'Avoid lying flat on your back. Use left-side position for rest. Stay hydrated.',
                'contraindications'      => ['preterm_labor_risk', 'cervical_insufficiency'],
                'language'               => 'en',
            ],

            // ====================== POSTNATAL ======================
            [
                'stage'                  => 'postnatal_0_3m',
                'week_number'            => 2,
                'day_of_week'            => 'Daily',
                'routine_name'           => 'Postnatal Core Restoration (Diastasis-Safe)',
                'cultural_origin'        => null,
                'intensity'              => 'low',
                'total_duration_minutes' => 10,
                'benefit_explanation'    => 'After birth, the abdominal muscles have separated (diastasis recti). These exercises gently reconnect the deep core muscles without worsening the separation. Starting gentle core work within 2 weeks (if vaginal birth) speeds recovery significantly.',
                'warmup_instructions'    => 'Lie on your back with knees bent. Take 5 deep breaths, feeling your belly rise and fall.',
                'cooldown_instructions'  => 'Gentle pelvic tilts for 1 minute, then rest.',
                'exercises'              => [
                    ['name' => 'Diaphragmatic Breathing', 'duration' => '3 min', 'reps' => '10 breaths', 'notes' => 'Inhale: belly expands. Exhale: draw belly gently toward spine. This IS a core exercise.'],
                    ['name' => 'Heel Slides', 'duration' => '3 min', 'reps' => '10 each leg', 'notes' => 'Lying down, slowly slide one heel away, keeping back flat. Return slowly.'],
                    ['name' => 'Pelvic Floor Engagement', 'duration' => '2 min', 'reps' => '10 squeezes (5 sec each)', 'notes' => 'Imagine stopping the flow of urine. Hold 5 sec. Release fully.'],
                    ['name' => 'Toe Taps', 'duration' => '2 min', 'reps' => '10 each leg', 'notes' => 'Knees at 90°, slowly lower one foot to tap floor. Return. Keep back flat.'],
                ],
                'safety_notes'           => 'Wait 6-8 weeks after cesarean. Stop if any pain. No crunches, sit-ups, or planks until diastasis is assessed.',
                'language'               => 'en',
            ],
        ];

        foreach ($plans as $plan) {
            MaternalExercisePlan::create($plan);
        }

        $this->command->info('Seeded ' . count($plans) . ' maternal exercise plans.');
    }
}
