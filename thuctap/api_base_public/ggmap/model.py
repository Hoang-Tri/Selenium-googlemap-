from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from collections import defaultdict
from datetime import datetime, timedelta
import time, json, re, os, csv
import unicodedata
import uuid
from test_datallm import process_csv_file

def remove_accents(text):
    return ''.join(c for c in unicodedata.normalize('NFD', text) if unicodedata.category(c) != 'Mn')

def generate_unique_id():
    return str(uuid.uuid4())[:8] 

def is_single_place_page(driver):
    try:
        WebDriverWait(driver, 5).until(
            EC.presence_of_element_located((By.CLASS_NAME, 'DUwDvf'))  
        )
        return True
    except:
        return False

def scroll_to_load_all(driver, max_items=4):
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

def scroll_reviews(driver, max_scrolls=10):
    try:
        scrollable_div = WebDriverWait(driver, 10).until(
            EC.presence_of_element_located(
                (By.XPATH, '//div[contains(@class, "m6QErb") and contains(@class, "DxyBCb") and contains(@class, "kA9KIf") and contains(@class, "dS8AEf")]')
            )
        )

        previous_count = 0
        retries = 0

        for i in range(max_scrolls):
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

            driver.execute_script("arguments[0].scrollTop = arguments[0].scrollHeight", scrollable_div)
            time.sleep(2)

        if current_count == 0:
            print("Không có review nào.")

    except Exception as e:
        print("Lỗi khi cuộn review:", e)

def calculate_review_date(review_time_str):
    today = datetime.today()

    # Xử lý "Hôm nay"
    if "Hôm nay" in review_time_str:
        return today.strftime("%Y-%m-%d")

    # Xử lý "Hôm qua"
    if "Hôm qua" in review_time_str:
        return (today - timedelta(days=1)).strftime("%Y-%m-%d")

    # Xử lý "a year ago"
    if "a year ago" in review_time_str:
        return f"{today.year - 1}-{today.month:02d}-{today.day:02d}"

    # Xử lý "a month ago"
    if "a month ago" in review_time_str:
        new_month = today.month - 1
        new_year = today.year
        if new_month <= 0:
            new_month += 12
            new_year -= 1
        return f"{new_year}-{new_month:02d}-{today.day:02d}"
    
    # Xử lý "X months ago"
    month_match = re.search(r"(\d+) months? ago", review_time_str)
    if month_match:
        months = int(month_match.group(1))
        new_month = today.month - months
        new_year = today.year

        while new_month <= 0:
            new_month += 12
            new_year -= 1

        return f"{new_year}-{new_month:02d}-{today.day:02d}"

    # Xử lý "X weeks ago"
    week_match = re.search(r"(\d+) weeks? ago", review_time_str)
    if week_match:
        weeks = int(week_match.group(1))
        return (today - timedelta(weeks=weeks)).strftime("%Y-%m-%d")

    # Xử lý "X days ago"
    day_match = re.search(r"(\d+) days? ago", review_time_str)
    if day_match:
        days = int(day_match.group(1))
        return (today - timedelta(days=days)).strftime("%Y-%m-%d")

    # Xử lý "X năm trước"
    year_match = re.search(r"(\d+) năm trước", review_time_str)
    if year_match:
        years = int(year_match.group(1))
        return f"{today.year - years}-{today.month:02d}-{today.day:02d}"
    
    # Xử lý "X years ago"
    year_eng_match = re.search(r"(\d+) years? ago", review_time_str)
    if year_eng_match:
        years = int(year_eng_match.group(1))
        return f"{today.year - years}-{today.month:02d}-{today.day:02d}"

    return today.strftime("%Y-%m-%d")

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

            try:
                review_time = review_element.find_element(By.CLASS_NAME, 'rsqaWe').text.strip()
                review_date = calculate_review_date(review_time)
                # print(f"Debug: Raw time='{review_time}' → Processed date='{review_date}'")  # Debug
            except:
                review_date = "Không rõ ngày"

            reviews.append({
                "name": name,
                "review": comment,
                "star": star,
                "date": review_date  
            })
            
    except Exception as e:
        print("Không thể lấy review:", e)

    return reviews

def process_single_place(driver):
    data = {}
    scraped_date = datetime.today().strftime("%Y-%m-%d")  

    try:
        title = driver.find_element(By.CLASS_NAME, "DUwDvf").text.strip()
        print(f"Đang xử lý địa điểm: {title}")
        data['title'] = f"{title}"  # 
        data['address'] = driver.find_element(By.CLASS_NAME, 'Io6YTe').text.strip()
        data['link'] = driver.current_url
        data['scraped_date'] = scraped_date  
    except:
        pass  

    try:
        rating_element = driver.find_element(By.CSS_SELECTOR, 'span[role="img"]')
        rating_text = rating_element.get_attribute('aria-label')  
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
    place_counter = 1  

    for url in urls:
        print(f"Đang xử lý: {url}")
        driver.get(url)
        time.sleep(5)

        try:
            data = process_single_place(driver)
            if data:
                place_name = data.get("title", "").strip()
                place_id = generate_unique_id()
                data["ID_place"] = place_id

                # Gán ID_review cho từng review
                review_counter = 1
                for review in data.get("reviews", []):
                    review["ID_review"] = f"{place_id}_{review_counter}"
                    review_counter += 1

                all_data.append(data)
                place_counter += 1  
        except Exception as e:
            print("Lỗi khi xử lý:", e)
            continue

    json_path = f"data/data_result/{keyword_name}.json"
    with open(json_path, "w", encoding="utf-8") as f:
        json.dump(all_data, f, indent=2, ensure_ascii=False)

    print(f"Đã lưu kết quả vào {json_path}")

    scraped_date = datetime.today().strftime("%Y-%m-%d")

    os.makedirs("demo/data_in", exist_ok=True)
    for item in all_data:
        place_id = item["ID_place"]
        place_name = item.get("title", "").strip().replace(" ", "_")  
        address = item.get("address", "Không rõ địa chỉ").strip()
        link = item.get("link", "Không có link").strip()
        reviews = item.get("reviews", [])

        # Định dạng tên file tránh trùng lặp
        csv_filename = f"{place_id}_{place_name}_{scraped_date}.csv"
        csv_path = os.path.join("data/data_in", csv_filename)

        with open(csv_path, mode="w", newline='', encoding="utf-8") as file:
            writer = csv.writer(file)
            
            # Ghi header vào file CSV
            writer.writerow(["places","address", "link", "user", "star", "creat_date", "comment", "data_llm", "scraped_date"])

            for review in reviews:
                user = review.get("name", "Ẩn danh").strip()
                comment = review.get("review", "").strip().replace("\n", " ")
                stars = review.get("star", None)
                review_date = review.get("date", "Không rõ ngày").strip()

                if comment:
                    # Ghi dữ liệu review vào file CSV
                    writer.writerow([place_name, address, link, user, stars if stars is not None else "N/A", review_date, comment, None, scraped_date])
        print(f"Đã lưu kết quả vào {csv_path}")
        process_csv_file(csv_path)
