from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import time, json, re, os

def is_single_place_page(driver):
    try:
        WebDriverWait(driver, 5).until(
            EC.presence_of_element_located((By.CLASS_NAME, 'DUwDvf'))  # Tên địa điểm
        )
        return True
    except:
        return False

def scroll_to_load_all(driver, max_items=5):
    scrollable_div = WebDriverWait(driver, 10).until(
        EC.presence_of_element_located((By.CSS_SELECTOR, 'div[role="feed"]'))
    )
    previous_count = 0
    while True:
        items = driver.find_elements(By.CSS_SELECTOR, 'div[role="feed"] > div > div[jsaction]')
        current_count = len(items)

        if current_count >= max_items or current_count == previous_count:
            break

        previous_count = current_count
        driver.execute_script("arguments[0].scrollBy(0, 1000);", scrollable_div)
        time.sleep(2)

def open_reviews_tab(driver):
    try:
        review_tab = WebDriverWait(driver, 5).until(
            EC.element_to_be_clickable(
                (By.XPATH, '//button[contains(@aria-label, "Reviews") or contains(@aria-label, "Nhận xét")]')
            )
        )
        review_tab.click()
        time.sleep(10)
    except:
        print("Không tìm thấy nút mở review – có thể đã hiện sẵn.")

def scroll_reviews(driver, max_scrolls=20):
    try:
        # Chờ tối đa 10s để phần div scroll chứa review hiển thị
        scrollable_div = WebDriverWait(driver, 20).until(
            EC.presence_of_element_located(
                (By.XPATH, '//div[contains(@class, "m6QErb") and contains(@class, "DxyBCb") and contains(@class, "kA9KIf") and contains(@class, "dS8AEf")]')
            )
        )

        previous_count = 0
        retries = 0

        for i in range(max_scrolls):
            # Lấy số lượng review hiện tại
            reviews = driver.find_elements(By.CLASS_NAME, 'jftiEf')
            current_count = len(reviews)
            print(f"Scroll {i+1}: đã có {current_count} review")

            if current_count == previous_count:
                retries += 1
                if retries >= 1:
                    print(f"Không còn review mới sau {i+1} lần cuộn.")
                    break
            else:
                retries = 0
                previous_count = current_count

            # Cuộn xuống để load thêm review
            driver.execute_script("arguments[0].scrollTop = arguments[0].scrollHeight", scrollable_div)

            # Chờ để review mới load xong (tùy mạng, để 2.5s ~ 3s là ổn)
            time.sleep(2)

        if current_count == 0:
            print("Không có review nào.")

    except Exception as e:
        print("Lỗi khi cuộn review:", e)

def get_reviews(driver):
    reviews = []
    try:
        review_elements = driver.find_elements(By.CLASS_NAME, 'jftiEf')

        for review_element in review_elements:
            try:
                more_button = review_element.find_element(By.CLASS_NAME, 'w8nwRe')
                driver.execute_script("arguments[0].click();", more_button)
                time.sleep(0.3)
            except:
                pass

            try:
                name = review_element.find_element(By.CLASS_NAME, 'd4r55').text.strip()
            except:
                name = "Ẩn danh"

            try:
                comment = review_element.find_element(By.CLASS_NAME, 'wiI7pd').text.strip()
            except:
                comment = ""

            try:
                star_element = review_element.find_element(By.CSS_SELECTOR, 'span[class*="kvMYJc"]')
                star_text = star_element.get_attribute('aria-label')
                star = float(star_text.split(" ")[0].replace(",", "."))
            except:
                star = None

            reviews.append({
                "name": name,
                "review": comment,
                "star": star
            })
            
    except Exception as e:
        print("Không thể lấy review:", e)

    return reviews

def process_single_place(driver):
    data = {}
    try:
        title = driver.find_element(By.CLASS_NAME, "DUwDvf").text.strip()
        print(f"Đang xử lý địa điểm: {title}")
        data['title'] = title
        data['address'] = driver.find_element(By.CLASS_NAME, 'Io6YTe').text.strip()
        data['link'] = driver.current_url
    except:
        pass  # Nếu không load được địa điểm

    try:
        rating_element = driver.find_element(By.CSS_SELECTOR, 'span[role="img"]')
        rating_text = rating_element.get_attribute('aria-label')  # Ví dụ: "4.4 stars"
        rating_number = float(re.search(r"[\d.]+", rating_text).group())
        data['star'] = rating_number
    except:
        data['star'] = 0

    try:
        time.sleep(2)
        open_reviews_tab(driver)
        scroll_reviews(driver)
        data['reviews'] = get_reviews(driver)
    except:
        data['reviews'] = []

    return data

def collect_place_urls_from_keywords(driver, keywords, keyword_name="default"):
    all_place_urls = []

    for keyword in keywords:
        print(f"Tìm kiếm với từ khóa: {keyword}")
        driver.get(f'https://www.google.com/maps/search/{keyword}/')
        time.sleep(5)

        # Kiểm tra có phải trang địa điểm cụ thể không
        if is_single_place_page(driver):
            print(f"→ '{keyword}' là địa điểm cụ thể.")
            all_place_urls.append(driver.current_url)
            continue

        # Nếu không phải thì xử lý như danh sách
        try:
            first_result = WebDriverWait(driver, 5).until(
                EC.element_to_be_clickable((By.CSS_SELECTOR, 'a.hfpxzc'))
            )
            driver.execute_script("arguments[0].click();", first_result)
            time.sleep(3)
        except Exception as e:
            print(f"Không thể chọn kết quả đầu tiên cho '{keyword}':", e)
            continue

        collected_urls = set()
        scroll_to_load_all(driver, max_items=20)

        items = WebDriverWait(driver, 10).until(
            EC.presence_of_all_elements_located((By.CSS_SELECTOR, 'div[role="feed"] > div > div[jsaction]'))
        )

        for item in items:
            try:
                link_element = item.find_element(By.CSS_SELECTOR, "a")
                place_url = link_element.get_attribute('href')
                if place_url and place_url not in collected_urls:
                    collected_urls.add(place_url)
            except:
                continue

        all_place_urls.extend(collected_urls)

    # Lưu URL vào file
    os.makedirs("data/data_url", exist_ok=True)

    with open(f"data/data_url/{keyword_name}_urls.json", "w", encoding="utf-8") as f:
        json.dump(list(set(all_place_urls)), f, indent=2, ensure_ascii=False)

    print(f"Đã thu thập {len(all_place_urls)} URL cho từ khóa {keyword_name}.")

def process_places_from_urls(driver,keyword_name="default"):
    os.makedirs("data/data_result", exist_ok=True)

    try:
        with open(f"data/data_url/{keyword_name}_urls.json", "r", encoding="utf-8") as f:
            urls = json.load(f)
    except FileNotFoundError:
        print(f"Không tìm thấy file URL cho '{keyword_name}'")
        return

    all_data = []

    for url in urls:
        print(f"Đang xử lý: {url}")
        driver.get(url)
        time.sleep(5)

        try:
            data = process_single_place(driver)
            if data:
                all_data.append(data)
        except Exception as e:
            print("Lỗi khi xử lý:", e)
            continue

    with open(f"data/data_result/{keyword_name}.json", "w", encoding="utf-8") as f:
        json.dump(all_data, f, indent=2, ensure_ascii=False)

    print(f"Đã lưu kết quả vào data/data_result/{keyword_name}.json")

