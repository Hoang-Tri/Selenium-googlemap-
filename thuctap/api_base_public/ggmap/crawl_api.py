from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from ggmap.model import collect_place_urls_from_keywords, process_places_from_urls
import os
import platform

def crawl_places(keywords: str):
    """Hàm crawl dữ liệu từ danh sách từ khóa"""
    try:
        chrome_options = webdriver.ChromeOptions()
        chrome_options.add_argument("--headless") 
        chrome_options.add_argument("--no-sandbox")
        chrome_options.add_argument("--disable-dev-shm-usage")

        if platform.system() == "Windows":
            driver_path = "chromedriver-win64/chromedriver.exe"
        elif platform.system() == "Linux":
            driver_path = "chromedriver-linux64/chromedriver"
        else:
            return {"message": "Hệ điều hành không được hỗ trợ!"}
        
        print(driver_path)
        driver = webdriver.Chrome(service=Service(driver_path), options=chrome_options)

        keyword_name = keywords.replace(" ", "_")
        collect_place_urls_from_keywords(driver, [keywords], keyword_name=keyword_name)
        process_places_from_urls(driver, keyword_name=keyword_name)

        driver.quit()
        return {"message": "Crawl dữ liệu thành công!"}

    except Exception as e:
        return {"error": str(e)}
