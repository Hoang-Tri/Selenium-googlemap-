# setting_repository.py
import mysql.connector
from dotenv import load_dotenv
import os

load_dotenv()  # Load biến từ file .env nếu có

class SettingRepository:
    def __init__(self):
        self.conn = mysql.connector.connect(
            host=os.getenv("DB_HOST", "localhost"),
            user=os.getenv("DB_USER", "root"),
            password=os.getenv("DB_PASSWORD", ""),
            database=os.getenv("DB_NAME", "db_googlemap"),
            port=int(os.getenv("DB_PORT", 3306))  
        )

    def get_all(self):
        cursor = self.conn.cursor(dictionary=True)
        cursor.execute("SELECT `key_name` AS `key`, `value` FROM settings")
        result = cursor.fetchall()
        cursor.close()
        return result
 