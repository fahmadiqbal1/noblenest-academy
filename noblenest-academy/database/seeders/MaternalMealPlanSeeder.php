<?php

namespace Database\Seeders;

use App\Models\MaternalMealPlan;
use Illuminate\Database\Seeder;

/**
 * Seeds meal plans across trimesters with Ayurvedic, Chinese, Japanese,
 * and general evidence-based nutrition guidance.
 */
class MaternalMealPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            // ====================== TRIMESTER 1 ======================
            [
                'stage'                    => 'trimester_1',
                'week_number'              => 6,
                'day_of_week'              => 'Monday',
                'breakfast'                => ['name' => 'Ginger Oatmeal with Banana', 'description' => 'Rolled oats cooked with fresh ginger slices, topped with sliced banana, walnuts, and a drizzle of honey. Ginger combats morning nausea while oats provide slow-release energy.', 'calories' => 380],
                'morning_snack'            => ['name' => 'Plain Crackers with Almond Butter', 'description' => 'Dry crackers settle the stomach. Almond butter adds protein to sustain blood sugar through the morning.', 'calories' => 180],
                'lunch'                    => ['name' => 'Miso Soup with Tofu and Spinach', 'description' => 'Light, brothy miso soup with soft tofu cubes and wilted spinach. Miso provides probiotics, tofu delivers protein, and spinach adds folate — the most critical nutrient in trimester 1.', 'calories' => 320],
                'afternoon_snack'          => ['name' => 'Apple Slices with Cheese', 'description' => 'Crisp apple with mild cheddar. The combination of fiber and protein stabilizes blood sugar.', 'calories' => 200],
                'dinner'                   => ['name' => 'Lemon Herb Baked Salmon with Sweet Potato', 'description' => 'Wild salmon provides DHA omega-3 essential for baby\'s brain development. Sweet potato adds folate and vitamin A. Light lemon herb seasoning is gentle on a sensitive stomach.', 'calories' => 520],
                'hydration_notes'          => 'Aim for 8-10 glasses of room-temperature water. Add lemon if nauseous. Avoid ice-cold drinks (TCM recommendation).',
                'herb_tea_recommendation'  => 'Ginger tea (2-3 cups): steep fresh ginger in hot water with honey. Best sipped slowly in the morning before rising.',
                'key_nutrients'            => ['folate', 'DHA omega-3', 'iron', 'ginger', 'probiotics'],
                'benefit_explanation'      => 'First trimester nutrition focuses on neural tube development (folate), nausea management (ginger), and laying the DHA foundation for brain development. Small, frequent meals prevent blood sugar crashes that worsen nausea.',
                'language'                 => 'en',
            ],
            [
                'stage'                    => 'trimester_1',
                'week_number'              => 8,
                'day_of_week'              => 'Wednesday',
                'breakfast'                => ['name' => 'Ayurvedic Warm Rice Porridge (Kanji)', 'description' => 'Soft rice cooked in extra water with cumin, turmeric, and ghee. In Ayurveda, kanji is the ultimate first-trimester comfort food — easy to digest, grounding, and warming.', 'calories' => 300],
                'morning_snack'            => ['name' => 'Soaked Almonds and Dates', 'description' => 'Almonds soaked overnight (easier to digest) with 2 Medjool dates. Provides vitamin E and natural iron.', 'calories' => 220],
                'lunch'                    => ['name' => 'Japanese Clear Dashi Soup with Udon', 'description' => 'Light kombu-bonito dashi with soft udon noodles, shredded nori, and a soft-boiled egg. Gentle on the stomach with excellent mineral content from seaweed.', 'calories' => 400],
                'afternoon_snack'          => ['name' => 'Greek Yogurt with Berries', 'description' => 'Plain yogurt with mixed berries provides calcium, protein, and antioxidants.', 'calories' => 180],
                'dinner'                   => ['name' => 'Chinese Ginger Chicken Congee', 'description' => 'Rice porridge slow-cooked with chicken, ginger, and green onions. A staple of Chinese prenatal nutrition — warm, nourishing, and deeply soothing. The collagen-rich broth supports growing connective tissue.', 'calories' => 450],
                'hydration_notes'          => 'Warm water throughout the day. Add a slice of lemon or cucumber. Coconut water for electrolytes if vomiting.',
                'herb_tea_recommendation'  => 'Chamomile tea (1 cup evening): gentle calming effect, helps with first-trimester insomnia.',
                'key_nutrients'            => ['folate', 'iron', 'calcium', 'vitamin_E', 'iodine'],
                'benefit_explanation'      => 'This meal plan draws from three traditions: Ayurvedic warming foods to calm Vata, Japanese mineral-rich seaweed for iodine, and Chinese congee for easily digestible protein. All prioritize warm, cooked foods over raw.',
                'language'                 => 'en',
            ],

            // ====================== TRIMESTER 2 ======================
            [
                'stage'                    => 'trimester_2',
                'week_number'              => 18,
                'day_of_week'              => 'Monday',
                'breakfast'                => ['name' => 'Spinach and Feta Omelette with Toast', 'description' => 'Three-egg omelette with sautéed spinach and feta cheese on whole grain toast. Eggs provide choline vital for fetal brain development — second trimester is the critical window.', 'calories' => 480],
                'morning_snack'            => ['name' => 'Trail Mix with Pumpkin Seeds', 'description' => 'Pumpkin seeds are one of the best plant sources of iron and zinc. Mixed with dried cranberries and dark chocolate chips.', 'calories' => 250],
                'lunch'                    => ['name' => 'Lentil Dal with Brown Rice', 'description' => 'Ayurvedic red lentil dal cooked with cumin, turmeric, and ginger. Served with brown rice. Lentils provide 18g protein and 15g fiber per cup — supporting the increased blood volume of trimester 2.', 'calories' => 520],
                'afternoon_snack'          => ['name' => 'Edamame with Sea Salt', 'description' => 'Steamed edamame provides complete protein, folate, and fiber. A Japanese staple.', 'calories' => 190],
                'dinner'                   => ['name' => 'Grilled Chicken with Quinoa and Roasted Vegetables', 'description' => 'Lean protein with quinoa (complete protein grain) and colorful roasted vegetables (bell peppers, zucchini, sweet potato). Covers all macronutrients needed for the rapid growth phase.', 'calories' => 580],
                'hydration_notes'          => '10-12 glasses daily. Your blood volume increases by 45% in trimester 2 — extra fluids are essential.',
                'herb_tea_recommendation'  => 'Turmeric golden milk (1 cup): warm milk with 1/2 tsp turmeric, pinch of black pepper, honey. Anti-inflammatory support.',
                'key_nutrients'            => ['choline', 'iron', 'protein', 'zinc', 'fiber'],
                'benefit_explanation'      => 'Second trimester is the rapid growth phase — calorie needs increase by 340 kcal/day. This plan emphasizes iron (baby is building its own blood supply), choline (brain development peaks), and protein (muscle and organ formation).',
                'language'                 => 'en',
            ],

            // ====================== TRIMESTER 3 ======================
            [
                'stage'                    => 'trimester_3',
                'week_number'              => 32,
                'day_of_week'              => 'Tuesday',
                'breakfast'                => ['name' => 'Overnight Oats with Chia and Berries', 'description' => 'Oats soaked overnight in milk with chia seeds, topped with mixed berries and a spoon of almond butter. Chia provides omega-3 and calcium. Prep the night before — essential when fatigue returns.', 'calories' => 420],
                'morning_snack'            => ['name' => 'Hummus with Veggie Sticks', 'description' => 'Chickpea hummus with carrot, cucumber, and bell pepper sticks. Fiber helps with third-trimester constipation.', 'calories' => 200],
                'lunch'                    => ['name' => 'Chinese Warming Bone Broth Noodles', 'description' => 'Slow-cooked bone broth rich in collagen and minerals, with rice noodles, bok choy, and soft tofu. In Chinese tradition, bone broth in the third trimester strengthens mother\'s bones and supports the baby\'s skeletal calcification.', 'calories' => 480],
                'afternoon_snack'          => ['name' => 'Dates and Walnut Energy Balls', 'description' => 'Medjool dates blended with walnuts and coconut. Studies show eating 6 dates daily from week 36 reduces need for labor induction by 20%.', 'calories' => 240],
                'dinner'                   => ['name' => 'Baked Cod with Roasted Root Vegetables', 'description' => 'Mild white fish with roasted parsnips, carrots, and beets. Beets support blood pressure (key in third trimester). Cod provides lean protein without mercury concerns.', 'calories' => 500],
                'hydration_notes'          => '12+ glasses daily. Frequent small sips prevent the "too-full" feeling as baby takes up stomach space.',
                'herb_tea_recommendation'  => 'Raspberry leaf tea (2-3 cups): uterine toning from week 32. Steep for 10 minutes for full potency.',
                'key_nutrients'            => ['calcium', 'DHA omega-3', 'iron', 'fiber', 'vitamin_K'],
                'benefit_explanation'      => 'Third trimester priorities: calcium (baby\'s skeleton hardens, taking 250mg/day from you), DHA (brain reaches 60% of adult size), and iron (you need 27mg/day — double pre-pregnancy). Dates from week 36 prepare the cervix.',
                'language'                 => 'en',
            ],

            // ====================== POSTNATAL ======================
            [
                'stage'                    => 'postnatal_0_3m',
                'week_number'              => 1,
                'day_of_week'              => 'Monday',
                'breakfast'                => ['name' => 'Chinese Ginger and Sesame Oil Fried Rice', 'description' => 'Warm fried rice cooked in sesame oil with beaten eggs, ginger, and spring onions. Classic zuo yue zi breakfast — warming foods restore postpartum body heat and promote milk flow.', 'calories' => 450],
                'morning_snack'            => ['name' => 'Red Date and Goji Berry Tea', 'description' => 'TCM blood-building tea: steep red dates (jujubes) and goji berries in hot water. Replenishes the blood lost during birth.', 'calories' => 80],
                'lunch'                    => ['name' => 'Pigs Trotter and Peanut Soup (Zhu Jiao Tang)', 'description' => 'Traditional Chinese postpartum soup rich in collagen, protein, and galactagogue compounds. Peanuts add niacin and healthy fats. This soup has been the cornerstone of Chinese postpartum recovery for centuries.', 'calories' => 580],
                'afternoon_snack'          => ['name' => 'Fenugreek Lactation Cookie', 'description' => 'Oat-based cookie with fenugreek, brewer\'s yeast, and flaxseed — three clinically-supported galactagogues in one snack.', 'calories' => 220],
                'dinner'                   => ['name' => 'Ayurvedic Ghee and Turmeric Kitchari', 'description' => 'Kitchari (rice and mung beans cooked together) is considered the ultimate healing food in Ayurveda. Seasoned with warming spices (cumin, coriander, turmeric) and generous ghee. Easy to digest, deeply nourishing.', 'calories' => 500],
                'hydration_notes'          => 'Hydration is critical for milk production. Aim for 12+ glasses of warm water or broth. Drink a glass every time you breastfeed.',
                'herb_tea_recommendation'  => 'Fenugreek + fennel lactation tea: 1 tsp each, steeped 10 minutes. Drink 3 cups daily for milk supply.',
                'key_nutrients'            => ['protein', 'iron', 'calcium', 'galactagogues', 'collagen'],
                'benefit_explanation'      => 'Postpartum nutrition serves two goals: rebuild the mother (replace blood loss, heal tissues) and produce nutrient-dense breast milk. This plan combines Chinese blood-building soups, Ayurvedic kitchari for digestive ease, and evidence-based galactagogue foods.',
                'language'                 => 'en',
            ],
        ];

        foreach ($plans as $plan) {
            MaternalMealPlan::create($plan);
        }

        $this->command->info('Seeded ' . count($plans) . ' maternal meal plans.');
    }
}
