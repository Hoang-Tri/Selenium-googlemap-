from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import time, json

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
        time.sleep(3)
    except:
        print("Không tìm thấy nút mở review – có thể đã hiện sẵn.")

def scroll_reviews(driver, max_scrolls=20):
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
                if retries >= 2:
                    print(f"Không còn review mới sau {i+1} lần cuộn.")
                    break
            else:
                retries = 0
                previous_count = current_count

            driver.execute_script("arguments[0].scrollTop = arguments[0].scrollHeight", scrollable_div)
            time.sleep(2)

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

            if star is not None:
                reviews.append({
                    "name": name,
                    "review": comment,
                    "star": star
                })

    except Exception as e:
        print("Không thể lấy review:", e)

    return reviews

def process_single_place(driver):
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
    try:
        rating_element = driver.find_element(By.CSS_SELECTOR, 'div.fontBodyMedium span[role="img"]')
        rating_text = rating_element.get_attribute('aria-label')
        rating_number = [float(piece) for piece in rating_text.split(" ") if piece.replace(".", "", 1).isdigit()]
        data['star'] = rating_number[0] if rating_number else 0

        # Lấy số bài đánh giá (review)
        # try:
        #     # Tìm span có aria-label chứa "bài đánh giá"
        #     review_element = driver.find_element(By.XPATH, '//span[contains(@aria-label, "bài đánh giá")]')
        #     review_text = review_element.get_attribute('aria-label')  # "215 bài đánh giá"
        #     review_number = int("".join(filter(str.isdigit, review_text)))  # lấy 215
        #     data['review'] = review_number
        # except:
        #      data['review'] = 0
    except:
        pass

    try:
        open_reviews_tab(driver)  # Mở tab Review
        # Cuộn để tải tất cả review
        scroll_reviews(driver)
        data['reviews'] = get_reviews(driver)

    except:
        pass

    with open('results.json', 'w', encoding='utf-8') as f:
        json.dump([data], f , indent=2, ensure_ascii=False)

    driver.quit()
    exit()  #


