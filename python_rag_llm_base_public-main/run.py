# # chuẩn bị dữ liệu
# from ingestion.ingestion import Ingestion
# from app.config import settings
# Ingestion(settings.LLM_NAME).ingestion_folder(
#     path_input_folder="demo\data_in",
#     path_vector_store="demo\data_vector",
# )

# chatbot
from chatbot.services.files_chat_agent import FilesChatAgent  # noqa: E402
from app.config import settings

# settings.LLM_NAME = "google"

_question = "Nhu cầu tuyển dụng trong nhóm kinh doanh/bán hàng có đang giữ vững vị trí dẫn đầu và gia tăng không?"
chat = FilesChatAgent("demo\data_vector").get_workflow().compile().invoke(
    input={
        "question": _question,
    }
)

print(chat)

print("generation", chat["generation"])