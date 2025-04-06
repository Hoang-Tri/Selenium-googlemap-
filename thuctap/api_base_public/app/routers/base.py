from fastapi import APIRouter
from fastapi import FastAPI, File, UploadFile, Header, HTTPException, Request, Form ,  Depends # noqa: E402, F401
from app.security.security import get_api_key
from app.models.base import Base
from chatbot.services.chatbot_api_agent import FilesChatAgent
from ingestion.ingestion  import Ingestion
import json, re
from app.config import settings
from ggmap.crawl_api import crawl_places
# Tạo router cho người dùng
router = APIRouter(prefix="/base", tags=["base"])


@router.post("/base-url/", response_model=Base)
async def base_url(
    api_key: str = get_api_key,  # Khóa API để xác thực
    base_data: str = Form(""),
):

    return Base(id = "gnqAYAVeDMR7dzocBfH5j89O4oXUPpEa", data=base_data)

@router.post("/chat-bot/", response_model=Base)
async def chat_bot(
        api_key: str = get_api_key,  # Khóa API để xác thực
        Comment: str = Form(""),
        Place: str = Form(""),

):
    try:
        prompt = Place + "\n\n" + Comment
        # Khởi tạo chatbot với dữ liệu vector đã lưu
        chat = FilesChatAgent().get_workflow().compile().invoke(
            input={"question": prompt}
        )

        # Lấy kết quả chatbot sinh ra
        response = chat["generation"]
        return Base(id="chatbot-response", data=response)
    
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Chatbot error: {str(e)}")


#tạo router cho chat place
@router.post("/chat-ingestion/", response_model=Base)
async def chat_ingestion(
    api_key: str = get_api_key,
    Place: str = Form(...)
):
    # Ingestion(settings.LLM_NAME).ingestion_folder(
    #     path_input_folder="demo\data_in",
    #     path_vector_store="demo\data_vector",
    # )
    try:
        chat = FilesChatAgent("demo\data_vector").get_workflow().compile().invoke(
            input={"question": Place}
        )
        response = chat["generation"]

        return Base(id="chatbot-response", data=response)

    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Chatbot error: {str(e)}")
    

#tạo api crawl data
@router.post("/start/", response_model=Base)
async def start_crawl(
    api_key: str = get_api_key,
    keywords: str = Form(...)
):
    print(f"Từ khóa nhận được: {keywords}")
    """API để crawl dữ liệu từ danh sách từ khóa"""
    try:
        results = crawl_places(keywords)

        return Base(id="crawl-response", data=json.dumps(results))

    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Lỗi khi crawl dữ liệu: {str(e)}")
