from fastapi import APIRouter
from fastapi import FastAPI, File, UploadFile, Header, HTTPException, Request, Form ,  Depends # noqa: E402, F401
from app.security.security import get_api_key
from app.models.base import Base
from chatbot.services.chatbot_api_agent import FilesChatAgent
from ingestion.ingestion  import Ingestion
import json, re
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
    try:
        ingestion = Ingestion("all-MiniLM-L6-v2")

        # Đọc file comment
        with open("demo/data_in/data_feedback.txt", "r", encoding="utf-8") as f:
            all_lines = f.readlines()

        filtered_lines = [line.strip() for line in all_lines if Place.lower() in line.lower()]

        results = []

        for i, line in enumerate(filtered_lines):
            prompt = f"{Place}\n\n{line}"

            chat = FilesChatAgent().get_workflow().compile().invoke(
                input={"question": prompt}
            )
            response = chat["generation"]

            # results.append({
            #     "index": i,
            #     "prompt": prompt,
            #     "response": response
            # })

        # Tổng hợp phản hồi
        # response_text = "\n\n".join([f"[{r['index']}] {r['response']}" for r in results])
        # cleaned_data = response_text.replace('[0] ', '', 1)

            json_match = re.search(r"```json\n(.*?)```", response, re.DOTALL)
            if json_match:
                response_data = json.loads(json_match.group(1))

            results.append(response_data)
        print(json.dumps(results, indent=2, ensure_ascii=False)) 
        return Base(id="chatbot-response", data=json.dumps(results, ensure_ascii=False))

        # return Base(id="chatbot-response", data=final_result)

    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Chatbot error: {str(e)}")

