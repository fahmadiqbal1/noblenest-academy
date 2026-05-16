<!-- markdownlint-disable MD013 -->
# Per-Age-Tier Learning Outcomes

Mapped to EYFS (UK), Reggio Emilia, and Montessori frameworks.

## Framework Mapping Key

| Code | Framework |
|---|---|
| EY | EYFS (Early Years Foundation Stage, UK DfE) |
| RE | Reggio Emilia |
| MT | Montessori |
| IB | IB Primary Years Programme |

---

## Tier: Infant (0–12 months)

| Outcome | Indicator | Framework |
|---|---|---|
| Responds to caregiver voice | Turns head toward familiar voice | EY-CL1, RE |
| Tracks moving objects | Eyes follow slow-moving stimulus | EY-PD1 |
| Parent-led tummy time | 3× daily per AAP guidance | EY-PD2, MT |

*Assessment:* Parent-reported milestone check-in form (no in-app activity required at this tier).

---

## Tier: Toddler (12 months – 3 years)

| Outcome | Indicator | Framework |
|---|---|---|
| Names 10+ objects in home language | Correctly labels image in quiz | EY-CL2, MT |
| Identifies 5 basic shapes | Shape-sort drag-and-match ≥ 80 % | EY-MATHS1 |
| Sings along to 2 nursery rhymes | Participates in song-and-movement | EY-EAD1, RE |
| Shows empathy | Selects correct emotion card in story | EY-PSED1 |
| Completes 3-step motor sequence | Tracing path without lifting | EY-PD3, MT |

---

## Tier: Pre-K / Preschool (3–5 years)

| Outcome | Indicator | Framework |
|---|---|---|
| Counts to 20 with 1:1 correspondence | Quiz score ≥ 70 % on number activities | EY-MATHS2, MT |
| Recognises all 26 letters | Letter tracing + sound-match activity | EY-LIT1 |
| Retells a 5-event story | Sequencing drag-and-match | EY-CL3, RE |
| Identifies 3 cultural traditions | Cultural module quiz | EY-UW1 |
| Draws a recognisable figure | Drawing-canvas peer review rubric | EY-EAD2, MT |

---

## Tier: Early Primary (5–7 years)

| Outcome | Indicator | Framework |
|---|---|---|
| Reads 30 CVC words | Phonics assessment score ≥ 75 % | EY-LIT2, IB |
| Adds & subtracts to 20 | Maths quiz ≥ 70 % | EY-MATHS3 |
| Writes 3-sentence journal entry | Writing activity rubric | EY-LIT3 |
| Builds a 5-step Blockly program | Sequencing module completion | IB-TD1 |
| Collaborates in virtual classroom | Peer comment + teacher observation | RE, EY-PSED2 |

---

## Tier: Upper Primary (7–10 years)

| Outcome | Indicator | Framework |
|---|---|---|
| Writes and runs Python Turtle program | Code-blocks activity completion | IB-TD2, KS2-C |
| Explains algorithmic thinking | Assessment quiz ≥ 80 % | KS2-C |
| Describes AI bias with real example | Written reflection (teacher marked) | IB-ATL3 |
| Demonstrates digital citizenship | Module quiz + scenario roleplay | IB-PYP, ISTE-2 |
| Applies cultural lens to tech ethics | Islamic/Scandinavian ethics reflection | IB-PYP |

---

## Assessment Battery Alignment (`AssessmentScoringService`)

The in-app `assessment` player administers the discovery battery. Scores feed `assessment_responses`:

| Battery section | Outcome measured | Scoring |
|---|---|---|
| Spatial reasoning | IQ-adjacent visual-spatial | Standardised z-score |
| Verbal analogies | Language + logical reasoning | Raw score → percentile |
| Working memory | Short-term memory capacity | Forward/backward span |
| Personality (Big-5 lite) | Social-emotional profile | Trait scores per factor |
