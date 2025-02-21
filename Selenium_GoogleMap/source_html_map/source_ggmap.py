import time
import random
import requests
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
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

# Hàm kiểm tra proxy có hoạt động hay không
def check_proxy(proxy):
    try:
        response = requests.get("https://httpbin.org/ip", proxies={"http": f"http://{proxy}", "https": f"http://{proxy}"}, timeout=5)
        if response.status_code == 200:
            return True
    except:
        pass
    return False

# # Chọn ngẫu nhiên proxy đang hoạt động
selected_proxy = None
while not selected_proxy:
    proxy = random.choice(proxy_list)
    if check_proxy(proxy):
        selected_proxy = proxy
        print(f"Đang sử dụng proxy: {selected_proxy}")

# Cấu hình ChromeOptions để tránh bị phát hiện là bot và thêm proxy xoay
chrome_options = Options()
chrome_options.add_experimental_option("excludeSwitches", ["enable-automation"])

# Khởi tạo WebDriver
driver = webdriver.Chrome(service=Service(chrome_driver_path), options=chrome_options)

try:
    url = "https://www.google.com/maps/place/B%E1%BA%BFn+Ninh+Ki%E1%BB%81u/@10.0322768,105.7856559,17z/data=!4m14!1m7!3m6!1s0x31a06298aae43e71:0xc6a64bdac582285d!2zQuG6v24gTmluaCBLaeG7gXU!8m2!3d10.0322715!4d105.7882308!16s%2Fg%2F11_ygvs77!3m5!1s0x31a06298aae43e71:0xc6a64bdac582285d!8m2!3d10.0322715!4d105.7882308!16s%2Fg%2F11_ygvs77?entry=ttu&g_ep=EgoyMDI1MDIxOC4wIKXMDSoASAFQAw%3D%3D"
    driver.get(url)

    # Sử dụng WebDriverWait để đợi trang tải hoàn toàn
    wait = WebDriverWait(driver, 30)
    wait.until(EC.presence_of_element_located((By.TAG_NAME, "body")))

    # Thêm delay ngẫu nhiên trước khi lấy mã nguồn
    time.sleep(random.uniform(10, 15))

    # Lấy toàn bộ mã nguồn HTML của trang
    html_source = driver.page_source

    # Lưu mã nguồn HTML vào file text
    with open("source_code.txt", "w", encoding="utf-8") as file:
        file.write(html_source)
    print("Đã lưu mã nguồn HTML vào file source_code.txt")

except Exception:
    pass
finally:
    driver.quit()
