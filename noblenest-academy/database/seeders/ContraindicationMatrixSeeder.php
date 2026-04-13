<?php

namespace Database\Seeders;

use App\Models\ContraindicationMatrix;
use App\Models\MaternalContent;
use Illuminate\Database\Seeder;

/**
 * Seeds the contraindication matrix linking health conditions to unsafe content.
 * Run AFTER MaternalContentSeeder so that content IDs exist.
 */
class ContraindicationMatrixSeeder extends Seeder
{
    public function run(): void
    {
        // Build a lookup of content by slug
        $contentBySlug = MaternalContent::pluck('id', 'slug');

        $matrix = [
            // Ginger root — unsafe for bleeding disorders & blood thinners
            [
                'slug'      => 'ginger-root-natures-anti-nausea-remedy',
                'condition' => 'bleeding_disorder',
                'reason'    => 'Ginger may increase bleeding risk by inhibiting platelet aggregation.',
            ],
            [
                'slug'      => 'ginger-root-natures-anti-nausea-remedy',
                'condition' => 'on_blood_thinners',
                'reason'    => 'Ginger may interact with anticoagulant medications, increasing bleeding risk.',
            ],

            // Raspberry leaf tea — unsafe for preterm labor risk, previous C-section, placenta previa
            [
                'slug'      => 'raspberry-leaf-tea-preparing-for-labor',
                'condition' => 'preterm_labor_risk',
                'reason'    => 'Raspberry leaf stimulates uterine contractions and may trigger premature labor.',
            ],
            [
                'slug'      => 'raspberry-leaf-tea-preparing-for-labor',
                'condition' => 'previous_cesarean',
                'reason'    => 'Uterine stimulation from raspberry leaf may stress a cesarean scar.',
            ],
            [
                'slug'      => 'raspberry-leaf-tea-preparing-for-labor',
                'condition' => 'placenta_previa',
                'reason'    => 'Uterine contractions with placenta previa can cause dangerous hemorrhage.',
            ],

            // Chamomile — unsafe for ragweed allergy
            [
                'slug'      => 'chamomile-gentle-calm-for-anxious-mothers',
                'condition' => 'ragweed_allergy',
                'reason'    => 'Chamomile is in the ragweed family and may trigger severe allergic reactions.',
            ],

            // Fenugreek — unsafe for gestational diabetes, blood thinners, peanut allergy
            [
                'slug'      => 'fenugreek-seeds-powerful-milk-booster',
                'condition' => 'gestational_diabetes',
                'reason'    => 'Fenugreek may lower blood sugar unpredictably, interacting with diabetes medication.',
            ],
            [
                'slug'      => 'fenugreek-seeds-powerful-milk-booster',
                'condition' => 'on_blood_thinners',
                'reason'    => 'Fenugreek has anticoagulant properties that may potentiate blood-thinning medications.',
            ],
            [
                'slug'      => 'fenugreek-seeds-powerful-milk-booster',
                'condition' => 'peanut_allergy',
                'reason'    => 'Fenugreek is in the same botanical family as peanuts and may cross-react in allergic individuals.',
            ],

            // Shatavari — unsafe for estrogen-sensitive conditions
            [
                'slug'      => 'shatavari-the-queen-of-maternal-herbs',
                'condition' => 'estrogen_sensitive_condition',
                'reason'    => 'Shatavari contains phytoestrogens that may worsen estrogen-sensitive conditions.',
            ],

            // Turmeric golden milk — unsafe for gallbladder disease, blood thinners
            [
                'slug'      => 'turmeric-golden-milk-for-pregnancy-inflammation',
                'condition' => 'gallbladder_disease',
                'reason'    => 'Curcumin stimulates bile production which can worsen gallbladder disease.',
            ],
            [
                'slug'      => 'turmeric-golden-milk-for-pregnancy-inflammation',
                'condition' => 'on_blood_thinners',
                'reason'    => 'Curcumin has anticoagulant properties that may interact with blood-thinning medications.',
            ],

            // General: high blood pressure → restrict intense exercises
            [
                'slug'      => 'prenatal-tai-chi-flowing-movements-for-pregnancy',
                'condition' => 'hypertension',
                'reason'    => 'While Tai Chi is generally safe, monitor blood pressure and stop if dizzy. Modified routine recommended.',
            ],
            [
                'slug'      => 'hip-opening-yoga-for-birth-preparation',
                'condition' => 'preterm_labor_risk',
                'reason'    => 'Deep squats and hip-opening poses may stimulate contractions in women at risk of preterm labor.',
            ],

            // Onsen / warm bath — unsafe for hypertension, preeclampsia
            [
                'slug'      => 'onsen-inspired-warm-bath-therapy',
                'condition' => 'hypertension',
                'reason'    => 'Warm water immersion may further lower blood pressure dangerously in hypertensive women.',
            ],
            [
                'slug'      => 'onsen-inspired-warm-bath-therapy',
                'condition' => 'preeclampsia',
                'reason'    => 'Women with preeclampsia should avoid prolonged heat exposure which affects blood pressure.',
            ],
        ];

        $seeded = 0;

        foreach ($matrix as $entry) {
            $contentId = $contentBySlug[$entry['slug']] ?? null;

            if (! $contentId) {
                $this->command->warn("Content not found for slug: {$entry['slug']} — skipping.");
                continue;
            }

            ContraindicationMatrix::create([
                'maternal_content_id' => $contentId,
                'condition'           => $entry['condition'],
                'reason'              => $entry['reason'],
            ]);

            $seeded++;
        }

        $this->command->info("Seeded {$seeded} contraindication matrix entries.");
    }
}
