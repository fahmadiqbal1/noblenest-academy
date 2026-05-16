<?php

namespace Database\Seeders;

use App\Models\AssessmentQuestion;
use Illuminate\Database\Seeder;

/**
 * Phase 3 — 30-question discovery battery.
 *
 * Each question carries 4 options. Each option contributes points to 1-2
 * dimensions: cognitive_logic, creative, social, kinetic, naturalist,
 * linguistic. Scoring is additive — the assessment_responses row stores
 * both the raw answer vector and the computed per-dimension scores.
 *
 * Framing: this is an "interest-and-strength indicator" suitable for ages
 * 7–10. NOT a clinical assessment. The parent-facing PDF report (Phase 3
 * follow-up) repeats this framing prominently.
 *
 * Sources: WPPSI-IV theme prompts + Big Five Mini for Kids style cues, all
 * paraphrased — no copyrighted instrument items reproduced.
 */
class AssessmentBatterySeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->questions() as $i => $q) {
            AssessmentQuestion::updateOrCreate(
                ['battery' => 'discovery', 'sequence' => $i + 1],
                [
                    'age_min_months' => 84,   // ~age 7
                    'age_max_months' => 132,  // ~age 11
                    'prompt'         => $q['prompt'],
                    'options'        => $q['options'],
                ]
            );
        }
    }

    private function questions(): array
    {
        $dim = fn (...$pairs) => $this->dim(...$pairs);
        return [
            [
                'prompt' => 'When you have a free afternoon, what do you most want to do?',
                'options' => [
                    ['label' => 'Build something with blocks, Lego, or tools', 'dimensions' => $dim('kinetic', 2, 'creative', 1)],
                    ['label' => 'Read or write a story', 'dimensions' => $dim('linguistic', 2, 'creative', 1)],
                    ['label' => 'Play with friends outside', 'dimensions' => $dim('social', 2, 'kinetic', 1)],
                    ['label' => 'Take care of plants or animals', 'dimensions' => $dim('naturalist', 2, 'social', 1)],
                ],
            ],
            [
                'prompt' => 'A puzzle takes you a while to solve. What feels most true?',
                'options' => [
                    ['label' => 'I work on it until it clicks', 'dimensions' => $dim('cognitive_logic', 2)],
                    ['label' => 'I ask someone to help me think it through', 'dimensions' => $dim('social', 2, 'cognitive_logic', 1)],
                    ['label' => 'I try a completely different angle', 'dimensions' => $dim('creative', 2, 'cognitive_logic', 1)],
                    ['label' => 'I take a break and come back later', 'dimensions' => $dim('cognitive_logic', 1, 'naturalist', 1)],
                ],
            ],
            [
                'prompt' => 'Which classroom subject feels most exciting?',
                'options' => [
                    ['label' => 'Maths or science', 'dimensions' => $dim('cognitive_logic', 2)],
                    ['label' => 'Reading and writing', 'dimensions' => $dim('linguistic', 2)],
                    ['label' => 'Art or music', 'dimensions' => $dim('creative', 2)],
                    ['label' => 'PE or recess', 'dimensions' => $dim('kinetic', 2)],
                ],
            ],
            [
                'prompt' => 'A friend looks sad. What is your first move?',
                'options' => [
                    ['label' => 'Sit next to them quietly', 'dimensions' => $dim('social', 2)],
                    ['label' => 'Tell a joke to cheer them up', 'dimensions' => $dim('social', 1, 'creative', 1)],
                    ['label' => 'Ask if I can help solve the problem', 'dimensions' => $dim('cognitive_logic', 1, 'social', 1)],
                    ['label' => 'Give them space until they want to talk', 'dimensions' => $dim('social', 1, 'cognitive_logic', 1)],
                ],
            ],
            [
                'prompt' => 'In a group project, which role fits you best?',
                'options' => [
                    ['label' => 'The one organising the plan', 'dimensions' => $dim('cognitive_logic', 1, 'social', 2)],
                    ['label' => 'The one with bold new ideas', 'dimensions' => $dim('creative', 2, 'social', 1)],
                    ['label' => 'The one keeping everyone happy', 'dimensions' => $dim('social', 2)],
                    ['label' => 'The one building or making the thing', 'dimensions' => $dim('kinetic', 2)],
                ],
            ],
            [
                'prompt' => 'How do you feel about being outdoors for a long time?',
                'options' => [
                    ['label' => 'Love it — I want to explore', 'dimensions' => $dim('naturalist', 2, 'kinetic', 1)],
                    ['label' => 'Like it if there is something to do', 'dimensions' => $dim('kinetic', 1, 'social', 1)],
                    ['label' => 'I prefer reading in a cosy spot', 'dimensions' => $dim('linguistic', 2)],
                    ['label' => 'I prefer building or making things indoors', 'dimensions' => $dim('creative', 1, 'cognitive_logic', 1)],
                ],
            ],
            [
                'prompt' => 'Which kind of book grabs you most?',
                'options' => [
                    ['label' => 'Science or how-things-work books', 'dimensions' => $dim('cognitive_logic', 2)],
                    ['label' => 'Adventure or fantasy stories', 'dimensions' => $dim('linguistic', 1, 'creative', 1)],
                    ['label' => 'Books about animals or nature', 'dimensions' => $dim('naturalist', 2)],
                    ['label' => 'Comics or graphic novels', 'dimensions' => $dim('creative', 1, 'linguistic', 1)],
                ],
            ],
            [
                'prompt' => 'When you imagine an invention you would build, it most looks like…',
                'options' => [
                    ['label' => 'A clever machine that solves a problem', 'dimensions' => $dim('cognitive_logic', 2, 'kinetic', 1)],
                    ['label' => 'A new game or toy', 'dimensions' => $dim('creative', 2)],
                    ['label' => 'A device that helps animals or plants', 'dimensions' => $dim('naturalist', 2)],
                    ['label' => 'A communication tool to help people', 'dimensions' => $dim('social', 1, 'linguistic', 1)],
                ],
            ],
            [
                'prompt' => 'You learn a new sport. What part is most fun?',
                'options' => [
                    ['label' => 'Figuring out the strategy', 'dimensions' => $dim('cognitive_logic', 2)],
                    ['label' => 'Moving fast and feeling strong', 'dimensions' => $dim('kinetic', 2)],
                    ['label' => 'Playing on a team with friends', 'dimensions' => $dim('social', 2)],
                    ['label' => 'Cheering for others', 'dimensions' => $dim('social', 1, 'kinetic', 1)],
                ],
            ],
            [
                'prompt' => 'You are given a blank notebook. You fill it with…',
                'options' => [
                    ['label' => 'A long story I am inventing', 'dimensions' => $dim('linguistic', 2, 'creative', 1)],
                    ['label' => 'Drawings or comics', 'dimensions' => $dim('creative', 2)],
                    ['label' => 'Maths puzzles and patterns', 'dimensions' => $dim('cognitive_logic', 2)],
                    ['label' => 'Plans for a club or event', 'dimensions' => $dim('social', 2)],
                ],
            ],
            [
                'prompt' => 'Music in your headphones is most often…',
                'options' => [
                    ['label' => 'Songs with great lyrics', 'dimensions' => $dim('linguistic', 1, 'creative', 1)],
                    ['label' => 'Fast beats I can move to', 'dimensions' => $dim('kinetic', 2)],
                    ['label' => 'Soundtracks from movies or games', 'dimensions' => $dim('creative', 1, 'cognitive_logic', 1)],
                    ['label' => 'Nature sounds or calm music', 'dimensions' => $dim('naturalist', 2)],
                ],
            ],
            [
                'prompt' => 'A grown-up asks for help. You feel best when you…',
                'options' => [
                    ['label' => 'Solve the puzzle for them', 'dimensions' => $dim('cognitive_logic', 2)],
                    ['label' => 'Cheer them on while they try', 'dimensions' => $dim('social', 2)],
                    ['label' => 'Help them make something', 'dimensions' => $dim('kinetic', 1, 'creative', 1)],
                    ['label' => 'Look up the answer in a book', 'dimensions' => $dim('linguistic', 1, 'cognitive_logic', 1)],
                ],
            ],
            [
                'prompt' => 'Your favourite kind of weekend trip is to a…',
                'options' => [
                    ['label' => 'Science museum or planetarium', 'dimensions' => $dim('cognitive_logic', 2)],
                    ['label' => 'Forest, mountain, or beach', 'dimensions' => $dim('naturalist', 2)],
                    ['label' => 'Library or bookshop', 'dimensions' => $dim('linguistic', 2)],
                    ['label' => 'Sports event or theme park', 'dimensions' => $dim('kinetic', 1, 'social', 1)],
                ],
            ],
            [
                'prompt' => 'When meeting someone new, you usually…',
                'options' => [
                    ['label' => 'Say hi first', 'dimensions' => $dim('social', 2)],
                    ['label' => 'Watch and listen before speaking', 'dimensions' => $dim('cognitive_logic', 1, 'social', 1)],
                    ['label' => 'Want to know what they like to do', 'dimensions' => $dim('social', 1, 'linguistic', 1)],
                    ['label' => 'Stick close to a friend I know', 'dimensions' => $dim('social', 1)],
                ],
            ],
            [
                'prompt' => 'When something goes wrong in a project, you…',
                'options' => [
                    ['label' => 'Investigate the cause carefully', 'dimensions' => $dim('cognitive_logic', 2)],
                    ['label' => 'Get creative about a workaround', 'dimensions' => $dim('creative', 2)],
                    ['label' => 'Ask team-mates for ideas', 'dimensions' => $dim('social', 2)],
                    ['label' => 'Take a break and reset', 'dimensions' => $dim('naturalist', 1, 'cognitive_logic', 1)],
                ],
            ],
            [
                'prompt' => 'You feel most proud after you…',
                'options' => [
                    ['label' => 'Crack a tricky problem', 'dimensions' => $dim('cognitive_logic', 2)],
                    ['label' => 'Help a friend feel better', 'dimensions' => $dim('social', 2)],
                    ['label' => 'Finish a beautiful drawing or song', 'dimensions' => $dim('creative', 2)],
                    ['label' => 'Run, climb, or finish a challenge', 'dimensions' => $dim('kinetic', 2)],
                ],
            ],
            [
                'prompt' => 'Words that describe a perfect afternoon are…',
                'options' => [
                    ['label' => 'Quiet, focused, thinking', 'dimensions' => $dim('cognitive_logic', 2)],
                    ['label' => 'Friends, laughing, together', 'dimensions' => $dim('social', 2)],
                    ['label' => 'Imagining, making, creating', 'dimensions' => $dim('creative', 2)],
                    ['label' => 'Outside, moving, exploring', 'dimensions' => $dim('kinetic', 1, 'naturalist', 1)],
                ],
            ],
            [
                'prompt' => 'If you could keep one kind of class, it would be…',
                'options' => [
                    ['label' => 'A coding or robotics class', 'dimensions' => $dim('cognitive_logic', 2, 'kinetic', 1)],
                    ['label' => 'A writing or theatre class', 'dimensions' => $dim('linguistic', 2)],
                    ['label' => 'An art studio class', 'dimensions' => $dim('creative', 2)],
                    ['label' => 'A nature or ecology class', 'dimensions' => $dim('naturalist', 2)],
                ],
            ],
            [
                'prompt' => 'A pet you would most enjoy caring for would be…',
                'options' => [
                    ['label' => 'A dog who needs walks and play', 'dimensions' => $dim('kinetic', 1, 'naturalist', 1)],
                    ['label' => 'A cat who likes to nap on books', 'dimensions' => $dim('linguistic', 1, 'naturalist', 1)],
                    ['label' => 'Fish in a careful tank', 'dimensions' => $dim('cognitive_logic', 1, 'naturalist', 1)],
                    ['label' => 'A rescue I could help train', 'dimensions' => $dim('social', 1, 'naturalist', 1)],
                ],
            ],
            [
                'prompt' => 'Maths feels best when…',
                'options' => [
                    ['label' => 'There is a clear right answer', 'dimensions' => $dim('cognitive_logic', 2)],
                    ['label' => 'I can apply it to a real thing', 'dimensions' => $dim('kinetic', 1, 'cognitive_logic', 1)],
                    ['label' => 'It is part of a game', 'dimensions' => $dim('social', 1, 'creative', 1)],
                    ['label' => 'I solve it next to a friend', 'dimensions' => $dim('social', 2)],
                ],
            ],
            [
                'prompt' => 'Public speaking feels…',
                'options' => [
                    ['label' => 'Fine — I have things to say', 'dimensions' => $dim('linguistic', 2, 'social', 1)],
                    ['label' => 'Easier if I bring drawings', 'dimensions' => $dim('creative', 1, 'linguistic', 1)],
                    ['label' => 'Easier if it is a small group', 'dimensions' => $dim('social', 1)],
                    ['label' => 'Hard but I push through', 'dimensions' => $dim('cognitive_logic', 1, 'social', 1)],
                ],
            ],
            [
                'prompt' => 'When learning something new, I prefer…',
                'options' => [
                    ['label' => 'Watching a clear explanation', 'dimensions' => $dim('cognitive_logic', 2)],
                    ['label' => 'Trying it with my hands first', 'dimensions' => $dim('kinetic', 2)],
                    ['label' => 'Reading and taking notes', 'dimensions' => $dim('linguistic', 2)],
                    ['label' => 'Doing it with a teacher beside me', 'dimensions' => $dim('social', 2)],
                ],
            ],
            [
                'prompt' => 'My favourite kind of weekend chore is…',
                'options' => [
                    ['label' => 'Tidying or organising a space', 'dimensions' => $dim('cognitive_logic', 1, 'naturalist', 1)],
                    ['label' => 'Cooking or baking', 'dimensions' => $dim('kinetic', 1, 'creative', 1)],
                    ['label' => 'Reading something while waiting', 'dimensions' => $dim('linguistic', 2)],
                    ['label' => 'Caring for plants or pets', 'dimensions' => $dim('naturalist', 2)],
                ],
            ],
            [
                'prompt' => 'Choose a hero kind of character:',
                'options' => [
                    ['label' => 'A clever inventor', 'dimensions' => $dim('cognitive_logic', 2)],
                    ['label' => 'A kind leader who unites people', 'dimensions' => $dim('social', 2)],
                    ['label' => 'A brave explorer of wild places', 'dimensions' => $dim('naturalist', 1, 'kinetic', 1)],
                    ['label' => 'A wise storyteller', 'dimensions' => $dim('linguistic', 2)],
                ],
            ],
            [
                'prompt' => 'You can pick one club. You pick…',
                'options' => [
                    ['label' => 'Robotics or maths', 'dimensions' => $dim('cognitive_logic', 2)],
                    ['label' => 'Theatre or debate', 'dimensions' => $dim('linguistic', 1, 'social', 1)],
                    ['label' => 'Art or music', 'dimensions' => $dim('creative', 2)],
                    ['label' => 'Outdoor sports', 'dimensions' => $dim('kinetic', 2)],
                ],
            ],
            [
                'prompt' => 'When stressed, what helps most?',
                'options' => [
                    ['label' => 'A quiet, organised space', 'dimensions' => $dim('cognitive_logic', 1)],
                    ['label' => 'Time outside', 'dimensions' => $dim('naturalist', 2)],
                    ['label' => 'Talking with a friend', 'dimensions' => $dim('social', 2)],
                    ['label' => 'Moving — running, dancing', 'dimensions' => $dim('kinetic', 2)],
                ],
            ],
            [
                'prompt' => 'You could plan one school trip. It is to…',
                'options' => [
                    ['label' => 'A robotics lab', 'dimensions' => $dim('cognitive_logic', 2)],
                    ['label' => 'A national park', 'dimensions' => $dim('naturalist', 2)],
                    ['label' => 'A theatre or concert', 'dimensions' => $dim('creative', 2)],
                    ['label' => 'A history museum', 'dimensions' => $dim('linguistic', 1, 'cognitive_logic', 1)],
                ],
            ],
            [
                'prompt' => 'A new game arrives. Your first move?',
                'options' => [
                    ['label' => 'Read every rule', 'dimensions' => $dim('linguistic', 1, 'cognitive_logic', 1)],
                    ['label' => 'Set it up and try', 'dimensions' => $dim('kinetic', 1, 'creative', 1)],
                    ['label' => 'Invite people over', 'dimensions' => $dim('social', 2)],
                    ['label' => 'Plan a strategy', 'dimensions' => $dim('cognitive_logic', 2)],
                ],
            ],
            [
                'prompt' => 'A grown-up calls you "brave". It is probably because you…',
                'options' => [
                    ['label' => 'Spoke up for someone', 'dimensions' => $dim('social', 1, 'linguistic', 1)],
                    ['label' => 'Tried something hard physically', 'dimensions' => $dim('kinetic', 2)],
                    ['label' => 'Asked a question others were scared to ask', 'dimensions' => $dim('cognitive_logic', 1, 'linguistic', 1)],
                    ['label' => 'Tried a new creative thing in public', 'dimensions' => $dim('creative', 2)],
                ],
            ],
            [
                'prompt' => 'Finally — pick the word that feels most you:',
                'options' => [
                    ['label' => 'Curious', 'dimensions' => $dim('cognitive_logic', 1, 'naturalist', 1)],
                    ['label' => 'Kind', 'dimensions' => $dim('social', 2)],
                    ['label' => 'Imaginative', 'dimensions' => $dim('creative', 2)],
                    ['label' => 'Energetic', 'dimensions' => $dim('kinetic', 2)],
                ],
            ],
        ];
    }

    private function dim(...$pairs): array
    {
        $out = [];
        for ($i = 0; $i < count($pairs); $i += 2) {
            $out[$pairs[$i]] = $pairs[$i + 1];
        }
        return $out;
    }
}
