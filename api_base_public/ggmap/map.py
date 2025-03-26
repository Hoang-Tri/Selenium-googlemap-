# import pandas as pd
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from model import(process_places_from_urls,
                  collect_place_urls_from_keywords, )
# Khởi tạo WebDriver
chrome_options = webdriver.ChromeOptions()
driver = webdriver.Chrome(service=Service(r"E:\Student\New folder\chromedriver-win32\chromedriver.exe"))

if __name__ == "__main__":
    try:
        keywords = ["Phúc Long"]
        for keyword in keywords:
            collect_place_urls_from_keywords(driver, [keyword], keyword_name=keyword.replace(" ", "_"))
            process_places_from_urls(driver, keyword_name=keyword.replace(" ", "_"))
    finally:
        driver.quit()