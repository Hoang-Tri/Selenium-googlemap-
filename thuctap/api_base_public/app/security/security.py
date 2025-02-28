from fastapi import Security  # noqa: E402
from fastapi import FastAPI, File, UploadFile, Header, HTTPException, Request, Form  # noqa: E402, F401
from fastapi.security import APIKeyHeader  # noqa: E402

from app.config import settings

# Định nghĩa header cho API key
api_key_header = APIKeyHeader(name="API_Key", auto_error=False)


async def get_api_key(api_key_header: str = Security(api_key_header)):
    print("Received API Key:", api_key_header)  # In ra API Key nhận được
    print("Expected API Key:", settings.API_KEY)  # In API Key từ .env
    if api_key_header == settings.API_KEY:
        return api_key_header
    raise HTTPException(status_code=403, detail="Could not validate API Key")



get_api_key = Security(get_api_key)
