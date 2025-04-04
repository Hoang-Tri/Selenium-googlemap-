from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from ggmap.model import collect_place_urls_from_keywords, process_places_from_urls
import os

def crawl_places(keywords: list):
    """Hàm crawl dữ liệu từ danh sách từ khóa"""
    try:
        chrome_options = webdriver.ChromeOptions()
        chrome_options.add_argument("--headless")  # Chạy không mở trình duyệt

        driver_path = r"E:\Student\New folder\chromedriver-win32\chromedriver.exe"
        driver = webdriver.Chrome(service=Service(driver_path), options=chrome_options)

        keyword_name = keywords.replace(" ", "_")
        collect_place_urls_from_keywords(driver, [keywords], keyword_name=keyword_name)
        process_places_from_urls(driver, keyword_name=keyword_name)

        driver.quit()
        return {"message": "Crawl dữ liệu thành công!"}

    except Exception as e:
        return {"error": str(e)}
