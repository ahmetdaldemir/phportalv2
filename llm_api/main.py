"""
FastAPI LLM Service for PHPortal Project
This service acts as a bridge between Laravel and Ollama LLM
"""
from fastapi import FastAPI, HTTPException, BackgroundTasks
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel, Field
from typing import Optional, List, Dict, Any
import httpx
import logging
from datetime import datetime
import json

# Logging configuration
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

app = FastAPI(
    title="PHPortal LLM API",
    description="LLM Integration Service for PHPortal Laravel Application",
    version="1.0.0"
)

# CORS configuration for Laravel integration
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # Configure this in production
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Configuration
OLLAMA_BASE_URL = "http://localhost:11434"
DEFAULT_MODEL = "phportal-assistant"

# Request Models
class CodeAnalysisRequest(BaseModel):
    code: str = Field(..., description="Code to analyze")
    file_path: Optional[str] = Field(None, description="File path for context")
    context: Optional[Dict[str, Any]] = Field(None, description="Additional context")
    model: Optional[str] = Field(DEFAULT_MODEL, description="Model to use")

class BugFixRequest(BaseModel):
    code: str = Field(..., description="Code with bug")
    error_message: Optional[str] = Field(None, description="Error message")
    file_path: Optional[str] = Field(None, description="File path")
    context: Optional[Dict[str, Any]] = Field(None, description="Additional context")
    model: Optional[str] = Field(DEFAULT_MODEL, description="Model to use")

class ProjectLearningRequest(BaseModel):
    project_structure: Dict[str, Any] = Field(..., description="Project structure")
    code_samples: Optional[List[Dict[str, str]]] = Field(None, description="Code samples")
    documentation: Optional[str] = Field(None, description="Project documentation")
    model: Optional[str] = Field(DEFAULT_MODEL, description="Model to use")

class ChatRequest(BaseModel):
    message: str = Field(..., description="User message")
    conversation_history: Optional[List[Dict[str, str]]] = Field([], description="Conversation history")
    model: Optional[str] = Field(DEFAULT_MODEL, description="Model to use")

# Response Models
class LLMResponse(BaseModel):
    success: bool
    response: Optional[str] = None
    suggestions: Optional[List[str]] = None
    fixed_code: Optional[str] = None
    error: Optional[str] = None
    timestamp: str = Field(default_factory=lambda: datetime.now().isoformat())

# Ollama Client
async def call_ollama(model: str, prompt: str, system_prompt: Optional[str] = None) -> str:
    """Call Ollama API"""
    try:
        async with httpx.AsyncClient(timeout=300.0) as client:
            payload = {
                "model": model,
                "prompt": prompt,
                "stream": False
            }
            
            if system_prompt:
                payload["system"] = system_prompt
            
            response = await client.post(
                f"{OLLAMA_BASE_URL}/api/generate",
                json=payload
            )
            response.raise_for_status()
            result = response.json()
            return result.get("response", "")
    except httpx.HTTPError as e:
        logger.error(f"Ollama API error: {str(e)}")
        raise HTTPException(status_code=500, detail=f"Ollama API error: {str(e)}")
    except Exception as e:
        logger.error(f"Unexpected error: {str(e)}")
        raise HTTPException(status_code=500, detail=f"Unexpected error: {str(e)}")

# Endpoints
@app.get("/")
async def root():
    """Health check endpoint"""
    return {
        "status": "running",
        "service": "PHPortal LLM API",
        "version": "1.0.0",
        "timestamp": datetime.now().isoformat()
    }

@app.get("/health")
async def health_check():
    """Detailed health check"""
    try:
        async with httpx.AsyncClient(timeout=10.0) as client:
            response = await client.get(f"{OLLAMA_BASE_URL}/api/tags")
            ollama_status = "connected" if response.status_code == 200 else "disconnected"
    except:
        ollama_status = "disconnected"
    
    return {
        "status": "healthy",
        "ollama_status": ollama_status,
        "timestamp": datetime.now().isoformat()
    }

@app.post("/api/analyze-code", response_model=LLMResponse)
async def analyze_code(request: CodeAnalysisRequest):
    """Analyze code and provide suggestions"""
    try:
        context_info = f"\nFile: {request.file_path}" if request.file_path else ""
        if request.context:
            context_info += f"\nContext: {json.dumps(request.context, indent=2)}"
        
        prompt = f"""Analyze the following PHP/Laravel code and provide suggestions for improvement:

{context_info}

Code:
```php
{request.code}
```

Please provide:
1. Code quality assessment
2. Potential bugs or issues
3. Security concerns
4. Performance optimization suggestions
5. Best practices recommendations
"""
        
        response = await call_ollama(request.model, prompt)
        
        return LLMResponse(
            success=True,
            response=response,
            suggestions=[line.strip() for line in response.split('\n') if line.strip()]
        )
    except Exception as e:
        logger.error(f"Code analysis error: {str(e)}")
        return LLMResponse(success=False, error=str(e))

@app.post("/api/fix-bug", response_model=LLMResponse)
async def fix_bug(request: BugFixRequest):
    """Fix bugs in code"""
    try:
        context_info = f"\nFile: {request.file_path}" if request.file_path else ""
        error_info = f"\nError: {request.error_message}" if request.error_message else ""
        if request.context:
            context_info += f"\nContext: {json.dumps(request.context, indent=2)}"
        
        prompt = f"""Fix the following bug in this PHP/Laravel code:

{context_info}
{error_info}

Code with bug:
```php
{request.code}
```

Please provide:
1. Explanation of the bug
2. Fixed code
3. Why this fix works
4. Additional recommendations

Format the fixed code between ```php and ``` tags.
"""
        
        response = await call_ollama(request.model, prompt)
        
        # Extract fixed code from response
        fixed_code = None
        if "```php" in response:
            parts = response.split("```php")
            if len(parts) > 1:
                fixed_code = parts[1].split("```")[0].strip()
        
        return LLMResponse(
            success=True,
            response=response,
            fixed_code=fixed_code
        )
    except Exception as e:
        logger.error(f"Bug fix error: {str(e)}")
        return LLMResponse(success=False, error=str(e))

@app.post("/api/learn-project")
async def learn_project(request: ProjectLearningRequest, background_tasks: BackgroundTasks):
    """Learn about the project structure and patterns"""
    try:
        prompt = f"""Learn about this Laravel project:

Project Structure:
{json.dumps(request.project_structure, indent=2)}

"""
        if request.code_samples:
            prompt += "\nCode Samples:\n"
            for sample in request.code_samples:
                prompt += f"\n{sample.get('path', 'Unknown')}:\n```php\n{sample.get('code', '')}\n```\n"
        
        if request.documentation:
            prompt += f"\nDocumentation:\n{request.documentation}\n"
        
        prompt += """
Please analyze this project and remember:
1. Architecture patterns used
2. Coding conventions
3. Common patterns in controllers, models, services
4. Database structure
5. Key dependencies and integrations

This information will be used to provide better assistance for this specific project.
"""
        
        response = await call_ollama(request.model, prompt)
        
        return {
            "success": True,
            "message": "Project learning completed",
            "response": response
        }
    except Exception as e:
        logger.error(f"Project learning error: {str(e)}")
        return {"success": False, "error": str(e)}

@app.post("/api/chat", response_model=LLMResponse)
async def chat(request: ChatRequest):
    """General chat endpoint"""
    try:
        # Build conversation context
        conversation = ""
        if request.conversation_history:
            for msg in request.conversation_history[-5:]:  # Last 5 messages
                role = msg.get("role", "user")
                content = msg.get("content", "")
                conversation += f"{role}: {content}\n\n"
        
        prompt = f"""{conversation}user: {request.message}

assistant: """
        
        response = await call_ollama(request.model, prompt)
        
        return LLMResponse(
            success=True,
            response=response
        )
    except Exception as e:
        logger.error(f"Chat error: {str(e)}")
        return LLMResponse(success=False, error=str(e))

@app.get("/api/models")
async def list_models():
    """List available models"""
    try:
        async with httpx.AsyncClient(timeout=10.0) as client:
            response = await client.get(f"{OLLAMA_BASE_URL}/api/tags")
            response.raise_for_status()
            return response.json()
    except Exception as e:
        logger.error(f"List models error: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8000)

