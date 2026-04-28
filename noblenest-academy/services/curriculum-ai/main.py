"""
Noble Nest Academy — Curriculum AI Sidecar
FastAPI service that generates curriculum activities using LangChain + Anthropic.
Integrates with PageIndex for coherence checking (no duplicate activities).

Start with:
    uvicorn main:app --host 127.0.0.1 --port 8001 --workers 2
"""

from __future__ import annotations

from typing import Literal

from dotenv import load_dotenv
from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel

from chains.activity_chain import generate_batch

load_dotenv()

app = FastAPI(
    title="Noble Nest Curriculum AI",
    version="1.0.0",
    docs_url=None,   # Disable Swagger in production
    redoc_url=None,
)

# Only accept requests from localhost (Laravel calls this internally)
app.add_middleware(
    CORSMiddleware,
    allow_origins=["http://127.0.0.1", "http://localhost"],
    allow_methods=["POST"],
    allow_headers=["Content-Type"],
)


# ---------------------------------------------------------------------------
# Request / Response models
# ---------------------------------------------------------------------------

class GenerateActivityRequest(BaseModel):
    subject: str
    age_group: Literal["baby", "preschool", "school"]
    language: str = "en"
    is_free: bool = True
    count: int = 1          # How many activities to generate (1–20)


class ActivityOut(BaseModel):
    title: str
    description: str
    instructions: str
    materials: list[str]
    duration_minutes: int
    difficulty: Literal["easy", "medium", "hard"]
    age_group: str
    subject: str
    language: str
    is_free: bool


class GenerateActivityResponse(BaseModel):
    activities: list[ActivityOut]
    model: str
    usage_tokens: int


# ---------------------------------------------------------------------------
# Routes
# ---------------------------------------------------------------------------

@app.get("/health")
async def health():
    """Laravel calls this to confirm the sidecar is running."""
    return {"status": "ok", "service": "curriculum-ai"}


@app.post("/generate-activity", response_model=GenerateActivityResponse)
async def generate_activity_endpoint(req: GenerateActivityRequest):
    """
    Generate one or more curriculum activities.
    Called by Laravel's AIAssistantService.
    """
    if req.count < 1 or req.count > 20:
        raise HTTPException(400, "count must be between 1 and 20")

    try:
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
