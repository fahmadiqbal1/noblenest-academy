<?php

namespace Database\Seeders;

use App\Models\Activity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityStepSeeder extends Seeder
{
    // ──────────────────────────────────────────────────────────────
    // Subject-based benefit notes for developmental context
    // ──────────────────────────────────────────────────────────────
    private array $benefits = [
        'social' => ['Builds social bonding and trust', 'Develops emotional connection', 'Encourages cooperative play', 'Strengthens communication skills', 'Fosters empathy and sharing'],
        'motor' => ['Develops gross motor coordination', 'Strengthens core muscles', 'Builds balance and spatial awareness', 'Encourages body confidence', 'Improves fine motor control'],
        'sensory' => ['Stimulates sensory processing', 'Develops tactile discrimination', 'Builds sensory vocabulary', 'Encourages curiosity and exploration', 'Regulates sensory responses'],
        'art' => ['Encourages creative expression', 'Develops fine motor skills', 'Builds colour and form recognition', 'Fosters imagination', 'Supports emotional literacy through art'],
        'language' => ['Expands vocabulary and expression', 'Builds narrative skills', 'Encourages verbal confidence', 'Develops listening comprehension', 'Supports early literacy'],
        'literacy' => ['Builds letter recognition', 'Develops phonemic awareness', 'Encourages a love of reading', 'Strengthens print awareness', 'Supports early writing'],
        'numeracy' => ['Builds number sense', 'Develops counting and comparison skills', 'Introduces mathematical thinking', 'Encourages pattern recognition', 'Builds problem-solving confidence'],
        'science' => ['Develops scientific curiosity', 'Builds observation and prediction skills', 'Encourages hypothesis testing', 'Develops analytical thinking', 'Fosters love of nature and discovery'],
        'stem' => ['Develops engineering thinking', 'Encourages design and iteration', 'Builds logical reasoning', 'Fosters problem-solving persistence', 'Introduces cause-and-effect concepts'],
        'coding' => ['Introduces computational thinking', 'Builds sequencing and logic', 'Encourages algorithmic reasoning', 'Develops debugging skills', 'Fosters creativity through technology'],
        'quran' => ['Connects to spiritual identity', 'Builds recitation confidence', 'Develops Arabic pronunciation', 'Encourages love of Quran', 'Strengthens memory and concentration'],
        'arabic' => ['Develops Arabic letter recognition', 'Builds bilingual language skills', 'Encourages Arabic vocabulary', 'Strengthens cultural connection', 'Supports Arabic reading readiness'],
        'cultural' => ['Builds cultural appreciation', 'Develops global curiosity', 'Encourages respect for diversity', 'Strengthens identity and belonging', 'Broadens worldview'],
        'etiquette' => ['Builds social confidence', 'Develops respectful communication', 'Encourages good manners', 'Teaches social boundaries', 'Fosters consideration for others'],
        'islamic_studies' => ['Connects to Islamic values', 'Builds understanding of faith practices', 'Develops character through Islamic teachings', 'Encourages reflection and gratitude', 'Strengthens spiritual foundation'],
        'character' => ['Builds moral reasoning', 'Encourages virtuous habits', 'Develops emotional regulation', 'Fosters kindness and empathy', 'Strengthens inner character'],
        'math' => ['Builds mathematical thinking', 'Develops problem-solving skills', 'Encourages logical reasoning', 'Introduces abstract concepts', 'Strengthens number confidence'],
        'cognitive' => ['Develops critical thinking', 'Builds memory and attention', 'Encourages creative problem-solving', 'Develops executive functioning', 'Fosters intellectual curiosity'],
        'creative' => ['Fosters imaginative thinking', 'Encourages self-expression', 'Builds creative confidence', 'Develops innovative ideas', 'Supports artistic exploration'],
        'robotics' => ['Introduces robotics concepts', 'Builds engineering and design skills', 'Encourages systematic thinking', 'Develops hands-on problem-solving', 'Fosters innovation mindset'],
        'routine' => ['Builds healthy daily habits', 'Develops time awareness', 'Encourages independence', 'Supports executive functioning', 'Fosters predictability and security'],
    ];

    // ──────────────────────────────────────────────────────────────
    // Subject step templates – arrays of [title, instruction, duration_seconds]
    // ──────────────────────────────────────────────────────────────
    private function getSubjectSteps(string $subject, string $type, string $title, string $ageTier): array
    {
        $benefitPool = $this->benefits[$subject] ?? $this->benefits['cognitive'];

        // ── QURAN ──────────────────────────────────────────────────
        if ($subject === 'quran') {
            return [
                ['title' => 'Prepare Your Heart', 'instruction' => 'Find a quiet, clean space. Make wudu (if applicable for your child\'s age) and sit comfortably together, facing the direction that feels peaceful.', 'duration' => 60, 'benefit' => $benefitPool[0]],
                ['title' => 'Listen First', 'instruction' => 'Play an audio recitation of the relevant surah or dua. Listen together without speaking — let the words wash over you both.', 'duration' => 120, 'benefit' => $benefitPool[2]],
                ['title' => 'Repeat Together', 'instruction' => 'Recite the verse, phrase, or sound slowly. Ask your child to echo each part back to you. Keep it gentle and joyful — there is no pressure.', 'duration' => 180, 'benefit' => $benefitPool[1]],
                ['title' => 'Explore the Meaning', 'instruction' => 'In simple words, share what this verse or practice means. For example: "Bismillah means we start with the name of Allah." Use their language level.', 'duration' => 120, 'benefit' => $benefitPool[3]],
                ['title' => 'Make Dua Together', 'instruction' => 'End with a short, heartfelt dua. Raise your hands together and encourage your child to say "Ameen" at the end. Smile and hug them.', 'duration' => 60, 'benefit' => $benefitPool[4]],
            ];
        }

        // ── ARABIC ────────────────────────────────────────────────
        if ($subject === 'arabic') {
            if (str_contains(strtolower($type), 'tracing') || str_contains(strtolower($title), 'tracing') || str_contains(strtolower($title), 'letter')) {
                return [
                    ['title' => 'Introduce the Letter', 'instruction' => 'Point to the Arabic letter and say its name clearly. Trace it in the air with your finger together, saying the letter name as you go.', 'duration' => 60, 'benefit' => $benefitPool[0]],
                    ['title' => 'Sound Practice', 'instruction' => 'Make the letter\'s sound together — short sound, long sound if applicable. Put it in a word your child knows (e.g., أسد for alif, lion).', 'duration' => 90, 'benefit' => $benefitPool[1]],
                    ['title' => 'Trace the Letter', 'instruction' => 'Guide your child\'s finger along the letter strokes on paper or a whiteboard. Start from the correct starting point and follow the direction dots.', 'duration' => 120, 'benefit' => $benefitPool[2]],
                    ['title' => 'Write Independently', 'instruction' => 'Let your child try writing the letter on their own. Encourage effort over perfection — every attempt strengthens their writing hand!', 'duration' => 120, 'benefit' => $benefitPool[3]],
                    ['title' => 'Spot It Game', 'instruction' => 'Find the letter in a book, on packaging, or around the home. Exclaim excitedly each time you spot it — "There it is!"', 'duration' => 60, 'benefit' => $benefitPool[4]],
                ];
            }

            return [
                ['title' => 'Warm Up', 'instruction' => 'Begin with the Bismillah together. This centres the mind and signals that we are learning something special.', 'duration' => 30, 'benefit' => $benefitPool[0]],
                ['title' => 'Introduce the Concept', 'instruction' => 'Say the Arabic word, letter, or phrase clearly. Repeat it three times with your child, getting progressively quicker.', 'duration' => 90, 'benefit' => $benefitPool[1]],
                ['title' => 'Picture & Word Connection', 'instruction' => 'Draw or show an image that matches the Arabic word. Say the word while pointing: "This is " كتاب — kitaab — book!"', 'duration' => 90, 'benefit' => $benefitPool[2]],
                ['title' => 'Practice Together', 'instruction' => 'Use the word in a simple sentence together. Clap or stamp for each syllable to make it memorable and fun.', 'duration' => 120, 'benefit' => $benefitPool[3]],
                ['title' => 'Celebrate & Review', 'instruction' => 'Ask your child to teach the word back to you, or to a favourite toy. This reinforcement is the strongest way learning sticks!', 'duration' => 60, 'benefit' => $benefitPool[4]],
            ];
        }

        // ── MOTOR ─────────────────────────────────────────────────
        if ($subject === 'motor') {
            return [
                ['title' => 'Warm-Up & Wiggle', 'instruction' => 'Shake hands, wiggle fingers, roll shoulders, and stamp feet together. Get the body awake and ready — make it silly and fun!', 'duration' => 60, 'benefit' => $benefitPool[0]],
                ['title' => 'Watch & Learn', 'instruction' => 'Demonstrate the movement slowly so your child can observe clearly. Narrate as you go: "First I put my foot here..."', 'duration' => 60, 'benefit' => $benefitPool[1]],
                ['title' => 'Try Together', 'instruction' => 'Do the movement side by side or with gentle physical guidance. Keep your voice encouraging: "You\'ve got this!" and "Try again — brilliant!"', 'duration' => 150, 'benefit' => $benefitPool[2]],
                ['title' => 'Independent Practice', 'instruction' => 'Step back and let your child practise on their own. Count attempts, cheer each effort, and note improvement — however small.', 'duration' => 180, 'benefit' => $benefitPool[3]],
                ['title' => 'Celebrate & Cool Down', 'instruction' => 'Gently shake out muscles. High-five, hug, or do a little happy dance together. Name what your child did well specifically.', 'duration' => 60, 'benefit' => $benefitPool[4]],
            ];
        }

        // ── SENSORY ───────────────────────────────────────────────
        if ($subject === 'sensory') {
            return [
                ['title' => 'Set Up the Space', 'instruction' => 'Lay a mat or tray on the floor. Gather all sensory materials and place them within reach. A calm space = a focused explorer.', 'duration' => 60, 'benefit' => $benefitPool[0]],
                ['title' => 'Introduce the Materials', 'instruction' => 'Show each item slowly. Name its texture, temperature, or smell: "This is rough. This feels cold. Can you smell this?" Let curiosity build.', 'duration' => 90, 'benefit' => $benefitPool[1]],
                ['title' => 'Free Exploration', 'instruction' => 'Let your child touch, squish, pour, or sort freely. Resist directing — follow their lead. Narrate what you observe: "You\'re pouring the water!"', 'duration' => 240, 'benefit' => $benefitPool[2]],
                ['title' => 'Guided Discovery', 'instruction' => 'Introduce a gentle challenge: "Can you find something that feels soft?" or "Mix the two colours — what happens?" Ask and wait.', 'duration' => 120, 'benefit' => $benefitPool[3]],
                ['title' => 'Tidy Up Together', 'instruction' => 'Involve your child in cleaning up — scooping, wiping, sorting. Name items as you go. Make tidy-up feel like part of the fun, not the end of it.', 'duration' => 60, 'benefit' => $benefitPool[4]],
            ];
        }

        // ── SOCIAL ────────────────────────────────────────────────
        if ($subject === 'social') {
            return [
                ['title' => 'Come Together', 'instruction' => 'Sit face-to-face with your child. Make eye contact, smile, and say "We\'re going to do something lovely together!" This builds anticipation and connection.', 'duration' => 30, 'benefit' => $benefitPool[0]],
                ['title' => 'Set the Scene', 'instruction' => 'Explain what you\'re going to do in simple, warm language: "We\'re going to cuddle and sing / play / share." Keep it light and joyful.', 'duration' => 60, 'benefit' => $benefitPool[1]],
                ['title' => 'Main Activity', 'instruction' => 'Engage in the core interaction — whether that\'s dancing, playing, sharing, or talking. Follow your child\'s cues and match their energy.', 'duration' => 180, 'benefit' => $benefitPool[2]],
                ['title' => 'Talk About Feelings', 'instruction' => 'Ask "How does that feel?" or narrate feelings: "You look happy! I\'m happy too." Naming emotions helps children understand and express themselves.', 'duration' => 90, 'benefit' => $benefitPool[3]],
                ['title' => 'Warm Close', 'instruction' => 'End with a hug, a thank-you, or a shared smile. Say: "I really loved doing that with you." This reinforces the bond and the value of the activity.', 'duration' => 30, 'benefit' => $benefitPool[4]],
            ];
        }

        // ── ART ───────────────────────────────────────────────────
        if ($subject === 'art' || $subject === 'creative') {
            return [
                ['title' => 'Gather Art Supplies', 'instruction' => 'Collect everything you need — paper, colours, glue, or craft materials. Lay them invitingly on a protected surface. Let your child choose their favourite colour first!', 'duration' => 60, 'benefit' => $benefitPool[0]],
                ['title' => 'Explore & Experiment', 'instruction' => 'Before making the "final piece," let your child experiment on a practice sheet. Try stamping, blending colours, or testing different tools.', 'duration' => 90, 'benefit' => $benefitPool[1]],
                ['title' => 'Create Freely', 'instruction' => 'Begin the main artwork. There\'s no wrong way! Encourage your child to fill the paper, mix colours, and try bold marks. Resist the urge to correct or redo.', 'duration' => 240, 'benefit' => $benefitPool[2]],
                ['title' => 'Add Details', 'instruction' => 'Invite your child to add something special — perhaps a pattern, a favourite character, or their name. Details show pride and ownership in their work.', 'duration' => 90, 'benefit' => $benefitPool[3]],
                ['title' => 'Share & Display', 'instruction' => 'Ask your child to tell you about their artwork: "What did you create? What is your favourite part?" Then display it proudly on the wall.', 'duration' => 60, 'benefit' => $benefitPool[4]],
            ];
        }

        // ── LITERACY ──────────────────────────────────────────────
        if ($subject === 'literacy' || $subject === 'language') {
            if (str_contains(strtolower($title), 'tracing') || $type === 'tracing') {
                return [
                    ['title' => 'Introduce the Letter', 'instruction' => 'Say the letter name and its sound: "This is B — it says buh, like ball and butterfly." Trace it in the air together with a big, sweeping motion.', 'duration' => 60, 'benefit' => $benefitPool[0]],
                    ['title' => 'Trace With Finger', 'instruction' => 'Guide your child\'s finger along the letter on a large card or worksheet. Use direction language: "Start at the top, go down, then add the bump."', 'duration' => 90, 'benefit' => $benefitPool[1]],
                    ['title' => 'Multisensory Practice', 'instruction' => 'Practice the letter in different media — in sand, in salt, with a wet paintbrush on paper, or moulded in playdough. Feel the shape!', 'duration' => 120, 'benefit' => $benefitPool[2]],
                    ['title' => 'Write the Letter', 'instruction' => 'Give your child a pencil and lined or dotted paper. Encourage 3–5 careful attempts. Praise the effort: "Your letter is getting stronger every time!"', 'duration' => 120, 'benefit' => $benefitPool[3]],
                    ['title' => 'Find It in the Wild', 'instruction' => 'Go on a letter hunt — find it in books, cereal boxes, street signs, or t-shirts. Exclaim with delight each time: "There\'s our letter!"', 'duration' => 60, 'benefit' => $benefitPool[4]],
                ];
            }

            return [
                ['title' => 'Reading Time Ritual', 'instruction' => 'Snuggle together with the book or text. Run your finger under the title: "This story is called..." Let your child predict what it might be about from the cover.', 'duration' => 60, 'benefit' => $benefitPool[0]],
                ['title' => 'Read Aloud Together', 'instruction' => 'Read with expression — change your voice for different characters, pause for effect, and point to pictures. Ask "What do you think will happen next?"', 'duration' => 240, 'benefit' => $benefitPool[1]],
                ['title' => 'Word Spotlight', 'instruction' => 'Pick one interesting or tricky word. Look at its shape, sound, and meaning together. Try using it in a funny sentence.', 'duration' => 60, 'benefit' => $benefitPool[2]],
                ['title' => 'Retell the Story', 'instruction' => 'Ask your child to retell the story in their own words — using the "beginning, middle, end" structure. Prompt gently if needed: "And then what happened?"', 'duration' => 90, 'benefit' => $benefitPool[3]],
                ['title' => 'Connect to Life', 'instruction' => 'Ask: "Has anything like this ever happened to you?" or "How would YOU feel in that situation?" Making real-world connections deepens comprehension.', 'duration' => 60, 'benefit' => $benefitPool[4]],
            ];
        }

        // ── NUMERACY / MATH ──────────────────────────────────────
        if ($subject === 'numeracy' || $subject === 'math') {
            return [
                ['title' => 'Introduce the Concept', 'instruction' => 'Explain the mathematical idea in simple, concrete terms. Use real objects (toys, fruit, fingers) to make the abstract tangible and fun.', 'duration' => 60, 'benefit' => $benefitPool[0]],
                ['title' => 'Explore Together', 'instruction' => 'Work through examples side by side. Count aloud, sort objects, or build patterns together. Make errors okay — learning lives in mistakes!', 'duration' => 120, 'benefit' => $benefitPool[1]],
                ['title' => 'Guided Practice', 'instruction' => 'Give your child 2–3 examples to try with some support. Ask "Why do you think that?" to build mathematical reasoning.', 'duration' => 120, 'benefit' => $benefitPool[2]],
                ['title' => 'Independent Challenge', 'instruction' => 'Set a small independent task — sorting, counting, matching, or solving. Celebrate every attempt, correct or not. Say: "Tell me how you worked that out!"', 'duration' => 120, 'benefit' => $benefitPool[3]],
                ['title' => 'Real-World Connection', 'instruction' => 'Find this maths concept in daily life: counting stairs, sorting laundry, measuring ingredients. Maths is everywhere — point it out and smile!', 'duration' => 60, 'benefit' => $benefitPool[4]],
            ];
        }

        // ── SCIENCE / STEM ────────────────────────────────────────
        if ($subject === 'science' || $subject === 'stem') {
            return [
                ['title' => 'Ask a Question', 'instruction' => 'Start with wonder: "I wonder what will happen if...?" Let your child predict the outcome. Write or draw their prediction — there are no wrong answers in science!', 'duration' => 60, 'benefit' => $benefitPool[0]],
                ['title' => 'Gather Materials', 'instruction' => 'Collect everything needed for the experiment or exploration. Name each item and talk about its purpose. Safety reminders are part of the science process!', 'duration' => 60, 'benefit' => $benefitPool[1]],
                ['title' => 'Investigate!', 'instruction' => 'Carry out the experiment step by step. Observe closely — look, listen, smell (safely!). Narrate what you see: "The water is changing colour!"', 'duration' => 210, 'benefit' => $benefitPool[2]],
                ['title' => 'Record Observations', 'instruction' => 'Draw, write, or photograph what happened. Ask: "Was our prediction right?" Talk about what you observed — the language of science is observation and evidence.', 'duration' => 90, 'benefit' => $benefitPool[3]],
                ['title' => 'Ask "Why?"', 'instruction' => 'Explore why this happened together. Look it up, make a guess, or connect it to something they already know. Curiosity is the engine of scientific thinking.', 'duration' => 60, 'benefit' => $benefitPool[4]],
            ];
        }

        // ── CODING ────────────────────────────────────────────────
        if ($subject === 'coding' || $subject === 'robotics') {
            return [
                ['title' => 'Understand the Problem', 'instruction' => 'Read the challenge together. Ask: "What do we need the computer/robot to do?" Break it into smaller goals. A clear problem is half the solution!', 'duration' => 60, 'benefit' => $benefitPool[0]],
                ['title' => 'Plan the Steps', 'instruction' => 'Before coding, plan the steps on paper or with blocks: "First... then... last..." This is called an algorithm — and your child is writing one!', 'duration' => 90, 'benefit' => $benefitPool[1]],
                ['title' => 'Write the Code', 'instruction' => 'Open the coding tool (Scratch, Blockly, or physical coding cards). Implement the planned steps one at a time. Type or drag with purpose.', 'duration' => 180, 'benefit' => $benefitPool[2]],
                ['title' => 'Test & Debug', 'instruction' => 'Run the program! If something goes wrong — celebrate it! "A bug! We\'re real programmers now." Read the output and find the mistake together.', 'duration' => 120, 'benefit' => $benefitPool[3]],
                ['title' => 'Add a Feature', 'instruction' => 'Once working, ask: "What could we add to make it even better?" This encourages creative extension and ownership of their creation.', 'duration' => 90, 'benefit' => $benefitPool[4]],
            ];
        }

        // ── CULTURAL / ETIQUETTE / CHARACTER / ISLAMIC ──────────
        if (in_array($subject, ['cultural', 'etiquette', 'character', 'islamic_studies'])) {
            return [
                ['title' => 'Tell a Story', 'instruction' => 'Begin with a short, relatable story or real-life scenario that introduces today\'s value or practice. Keep it warm and concrete, not preachy.', 'duration' => 90, 'benefit' => $benefitPool[0]],
                ['title' => 'Discuss Together', 'instruction' => 'Ask open questions: "What do you think that person was feeling?" or "What would YOU do?" There are no wrong answers — thinking is the goal.', 'duration' => 90, 'benefit' => $benefitPool[1]],
                ['title' => 'Role Play', 'instruction' => 'Act out the scenario together. Take turns playing different characters. Help your child practise the right words and actions in a safe, fun way.', 'duration' => 120, 'benefit' => $benefitPool[2]],
                ['title' => 'Real-World Connection', 'instruction' => 'Ask when and where this value appears in their daily life. "When do we say Salam?" or "Who do you know who is very generous?" Make it personal.', 'duration' => 60, 'benefit' => $benefitPool[3]],
                ['title' => 'Make a Promise', 'instruction' => 'End with a gentle commitment: "This week, let\'s try to..." Write or draw it together. A child who is heard is a child who grows.', 'duration' => 60, 'benefit' => $benefitPool[4]],
            ];
        }

        // ── ROUTINE ──────────────────────────────────────────────
        if ($subject === 'routine') {
            return [
                ['title' => 'Introduce the Routine', 'instruction' => 'Explain today\'s routine step in simple words. Show it visually if possible — a picture chart, a song, or a hand gesture can make routines stick.', 'duration' => 60, 'benefit' => $benefitPool[0]],
                ['title' => 'Model Together', 'instruction' => 'Do the routine action together: wash hands, brush teeth, make the bed, pack the bag. Narrate each step as you go.', 'duration' => 90, 'benefit' => $benefitPool[1]],
                ['title' => 'Your Turn!', 'instruction' => 'Step aside and let your child try independently. Be patient, stay close, and offer only the minimum support needed. Independence grows here.', 'duration' => 120, 'benefit' => $benefitPool[2]],
                ['title' => 'Check & Celebrate', 'instruction' => 'Review together: "Did we do all the steps?" Use a checklist or count on fingers. A sticker or a specific compliment seals the habit.', 'duration' => 60, 'benefit' => $benefitPool[3]],
                ['title' => 'Connect to Why', 'instruction' => 'Share the reason simply: "We wash our hands so we stay healthy and clean for Allah." Values behind routines make them meaningful, not just mechanical.', 'duration' => 30, 'benefit' => $benefitPool[4]],
            ];
        }

        // ── COGNITIVE ────────────────────────────────────────────
        if ($subject === 'cognitive') {
            return [
                ['title' => 'Open with a Puzzle', 'instruction' => 'Present a puzzle, riddle, or thinking challenge: "How many ways can you sort these objects?" Curiosity is ignited by an open question.', 'duration' => 60, 'benefit' => $benefitPool[0]],
                ['title' => 'Think Aloud Together', 'instruction' => 'Model your thinking process out loud: "I\'m not sure... let me think... what if I try this?" Children learn to reason by watching you reason.', 'duration' => 90, 'benefit' => $benefitPool[1]],
                ['title' => 'Try Different Approaches', 'instruction' => 'Encourage at least two different solutions. Praise divergent thinking: "That\'s a way I never thought of — tell me more!"', 'duration' => 120, 'benefit' => $benefitPool[2]],
                ['title' => 'Reflect on the Process', 'instruction' => 'Ask: "Which approach worked best? Why?" Reasoning about reasoning — metacognition — is the deepest kind of learning.', 'duration' => 90, 'benefit' => $benefitPool[3]],
                ['title' => 'Apply to Real Life', 'instruction' => 'Connect today\'s challenge to something real: "This is how engineers design bridges!" or "Detectives think exactly this way." Make thinking feel powerful.', 'duration' => 60, 'benefit' => $benefitPool[4]],
            ];
        }

        // ── ACTIVITY TYPE FALLBACKS ────────────────────────────────

        if ($type === 'drawing' || $type === 'tracing' || $type === 'worksheet') {
            return [
                ['title' => 'Prepare Your Paper', 'instruction' => 'Lay out a clean sheet and gather pencils, crayons, or pens. Choose colours that match the theme. Make the space inviting and well-lit.', 'duration' => 60, 'benefit' => $benefitPool[0]],
                ['title' => 'Observe & Discuss', 'instruction' => 'Look at an example or the prompt together. Ask: "What do you notice? What shapes can you see? Where should we start?" Look before you leap!', 'duration' => 60, 'benefit' => $benefitPool[1]],
                ['title' => 'Start Drawing', 'instruction' => 'Begin with light pencil lines. Build shapes gradually — start with the biggest forms and add details. Remind your child: every great artist begins with a single line.', 'duration' => 180, 'benefit' => $benefitPool[2]],
                ['title' => 'Add Colour & Detail', 'instruction' => 'Colour in, add patterns, or trace with a darker line. Take pleasure in the details — each one adds personality to the work.', 'duration' => 120, 'benefit' => $benefitPool[3]],
                ['title' => 'Present Your Work', 'instruction' => 'Hold up the finished piece and describe it: "I drew... because..." Sharing the story behind art develops confidence and language.', 'duration' => 60, 'benefit' => $benefitPool[4]],
            ];
        }

        if ($type === 'story' || $type === 'reading') {
            return [
                ['title' => 'Choose the Story', 'instruction' => 'Find a comfortable, cosy spot together. Choose the book or settle in for the story. Look at the cover — ask: "What do you think this story is about?"', 'duration' => 30, 'benefit' => $benefitPool[0]],
                ['title' => 'Read Aloud', 'instruction' => 'Read the story with expression and warmth. Use different voices for characters, pause dramatically, and point to pictures. Make it a performance!', 'duration' => 300, 'benefit' => $benefitPool[1]],
                ['title' => 'Stop & Wonder', 'instruction' => 'Pause at a key moment and ask: "What do you think will happen next?" or "How would YOU feel?" Great questions make good readers.', 'duration' => 60, 'benefit' => $benefitPool[2]],
                ['title' => 'Retell the Story', 'instruction' => 'After reading, ask your child to retell the story in their own words. Use prompts: "Who was in the story? What happened? How did it end?"', 'duration' => 90, 'benefit' => $benefitPool[3]],
                ['title' => 'My Favourite Part', 'instruction' => 'Ask: "What was your favourite part and why?" Share yours too! Stories are conversations — and the best conversations last long after the book closes.', 'duration' => 60, 'benefit' => $benefitPool[4]],
            ];
        }

        if ($type === 'quiz') {
            return [
                ['title' => 'Set the Stage', 'instruction' => 'Explain that we\'re going to play a quiz game! Make it feel exciting: "I\'m going to ask you some questions — you\'re going to be amazing!"', 'duration' => 30, 'benefit' => $benefitPool[0]],
                ['title' => 'Warm-Up Questions', 'instruction' => 'Start with 2-3 easy questions to build confidence. Cheer enthusiastically for correct answers. If they struggle, say "Good try! The answer is..." and move on warmly.', 'duration' => 90, 'benefit' => $benefitPool[1]],
                ['title' => 'Main Quiz', 'instruction' => 'Work through the main questions. Give thinking time — count silently to 5. Avoid rushing. Celebrate the thinking process as much as the right answers.', 'duration' => 180, 'benefit' => $benefitPool[2]],
                ['title' => 'Review Together', 'instruction' => 'Go back over any questions that were tricky. Re-explain with a different approach — a drawing, an example, or a story. Learning happens in the review.', 'duration' => 90, 'benefit' => $benefitPool[3]],
                ['title' => 'Celebrate the Score', 'instruction' => 'Tally up and celebrate! Whether 3/5 or 5/5, they showed their brain to the world today. What will they try to improve next time?', 'duration' => 30, 'benefit' => $benefitPool[4]],
            ];
        }

        if ($type === 'matching') {
            return [
                ['title' => 'Introduce the Sets', 'instruction' => 'Lay out all items or cards face-up. Name each one together: "This is big, and this is small. This is hot, and this is cold." Slowing down here builds vocabulary.', 'duration' => 60, 'benefit' => $benefitPool[0]],
                ['title' => 'Find the Pairs', 'instruction' => 'Start matching! Pick up one item and ask: "What goes with this?" Wait — let your child look and think. Avoid jumping in too quickly.', 'duration' => 150, 'benefit' => $benefitPool[1]],
                ['title' => 'Talk About Why', 'instruction' => 'For each match, ask: "Why do these go together?" Explaining reasoning reinforces the concept far more deeply than just pairing correctly.', 'duration' => 90, 'benefit' => $benefitPool[2]],
                ['title' => 'Mix It Up', 'instruction' => 'Shuffle the cards or items and try again, faster. Or play a memory game version: flip cards face-down and take turns flipping two to find matches.', 'duration' => 120, 'benefit' => $benefitPool[3]],
                ['title' => 'Create Your Own!', 'instruction' => 'Ask your child to invent a new matching set: "Can you find two more things that match in our house?" Ownership of learning is the deepest kind.', 'duration' => 60, 'benefit' => $benefitPool[4]],
            ];
        }

        if ($type === 'outdoor' || $type === 'game') {
            return [
                ['title' => 'Get Ready!', 'instruction' => 'Put on appropriate clothing and shoes. Set boundaries if outdoors. Do a quick body warm-up — jump 5 times, touch your toes, spin around!', 'duration' => 60, 'benefit' => $benefitPool[0]],
                ['title' => 'Explain the Rules', 'instruction' => 'Share the rules of the activity clearly and simply. Ask "Do you understand?" and check by asking them to explain back. Play fair from the start!', 'duration' => 60, 'benefit' => $benefitPool[1]],
                ['title' => 'Play Together!', 'instruction' => 'Dive in! Engage fully — don\'t just watch. Your enthusiastic participation is the greatest motivator your child has. Go all in!', 'duration' => 240, 'benefit' => $benefitPool[2]],
                ['title' => 'Try Something New', 'instruction' => 'Introduce a variation or extension: play in reverse, make it harder, add a new rule. Extending play develops creative thinking and adaptability.', 'duration' => 90, 'benefit' => $benefitPool[3]],
                ['title' => 'Wind Down & Talk', 'instruction' => 'Cool down with some slow movements or a drink of water. Ask: "What was your favourite moment?" Reflecting after activity helps children process learning.', 'duration' => 60, 'benefit' => $benefitPool[4]],
            ];
        }

        if ($type === 'music' || $type === 'vocal') {
            return [
                ['title' => 'Listen First', 'instruction' => 'Play the song or audio clip and just listen together — eyes closed if you like! Tap the beat gently on your knees. Let the music settle in.', 'duration' => 60, 'benefit' => $benefitPool[0]],
                ['title' => 'Learn the Words', 'instruction' => 'Go line by line through the lyrics or sounds. Echo each phrase: you say it, they repeat. Keep it slow and rhythmic. Smiling makes memories!', 'duration' => 120, 'benefit' => $benefitPool[1]],
                ['title' => 'Sing Together!', 'instruction' => 'Sing the whole song (or section) together from start to finish. Add actions, clapping, or swaying. Don\'t worry about perfection — joy is the goal.', 'duration' => 120, 'benefit' => $benefitPool[2]],
                ['title' => 'Perform It!', 'instruction' => 'Put on a little "concert" — your child performs while you are the audience. Cheer, clap, and ask for an encore! Performance builds confidence.', 'duration' => 90, 'benefit' => $benefitPool[3]],
                ['title' => 'Explore the Meaning', 'instruction' => 'Talk about what the song is about: the words, the feeling, the lesson. "What does this song teach us?" helps children connect music to meaning.', 'duration' => 60, 'benefit' => $benefitPool[4]],
            ];
        }

        if ($type === 'experiment' || $type === 'hands_on' || $type === 'craft') {
            return [
                ['title' => 'Gather Materials', 'instruction' => 'Collect everything you\'ll need. Lay it all out so it\'s visible. Ask your child to name each item — "What do you think we\'ll use this for?"', 'duration' => 60, 'benefit' => $benefitPool[0]],
                ['title' => 'Prepare & Predict', 'instruction' => 'Before starting, ask: "What do you think will happen?" Accept all predictions confidently — the prediction IS the learning, whatever the outcome.', 'duration' => 60, 'benefit' => $benefitPool[1]],
                ['title' => 'Make & Do!', 'instruction' => 'Work through the activity together step by step. Let your child do as much as they safely can. Hands-on effort is ten times more powerful than watching.', 'duration' => 240, 'benefit' => $benefitPool[2]],
                ['title' => 'Observe & Discuss', 'instruction' => 'Look at what you\'ve made or discovered together. "What changed? What surprised you? What worked well?" Talk through what was observed.', 'duration' => 90, 'benefit' => $benefitPool[3]],
                ['title' => 'Display or Share', 'instruction' => 'Show the creation to a family member, take a photo, or display it at home. Recognition of the effort completed — not just the outcome — builds a growth mindset.', 'duration' => 60, 'benefit' => $benefitPool[4]],
            ];
        }

        if ($type === 'puzzle') {
            return [
                ['title' => 'Set Out the Puzzle', 'instruction' => 'Spread puzzle pieces picture-side up on a clear surface. Take a moment to look at the completed picture. "Where shall we start?"', 'duration' => 60, 'benefit' => $benefitPool[0]],
                ['title' => 'Find the Edges', 'instruction' => 'Sort for edge/corner pieces first. This is a strategy — and teaching strategies is teaching thinking. "Corners have two straight sides — can you find them?"', 'duration' => 90, 'benefit' => $benefitPool[1]],
                ['title' => 'Work Section by Section', 'instruction' => 'Group pieces by colour or feature. Work on one section at a time: "Let\'s do all the blue sky pieces." Progress is visible — that motivates!', 'duration' => 180, 'benefit' => $benefitPool[2]],
                ['title' => 'Try, Try, Try', 'instruction' => 'When a piece doesn\'t fit, say: "That\'s not right — but we\'re one step closer to finding where it goes!" Persistence is the puzzle\'s real lesson.', 'duration' => 90, 'benefit' => $benefitPool[3]],
                ['title' => 'Complete & Celebrate!', 'instruction' => 'Place the final piece together! Take a photo, clap, and celebrate. Ask: "What was the hardest part?" Talking about challenge builds resilience.', 'duration' => 60, 'benefit' => $benefitPool[4]],
            ];
        }

        if ($type === 'video' || $type === 'observation') {
            return [
                ['title' => 'Prepare to Watch', 'instruction' => 'Sit comfortably together. Ask your child to think of one question they want answered by the video or observation. "What are you curious about?"', 'duration' => 30, 'benefit' => $benefitPool[0]],
                ['title' => 'Watch & Notice', 'instruction' => 'Watch or observe with full attention. Pause at key moments and say: "Did you see that? What do you think is happening?" Active watching beats passive watching.', 'duration' => 240, 'benefit' => $benefitPool[1]],
                ['title' => 'Discuss What You Saw', 'instruction' => 'Talk about the key moments: "What was the most interesting thing? Did you learn something new?" Let your child lead the conversation.', 'duration' => 90, 'benefit' => $benefitPool[2]],
                ['title' => 'Ask the Big Question', 'instruction' => 'Return to their opening question: "Was your question answered? What do you know now that you didn\'t know before?" Closure is powerful.', 'duration' => 60, 'benefit' => $benefitPool[3]],
                ['title' => 'Try It Yourself', 'instruction' => 'If possible, attempt to replicate something from the video or re-enact an observed scene. Bridging watching to doing seals the learning.', 'duration' => 90, 'benefit' => $benefitPool[4]],
            ];
        }

        // ── ABSOLUTE FALLBACK ─────────────────────────────────────
        return [
            ['title' => 'Set Up & Get Ready', 'instruction' => 'Gather your materials and find a comfortable space. Take a moment to preview what you\'re about to do — a quick look ahead helps children feel safe and prepared.', 'duration' => 60, 'benefit' => $benefitPool[0]],
            ['title' => 'Introduce the Activity', 'instruction' => 'Explain what you\'ll be doing today in simple, warm language. Answer any questions your child has before diving in. Curiosity is a great starting engine!', 'duration' => 60, 'benefit' => $benefitPool[1]],
            ['title' => 'Dive In Together', 'instruction' => 'Work through the activity step by step, at your child\'s pace. Offer support when needed, but let them lead where they can. Progress over perfection!', 'duration' => 240, 'benefit' => $benefitPool[2]],
            ['title' => 'Reflect & Share', 'instruction' => 'Ask: "What did you enjoy most? Was there anything that felt tricky? What would you do differently?" Reflection after activity deepens learning.', 'duration' => 90, 'benefit' => $benefitPool[3]],
            ['title' => 'Connect to the World', 'instruction' => 'Find a real-world connection to today\'s activity. Where do we see this in daily life? Linking school to life shows children why learning matters.', 'duration' => 60, 'benefit' => $benefitPool[4]],
        ];
    }

    // ──────────────────────────────────────────────────────────────
    // Parse plain-text numbered instructions into step arrays
    // e.g. "1. Do this.\n2. Then this.\n3. Finally..."
    // ──────────────────────────────────────────────────────────────
    private function parseInstructions(string $instructions, string $subject): array
    {
        $benefitPool = $this->benefits[$subject] ?? $this->benefits['cognitive'];
        $steps = [];

        // Normalise literal \n sequences to real newlines
        $instructions = str_replace(['\\n', '\n'], "\n", $instructions);

        // Try to split on numbered list: "1. ...\n2. ..."
        preg_match_all('/\d+\.\s*(.+?)(?=\n\d+\.|$)/s', $instructions, $matches);
        $items = $matches[1] ?? [];

        // Fallback: split on newlines
        if (count($items) < 2) {
            $items = array_filter(array_map('trim', explode("\n", $instructions)));
        }

        // Fallback: split on ". " sentence boundary
        if (count($items) < 2) {
            $items = array_filter(array_map('trim', preg_split('/(?<=[.!?])\s+(?=[A-Z\d])/', $instructions)));
        }

        foreach (array_values($items) as $i => $item) {
            $item = trim(preg_replace('/^\d+\.\s*/', '', $item));
            // Strip residual step-number artifacts (e.g. "text.\n2." at end)
            $item = preg_replace('/\s*\\\?\\n\d+\..*$/s', '', $item);
            $item = trim($item);
            if (empty($item)) {
                continue;
            }
            // Generate a short title: first sentence up to 50 chars
            $firstSentence = preg_split('/(?<=[.!?:])\s+/', $item)[0] ?? $item;
            $title = strlen($firstSentence) <= 50
                ? rtrim($firstSentence, '.,;: ')
                : rtrim(mb_substr($firstSentence, 0, 47), ' ').'…';

            $steps[] = [
                'title' => $title,
                'instruction' => $item,
                'duration' => 120,
                'benefit' => $benefitPool[$i % count($benefitPool)],
            ];
        }

        return array_slice($steps, 0, 8); // max 8 steps from text
    }

    // ──────────────────────────────────────────────────────────────
    // run()
    // ──────────────────────────────────────────────────────────────
    public function run(): void
    {
        $this->command->info('Seeding activity steps…');

        $now = now();
        $batch = [];
        $total = 0;
        $chunk = 100; // rows flushed per batch

        Activity::with('steps')
            ->orderBy('id')
            ->chunkById(500, function ($activities) use (&$batch, &$total, &$chunk, $now) {
                foreach ($activities as $activity) {
                    // Skip if already has steps
                    if ($activity->steps->isNotEmpty()) {
                        continue;
                    }

                    $rawInstructions = $activity->getRawOriginal('instructions');
                    $subject = $activity->subject ?? 'cognitive';
                    $type = $activity->activity_type ?? '';
                    $ageTier = $activity->age_tier ?? '';

                    // Get steps
                    if ($rawInstructions) {
                        $steps = $this->parseInstructions($rawInstructions, $subject);
                    } else {
                        $steps = $this->getSubjectSteps($subject, $type, $activity->title ?? '', $ageTier);
                    }

                    foreach ($steps as $idx => $step) {
                        $batch[] = [
                            'activity_id' => $activity->id,
                            'step_number' => $idx + 1,
                            'title' => $step['title'],
                            'instruction' => $step['instruction'],
                            'visual_url' => null,
                            'video_url' => null,
                            'audio_url' => null,
                            'duration_seconds' => $step['duration'],
                            'benefit_note' => $step['benefit'],
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }

                    $total++;

                    // Flush in chunks
                    if (count($batch) >= 500) {
                        DB::table('activity_steps')->insert($batch);
                        $batch = [];
                        $this->command->line("  ∙ {$total} activities processed…");
                    }
                }
            });

        // Final flush
        if (! empty($batch)) {
            DB::table('activity_steps')->insert($batch);
        }

        $stepCount = DB::table('activity_steps')->count();
        $this->command->info("Done — {$total} activities seeded, {$stepCount} total steps inserted.");
    }
}
