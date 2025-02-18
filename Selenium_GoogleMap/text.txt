from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import json
import time

# Khởi tạo WebDriver
chrome_options = webdriver.ChromeOptions()
driver = webdriver.Chrome(service=Service(r"E:\Student\New folder\chromedriver-win32\chromedriver.exe"))

#cuộn trang
def scroll_to_load_all():
    scrollable_div = WebDriverWait(driver, 10).until(
        EC.presence_of_element_located((By.CSS_SELECTOR, 'div[role="feed"]'))
    )
    previous_count = 0
    while True:
        items = driver.find_elements(By.CSS_SELECTOR, 'div[role="feed"] > div > div[jsaction]')
        current_count = len(items)
        if current_count == previous_count:
            break  # Dừng khi không còn dữ liệu mới
        previous_count = current_count
        driver.execute_script("arguments[0].scrollBy(0, 1000);", scrollable_div)
        time.sleep(2)

#click vào tab review
def open_reviews_tab():
    try:
        review_tab = WebDriverWait(driver, 5).until(
            EC.element_to_be_clickable((By.XPATH, '//button[contains(@aria-label, "Review")]'))
        )
        review_tab.click()
        time.sleep(2)  # Chờ tab review tải xong
    except Exception:
        pass  # Nếu không có tab review, bỏ qua

#cuộn feedback
def scroll_reviews():
    try:
        review_section = WebDriverWait(driver, 5).until(
            EC.presence_of_element_located((By.CLASS_NAME, "m6QErb"))
        )
        while True:
            last_height = driver.execute_script("return arguments[0].scrollHeight;", review_section)
            driver.execute_script("arguments[0].scrollTop = arguments[0].scrollHeight;", review_section)
            time.sleep(2)  # Chờ nhận xét mới tải

            new_height = driver.execute_script("return arguments[0].scrollHeight;", review_section)
            if new_height == last_height:
                break  # Nếu không còn nội dung mới thì dừng
    except Exception:
        pass

#Lấy feedback
def get_reviews():
    reviews = []
    try:
        review_elements = driver.find_elements(By.CLASS_NAME, 'jftiEf')  # Mỗi review trong danh sách

        for review_element in review_elements:
            try:
                name = review_element.find_element(By.CLASS_NAME, 'd4r55').text.strip()  # Lấy tên
                comment = review_element.find_element(By.CLASS_NAME, 'wiI7pd').text.strip()  # Lấy nhận xét
                reviews.append({"name": name, "review": comment})
            except Exception:
                continue  # Nếu lỗi, bỏ qua review này

    except Exception:
        pass  # Nếu không có nhận xét thì bỏ qua
    return reviews
try:
    keyword = "Coffee"
    driver.get(f'https://www.google.com/maps/search/{keyword}/')
    time.sleep(5)

    # Đóng thông báo nếu có
    try:
        WebDriverWait(driver, 3).until(EC.element_to_be_clickable((By.CSS_SELECTOR, "form:nth-child(2)"))).click()
    except Exception:
        pass

    results = []
    collected_urls = set()
    has_new_data = True

    while has_new_data:
        scroll_to_load_all()  # Cuộn để tải toàn bộ danh sách
        has_new_data = False  # Đánh dấu mặc định không có dữ liệu mới

        items = WebDriverWait(driver, 10).until(
            EC.presence_of_all_elements_located((By.CSS_SELECTOR, 'div[role="feed"] > div > div[jsaction]'))
        )

        for item in items:
            try:
                link_element = item.find_element(By.CSS_SELECTOR, "a")
                place_url = link_element.get_attribute('href')

                if not place_url or place_url in collected_urls:
                    continue  # Bỏ qua nếu URL không hợp lệ hoặc đã thu thập

                collected_urls.add(place_url)  # Lưu URL để tránh trùng lặp
                item.find_element(By.CLASS_NAME, 'hfpxzc').click()
                time.sleep(3)  # Chờ popup hiển thị

                WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.CLASS_NAME, 'Io6YTe')))

                # Lấy thông tin địa điểm
                data = {
                    'title': driver.find_element(By.CLASS_NAME, 'DUwDvf').text,
                    'address': driver.find_element(By.CLASS_NAME, 'Io6YTe').text.strip(),
                    #'link': driver.current_url
                }
                try:
                    data['link'] = item.find_element(By.CSS_SELECTOR, "a").get_attribute('href')
                except Exception:
                    pass
                try:
                    data['website'] = item.find_element(By.CSS_SELECTOR, 'div a').get_attribute('href')
                except Exception:
                    pass

                try:
                    rating_element = item.find_element(By.CSS_SELECTOR, 'div.fontBodyMedium span[role="img"]')
                    rating_text = rating_element.get_attribute('aria-label')

                    rating_number = [float(piece) for piece in rating_text.split(" ") if
                                     piece.replace(".", "", 1).isdigit()]

                    if len(rating_number) > 1:
                        data['star'] = rating_number[0]
                        data['review'] = int(rating_number[1])
                    else:
                        data['star'] = rating_number[0]
                        data['review'] = 0

                except Exception:
                    pass
                    # Cuộn và lấy nhận xét + tên người đánh giá
                try:
                    # Tìm và click vào tab Review
                    review_tab = WebDriverWait(driver, 5).until(
                        EC.element_to_be_clickable((By.XPATH, '//button[contains(@aria-label, "Reviews") or contains(@aria-label, "Nhận xét")]'))
                    )
                    review_tab.click()
                    time.sleep(5)  # Đợi tab mở hoàn toàn

                    open_reviews_tab()  # Mở tab Review
                    # Cuộn để tải tất cả review
                    scroll_reviews()
                    data['reviews'] = get_reviews()

                except Exception:
                    pass

                results.append(data)
                has_new_data = True  # Đánh dấu có dữ liệu mới

            except Exception:
                continue  # Nếu lỗi, bỏ qua item này

            # Quay lại danh sách và chờ một chút
            driver.back()
            time.sleep(2)  # Giảm nguy cơ lỗi do load trang chưa hoàn tất

    # Lưu dữ liệu vào file JSON
    with open('results.json', 'w', encoding='utf-8') as f:
        json.dump(results, f, indent=2, ensure_ascii=False)

finally:
    driver.quit()
