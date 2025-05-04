from fastapi import APIRouter
from fastapi import FastAPI, File, UploadFile, Header, HTTPException, Request, Form ,  Depends # noqa: E402, F401
from app.security.security import get_api_key
from app.models.base import Base
from chatbot.services.chatbot_api_agent import FilesChatAgent
from ingestion.ingestion  import Ingestion
import json, re
from pathlib import Path
from ggmap.crawl_api import crawl_places
import mysql.connector

# from app.config import settings
from app.ai_config import settings
import os


# connect_db
def get_place_data_from_db(place_name: str):
    host = "localhost"
        
    # Nếu chạy trong Docker, dùng host.docker.internal để kết nối ra ngoài
    if os.getenv("IN_DOCKER") == "true":
        host = "host.docker.internal"
    conn = mysql.connector.connect(
        host=host,
        user="root",      
        password="",       
        database="db_googlemap" 
    )
    cursor = conn.cursor(dictionary=True)

    query = "SELECT id, data_llm FROM locations WHERE name = %s"
    cursor.execute(query, (place_name,))
    result = cursor.fetchone()

    cursor.close()
    conn.close()

    if not result:
        raise ValueError(f"Không tìm thấy dữ liệu cho địa điểm: {place_name}")

    return result["id"], result["data_llm"]

# Tạo router cho người dùng
router = APIRouter(prefix="/base", tags=["base"])

# @router.post("/base-url/", response_model=Base)
# async def base_url(
#     api_key: str = get_api_key,  # Khóa API để xác thực
#     base_data: str = Form(""),
# ):

#     return Base(id = "gnqAYAVeDMR7dzocBfH5j89O4oXUPpEa", data=base_data)

# @router.post("/chat-bot/", response_model=Base)
# async def chat_bot(
#         api_key: str = get_api_key,  # Khóa API để xác thực
#         Comment: str = Form(""),
#         Place: str = Form(""),

# ):
#     try:
#         vector_folder = Path("demo") / "data_vector"
#         prompt = Place + "\n\n" + Comment
#         # Khởi tạo chatbot với dữ liệu vector đã lưu
#         chat = FilesChatAgent(vector_folder).get_workflow().compile().invoke(
#             input={"question": prompt}
#         )

#         # Lấy kết quả chatbot sinh ra
#         response = chat["generation"]
#         return Base(id="chatbot-response", data=response)
    
#     except Exception as e:
#         raise HTTPException(status_code=500, detail=f"Chatbot error: {str(e)}")


#tạo router cho chat place
@router.post("/chat-ingestion/", response_model=Base)
async def chat_ingestion(
    api_key: str = get_api_key,
    Place: str = Form(...)
):
    """
    Tạo phản hồi từ chatbot bằng cách:
    
    1. Đọc dữ liệu đánh giá của địa điểm từ cơ sở dữ liệu
    2. Tiến hành ingest dữ liệu (vector hóa)
    3. Gọi chatbot để phân tích và phản hồi thông tin

    ## Request
    - `Place`: Tên địa điểm (bắt buộc, kiểu form data)
    - `api_key`: API Key bảo vệ endpoint (bắt buộc)

    ## Response
    - `id`: ID của địa điểm trong cơ sở dữ liệu
    - `data`: Kết quả phân tích từ chatbot (JSON dạng chuỗi)
    """
    input_folder = Path("demo") / "data_in"
    vector_folder = Path("demo") / "data_vector"

      # Đọc dữ liệu từ DB
    try:
        id, content = get_place_data_from_db(Place)
    except ValueError as e:
        raise HTTPException(status_code=404, detail=str(e))

    Ingestion(settings.AI).ingestion_folder(
        path_input_folder=str(input_folder),
        path_vector_store=str(vector_folder),
    )

    try:
        chat = FilesChatAgent(str(vector_folder)).get_workflow().compile().invoke(
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
    """
    API để crawl dữ liệu từ Google Maps dựa trên danh sách từ khóa nhập vào.

    ## Request
    - `keywords`: Chuỗi chứa các từ khóa hoặc tên địa điểm (ngăn cách bằng dấu phẩy hoặc xuống dòng)
    - `api_key`: API Key để xác thực

    ## Response
    - `id`: Mã định danh phản hồi (crawl-response)
    - `data`: Kết quả crawl được (dạng JSON chuỗi)

    ## Ví dụ `keywords`
    ```
    Lotte Mart Cần Thơ, Nhà Yên - Coffee & tea, sense city can tho
    ```

    ## Ghi chú
    - Dữ liệu sẽ được crawl theo từng từ khóa và gom lại trong một phản hồi duy nhất.
    - Có thể mất vài giây nếu từ khóa dài hoặc nhiều kết quả.
    """

    print(f"Từ khóa nhận được: {keywords}")
    """API để crawl dữ liệu từ danh sách từ khóa"""
    try:
        results = crawl_places(keywords)

        return Base(id="crawl-response", data=json.dumps(results))

    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Lỗi khi crawl dữ liệu: {str(e)}")

# api cấu hình ai
@router.post("/refresh-settings")
def refresh():
    """
        API để tải lại cấu hình hệ thống từ tệp cấu hình.

        Tham số:
        - Không có tham số đầu vào.

        Trả về:
        - `status`: Trạng thái sau khi tải lại (ví dụ: "reloaded").

        API này được sử dụng để làm mới các thiết lập hệ thống mà không cần khởi động lại server.
    """
    settings.reload()
    return {"status": "reloaded"}

@router.get("/export-location-data", response_model=Base)
def export_location_data(location_name: str,  api_key: str = get_api_key,):
    """
    API để xuất toàn bộ dữ liệu liên quan đến một địa điểm.

    ## Request
    - `location_name`: Tên địa điểm
    - `api_key`: API Key để xác thực

    ## Response
    - `id`: Mã định danh phản hồi (export-location)
    - `data`: Dữ liệu bảng `locations` và `users_review` liên quan
    """
    try:
        host = "localhost"
        if os.getenv("IN_DOCKER") == "true":
            host = "host.docker.internal"

        conn = mysql.connector.connect(
            host=host,
            user="root",
            password="",
            database="db_googlemap"
        )
        cursor = conn.cursor(dictionary=True)

        # Tìm địa điểm theo tên
        cursor.execute("SELECT * FROM locations WHERE name = %s", (location_name,))
        location = cursor.fetchone()

        if not location:
            raise HTTPException(status_code=404, detail="Không tìm thấy địa điểm")

        # Lấy các review tương ứng với location_id
        cursor.execute("SELECT * FROM users_review WHERE location_id = %s", (location["id"],))
        reviews = cursor.fetchall()

        cursor.close()
        conn.close()

        result = {
            "location": location,
            "reviews": reviews
        }

        return Base(id="export-location", data=json.dumps(result, ensure_ascii=False, indent=2, default=str))

    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Lỗi khi export location data: {str(e)}")
