<!-- markdownlint-disable MD013 -->
# STEM Track Outline — Ages 7–10

The 7–10 track transitions from play-first exploration to structured learning, with STEM as the backbone and cultural literacy as the connective tissue.

## Track Architecture

```
Year 1 (age 7–8)
  └── Computational Thinking Foundations
        Blockly visual programming, sequencing, loops, conditionals

Year 2 (age 8–9)
  └── Python Lite
        Turtle graphics, variables, simple I/O via the in-browser Python player

Year 3 (age 9–10)
  └── AI Literacy + Data
        What is training data? Bias in datasets. Simple classification demo.
        Cross-curricular: Islamic ethics of technology, Scandinavian digital citizenship.
```

## Modules

### Computational Thinking (age 7–8)
| Module | Delivery | Outcomes |
|---|---|---|
| Sequencing with Blockly | code-blocks player | Builds 5-step programs; understands order matters |
| Loops | code-blocks player | Repeat loops, nested loops |
| Conditionals | code-blocks player | if/else logic; Boolean reasoning |
| Debugging | guided-steps player | Spot-the-bug challenges |

### Python Lite (age 8–9)
| Module | Delivery | Outcomes |
|---|---|---|
| Turtle Graphics | video-lesson + code-blocks | Draw shapes programmatically |
| Variables & Types | quiz + code-blocks | String, int, float; type conversion |
| Functions | guided-steps | Decomposition, reuse |
| Mini-project: Islamic Geometry | drawing-canvas + code-blocks | Cross-curricular art + maths |

### AI Literacy (age 9–10)
| Module | Delivery | Outcomes |
|---|---|---|
| What is a model? | video-lesson | Supervised learning intuition |
| Training data & bias | quiz + discussion prompt | Critical thinking about AI fairness |
| Image classifier demo | assessment + video | Hands-on: train a 3-class Teachable Machine |
| AI & society | guided-steps | Islamic, Scandinavian, and global perspectives |

## Assessment
- Unit assessments: 10-question quiz (70 % pass threshold).
- Project reviews: teacher-reviewed artefact (Blockly export or Python script).
- Discovery battery: IQ-adjacent reasoning tasks administered via `assessment` player at year-end.
- Results stored in `assessment_responses` table; scored by `AssessmentScoringService`.

## Alignment
- UK KS2 Computing curriculum (sequencing, algorithms, data representation).
- Pakistan NECTA Digital Literacy strand (ICT basics, digital citizenship).
- ISTE Student Standards 1–4 (Empowered Learner, Digital Citizen, Knowledge Constructor, Innovative Designer).
