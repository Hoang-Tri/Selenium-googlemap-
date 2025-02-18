from selenium import webdriver

# Khởi tạo trình duyệt
driver = webdriver.Chrome()
driver.get("https://www.google.com/maps/place/Coffee+Ph%E1%BB%91+79")

# Lấy toàn bộ source code và lưu vào file
html_source = driver.page_source
with open("source_code.txt", "w", encoding="utf-8") as file:
    file.write(html_source)

driver.quit()
