import time
import getpass
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.options import Options

chrome_driver_path = r"E:\Student\New folder\chromedriver-win32\chromedriver.exe"

chrome_options = Options()
chrome_options.add_argument("--remote-debugging-port=9222")  
chrome_options.add_argument("--disable-blink-features=AutomationControlled")  
chrome_options.add_experimental_option("excludeSwitches", ["enable-automation"])
chrome_options.add_experimental_option("useAutomationExtension", False)
chrome_options.add_argument("user-agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36")

driver = webdriver.Chrome(service=Service(chrome_driver_path), options=chrome_options)

try:
    driver.get("https://www.google.com/maps")
    wait = WebDriverWait(driver, 10)

    sign_in_button = wait.until(EC.element_to_be_clickable((By.XPATH, "//a[contains(text(),'Sign in')]")))
    sign_in_button.click()

    email_input = wait.until(EC.presence_of_element_located((By.ID, "identifierId")))
    email_input.send_keys("hoangtea2110421@gmail.com")
    
    driver.find_element(By.ID, "identifierNext").click()

    password_input = wait.until(EC.presence_of_element_located((By.NAME, "Passwd")))
    password = getpass.getpass("Nhập mật khẩu Gmail: ") 
    password_input.send_keys(password)

    driver.find_element(By.ID, "passwordNext").click()

    wait.until(EC.presence_of_element_located((By.XPATH, "//a[contains(text(),'Profile')]")))
    print("Đăng nhập thành công!")

except Exception as e:
    print("Lỗi:", e)

finally:
    time.sleep(5)
    driver.quit()
