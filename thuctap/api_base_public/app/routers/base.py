from fastapi import APIRouter
from fastapi import FastAPI, File, UploadFile, Header, HTTPException, Request, Form ,  Depends # noqa: E402, F401
from app.security.security import get_api_key
from app.models.base import Base
from chatbot.services.chatbot_api_agent import FilesChatAgent
from ingestion.ingestion  import Ingestion
import json, re
from app.config import settings
from pathlib import Path
from ggmap.crawl_api import crawl_places
import mysql.connector


# connect_db
def get_place_data_from_db(place_name: str):
    conn = mysql.connector.connect(
        host="localhost",
        user="root",      
        password="",       
        database="db_googlemap" 
    )
    cursor = conn.cursor(dictionary=True)

    query = "SELECT id, data_llm FROM locations WHERE name LIKE %s"
    cursor.execute(query, (place_name,))
    result = cursor.fetchone()

    cursor.close()
    conn.close()

    if not result:
        raise ValueError(f"Không tìm thấy dữ liệu cho địa điểm: {place_name}")

    return result["id"], result["data_llm"]
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
    input_folder = Path("demo") / "data_in"
    vector_folder = Path("demo") / "data_vector"

      # Đọc dữ liệu từ DB
    try:
        id, content = get_place_data_from_db(Place)
    except ValueError as e:
        raise HTTPException(status_code=404, detail=str(e))

    Ingestion(settings.LLM_NAME).ingestion_folder(
        path_input_folder=str(input_folder),
        path_vector_store=str(vector_folder),
    )
    try:
        chat = FilesChatAgent(str(vector_folder)) .get_workflow().compile().invoke(
            input={"question": Place}
        )
        response = chat["generation"]

        json_string = response.replace('```json', '').replace('```', '').strip()
        parsed = json.loads(json_string)

        # return Base(id="chatbot-response", data=json.dumps(parsed, ensure_ascii=False, indent=4))
        return Base(
            id=str(id),  # Trả về id của địa điểm
            data=json.dumps(parsed, ensure_ascii=False, indent=4),
        )
    
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
