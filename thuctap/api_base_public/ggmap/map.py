# import pandas as pd
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from model import(process_places_from_urls,
                  collect_place_urls_from_keywords, )
# Khởi tạo WebDriver

if __name__ == "__main__":
    try:
        input_keywords = input("Nhập từ khóa tìm kiếm (cách nhau bằng dấu phẩy): ")
        
        # Chuyển chuỗi nhập vào thành danh sách, loại bỏ khoảng trắng thừa
        keywords = [kw.strip() for kw in input_keywords.split(",") if kw.strip()]
        
        chrome_options = webdriver.ChromeOptions()
        driver = webdriver.Chrome(service=Service(r"E:\Student\New folder\chromedriver-win32\chromedriver.exe"))


        for keyword in keywords:
            keyword_name = keyword.replace(" ", "_")
            collect_place_urls_from_keywords(driver, [keyword], keyword_name=keyword_name)
            process_places_from_urls(driver, keyword_name=keyword_name)
    finally:
        driver.quit()