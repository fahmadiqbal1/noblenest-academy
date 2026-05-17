<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use JsonSchema\Validator;
use JsonSchema\SchemaStorage;
use JsonSchema\Constraints\Constraint;

class ActivityPayloadContractTest extends TestCase
{
    protected static $schema;
    protected Validator $validator;

    public static function setUpBeforeClass(): void
    {
        $schemaPath = __DIR__ . '/../../../services/curriculum-ai/contracts/activity_payload.schema.json';
        self::$schema = json_decode(file_get_contents($schemaPath));
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new Validator();
    }

    /**
     * Valid payload that meets all requirements.
     */
    public function test_valid_payload_passes_schema()
    {
        $payload = (object) [
            'title' => 'Counting Stars',
            'description' => 'Count objects in the sky.',
            'instructions' => 'Step 1: Look up. Step 2: Count. Step 3: Tell me the number.',
            'materials' => ['blanket', 'night sky or glow stars'],
            'duration_minutes' => 15,
            'difficulty' => 'easy',
            'age_tier' => 'baby',
            'subject' => 'math',
            'language' => 'english',
            'is_free' => true,
            'mess_level' => 'low',
            'safety_warnings' => [],
            'adaptations' => [
                'easier' => 'Use fewer objects (1-3).',
                'harder' => 'Count to higher numbers or use 2D images.',
            ],
            'cognitive_domain' => 'math',
            'developmental_domains' => ['cognitive', 'language'],
            'materials_cost' => 0,
            'parent_involvement' => 'moderate',
            'instructions_for_parent' => 'Encourage your baby to point and repeat numbers.',
        ];

        $this->validator->validate(json_decode(json_encode($payload)), self::$schema, Constraint::CHECK_MODE_VALIDATE_SCHEMA);
        $this->assertTrue($this->validator->isValid(), json_encode($this->validator->getErrors()));
    }

    /**
     * Missing required field.
     */
    public function test_missing_required_field_fails()
    {
        $payload = (object) [
            'title' => 'Counting Stars',
            // missing description
            'instructions' => 'Step 1...',
            'materials' => [],
            'duration_minutes' => 15,
            'difficulty' => 'easy',
            'age_tier' => 'baby',
            'subject' => 'math',
            'language' => 'english',
            'is_free' => true,
            'mess_level' => 'low',
            'safety_warnings' => [],
            'adaptations' => ['easier' => 'x', 'harder' => 'y'],
            'cognitive_domain' => 'math',
            'developmental_domains' => ['cognitive'],
            'materials_cost' => 0,
            'parent_involvement' => 'moderate',
            'instructions_for_parent' => 'Help your child.',
        ];

        $this->validator->validate(json_decode(json_encode($payload)), self::$schema);
        $this->assertFalse($this->validator->isValid());
    }

    /**
     * Invalid enum value.
     */
    public function test_invalid_enum_value_fails()
    {
        $payload = (object) [
            'title' => 'Counting Stars',
            'description' => 'Count objects.',
            'instructions' => 'Step 1...',
            'materials' => [],
            'duration_minutes' => 15,
            'difficulty' => 'invalid-difficulty', // NOT in enum
            'age_tier' => 'baby',
            'subject' => 'math',
            'language' => 'english',
            'is_free' => true,
            'mess_level' => 'low',
            'safety_warnings' => [],
            'adaptations' => ['easier' => 'x', 'harder' => 'y'],
            'cognitive_domain' => 'math',
            'developmental_domains' => ['cognitive'],
            'materials_cost' => 0,
            'parent_involvement' => 'moderate',
            'instructions_for_parent' => 'Help.',
        ];

        $this->validator->validate(json_decode(json_encode($payload)), self::$schema);
        $this->assertFalse($this->validator->isValid());
    }

    /**
     * Out-of-range numeric value.
     */
    public function test_duration_out_of_range_fails()
    {
        $payload = (object) [
            'title' => 'Counting Stars',
            'description' => 'Count objects.',
            'instructions' => 'Step 1...',
            'materials' => [],
            'duration_minutes' => 999, // exceeds maximum of 60
            'difficulty' => 'easy',
            'age_tier' => 'baby',
            'subject' => 'math',
            'language' => 'english',
            'is_free' => true,
            'mess_level' => 'low',
            'safety_warnings' => [],
            'adaptations' => ['easier' => 'x', 'harder' => 'y'],
            'cognitive_domain' => 'math',
            'developmental_domains' => ['cognitive'],
            'materials_cost' => 0,
            'parent_involvement' => 'moderate',
            'instructions_for_parent' => 'Help.',
        ];

        $this->validator->validate(json_decode(json_encode($payload)), self::$schema);
        $this->assertFalse($this->validator->isValid());
    }

    /**
     * Additional properties not allowed.
     */
    public function test_unexpected_property_fails()
    {
        $payload = (object) [
            'title' => 'Counting Stars',
            'description' => 'Count objects.',
            'instructions' => 'Step 1...',
            'materials' => [],
            'duration_minutes' => 15,
            'difficulty' => 'easy',
            'age_tier' => 'baby',
            'subject' => 'math',
            'language' => 'english',
            'is_free' => true,
            'mess_level' => 'low',
            'safety_warnings' => [],
            'adaptations' => ['easier' => 'x', 'harder' => 'y'],
            'cognitive_domain' => 'math',
            'developmental_domains' => ['cognitive'],
            'materials_cost' => 0,
            'parent_involvement' => 'moderate',
            'instructions_for_parent' => 'Help.',
            'unexpected_field' => 'should fail', // NOT in schema
        ];

        $this->validator->validate(json_decode(json_encode($payload)), self::$schema);
        $this->assertFalse($this->validator->isValid());
    }
}
