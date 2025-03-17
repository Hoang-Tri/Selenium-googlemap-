import time
import random
import requests
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.support import expected_conditions as EC

# Đường dẫn tới ChromeDriver
chrome_driver_path = r"E:\Student\New folder\chromedriver-win32\chromedriver.exe"

# Danh sách proxy xoay
proxy_list = [
    "113.160.133.32:8080",
    "113.160.132.195:8080",
    "123.30.154.171:7777",
    "222.252.194.204:8080"
]

def check_proxy(proxy):
    try:
        response = requests.get("https://httpbin.org/ip", proxies={"http": f"http://{proxy}", "https": f"http://{proxy}"}, timeout=5)
        if response.status_code == 200:
            return True
    except:
        pass
    return False

selected_proxy = None
while not selected_proxy:
    proxy = random.choice(proxy_list)
    if check_proxy(proxy):
        selected_proxy = proxy
        print(f"Đang sử dụng proxy: {selected_proxy}")

chrome_options = Options()
chrome_options.add_experimental_option("excludeSwitches", ["enable-automation"])

driver = webdriver.Chrome(service=Service(chrome_driver_path), options=chrome_options)

try:
    driver.get("https://www.google.com/maps")

    # Chờ cho thanh tìm kiếm xuất hiện
    wait = WebDriverWait(driver, 30)
    search_box = wait.until(EC.presence_of_element_located((By.ID, "searchboxinput")))

    # Nhập tên địa điểm cần tìm
    search_box.clear()
    search_term = ("Bến Ninh Kiều")
    for char in search_term:
        search_box.send_keys(char)
        time.sleep(random.uniform(0.2, 0.4))
    search_box.send_keys(Keys.ENTER)

    time.sleep(random.uniform(10, 15))

    html_source = driver.page_source

    with open("source_code.txt", "w", encoding="utf-8") as file:
        file.write(html_source)
    print("Đã lưu mã nguồn HTML vào file source_code.txt")

except Exception:
    pass
finally:
    driver.quit()