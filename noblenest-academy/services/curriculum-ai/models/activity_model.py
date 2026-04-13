"""
Pydantic models for activity generation.
Validates against the shared JSON schema: services/curriculum-ai/contracts/activity_payload.schema.json
"""

from typing import Optional, List
from pydantic import BaseModel, Field, field_validator

class ActivityAdaptations(BaseModel):
    """Real-time adaptations for difficulty tuning."""
    easier: str = Field(
        ...,
        min_length=10,
        max_length=200,
        description="How to make the activity easier for struggling children"
    )
    harder: str = Field(
        ...,
        min_length=10,
        max_length=200,
        description="How to make the activity harder for advanced children"
    )

class ActivityPayload(BaseModel):
    """
    Complete activity payload for Noble Nest Academy.
    All fields required. Validates against activity_payload.schema.json.
    """

    # Basic metadata
    title: str = Field(
        ...,
        min_length=3,
        max_length=80,
        description="Activity name, max 8 words"
    )
    description: str = Field(
        ...,
        min_length=10,
        max_length=300,
        description="1-2 sentence overview for parents"
    )
    instructions: str = Field(
        ...,
        min_length=20,
        max_length=1000,
        description="Step-by-step guide for the child (3-6 steps)"
    )
    materials: List[str] = Field(
        ...,
        max_items=15,
        description="List of materials needed"
    )
    duration_minutes: int = Field(
        ...,
        ge=5,
        le=60,
        description="Activity duration in minutes"
    )
    difficulty: str = Field(
        ...,
        description="Activity difficulty level"
    )
    age_tier: str = Field(
        ...,
        description="Target age tier (baby, toddler, preschool, school)"
    )
    subject: str = Field(
        ...,
        description="Primary subject area"
    )
    language: str = Field(
        ...,
        description="Language of instruction"
    )
    is_free: bool = Field(
        ...,
        description="Whether activity is available in free tier"
    )

    # Phase 2: Enhanced parental context fields
    mess_level: str = Field(
        ...,
        description="How messy/cleanup-intensive (low, medium, high)"
    )
    safety_warnings: List[str] = Field(
        default_factory=list,
        description="Array of safety warnings (choking_hazard, heat_source, etc.)"
    )
    adaptations: ActivityAdaptations = Field(
        ...,
        description="Real-time adaptations for difficulty tuning"
    )
    cognitive_domain: str = Field(
        ...,
        description="Primary cognitive domain (math, language, science, art, music, executive_function, emotional_regulation, social_emotional, physical_development)"
    )
    developmental_domains: List[str] = Field(
        ...,
        min_items=1,
        max_items=5,
        description="Secondary developmental domains"
    )
    materials_cost: int = Field(
        ...,
        ge=0,
        le=100,
        description="Estimated cost in USD cents"
    )
    parent_involvement: str = Field(
        ...,
        description="How much parent involvement (minimal, moderate, high)"
    )
    instructions_for_parent: str = Field(
        ...,
        min_length=20,
        max_length=500,
        description="Guidance for parents on how to facilitate"
    )

    @field_validator('difficulty')
    def validate_difficulty(cls, v):
        if v not in ['easy', 'medium', 'hard']:
            raise ValueError('difficulty must be easy, medium, or hard')
        return v

    @field_validator('age_tier')
    def validate_age_tier(cls, v):
        if v not in ['baby', 'toddler', 'preschool', 'school']:
            raise ValueError('age_tier must be baby, toddler, preschool, or school')
        return v

    @field_validator('subject')
    def validate_subject(cls, v):
        allowed = ['math', 'language', 'science', 'art', 'music', 'physical', 'social', 'cooking']
        if v not in allowed:
            raise ValueError(f'subject must be one of {allowed}')
        return v

    @field_validator('language')
    def validate_language(cls, v):
        allowed = ['english', 'french', 'spanish', 'russian', 'chinese', 'korean', 'urdu', 'arabic']
        if v not in allowed:
            raise ValueError(f'language must be one of {allowed}')
        return v

    @field_validator('mess_level')
    def validate_mess_level(cls, v):
        if v not in ['low', 'medium', 'high']:
            raise ValueError('mess_level must be low, medium, or high')
        return v

    @field_validator('parent_involvement')
    def validate_parent_involvement(cls, v):
        if v not in ['minimal', 'moderate', 'high']:
            raise ValueError('parent_involvement must be minimal, moderate, or high')
        return v

    @field_validator('cognitive_domain')
    def validate_cognitive_domain(cls, v):
        allowed = [
            'math', 'language', 'science', 'art', 'music',
            'executive_function', 'emotional_regulation', 'social_emotional', 'physical_development'
        ]
        if v not in allowed:
            raise ValueError(f'cognitive_domain must be one of {allowed}')
        return v

    @field_validator('developmental_domains')
    def validate_developmental_domains(cls, v):
        allowed = [
            'gross_motor', 'fine_motor', 'language', 'cognitive',
            'social_emotional', 'self_care', 'attention', 'memory', 'reasoning'
        ]
        for domain in v:
            if domain not in allowed:
                raise ValueError(f'developmental_domain {domain} must be one of {allowed}')
        return v

    def model_dump_json(self, **kwargs):
        """Convert to JSON, ensuring all fields are serialized."""
        return super().model_dump_json(**kwargs)

    class Config:
        """Pydantic config."""
        json_schema_extra = {
            "example": {
                "title": "Counting Stars",
                "description": "Count objects in the sky.",
                "instructions": "Step 1: Look up. Step 2: Count. Step 3: Tell me the number.",
                "materials": ["blanket", "night sky"],
                "duration_minutes": 15,
                "difficulty": "easy",
                "age_tier": "baby",
                "subject": "math",
                "language": "english",
                "is_free": True,
                "mess_level": "low",
                "safety_warnings": [],
                "adaptations": {
                    "easier": "Use fewer objects (1-3).",
                    "harder": "Count to higher numbers."
                },
                "cognitive_domain": "math",
                "developmental_domains": ["cognitive", "language"],
                "materials_cost": 0,
                "parent_involvement": "moderate",
                "instructions_for_parent": "Encourage your baby to point and repeat."
            }
        }
