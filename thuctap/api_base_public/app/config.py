# File cấu hình chung cho ứng dụng

import os
from dotenv import load_dotenv


# Load các biến môi trường từ file .env
load_dotenv()


class Settings:
    # SETTING
    DIR_ROOT = os.path.dirname(os.path.abspath(".env"))
    # API KEY
    API_KEY = os.environ["API_KEY"]

    """
    Lớp cấu hình chung cho ứng dụng, quản lý các biến môi trường.

    Attributes:
        DIR_ROOT (str): Đường dẫn thư mục gốc của dự án.
    """

    # Thiết lập đường dẫn thư mục gốc của dự án

    KEY_API_GPT = os.environ["KEY_API_GPT"]

    NUM_DOC = os.environ["NUM_DOC"]

    LLM_NAME = os.environ["LLM_NAME"]

    OPENAI_LLM = os.environ["OPENAI_LLM"]

    GOOGLE_LLM = os.environ["GOOGLE_LLM"]

settings = Settings()

