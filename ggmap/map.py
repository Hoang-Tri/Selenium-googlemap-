import time
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options

# Đường dẫn tới ChromeDriver
chrome_driver_path = r"E:\Student\New folder\chromedriver-win32\chromedriver.exe"

# Cấu hình ChromeOptions để tránh bị phát hiện là bot
chrome_options = Options()
chrome_options.add_argument("--disable-blink-features=AutomationControlled")
chrome_options.add_experimental_option("excludeSwitches", ["enable-automation"])
chrome_options.add_experimental_option("useAutomationExtension", False)
chrome_options.add_argument("user-agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36")

# Khởi tạo WebDriver
driver = webdriver.Chrome(service=Service(chrome_driver_path), options=chrome_options)

try:
    # Vào trang Google Maps của địa điểm chỉ định
    url = "https://www.google.com/maps/place/Coffee+Ph%E1%BB%91+79"
    driver.get(url)

    # Đợi một lúc cho trang tải hoàn toàn
    time.sleep(5)

    # Lấy toàn bộ mã nguồn HTML của trang
    html_source = driver.page_source

    # Lưu mã nguồn HTML vào file text
    with open("source_code.txt", "w", encoding="utf-8") as file:
        file.write(html_source)

    print("Đã lưu mã nguồn HTML vào file source_code.txt")

except Exception as e:
    print("Đã xảy ra lỗi:", e)

finally:
    # Đóng trình duyệt
    driver.quit()
