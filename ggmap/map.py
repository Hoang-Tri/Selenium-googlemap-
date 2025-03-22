# import pandas as pd
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import json, re, time
from model import(is_single_place_page,
                  scroll_to_load_all,
                  open_reviews_tab,
                  scroll_reviews,
                  get_reviews,
                  process_single_place )
# Khởi tạo WebDriver
chrome_options = webdriver.ChromeOptions()
driver = webdriver.Chrome(service=Service(r"E:\Student\New folder\chromedriver-win32\chromedriver.exe"))
#kiểm tra là địa điểm cụ thể hay danh sách

try:
    keyword = "NHÀ PHẠM TRONG RỪNG"
    driver.get(f'https://www.google.com/maps/search/{keyword}/')
    time.sleep(5)

    if is_single_place_page(driver):
        process_single_place(driver)
    else:
        try:
            first_result = WebDriverWait(driver, 5).until(
                EC.element_to_be_clickable((By.CSS_SELECTOR, 'a.hfpxzc'))
            )
            driver.execute_script("arguments[0].click();", first_result)
            time.sleep(3)
        except Exception as e:
            print("Không thể chọn địa điểm đầu tiên:", e)
            driver.quit()
            exit()

        # Danh sách kết quả
        results = []
        collected_urls = set()
        has_new_data = True  # Đặt mặc định True để vào vòng while

        while has_new_data:
            scroll_to_load_all(driver, max_items=5)
            has_new_data = False

            items = WebDriverWait(driver, 10).until(
                EC.presence_of_all_elements_located((By.CSS_SELECTOR, 'div[role="feed"] > div > div[jsaction]'))
            )

            for item in items:
                try:
                    # Xử lý từng địa điểm
                    link_element = item.find_element(By.CSS_SELECTOR, "a")
                    place_url = link_element.get_attribute('href')
                    if not place_url or place_url in collected_urls:
                        continue

                    collected_urls.add(place_url)
                    item.find_element(By.CLASS_NAME, 'hfpxzc').click()
                    time.sleep(3)

                    WebDriverWait(driver, 10).until(
                        EC.presence_of_element_located((By.CLASS_NAME, 'Io6YTe'))
                    )
                    try:
                        title = driver.find_element(By.CLASS_NAME, "DUwDvf").text.strip()
                        print(f"Đang xử lý địa điểm: {title}")
                    except:
                        pass

                    data = {
                        'title': driver.find_element(By.CLASS_NAME, 'DUwDvf').text,
                        'address': driver.find_element(By.CLASS_NAME, 'Io6YTe').text.strip(),
                        'link': driver.current_url
                    }

                    # Star & Review
                    try:
                        rating_element = driver.find_element(By.CSS_SELECTOR, 'div.fontBodyMedium span[role="img"]')
                        rating_text = rating_element.get_attribute('aria-label')
                        rating_number = [float(piece) for piece in rating_text.split(" ") if piece.replace(".", "", 1).isdigit()]
                        if len(rating_number) > 1:
                            data['star'] = rating_number[0]
                            data['review'] = int(rating_number[1])
                        else:
                            data['star'] = rating_number[0]
                            data['review'] = 0
                    except:
                        pass

                    # Lấy tất cả review
                    try:
                        review_tab = WebDriverWait(driver, 5).until(
                            EC.element_to_be_clickable((By.XPATH, '//button[contains(@aria-label, "Reviews") or contains(@aria-label, "Nhận xét")]'))
                        )
                        review_tab.click()
                        time.sleep(5)

                        open_reviews_tab(driver)
                        scroll_reviews(driver)
                        data['reviews'] = get_reviews(driver)
                    except:
                        pass

                    results.append(data)
                    has_new_data = True

                except:
                    continue

                driver.back()
                time.sleep(2) 

        # Lưu kết quả
        with open('results.json', 'w', encoding='utf-8') as f:
            json.dump(results, f, indent=2, ensure_ascii=False)

except Exception :
    pass

finally:
    driver.quit()
