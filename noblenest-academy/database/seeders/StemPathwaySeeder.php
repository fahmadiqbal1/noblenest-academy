<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\ActivityStep;
use Illuminate\Database\Seeder;

/**
 * Phase 3 — STEM 7–10 starter activities.
 *
 * Three tracks tagged via subject:
 *   - coding      → Blockly (block-based programming, ages 7-10)
 *   - coding      → Brython (Python in the browser, ages 8-10)
 *   - technology  → TF.js / AI literacy demos (ages 9-10)
 *
 * Each activity ships with 3 ActivityStep rows. The Blockly lessons are
 * rendered inline via the `code-blocks` player (Phase 2). Brython + TF.js
 * use the `guided-steps` player today; dedicated players are a follow-up.
 */
class StemPathwaySeeder extends Seeder
{
    public function run(): void
    {
        $tracks = array_merge(
            $this->blocklyLessons(),
            $this->brythonLessons(),
            $this->tfjsLessons(),
        );

        foreach ($tracks as $row) {
            $steps = $row['steps'] ?? [];
            unset($row['steps']);
            $row += [
                'language' => 'en',
                'is_free'  => true,
                'duration_minutes' => 15,
                'emoji'    => '⚙️',
            ];

            $activity = Activity::updateOrCreate(
                ['title' => $row['title'], 'subject' => $row['subject'], 'age_min' => $row['age_min']],
                $row
            );

            foreach ($steps as $n => $step) {
                ActivityStep::updateOrCreate(
                    ['activity_id' => $activity->id, 'step_number' => $n + 1],
                    ['title' => $step[0], 'instruction' => $step[1]]
                );
            }
        }
    }

    private function blocklyLessons(): array
    {
        return [
            [
                'title' => 'Your first program (Blockly)',
                'description' => 'Drag a "print" block, type your name, and run it.',
                'age_min' => 84, 'age_max' => 120,
                'subject' => 'coding', 'activity_type' => 'code',
                'emoji' => '👋',
                'benefit_explanation' => 'First experience of telling a computer what to do; immediate visible result.',
                'steps' => [
                    ['Open the toolbox', 'Find the "print" block in the toolbox on the left.'],
                    ['Drag into workspace', 'Drag it onto the workspace.'],
                    ['Type + run', 'Type your name in the text block, then press Run.'],
                ],
            ],
            [
                'title' => 'Maths blocks (Blockly)',
                'description' => 'Use the maths blocks to add, subtract, multiply, and print the result.',
                'age_min' => 84, 'age_max' => 120,
                'subject' => 'coding', 'activity_type' => 'code',
                'emoji' => '➗',
                'benefit_explanation' => 'Connects number sense to computational thinking.',
                'steps' => [
                    ['Find maths blocks', 'Find the "number" and "math operation" blocks.'],
                    ['Build an expression', 'Connect: print( 3 + 4 ). Run it.'],
                    ['Change the operator', 'Swap + for × and re-run. Note how the answer changes.'],
                ],
            ],
            [
                'title' => 'Repeat with a loop (Blockly)',
                'description' => 'Use a "repeat N times" block to draw a row of stars.',
                'age_min' => 84, 'age_max' => 132,
                'subject' => 'coding', 'activity_type' => 'code',
                'emoji' => '🔁',
                'benefit_explanation' => 'Introduces iteration — the building block of automation.',
                'steps' => [
                    ['Find the loop block', 'Drag the "repeat 10 times" loop into the workspace.'],
                    ['Put print inside', 'Put a print("⭐") block inside the loop.'],
                    ['Run + tweak', 'Run, then change 10 to 25 and run again.'],
                ],
            ],
            [
                'title' => 'If / Else (Blockly)',
                'description' => 'Use an if-else block to print "even" or "odd" for a number.',
                'age_min' => 96, 'age_max' => 132,
                'subject' => 'coding', 'activity_type' => 'code',
                'emoji' => '🔀',
                'benefit_explanation' => 'Conditional thinking; first decision tree.',
                'steps' => [
                    ['Drag if-else', 'Drag the if-else block into the workspace.'],
                    ['Add the test', 'Set the test to: (number mod 2) = 0.'],
                    ['Print branches', 'Print "even" in one branch, "odd" in the other. Run with a few numbers.'],
                ],
            ],
            [
                'title' => 'Make a variable (Blockly)',
                'description' => 'Create a variable, change its value, and print it.',
                'age_min' => 96, 'age_max' => 132,
                'subject' => 'coding', 'activity_type' => 'code',
                'emoji' => '📦',
                'benefit_explanation' => 'Concept of named storage that holds different values over time.',
                'steps' => [
                    ['Create a variable', 'Use the Variables menu; name it "score".'],
                    ['Set + print', 'Set score to 0; print it.'],
                    ['Change + print again', 'Set score to score + 5; print it again.'],
                ],
            ],
        ];
    }

    private function brythonLessons(): array
    {
        return [
            [
                'title' => 'Print in Python (Brython)',
                'description' => 'Write a Python `print()` line and run it in the browser.',
                'age_min' => 96, 'age_max' => 132,
                'subject' => 'coding', 'activity_type' => 'code',
                'emoji' => '🐍',
                'benefit_explanation' => 'Transition from blocks to real Python syntax.',
                'steps' => [
                    ['Open the editor', 'A Python editor loads inside this lesson.'],
                    ['Type', 'Type: print("Hello, world!")'],
                    ['Run', 'Press Run; see the output appear below.'],
                ],
            ],
            [
                'title' => 'Python variables',
                'description' => 'Create a variable, assign a value, print it.',
                'age_min' => 96, 'age_max' => 132,
                'subject' => 'coding', 'activity_type' => 'code',
                'emoji' => '🔤',
                'benefit_explanation' => 'Foundations of any program: storing and reusing values.',
                'steps' => [
                    ['Name your variable', 'Type: name = "Ada"'],
                    ['Print it', 'Type: print(name)'],
                    ['Change + print', 'Set name = "Grace" and print again.'],
                ],
            ],
            [
                'title' => 'Python for-loop',
                'description' => 'Write a for-loop that counts from 1 to 5.',
                'age_min' => 108, 'age_max' => 132,
                'subject' => 'coding', 'activity_type' => 'code',
                'emoji' => '🔢',
                'benefit_explanation' => 'Iteration in actual code, not blocks.',
                'steps' => [
                    ['Type the loop', 'Type: for i in range(1, 6): print(i)'],
                    ['Run', 'Run it and watch the numbers appear.'],
                    ['Modify the range', 'Change range to (10, 21) and re-run.'],
                ],
            ],
        ];
    }

    private function tfjsLessons(): array
    {
        return [
            [
                'title' => 'How a computer sees a cat',
                'description' => 'Run a tiny image classifier in the browser and watch it label photos.',
                'age_min' => 108, 'age_max' => 132,
                'subject' => 'technology', 'activity_type' => 'experiment',
                'emoji' => '🐈',
                'benefit_explanation' => 'AI literacy: ML models give probabilities, not certainties.',
                'steps' => [
                    ['Load the demo', 'A small TF.js model loads (MobileNet, ~25 MB).'],
                    ['Try 3 images', 'Click each picture; read the top 3 guesses + confidence.'],
                    ['Try a tricky one', 'Try a photo where the model is unsure. Discuss why.'],
                ],
            ],
            [
                'title' => 'Teach a model with your camera',
                'description' => 'Use Teachable Machine in your browser to train a 3-class classifier.',
                'age_min' => 108, 'age_max' => 132,
                'subject' => 'technology', 'activity_type' => 'experiment',
                'emoji' => '🤖',
                'benefit_explanation' => 'Hands-on: collect data → train → test. Sees ML as data-shaped.',
                'steps' => [
                    ['Set up 3 classes', 'Label 3 classes (e.g. hand-up, thumbs-up, peace).'],
                    ['Capture examples', 'Hold each pose; capture ~30 examples per class.'],
                    ['Train + test', 'Train; then test live with your webcam. Reflect on errors.'],
                ],
            ],
        ];
    }
}
