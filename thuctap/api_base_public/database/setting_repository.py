import mysql.connector
from dotenv import load_dotenv
import os

load_dotenv()  # Load biến từ file .env nếu có

class SettingRepository:
    def __init__(self):
        # Mặc định là localhost
        host = "localhost"
        
        # Nếu chạy trong Docker, dùng host.docker.internal để kết nối ra ngoài
        if os.getenv("IN_DOCKER") == "true":
            host = "host.docker.internal"

        print(host)
        self.conn = mysql.connector.connect(
            host=host,  # Ưu tiên biến môi trường DB_HOST
            user=os.getenv("DB_USER", "root"),
            password=os.getenv("DB_PASSWORD", ""),
            database=os.getenv("DB_NAME", "db_googlemap")
        )

    def get_all(self):
        cursor = self.conn.cursor(dictionary=True)
        cursor.execute("SELECT `key_name` AS `key`, `value` FROM settings")
        result = cursor.fetchall()
        cursor.close()
        return result