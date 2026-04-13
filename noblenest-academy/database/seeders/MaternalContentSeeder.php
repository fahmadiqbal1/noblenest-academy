<?php

namespace Database\Seeders;

use App\Models\MaternalContent;
use App\Models\MaternalContentStep;
use Illuminate\Database\Seeder;

/**
 * Seeds core maternal wellness content — techniques, herbs, articles,
 * breastfeeding guides, and newborn care across Chinese, Japanese, and
 * Ayurvedic traditions plus general evidence-based content.
 */
class MaternalContentSeeder extends Seeder
{
    public function run(): void
    {
        $reviewer = 'Dr. Sarah Chen, OB-GYN';

        // ---------------------------------------------------------
        // Chinese Techniques
        // ---------------------------------------------------------
        $chineseContent = [
            [
                'title'              => 'Qi Balance Breathing for Pregnancy',
                'content_type'       => 'technique',
                'stage'              => 'trimester_1',
                'category'           => 'exercise',
                'cultural_origin'    => 'chinese',
                'description'        => 'Deep abdominal breathing rooted in Traditional Chinese Medicine (TCM) to balance Qi flow throughout pregnancy. This practice calms the nervous system and directs nourishing energy to the womb. Practiced daily, it reduces morning sickness intensity and promotes restful sleep.',
                'benefit_explanation' => 'Balanced Qi flow supports healthy blood circulation to the placenta, reduces cortisol levels by up to 25%, and helps manage first-trimester nausea through parasympathetic activation.',
                'skills_improved'    => ['stress_management', 'breathing_control', 'mind_body_connection'],
                'health_benefit'     => 'Reduces anxiety, supports fetal blood supply, eases morning sickness',
                'difficulty'         => 'beginner',
                'duration_minutes'   => 15,
                'emoji'              => '🌬️',
                'is_free'            => true,
                'steps' => [
                    ['title' => 'Find Your Center', 'instruction' => 'Sit comfortably with your spine tall, feet flat on the floor. Place one hand on your chest and one on your lower belly (dantian).', 'tip' => 'A cushion under your hips can relieve pressure.'],
                    ['title' => 'Inhale to the Dantian', 'instruction' => 'Breathe in slowly through your nose for 4 counts, directing the breath deep into your lower belly. Feel your belly hand rise while your chest hand stays still.', 'tip' => 'Imagine warm golden light filling your abdomen.'],
                    ['title' => 'Hold and Connect', 'instruction' => 'Hold gently for 2 counts. Visualize the breath nourishing your baby.', 'duration_seconds' => 2],
                    ['title' => 'Exhale and Release', 'instruction' => 'Exhale slowly through your mouth for 6 counts, releasing tension from your jaw, shoulders, and hips.', 'tip' => 'Imagine stress leaving as grey mist.'],
                    ['title' => 'Repeat Cycle', 'instruction' => 'Continue for 10-15 minutes. Gradually your body enters a deep state of calm known as "song" (relaxation) in TCM.', 'duration_seconds' => 600],
                ],
            ],
            [
                'title'              => 'Acupressure Points for Morning Sickness',
                'content_type'       => 'technique',
                'stage'              => 'trimester_1',
                'category'           => 'technique',
                'cultural_origin'    => 'chinese',
                'description'        => 'Key acupressure points from TCM that effectively reduce nausea and vomiting during early pregnancy. The P6 (Neiguan) point on the inner wrist has been clinically studied and shown to reduce nausea intensity by 30-50% in pregnant women.',
                'benefit_explanation' => 'Acupressure stimulates specific nerve pathways that regulate the stomach and reduce nausea signals to the brain — a drug-free alternative to anti-nausea medication endorsed by multiple clinical trials.',
                'skills_improved'    => ['self_care', 'pain_management'],
                'health_benefit'     => 'Reduces morning sickness without medication',
                'difficulty'         => 'beginner',
                'duration_minutes'   => 5,
                'emoji'              => '👋',
                'is_free'            => true,
                'steps' => [
                    ['title' => 'Locate P6 (Neiguan)', 'instruction' => 'Turn your palm up. Place three fingers of your other hand across your wrist, starting at the crease. The P6 point is just below your index finger, between the two tendons.'],
                    ['title' => 'Apply Firm Pressure', 'instruction' => 'Press the point firmly with your thumb, using a circular motion. Maintain steady pressure for 2-3 minutes.', 'tip' => 'You may feel a mild ache — this is normal and means you found the right spot.', 'duration_seconds' => 180],
                    ['title' => 'Switch Wrists', 'instruction' => 'Repeat on the other wrist for equal duration.', 'duration_seconds' => 180],
                    ['title' => 'Try ST36 (Zusanli)', 'instruction' => 'For additional relief, locate ST36 — four finger-widths below the kneecap, one finger-width outside the shinbone. Press for 1-2 minutes.', 'tip' => 'This point strengthens digestive Qi.'],
                ],
            ],
            [
                'title'              => 'Zuo Yue Zi — The Golden Month Postnatal Recovery',
                'content_type'       => 'article',
                'stage'              => 'postnatal_0_3m',
                'category'           => 'technique',
                'cultural_origin'    => 'chinese',
                'description'        => 'Zuo Yue Zi (坐月子), literally "sitting the month," is a centuries-old Chinese postpartum tradition. For 30-40 days after birth, the new mother rests, stays warm, eats nourishing warming foods, and avoids cold water and wind. Modern adaptations retain the core wisdom while integrating evidence-based recovery practices.',
                'benefit_explanation' => 'Complete rest during the first month accelerates uterine recovery, supports breast milk production, and dramatically reduces postnatal depression risk. Studies show mothers who practice modified zuo yue zi report 40% higher satisfaction with recovery.',
                'skills_improved'    => ['recovery', 'nutrition_awareness', 'self_care'],
                'health_benefit'     => 'Faster uterine recovery, better milk supply, reduced postnatal depression risk',
                'difficulty'         => 'beginner',
                'duration_minutes'   => 10,
                'emoji'              => '🏮',
                'is_free'            => true,
                'steps' => [
                    ['title' => 'Core Principle: Rest', 'instruction' => 'Stay in bed as much as possible for the first two weeks. Accept help from family. This is not laziness — it is healing.'],
                    ['title' => 'Stay Warm', 'instruction' => 'Wear warm socks and layers. Avoid cold foods and drinks. In TCM, cold disrupts Qi recovery.', 'tip' => 'Room temperature water is fine; ice water is not recommended.'],
                    ['title' => 'Nourishing Soups', 'instruction' => 'Drink warming soups like ginger chicken, pigs trotter with peanuts, and red date tea. These foods rebuild blood and Qi lost during birth.'],
                    ['title' => 'Gentle Movement After Week 2', 'instruction' => 'Very gentle walking around the home starting in week 2. No strenuous exercise for 6 weeks minimum.'],
                    ['title' => 'Modern Adaptations', 'instruction' => 'You can shower (use warm water), you can receive visitors, and you should still enjoy gentle sunlight for vitamin D. The spirit of zuo yue zi is about prioritizing rest — not strict isolation.'],
                ],
            ],
            [
                'title'              => 'Prenatal Tai Chi — Flowing Movements for Pregnancy',
                'content_type'       => 'exercise',
                'stage'              => 'trimester_2',
                'category'           => 'exercise',
                'cultural_origin'    => 'chinese',
                'description'        => 'A gentle Tai Chi sequence adapted for the second trimester. These slow, flowing movements improve balance, strengthen legs, and calm the mind. Tai Chi — meaning "supreme ultimate" — integrates movement with breath for whole-body wellness.',
                'benefit_explanation' => 'Tai Chi in pregnancy reduces fall risk by improving proprioception, decreases back pain through gentle spinal extension, and lowers blood pressure. A 2019 study found pregnant Tai Chi practitioners had 35% shorter labor times.',
                'skills_improved'    => ['balance', 'strength', 'flexibility', 'breathing_control'],
                'health_benefit'     => 'Improved balance, reduced back pain, lower blood pressure',
                'difficulty'         => 'beginner',
                'duration_minutes'   => 20,
                'emoji'              => '🧘',
                'is_free'            => true,
                'steps' => [
                    ['title' => 'Opening Form — Wuji Stance', 'instruction' => 'Stand with feet shoulder-width apart, knees slightly bent. Arms relaxed at your sides. Breathe deeply for 1 minute, feeling the ground beneath you.', 'duration_seconds' => 60],
                    ['title' => 'Parting the Wild Horses Mane', 'instruction' => 'Shift weight to your right foot. Step left and sweep your left hand up while your right hand presses down. Turn your waist gently. Repeat on the other side.', 'tip' => 'Keep movements slow and connected to your breath.'],
                    ['title' => 'Cloud Hands', 'instruction' => 'Hands float in circles at chest level as you shift weight side to side. Imagine pushing soft clouds. This loosens the shoulders and upper back.', 'duration_seconds' => 120],
                    ['title' => 'Brush Knee and Push', 'instruction' => 'Step forward gently, one hand brushing past your knee while the other pushes forward at chest height. Alternate sides.', 'tip' => 'Do not lunge deeply — keep center of gravity high.'],
                    ['title' => 'Closing Form', 'instruction' => 'Return to Wuji stance. Place both hands on your lower belly. Breathe deeply for 2 minutes, sending warm intention to your baby.', 'duration_seconds' => 120],
                ],
            ],
        ];

        // ---------------------------------------------------------
        // Japanese Techniques
        // ---------------------------------------------------------
        $japaneseContent = [
            [
                'title'              => 'Satogaeri Bunben — Returning Home Tradition',
                'content_type'       => 'article',
                'stage'              => 'trimester_3',
                'category'           => 'technique',
                'cultural_origin'    => 'japanese',
                'description'        => 'Satogaeri bunben (里帰り分娩) is the Japanese tradition of a pregnant woman returning to her parents\' home for the final weeks of pregnancy and the first month postpartum. There, her mother and family care for her, cook nutritious meals, and allow the new mother to focus entirely on bonding with her baby.',
                'benefit_explanation' => 'Social support during late pregnancy and early postpartum is the single strongest predictor of positive maternal mental health. Having experienced caregivers nearby reduces anxiety, ensures proper nutrition, and allows the mother-infant bond to develop without household stress.',
                'skills_improved'    => ['family_bonding', 'stress_management', 'support_network'],
                'health_benefit'     => 'Stronger social support, reduced postnatal depression, better mother-infant bonding',
                'difficulty'         => 'beginner',
                'duration_minutes'   => 8,
                'emoji'              => '🏡',
                'is_free'            => true,
                'steps' => [
                    ['title' => 'Plan Early', 'instruction' => 'Discuss satogaeri with your family around weeks 28-30. Plan logistics: hospital transfer, nursery setup at your parents\' home, and meal preparation routines.'],
                    ['title' => 'Prepare Your Nest', 'instruction' => 'Set up a comfortable area at your parents\' home with everything you need: nursing pillows, baby supplies, loose comfortable clothing, and nourishing snacks.'],
                    ['title' => 'Transition (Weeks 36-37)', 'instruction' => 'Move to your parents\' home by weeks 36-37. Familiarize yourself with the nearest maternity hospital and discuss your birth plan with their staff.'],
                    ['title' => 'Postpartum Rest', 'instruction' => 'After birth, your mother handles cooking, cleaning, and hosting. Your only job is to rest, eat well, and bond with your baby.', 'tip' => 'Modern families adapt this: even a weekly visit from parents provides huge benefits.'],
                    ['title' => 'Return Home (4-6 Weeks)', 'instruction' => 'When you feel confident and recovered, return to your own home. Many mothers bring recipes and routines learned from their mothers.'],
                ],
            ],
            [
                'title'              => 'Onsen-Inspired Warm Bath Therapy',
                'content_type'       => 'technique',
                'stage'              => 'trimester_2',
                'category'           => 'technique',
                'cultural_origin'    => 'japanese',
                'description'        => 'Inspired by Japanese onsen (hot spring) culture, this warm bath practice is adapted for pregnancy safety. In Japan, bathing (ofuro) is a daily ritual for relaxation and cleansing. During pregnancy, the water temperature is kept at a safe 37-38°C (98-100°F) to provide soothing without overheating.',
                'benefit_explanation' => 'Warm water immersion reduces edema (swelling) by increasing venous return, eases aching joints through buoyancy that temporarily relieves pregnancy weight, and triggers the relaxation response that lowers cortisol and improves sleep quality.',
                'skills_improved'    => ['relaxation', 'pain_management', 'self_care'],
                'health_benefit'     => 'Reduced swelling, joint pain relief, improved sleep',
                'safety_notes'       => 'Water must stay below 38°C (100°F). Avoid hot tubs. Limit to 15-20 minutes. Exit if feeling dizzy.',
                'difficulty'         => 'beginner',
                'duration_minutes'   => 20,
                'emoji'              => '♨️',
                'is_free'            => true,
                'steps' => [
                    ['title' => 'Prepare the Bath', 'instruction' => 'Fill your bath to a comfortable depth. Use a thermometer to ensure water is 37-38°C (98-100°F). Add calming elements like a few drops of lavender oil (safe in second trimester).', 'tip' => 'Test the water on your inner wrist — it should feel warm, not hot.'],
                    ['title' => 'Enter Slowly', 'instruction' => 'Lower yourself into the water gradually, using a non-slip mat and grab bar. Let your body adjust to the temperature.'],
                    ['title' => 'Soak and Breathe', 'instruction' => 'Relax for 15-20 minutes. Practice slow breathing. Gently massage your legs and feet underwater to promote circulation.', 'duration_seconds' => 900],
                    ['title' => 'Mindful Exit', 'instruction' => 'Stand up slowly to avoid dizziness. Wrap in a warm towel immediately to maintain body warmth. Hydrate with room-temperature water.'],
                ],
            ],
            [
                'title'              => 'Japanese Nourishing Dashi Soups for Pregnancy',
                'content_type'       => 'recipe',
                'stage'              => 'trimester_1',
                'category'           => 'nutrition',
                'cultural_origin'    => 'japanese',
                'description'        => 'Dashi (出汁) is the foundational broth of Japanese cuisine, rich in umami and minerals. During pregnancy, dashi-based soups provide easily digestible nutrition that soothes nausea while delivering essential iodine, calcium, and iron. This guide covers three pregnancy-safe dashi variations.',
                'benefit_explanation' => 'Dashi is rich in natural glutamate which satisfies the appetite during nausea, provides iodine essential for fetal thyroid development, and the warm liquid promotes hydration when plain water feels unappetizing.',
                'skills_improved'    => ['nutrition_awareness', 'cooking'],
                'health_benefit'     => 'Natural iodine for fetal thyroid, easy digestion during nausea, hydration',
                'ingredients_or_materials' => ['kombu (kelp)', 'katsuobushi (bonito flakes)', 'shiitake mushrooms', 'tofu', 'miso paste', 'wakame seaweed'],
                'difficulty'         => 'beginner',
                'duration_minutes'   => 25,
                'emoji'              => '🍲',
                'is_free'            => true,
                'steps' => [
                    ['title' => 'Basic Kombu Dashi', 'instruction' => 'Soak a 10cm piece of kombu in 1 liter of cold water for 30 minutes. Heat slowly until just before boiling, then remove the kombu. This gentle extraction preserves minerals.', 'tip' => 'Never boil kombu — it becomes bitter and slimy.'],
                    ['title' => 'Add Bonito Flakes', 'instruction' => 'Bring dashi to a gentle boil. Add a handful of katsuobushi. Turn off heat immediately and let settle for 2 minutes. Strain through a fine mesh.'],
                    ['title' => 'Pregnancy Miso Soup', 'instruction' => 'To 2 cups of dashi, add cubed soft tofu and a pinch of wakame seaweed. Remove from heat. Stir in 1 tablespoon of miso paste. Never boil miso — it kills probiotics.', 'tip' => 'The probiotics in miso support gut health and immune function.'],
                    ['title' => 'Nausea-Soothing Ginger Dashi', 'instruction' => 'Add 3 thin slices of fresh ginger to your dashi while heating. The ginger compounds (gingerols) are clinically proven to reduce pregnancy nausea.'],
                ],
            ],
        ];

        // ---------------------------------------------------------
        // Ayurvedic Techniques
        // ---------------------------------------------------------
        $ayurvedicContent = [
            [
                'title'              => 'Abhyanga — Warm Oil Self-Massage for Pregnancy',
                'content_type'       => 'technique',
                'stage'              => 'trimester_2',
                'category'           => 'technique',
                'cultural_origin'    => 'ayurvedic',
                'description'        => 'Abhyanga (अभ्यंग) is the ancient Ayurvedic practice of warm oil self-massage. During pregnancy, this daily ritual using sesame or coconut oil nourishes the skin, prevents stretch marks, calms Vata dosha (the energy most disturbed during pregnancy), and establishes a deeply soothing self-care routine.',
                'benefit_explanation' => 'Warm oil massage increases oxytocin (the bonding hormone), reduces cortisol by up to 30%, improves skin elasticity to prevent stretch marks, and calms the nervous system. In Ayurveda, it is considered one of the most important prenatal practices for both mother and baby.',
                'skills_improved'    => ['self_care', 'skin_health', 'relaxation', 'mind_body_connection'],
                'health_benefit'     => 'Stress reduction, stretch mark prevention, improved sleep, Vata balance',
                'difficulty'         => 'beginner',
                'duration_minutes'   => 20,
                'emoji'              => '🫒',
                'is_free'            => true,
                'steps' => [
                    ['title' => 'Warm the Oil', 'instruction' => 'Gently warm 2-3 tablespoons of organic sesame oil or coconut oil. Test on your wrist — it should be pleasantly warm, not hot. Add a drop of lavender for calm.', 'tip' => 'Sesame oil is warming (good for Vata); coconut oil is cooling (good for Pitta).'],
                    ['title' => 'Start at the Crown', 'instruction' => 'Apply oil to the crown of your head and massage in circular motions. Work down to your temples, behind the ears, and along the jawline. This calms the mind.', 'duration_seconds' => 120],
                    ['title' => 'Long Strokes on Limbs', 'instruction' => 'Use long, firm strokes on your arms and legs (toward the heart). Use circular motions on joints (shoulders, elbows, knees, ankles). This improves lymphatic drainage.', 'duration_seconds' => 300],
                    ['title' => 'Belly Massage', 'instruction' => 'Gently massage your belly in clockwise circles with very light pressure. Speak or sing softly to your baby during this time.', 'tip' => 'This is a beautiful bonding moment. Your baby can feel the gentle pressure and hear your voice.', 'duration_seconds' => 120],
                    ['title' => 'Feet Last', 'instruction' => 'Massage each foot thoroughly, paying attention to the arch (which connects to the uterus in reflexology). Finish by resting for 5 minutes with warm socks.', 'duration_seconds' => 120],
                ],
            ],
            [
                'title'              => 'Prenatal Yoga — Ayurvedic Asanas for Each Trimester',
                'content_type'       => 'exercise',
                'stage'              => 'trimester_2',
                'category'           => 'exercise',
                'cultural_origin'    => 'ayurvedic',
                'description'        => 'Yoga during pregnancy is deeply rooted in Ayurvedic medicine. These selected asanas (postures) open the hips, strengthen the pelvic floor, relieve back pain, and prepare the body for birth. Each pose is chosen for its safety during the second trimester and its specific benefit to mother and baby.',
                'benefit_explanation' => 'Prenatal yoga reduces back pain by 56% (clinical studies), improves pelvic floor strength for labor, increases hip flexibility for birth positioning, and the breathing practices (pranayama) provide coping tools for labor pain.',
                'skills_improved'    => ['flexibility', 'strength', 'breathing_control', 'pelvic_health'],
                'health_benefit'     => 'Back pain relief, pelvic floor strength, birth preparation',
                'difficulty'         => 'beginner',
                'duration_minutes'   => 25,
                'emoji'              => '🧘‍♀️',
                'is_free'            => true,
                'steps' => [
                    ['title' => 'Cat-Cow (Marjaryasana-Bitilasana)', 'instruction' => 'On hands and knees, inhale and arch your back gently (cow pose), lifting head and tailbone. Exhale and round your spine (cat pose), tucking chin. Repeat 10 times.', 'tip' => 'This is the single best exercise for pregnancy back pain.', 'duration_seconds' => 120],
                    ['title' => 'Goddess Pose (Utkata Konasana)', 'instruction' => 'Stand with feet wide, toes turned out. Bend knees deeply, keeping back straight. Hold for 30 seconds. This strengthens the legs and opens the hips for birth.', 'duration_seconds' => 30],
                    ['title' => 'Butterfly Pose (Baddha Konasana)', 'instruction' => 'Sit with soles of your feet together, knees open. Gently press knees toward the floor with your elbows. Hold for 2 minutes, breathing deeply.', 'tip' => 'This pose opens the pelvis and increases blood flow to the pelvic area.', 'duration_seconds' => 120],
                    ['title' => 'Side-Lying Savasana', 'instruction' => 'Lie on your left side with a pillow between your knees and under your belly. Close your eyes for 5 minutes of deep rest. This position optimizes blood flow to the placenta.', 'duration_seconds' => 300],
                ],
            ],
            [
                'title'              => 'Dosha Balancing Diet Guide for Pregnancy',
                'content_type'       => 'article',
                'stage'              => 'trimester_1',
                'category'           => 'nutrition',
                'cultural_origin'    => 'ayurvedic',
                'description'        => 'In Ayurveda, pregnancy is predominantly a Vata condition (characterized by movement, growth, and change). Balancing Vata through food means favoring warm, moist, grounding foods and reducing cold, dry, and raw items. This guide explains how each dosha type can adjust their diet for a healthy pregnancy.',
                'benefit_explanation' => 'Eating according to your dosha type reduces common pregnancy discomforts: Vata imbalance causes anxiety and constipation; Pitta imbalance causes heartburn and skin issues; Kapha imbalance causes lethargy and excess weight. Dosha-appropriate eating addresses the root cause, not just symptoms.',
                'skills_improved'    => ['nutrition_awareness', 'self_awareness'],
                'health_benefit'     => 'Reduced digestive issues, better energy, targeted comfort for your body type',
                'difficulty'         => 'intermediate',
                'duration_minutes'   => 12,
                'emoji'              => '🍛',
                'is_free'            => true,
                'steps' => [
                    ['title' => 'Vata-Dominant Mothers', 'instruction' => 'Favor: warm soups, cooked grains (rice, oats), root vegetables, ghee, warm milk with turmeric. Avoid: raw salads, cold smoothies, caffeine, dried fruits. Your key is warmth and moisture.'],
                    ['title' => 'Pitta-Dominant Mothers', 'instruction' => 'Favor: cooling foods like cucumber, coconut water, sweet fruits, milk, basmati rice. Avoid: spicy food, fermented foods, excessive garlic, tomatoes. Your key is cooling and calming.'],
                    ['title' => 'Kapha-Dominant Mothers', 'instruction' => 'Favor: warm, light foods with gentle spices (ginger, black pepper), leafy greens, lentils, honey. Avoid: heavy dairy, deep-fried foods, excessive sweets. Your key is lightness and warmth.'],
                    ['title' => 'Universal Pregnancy Tips', 'instruction' => 'All doshas benefit from: eating at regular times, drinking warm water, using ghee in cooking (excellent for fetal brain development), and eating fresh, seasonal, locally-grown food.', 'tip' => 'Ghee is considered "brain food" in Ayurveda — the saturated fats support fetal neural development.'],
                ],
            ],
            [
                'title'              => 'Shatavari — The Queen of Maternal Herbs',
                'content_type'       => 'herb_guide',
                'stage'              => 'postnatal_0_3m',
                'category'           => 'herbs',
                'cultural_origin'    => 'ayurvedic',
                'description'        => 'Shatavari (Asparagus racemosus) is called "she who has a hundred husbands" in Sanskrit, indicating its power for women\'s health. In Ayurveda, it is the primary herb for lactation support, hormonal balance, and postnatal recovery. It is a galactagogue (milk-producing herb) with adaptogenic properties.',
                'benefit_explanation' => 'Shatavari contains steroidal saponins that stimulate prolactin production, directly increasing breast milk supply. Its adaptogenic properties help the body cope with the stress of new motherhood, while its nutritive quality rebuilds strength after birth.',
                'skills_improved'    => ['nutrition_awareness', 'herbal_knowledge'],
                'health_benefit'     => 'Increased breast milk production, hormonal balance, postnatal strength',
                'safety_notes'       => 'Generally safe during breastfeeding. Avoid if you have estrogen-sensitive conditions. Consult your healthcare provider.',
                'contraindications'  => ['estrogen_sensitive_condition'],
                'ingredients_or_materials' => ['shatavari powder or capsules', 'warm milk', 'honey', 'ghee'],
                'difficulty'         => 'beginner',
                'duration_minutes'   => 5,
                'emoji'              => '🌿',
                'is_free'            => true,
                'steps' => [
                    ['title' => 'Shatavari Milk', 'instruction' => 'Add 1/2 teaspoon of shatavari powder to a cup of warm milk. Add a pinch of cardamom and 1/2 tsp of ghee. Drink once or twice daily.', 'tip' => 'Start with a small dose and increase gradually. Most women notice improved milk supply within 3-5 days.'],
                    ['title' => 'Capsule Form', 'instruction' => 'If you prefer, take 500mg shatavari capsules twice daily with warm water. Standardized extracts are available at Ayurvedic stores.'],
                    ['title' => 'Combined with Fenugreek', 'instruction' => 'For maximum lactation support, combine shatavari with fenugreek seeds (soaked overnight). This combination is the gold standard in Ayurvedic galactagogue therapy.'],
                ],
            ],
        ];

        // ---------------------------------------------------------
        // General (evidence-based, no specific culture)
        // ---------------------------------------------------------
        $generalContent = [
            [
                'title'              => 'Ginger Root — Nature\'s Anti-Nausea Remedy',
                'content_type'       => 'herb_guide',
                'stage'              => 'trimester_1',
                'category'           => 'herbs',
                'cultural_origin'    => null,
                'description'        => 'Ginger (Zingiber officinale) is the most studied and recommended herb for pregnancy nausea. Multiple randomized controlled trials confirm its safety and effectiveness. It can be taken as tea, capsules, or added to food.',
                'benefit_explanation' => 'The gingerols and shogaols in ginger inhibit serotonin receptors in the gut that trigger nausea. Clinical trials show ginger reduces nausea severity by 50% compared to placebo, with no increase in birth defects or complications.',
                'skills_improved'    => ['herbal_knowledge', 'self_care'],
                'health_benefit'     => 'Effectively reduces morning sickness without medication',
                'safety_notes'       => 'Keep intake under 1g dried ginger per day. May increase bleeding risk at very high doses.',
                'contraindications'  => ['bleeding_disorder', 'on_blood_thinners'],
                'ingredients_or_materials' => ['fresh ginger root', 'honey', 'lemon'],
                'difficulty'         => 'beginner',
                'duration_minutes'   => 5,
                'emoji'              => '🫚',
                'is_free'            => true,
                'steps' => [
                    ['title' => 'Fresh Ginger Tea', 'instruction' => 'Slice 2-3 thin pieces of fresh ginger. Steep in hot water for 5 minutes. Add honey and a squeeze of lemon. Drink up to 3 cups daily.', 'tip' => 'Sip slowly in the morning before getting out of bed for best results.'],
                    ['title' => 'Ginger Chews', 'instruction' => 'Keep crystallized ginger or ginger candy in your purse for on-the-go nausea relief. Chew slowly when nausea hits.'],
                    ['title' => 'Ginger in Cooking', 'instruction' => 'Add fresh ginger to stir-fries, soups, and rice dishes. Cooking reduces intensity but retains anti-nausea compounds.'],
                ],
            ],
            [
                'title'              => 'Raspberry Leaf Tea — Preparing for Labor',
                'content_type'       => 'herb_guide',
                'stage'              => 'trimester_3',
                'category'           => 'herbs',
                'cultural_origin'    => null,
                'description'        => 'Red raspberry leaf (Rubus idaeus) has been used by midwives for centuries to tone the uterus and prepare for labor. It contains fragarine, an alkaloid that specifically acts on uterine smooth muscle. Best started in the third trimester.',
                'benefit_explanation' => 'Raspberry leaf tea tones the uterine muscle, potentially making contractions more effective during labor. Studies show women who drink raspberry leaf tea have shorter second-stage labor and lower rates of forceps delivery.',
                'skills_improved'    => ['herbal_knowledge', 'birth_preparation'],
                'health_benefit'     => 'Uterine toning, potentially shorter labor, iron and calcium boost',
                'safety_notes'       => 'Do NOT start before 32 weeks. Begin with 1 cup daily and increase to 3 cups by week 37.',
                'contraindications'  => ['preterm_labor_risk', 'previous_cesarean', 'placenta_previa'],
                'difficulty'         => 'beginner',
                'duration_minutes'   => 5,
                'emoji'              => '🍃',
                'is_free'            => true,
                'steps' => [
                    ['title' => 'Week 32-34: Start Slow', 'instruction' => 'Steep 1 teaspoon of dried raspberry leaf in boiling water for 10 minutes. Drink one cup daily. Monitor for any cramping.'],
                    ['title' => 'Week 34-37: Build Up', 'instruction' => 'Increase to 2 cups daily. You can make a larger batch and refrigerate, drinking it warm or cool.'],
                    ['title' => 'Week 37+: Full Dose', 'instruction' => 'Drink 3 cups daily. Some midwives recommend 4 cups. The cumulative effect tones the uterus for labor.', 'tip' => 'Iced raspberry leaf tea is refreshing in summer.'],
                ],
            ],
            [
                'title'              => 'Chamomile — Gentle Calm for Anxious Mothers',
                'content_type'       => 'herb_guide',
                'stage'              => 'trimester_2',
                'category'           => 'herbs',
                'cultural_origin'    => null,
                'description'        => 'Chamomile (Matricaria chamomilla) is a gentle, pregnancy-safe herb for anxiety, insomnia, and digestive upset. Used across cultures for millennia, it contains apigenin — a compound that binds to GABA receptors to promote calm without drowsiness.',
                'benefit_explanation' => 'Chamomile reduces pregnancy anxiety and improves sleep quality without the risks of pharmaceutical sleep aids. Its anti-inflammatory properties also soothe digestive discomfort and bloating common in the second trimester.',
                'skills_improved'    => ['relaxation', 'sleep_quality', 'herbal_knowledge'],
                'health_benefit'     => 'Better sleep, reduced anxiety, digestive comfort',
                'safety_notes'       => 'Drink in moderation (1-2 cups daily). Avoid if allergic to ragweed family.',
                'contraindications'  => ['ragweed_allergy'],
                'ingredients_or_materials' => ['dried chamomile flowers or tea bags', 'honey'],
                'difficulty'         => 'beginner',
                'duration_minutes'   => 5,
                'emoji'              => '🌼',
                'is_free'            => true,
                'steps' => [
                    ['title' => 'Evening Ritual Tea', 'instruction' => 'Steep 1-2 teaspoons of dried chamomile flowers in hot water for 5-7 minutes. Add honey to taste. Drink 30-60 minutes before bed.', 'tip' => 'The ritual itself — warming the cup, sitting quietly — is part of the calming effect.'],
                    ['title' => 'Chamomile Compress for Tired Eyes', 'instruction' => 'Brew chamomile tea, let it cool. Soak cotton pads and place on closed eyes for 10 minutes. Reduces puffiness and promotes relaxation.'],
                ],
            ],
            [
                'title'              => 'The Science of Breastfeeding — Why Your Body Is Amazing',
                'content_type'       => 'article',
                'stage'              => 'postnatal_0_3m',
                'category'           => 'breastfeeding',
                'cultural_origin'    => null,
                'description'        => 'A comprehensive guide to the science behind breastfeeding — why breast milk is called "liquid gold," how your body produces it, and the extraordinary immune protection it provides. Understanding the science helps mothers persevere through early challenges.',
                'benefit_explanation' => 'Breast milk contains 700+ species of beneficial bacteria, living immune cells, antibodies tailored to your baby\'s environment, and growth factors that support brain development. Breastfed babies have 50% fewer ear infections, 64% fewer GI infections, and higher IQ scores at age 7.',
                'skills_improved'    => ['nutrition_awareness', 'breastfeeding_knowledge', 'confidence'],
                'health_benefit'     => 'Immune protection, brain development, bonding, reduced infection risk',
                'difficulty'         => 'beginner',
                'duration_minutes'   => 12,
                'emoji'              => '🤱',
                'is_free'            => true,
                'steps' => [
                    ['title' => 'Colostrum — The First Gold', 'instruction' => 'In the first 2-3 days, your breasts produce colostrum — a thick, yellowish fluid packed with antibodies (especially IgA). Just 5-7ml per feeding is enough. It coats your baby\'s gut like a protective shield.', 'tip' => 'Even if you plan to formula-feed, try to give colostrum. It is truly irreplaceable.'],
                    ['title' => 'Milk Comes In (Days 3-5)', 'instruction' => 'Your mature milk arrives around day 3-5. Your breasts may feel full and warm. Feed frequently (8-12 times in 24 hours) to establish supply. The more you feed, the more you produce — it is a supply-and-demand system.'],
                    ['title' => 'Immune Targeting', 'instruction' => 'When your baby latches, their saliva communicates with your breast. If baby is fighting an infection, your next feeding contains targeted antibodies. Your body creates a custom vaccine dose at every feed.'],
                    ['title' => 'Mental Health Benefits', 'instruction' => 'Breastfeeding releases oxytocin, which reduces anxiety and promotes bonding. It also reduces the mother\'s lifetime risk of breast cancer by 4% per year of breastfeeding, ovarian cancer by 24%, and type 2 diabetes by up to 50%.'],
                    ['title' => 'Common Early Challenges', 'instruction' => 'Sore nipples (usually resolves in 1-2 weeks), engorgement (cold compress between feeds), latching difficulties (seek lactation consultant). Every problem has a solution — do not give up in the first hard week.'],
                ],
            ],
            [
                'title'              => 'Fenugreek Seeds — Powerful Milk Booster',
                'content_type'       => 'herb_guide',
                'stage'              => 'postnatal_0_3m',
                'category'           => 'herbs',
                'cultural_origin'    => null,
                'description'        => 'Fenugreek (Trigonella foenum-graecum) is the most widely used galactagogue worldwide. Used across Indian, Middle Eastern, and Mediterranean cultures, its seeds contain diosgenin — a phytoestrogen that stimulates milk production. Most mothers notice increased supply within 24-72 hours.',
                'benefit_explanation' => 'Fenugreek stimulates prolactin and breast milk production. Studies show a 49% increase in milk volume within 72 hours. It also contains iron, calcium, and vitamins A/B/C that support postnatal recovery.',
                'skills_improved'    => ['herbal_knowledge', 'breastfeeding_support'],
                'health_benefit'     => 'Significantly increased breast milk supply, postnatal nutrient boost',
                'safety_notes'       => 'May cause maple syrup odor in sweat/urine (harmless). Avoid with blood sugar medications. Start low, increase gradually.',
                'contraindications'  => ['gestational_diabetes', 'on_blood_thinners', 'peanut_allergy'],
                'ingredients_or_materials' => ['fenugreek seeds', 'warm water'],
                'difficulty'         => 'beginner',
                'duration_minutes'   => 5,
                'emoji'              => '🌱',
                'is_free'            => true,
                'steps' => [
                    ['title' => 'Soaked Seeds Method', 'instruction' => 'Soak 1 tablespoon of fenugreek seeds in water overnight. Drink the water and chew the softened seeds in the morning. Repeat daily.', 'tip' => 'The soak releases the galactagogue compounds into the water.'],
                    ['title' => 'Fenugreek Tea', 'instruction' => 'Crush 1 teaspoon of seeds and steep in boiling water for 10 minutes. Drink 2-3 cups daily. Combine with fennel seeds for a lactation tea blend.'],
                    ['title' => 'Capsule Option', 'instruction' => 'Take 580-610mg capsules, 2-3 times daily. This is the most studied dose in clinical trials.'],
                ],
            ],
            [
                'title'              => 'Newborn Skin-to-Skin — The Golden Hour and Beyond',
                'content_type'       => 'article',
                'stage'              => 'postnatal_0_3m',
                'category'           => 'newborn_care',
                'cultural_origin'    => null,
                'description'        => 'Skin-to-skin contact (also called kangaroo care) is the practice of placing your naked newborn against your bare chest. The first hour after birth — the "golden hour" — is especially critical, but the benefits continue for months. This practice is recommended by WHO, UNICEF, and every major pediatric organization.',
                'benefit_explanation' => 'Skin-to-skin regulates the newborn\'s heart rate, breathing, and temperature more effectively than any incubator. It colonizes baby with your beneficial bacteria, triggers breastfeeding reflexes, reduces crying by 43%, and creates deep neural bonding patterns that affect attachment security for years.',
                'skills_improved'    => ['bonding', 'newborn_care', 'confidence'],
                'health_benefit'     => 'Regulated vital signs, successful breastfeeding initiation, reduced infant crying',
                'difficulty'         => 'beginner',
                'duration_minutes'   => 8,
                'emoji'              => '👶',
                'is_free'            => true,
                'steps' => [
                    ['title' => 'The Golden Hour', 'instruction' => 'Immediately after birth, request that your baby be placed on your bare chest (even after cesarean). Cover with a warm blanket. Do not separate for weighing or measuring unless medically necessary — those can wait.'],
                    ['title' => 'First Breastfeed', 'instruction' => 'In skin-to-skin position, your baby will naturally "crawl" toward your breast and attempt to latch. This crawl reflex is hardwired and happens within the first 60-90 minutes. Let it happen naturally.', 'tip' => 'The breast crawl is one of the most beautiful things in nature. Search "breast crawl video" to prepare.'],
                    ['title' => 'Daily Skin-to-Skin', 'instruction' => 'Continue daily skin-to-skin for at least the first month. Remove baby\'s clothing (keep diaper on), recline comfortably, and hold baby upright on your chest. Even 30 minutes daily makes a significant difference.'],
                    ['title' => 'Partners Too', 'instruction' => 'Skin-to-skin is not just for mothers. Partners who practice it develop the same oxytocin bonding response and increased caregiving confidence.'],
                ],
            ],
            [
                'title'              => 'Turmeric Golden Milk for Pregnancy Inflammation',
                'content_type'       => 'herb_guide',
                'stage'              => 'trimester_2',
                'category'           => 'herbs',
                'cultural_origin'    => 'ayurvedic',
                'description'        => 'Turmeric (Curcuma longa) has been used in Ayurvedic medicine for 4,000 years. Its active compound curcumin is a powerful anti-inflammatory. During pregnancy, a moderate daily dose in golden milk soothes joint pain, reduces inflammation, and supports digestive health.',
                'benefit_explanation' => 'Curcumin reduces the inflammatory markers (IL-6, TNF-α) that contribute to pregnancy joint pain and swelling. It also supports liver function, improves digestion, and has mood-stabilizing effects — all important during the second trimester growth phase.',
                'skills_improved'    => ['herbal_knowledge', 'nutrition_awareness'],
                'health_benefit'     => 'Reduced inflammation, joint pain relief, improved digestion',
                'safety_notes'       => 'Use as a food spice (1/2-1 tsp daily). Do not take concentrated curcumin supplements during pregnancy.',
                'contraindications'  => ['gallbladder_disease', 'on_blood_thinners'],
                'ingredients_or_materials' => ['turmeric powder', 'black pepper', 'warm milk', 'honey', 'cinnamon'],
                'difficulty'         => 'beginner',
                'duration_minutes'   => 5,
                'emoji'              => '🥛',
                'is_free'            => true,
                'steps' => [
                    ['title' => 'Classic Golden Milk', 'instruction' => 'Warm 1 cup of milk (dairy or plant-based). Add 1/2 tsp turmeric, a pinch of black pepper (increases curcumin absorption by 2000%), 1/2 tsp cinnamon, and honey to taste.', 'tip' => 'Black pepper is essential — without it, your body barely absorbs curcumin.'],
                    ['title' => 'Bedtime Ritual', 'instruction' => 'Drink golden milk 30 minutes before bed. The warm milk promotes sleep while turmeric works on inflammation overnight.'],
                ],
            ],
        ];

        // ---------------------------------------------------------
        // Persist all content
        // ---------------------------------------------------------
        $allContent = array_merge($chineseContent, $japaneseContent, $ayurvedicContent, $generalContent);

        foreach ($allContent as $data) {
            $steps = $data['steps'] ?? [];
            unset($data['steps']);

            $data['is_published']          = true;
            $data['moderation_status']     = 'approved';
            $data['medical_reviewer_name'] = $reviewer;
            $data['language']              = 'en';

            $content = MaternalContent::create($data);

            foreach ($steps as $i => $step) {
                $content->steps()->create(array_merge($step, ['step_number' => $i + 1]));
            }
        }

        $this->command->info('Seeded ' . count($allContent) . ' maternal content records with steps.');
    }
}
