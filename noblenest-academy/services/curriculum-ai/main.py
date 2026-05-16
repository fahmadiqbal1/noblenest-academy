"""
Noble Nest Academy — Curriculum AI Sidecar v2
AgentScope-powered agents for curriculum gap-filling.
Supports Grok (xAI) and Anthropic Claude only.

Start:
    uvicorn main:app --host 127.0.0.1 --port 8001 --workers 1
"""

from __future__ import annotations

from typing import Literal

from dotenv import load_dotenv
from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel

load_dotenv()

app = FastAPI(
    title="Noble Nest Curriculum AI",
    version="2.0.0",
    docs_url=None,
    redoc_url=None,
)

app.add_middleware(
    CORSMiddleware,
    allow_origins=["http://127.0.0.1", "http://localhost"],
    allow_methods=["GET", "POST"],
    allow_headers=["Content-Type"],
)


# ---------------------------------------------------------------------------
# Request / Response models
# ---------------------------------------------------------------------------

class GapItem(BaseModel):
    age_min: int
    age_max: int
    skill: str
    count: int = 1


class FillGapsRequest(BaseModel):
    gaps: list[GapItem]
    provider: Literal["grok", "anthropic"] = "anthropic"
    model: str | None = None
    api_key: str


class ActivityOut(BaseModel):
    title: str
    description: str
    instructions: str
    materials: list[str]
    duration_minutes: int
    difficulty: Literal["easy", "medium", "hard"]
    subject: str
    age_min: int
    age_max: int
    language: str
    is_free: bool


class FillGapsResponse(BaseModel):
    activities: list[ActivityOut]
    total_generated: int
    errors: list[str]


# Legacy models kept for backward compatibility
class GenerateActivityRequest(BaseModel):
    subject: str
    age_group: Literal["baby", "preschool", "school"]
    language: str = "en"
    is_free: bool = True
    count: int = 1


class GenerateActivityResponse(BaseModel):
    activities: list[dict]
    model: str
    usage_tokens: int


# ---------------------------------------------------------------------------
# Routes
# ---------------------------------------------------------------------------

@app.get("/health")
async def health():
    """Laravel calls this to confirm the sidecar is running."""
    return {"status": "ok", "service": "curriculum-ai", "version": "2.0.0"}


@app.post("/fill-gaps", response_model=FillGapsResponse)
async def fill_gaps(req: FillGapsRequest):
    """
    Auto-fill curriculum gaps using AgentScope agents.
    Accepts a list of gaps (skill + age range + count) and generates activities.
    Called by Laravel's OrchestratorController::fillGaps().
    """
    from agents.curriculum_agent import generate_for_gap

    activities: list[ActivityOut] = []
    errors: list[str] = []

    for gap in req.gaps:
        for _ in range(max(1, gap.count)):
            try:
                result = await generate_for_gap(
                    skill=gap.skill,
                    age_min=gap.age_min,
                    age_max=gap.age_max,
                    provider=req.provider,
                    model=req.model,
                    api_key=req.api_key,
                )
                activities.append(ActivityOut(**result))
            except Exception as exc:
                errors.append(
                    f"Gap '{gap.skill}' ages {gap.age_min}–{gap.age_max}: {exc}"
                )

    return FillGapsResponse(
        activities=activities,
        total_generated=len(activities),
        errors=errors,
    )


@app.post("/generate-activity", response_model=GenerateActivityResponse)
async def generate_activity_endpoint(req: GenerateActivityRequest):
    """Legacy endpoint — kept for backward compatibility."""
    if req.count < 1 or req.count > 20:
        raise HTTPException(400, "count must be between 1 and 20")

    try:
        from chains.activity_chain import generate_batch
        result = await generate_batch(
            subject=req.subject,
            age_group=req.age_group,
            language=req.language,
            is_free=req.is_free,
            count=req.count,
        )
        return result
    except Exception as exc:
        raise HTTPException(500, f"Generation failed: {exc}") from exc
