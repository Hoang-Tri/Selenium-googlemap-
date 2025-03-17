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
#kiểm tra là địa điểm cụ thể hay danh sách
def is_single_place_page():
    try:
        WebDriverWait(driver, 5).until(
            EC.presence_of_element_located((By.CLASS_NAME, 'DUwDvf'))  # Tên địa điểm
        )
        return True
    except:
        return False
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
        # Google dùng aria-label đa ngôn ngữ, nên tìm nút chứa từ "review"
        review_tab = WebDriverWait(driver, 5).until(
            EC.element_to_be_clickable(
                (By.XPATH, '//button[contains(@aria-label, "Reviews") or contains(@aria-label, "Nhận xét")]')
            )
        )
        review_tab.click()
        time.sleep(3)
    except:
        print("Không tìm thấy nút mở review – có thể đã hiện sẵn.")


#cuộn feedback
def scroll_reviews():
    try:
        review_section = WebDriverWait(driver, 10).until(
            EC.presence_of_element_located((By.CLASS_NAME, "m6QErb"))
        )
        previous_count = 0
        retries = 0

        while retries < 5:
            reviews = driver.find_elements(By.CLASS_NAME, 'jftiEf')
            current_count = len(reviews)

            if current_count == previous_count:
                retries += 1
            else:
                retries = 0  # reset nếu có review mới
                previous_count = current_count

            driver.execute_script("arguments[0].scrollTop = arguments[0].scrollHeight;", review_section)
            time.sleep(2)
    except Exception as e:
        print("Không thể cuộn review:", e)

#Lấy feedback
def get_reviews():
    reviews = []
    try:
        review_elements = driver.find_elements(By.CLASS_NAME, 'jftiEf')  # Mỗi review

        for review_element in review_elements:
            try:
                name = review_element.find_element(By.CLASS_NAME, 'd4r55').text.strip()
            except:
                name = "Ẩn danh"

            try:
                comment = review_element.find_element(By.CLASS_NAME, 'wiI7pd').text.strip()
            except:
                comment = ""

            if comment:  # Chỉ lấy review có nội dung
                reviews.append({"name": name, "review": comment})

    except Exception as e:
        print("Không thể lấy review:", e)

    return reviews

try:
    keyword = "Coffee"
    driver.get(f'https://www.google.com/maps/search/{keyword}/')
    time.sleep(5)

    # Kiểm tra nếu là 1 địa điểm cụ thể thì xử lý riêng
    if is_single_place_page():
        print("Trang địa điểm cụ thể → xử lý riêng")
        data = {
            'title': driver.find_element(By.CLASS_NAME, 'DUwDvf').text,
            'address': driver.find_element(By.CLASS_NAME, 'Io6YTe').text.strip(),
            'link': driver.current_url
        }

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

        try:
            open_reviews_tab()
            scroll_reviews()
            data['reviews'] = get_reviews()
        except:
            pass

        with open('results.json', 'w', encoding='utf-8') as f:
            json.dump([data], f, indent=2, ensure_ascii=False)

        driver.quit()
        exit()  # Dừng chương trình sau khi xử lý xong

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
