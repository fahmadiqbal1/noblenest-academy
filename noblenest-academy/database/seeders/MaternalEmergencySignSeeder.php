<?php

namespace Database\Seeders;

use App\Models\MaternalEmergencySign;
use Illuminate\Database\Seeder;

/**
 * Seeds emergency signs organized by severity and pregnancy stage.
 */
class MaternalEmergencySignSeeder extends Seeder
{
    public function run(): void
    {
        $signs = [
            // ====================== EMERGENCY (Call 911 / Go to ER) ======================
            ['stage' => 'trimester_1', 'severity' => 'emergency', 'symptom' => 'Heavy vaginal bleeding (soaking more than one pad per hour)',           'action_text' => 'Call emergency services or go to the nearest emergency room immediately. Lie on your left side while waiting for help.', 'order' => 1],
            ['stage' => 'trimester_1', 'severity' => 'emergency', 'symptom' => 'Severe abdominal pain on one side (possible ectopic pregnancy)',         'action_text' => 'This could indicate an ectopic pregnancy which is life-threatening. Call emergency services or go to the ER immediately.', 'order' => 2],
            ['stage' => 'trimester_1', 'severity' => 'emergency', 'symptom' => 'Fainting or loss of consciousness',                                     'action_text' => 'Call emergency services. If conscious, lie on your left side with feet elevated.', 'order' => 3],

            ['stage' => 'trimester_2', 'severity' => 'emergency', 'symptom' => 'Sudden severe headache with vision changes or upper abdominal pain',    'action_text' => 'These may be signs of preeclampsia. Call emergency services or go to the ER immediately. Do not drive yourself.', 'order' => 1],
            ['stage' => 'trimester_2', 'severity' => 'emergency', 'symptom' => 'Large gush of fluid from vagina (possible premature rupture of membranes)', 'action_text' => 'Go to the hospital immediately. Note the time and color of the fluid. Do not insert anything into the vagina.', 'order' => 2],
            ['stage' => 'trimester_2', 'severity' => 'emergency', 'symptom' => 'Regular contractions before 37 weeks (more than 4 per hour)',           'action_text' => 'Call your doctor or go to labor & delivery. Drink water and lie on your left side while awaiting instructions.', 'order' => 3],

            ['stage' => 'trimester_3', 'severity' => 'emergency', 'symptom' => 'Sudden severe headache that won\'t go away, with visual disturbances', 'action_text' => 'Possible preeclampsia or eclampsia. Call emergency services immediately. This is a medical emergency.', 'order' => 1],
            ['stage' => 'trimester_3', 'severity' => 'emergency', 'symptom' => 'Baby has not moved for 12+ hours or movements significantly decreased', 'action_text' => 'Go to labor & delivery immediately for monitoring. Do not wait until morning — go now.', 'order' => 2],
            ['stage' => 'trimester_3', 'severity' => 'emergency', 'symptom' => 'Vaginal bleeding with severe abdominal pain (possible placental abruption)', 'action_text' => 'Call emergency services. Lie on your left side. This can be life-threatening for both mother and baby.', 'order' => 3],
            ['stage' => 'trimester_3', 'severity' => 'emergency', 'symptom' => 'Seizures or convulsions',                                              'action_text' => 'Call emergency services. Clear the area around the person. Do not restrain or put anything in their mouth.', 'order' => 4],

            ['stage' => 'postnatal_0_3m', 'severity' => 'emergency', 'symptom' => 'Heavy bleeding (soaking more than one pad per hour, or large clots)', 'action_text' => 'This may be postpartum hemorrhage. Call emergency services or go to the ER immediately.', 'order' => 1],
            ['stage' => 'postnatal_0_3m', 'severity' => 'emergency', 'symptom' => 'Chest pain or difficulty breathing',                                 'action_text' => 'Could indicate a pulmonary embolism. Call emergency services immediately.', 'order' => 2],
            ['stage' => 'postnatal_0_3m', 'severity' => 'emergency', 'symptom' => 'Thoughts of harming yourself or your baby',                          'action_text' => 'Call your doctor, a crisis helpline, or go to the ER. You are not a bad mother — this is a medical emergency that needs treatment. Call 988 (Suicide & Crisis Lifeline).', 'order' => 3],

            // ====================== WARNING (Call Doctor Within 24hrs) ======================
            ['stage' => 'trimester_1', 'severity' => 'warning', 'symptom' => 'Spotting or light bleeding (less than a pad)',                             'action_text' => 'Light spotting can be normal, but always report it to your healthcare provider within 24 hours. Rest and avoid heavy lifting.', 'order' => 10],
            ['stage' => 'trimester_1', 'severity' => 'warning', 'symptom' => 'Severe nausea/vomiting (cannot keep any food or water down for 24 hours)', 'action_text' => 'This may be hyperemesis gravidarum. Call your doctor — you may need IV fluids and medication.', 'order' => 11],
            ['stage' => 'trimester_1', 'severity' => 'warning', 'symptom' => 'Painful urination or fever over 38°C (100.4°F)',                          'action_text' => 'Possible UTI or infection. Call your doctor for assessment. UTIs in pregnancy need prompt treatment.', 'order' => 12],

            ['stage' => 'trimester_2', 'severity' => 'warning', 'symptom' => 'Persistent headaches not relieved by rest or water',                      'action_text' => 'Could be early sign of preeclampsia. Call your doctor to check blood pressure and urine protein.', 'order' => 10],
            ['stage' => 'trimester_2', 'severity' => 'warning', 'symptom' => 'Sudden swelling of face, hands, or feet (especially if one-sided)',        'action_text' => 'Sudden swelling can indicate preeclampsia or DVT. Contact your healthcare provider today.', 'order' => 11],
            ['stage' => 'trimester_2', 'severity' => 'warning', 'symptom' => 'Itching all over body without rash (especially palms and soles)',          'action_text' => 'May indicate intrahepatic cholestasis of pregnancy (ICP). Call your doctor for bile acid blood test.', 'order' => 12],

            ['stage' => 'trimester_3', 'severity' => 'warning', 'symptom' => 'Fewer than 10 movements in 2 hours during kick counting',                 'action_text' => 'Drink cold water, lie on your left side, and count again. If still fewer than 10, call your doctor or go to hospital.', 'order' => 10],
            ['stage' => 'trimester_3', 'severity' => 'warning', 'symptom' => 'Blood pressure reading above 140/90',                                     'action_text' => 'Contact your healthcare provider immediately. Lie on your left side and recheck in 30 minutes. If still high, go to hospital.', 'order' => 11],
            ['stage' => 'trimester_3', 'severity' => 'warning', 'symptom' => 'Regular tightening or cramping (Braxton Hicks that don\'t stop with rest)', 'action_text' => 'If contractions are regular, increasing in intensity, or accompanied by bleeding/fluid, call your doctor or go to labor & delivery.', 'order' => 12],

            ['stage' => 'postnatal_0_3m', 'severity' => 'warning', 'symptom' => 'Fever over 38°C (100.4°F) in the first 6 weeks',                      'action_text' => 'Could indicate infection (endometritis, mastitis, or wound infection). Call your doctor today.', 'order' => 10],
            ['stage' => 'postnatal_0_3m', 'severity' => 'warning', 'symptom' => 'Foul-smelling vaginal discharge',                                      'action_text' => 'May indicate uterine infection. Contact your healthcare provider today. Normal lochia smells like menstrual blood, not foul.', 'order' => 11],
            ['stage' => 'postnatal_0_3m', 'severity' => 'warning', 'symptom' => 'Red, hot, painful area on breast with fever (mastitis)',                'action_text' => 'Continue breastfeeding (it helps clear the infection). Apply warm compresses. Call your doctor — antibiotics may be needed.', 'order' => 12],
            ['stage' => 'postnatal_0_3m', 'severity' => 'warning', 'symptom' => 'Persistent sadness, crying, or feeling disconnected from baby for 2+ weeks', 'action_text' => 'This may be postpartum depression — a medical condition, not a character flaw. Call your doctor. Treatment is very effective. You deserve help.', 'order' => 13],

            // ====================== INFO (Monitor / Self-Care) ======================
            ['stage' => 'trimester_1', 'severity' => 'info', 'symptom' => 'Mild nausea and food aversions',                                             'action_text' => 'Normal in weeks 6-14. Eat small, frequent meals. Ginger tea and crackers help. Usually resolves by week 14-16.', 'order' => 20],
            ['stage' => 'trimester_1', 'severity' => 'info', 'symptom' => 'Extreme fatigue and needing extra sleep',                                     'action_text' => 'Your body is building a placenta — this is exhausting work. Rest when you can. Nap guilt-free. This usually improves in trimester 2.', 'order' => 21],

            ['stage' => 'trimester_2', 'severity' => 'info', 'symptom' => 'Round ligament pain (sharp twinges in lower abdomen when moving)',            'action_text' => 'Normal as your uterus grows. Move slowly when changing positions. A warm compress helps. Mention at your next appointment.', 'order' => 20],
            ['stage' => 'trimester_2', 'severity' => 'info', 'symptom' => 'Heartburn and acid reflux',                                                  'action_text' => 'Eat smaller meals, avoid lying down after eating, and elevate your head at night. Antacids are safe — ask your doctor.', 'order' => 21],

            ['stage' => 'trimester_3', 'severity' => 'info', 'symptom' => 'Braxton Hicks contractions (irregular tightening that stops with rest)',      'action_text' => 'Normal "practice" contractions. Drink water and rest. They should be irregular and stop within an hour. If they become regular, see Warning section.', 'order' => 20],
            ['stage' => 'trimester_3', 'severity' => 'info', 'symptom' => 'Increased pelvic pressure and frequent urination',                           'action_text' => 'Baby is dropping into position. This is a good sign that labor may be approaching. Kegel exercises help with bladder control.', 'order' => 21],
            ['stage' => 'trimester_3', 'severity' => 'info', 'symptom' => 'Trouble sleeping and general discomfort',                                     'action_text' => 'Sleep on your left side with pillows between knees and under belly. A pregnancy pillow is a worthy investment. Limit fluids before bed.', 'order' => 22],

            ['stage' => 'postnatal_0_3m', 'severity' => 'info', 'symptom' => 'Baby blues (mood swings, tearfulness) in first 2 weeks',                  'action_text' => 'Affects 80% of new mothers. Caused by hormone drops. Usually resolves within 2 weeks. Rest, accept help, and talk to loved ones. If it lasts beyond 2 weeks, see Warning section.', 'order' => 20],
            ['stage' => 'postnatal_0_3m', 'severity' => 'info', 'symptom' => 'Vaginal bleeding (lochia) for 4-6 weeks postpartum',                      'action_text' => 'Normal postpartum bleeding. Starts bright red, transitions to pink, then yellowish-white over 4-6 weeks. Use pads only (no tampons). Report any increase after initial decrease.', 'order' => 21],
            ['stage' => 'postnatal_0_3m', 'severity' => 'info', 'symptom' => 'Night sweats and hot flashes',                                            'action_text' => 'Normal hormonal adjustment as estrogen drops. Keep cool, wear breathable fabrics, and stay hydrated. Typically resolves within a few weeks.', 'order' => 22],
        ];

        foreach ($signs as $sign) {
            $sign['language'] = $sign['language'] ?? 'en';
            MaternalEmergencySign::create($sign);
        }

        $this->command->info('Seeded ' . count($signs) . ' maternal emergency signs.');
    }
}
