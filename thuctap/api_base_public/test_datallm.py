import time, json, os
import pandas as pd
import mysql.connector
from chatbot.services.chatbot_api_agent_comment import FilesChatAgent

# Hàm kết nối đến cơ sở dữ liệu MySQL
def connect_to_database():
    return mysql.connector.connect(
        host="localhost",  
        user="root",      
        password="",       
        database="chatbot_api" 
    )

# CREATE TABLE locations (
#     id INT AUTO_INCREMENT PRIMARY KEY,
#     name TEXT,
#     data_llm TEXT
# );

# CREATE TABLE users (
#     id INT AUTO_INCREMENT PRIMARY KEY,
#     location_id INT,
#     user TEXT,
#     data_llm TEXT,
#     FOREIGN KEY (location_id) REFERENCES locations(id)
# );

# Hàm lưu thông tin địa điểm vào bảng locations
def save_location(name):
    conn = connect_to_database()
    cursor = conn.cursor()
    cursor.execute(''' 
        INSERT INTO locations (name) 
        VALUES (%s)
    ''', (name,))
    conn.commit()
    location_id = cursor.lastrowid  
    cursor.close()
    conn.close()
    return location_id

#Hàm tính toán phần trăm
def calculate_percentage(danh_sach_tu_tot, danh_sach_tu_xau):
    # Kiểm tra nếu danh_sach_tu_tot và danh_sach_tu_xau là chuỗi thì chuyển thành danh sách
    if isinstance(danh_sach_tu_tot, str):
        danh_sach_tu_tot = [word.strip() for word in danh_sach_tu_tot.split(",") if word.strip()]
    if isinstance(danh_sach_tu_xau, str):
        danh_sach_tu_xau = [word.strip() for word in danh_sach_tu_xau.split(",") if word.strip()]

    # Đảm bảo biến là danh sách, tránh lỗi
    danh_sach_tu_tot = danh_sach_tu_tot if isinstance(danh_sach_tu_tot, list) else []
    danh_sach_tu_xau = danh_sach_tu_xau if isinstance(danh_sach_tu_xau, list) else []

    # Tính tổng số từ
    total_words = len(danh_sach_tu_tot) + len(danh_sach_tu_xau)

    # Tính tỷ lệ phần trăm từ tốt và xấu
    if total_words > 0:
        percentage_tot = (len(danh_sach_tu_tot) / total_words) * 100
        percentage_xau = 100 - percentage_tot
    else:
        percentage_tot = 0
        percentage_xau = 0

    return percentage_tot, percentage_xau

# Hàm lưu thông tin người dùng vào bảng users
def save_or_update_user(location_id, user_name, data_llm, percentage_tot, percentage_xau):
    conn = connect_to_database()
    cursor = conn.cursor(buffered=True)

    try:
        data_llm_dict = json.loads(data_llm)  # Chuyển chuỗi JSON thành dictionary
    except json.JSONDecodeError:
        data_llm_dict = {"raw": data_llm}  # Nếu lỗi thì giữ nguyên dữ liệu cũ

    # Thêm tỷ lệ từ tốt và xấu vào dictionary
    data_llm_dict["percentage_tot"] = round(percentage_tot, 2)
    data_llm_dict["percentage_xau"] = round(percentage_xau, 2)

    # Chuyển lại thành JSON string để lưu vào database
    data_llm_with_percentage = json.dumps(data_llm_dict, ensure_ascii=False, indent=4)

    # Kiểm tra xem người dùng đã tồn tại chưa
    cursor.execute("SELECT id, data_llm FROM users WHERE location_id = %s AND user = %s", (location_id, user_name))
    user = cursor.fetchone()

    if user is None:
        # Nếu chưa có, thêm mới
        cursor.execute(''' 
            INSERT INTO users (location_id, user, data_llm) 
            VALUES (%s, %s, %s)
        ''', (location_id, user_name, data_llm_with_percentage))
    else:
        # Nếu đã có, cập nhật data_llm với tỷ lệ phần trăm
        updated_data_llm = user[1] + " " + data_llm_with_percentage if user[1] else data_llm_with_percentage
        cursor.execute(""" 
            UPDATE users SET data_llm = %s WHERE id = %s
        """, (updated_data_llm, user[0]))
    
    conn.commit()
    cursor.close()
    conn.close()

# Cập nhật data_llm trong bảng locations
def update_location_data_llm(location_id):
    # Lấy tất cả phản hồi của người dùng từ bảng users cho địa điểm cụ thể
    conn = connect_to_database()
    cursor = conn.cursor()

    cursor.execute(""" 
        SELECT data_llm FROM users WHERE location_id = %s
    """, (location_id,))
    
    user_responses = cursor.fetchall()

    # Biến để lưu các từ tốt, từ xấu và tỷ lệ phần trăm
    all_good_words = []
    all_bad_words = []
    total_percentage_tot = 0
    num_responses = len(user_responses)

    # Gộp tất cả phản hồi
    for response in user_responses:
        try:
            data_llm_dict = json.loads(response[0])  # Chuyển chuỗi JSON thành dictionary
            
            # Kiểm tra danh sách từ tốt
            if data_llm_dict.get("danh_sach_tu_tot"):
                if isinstance(data_llm_dict["danh_sach_tu_tot"], str):
                    all_good_words.extend(data_llm_dict["danh_sach_tu_tot"].split(", ")) 
                elif isinstance(data_llm_dict["danh_sach_tu_tot"], list):
                    all_good_words.extend(data_llm_dict["danh_sach_tu_tot"]) 
            
            # Kiểm tra danh sách từ xấu
            if data_llm_dict.get("danh_sach_tu_xau"):
                if isinstance(data_llm_dict["danh_sach_tu_xau"], str):
                    all_bad_words.extend(data_llm_dict["danh_sach_tu_xau"].split(", "))  
                elif isinstance(data_llm_dict["danh_sach_tu_xau"], list):
                    all_bad_words.extend(data_llm_dict["danh_sach_tu_xau"])  

            total_percentage_tot += data_llm_dict.get("percentage_tot", 0)

        except json.JSONDecodeError as e:
            print(f"Lỗi giải mã JSON: {e}, dữ liệu: {response[0]}")

    # Tính tỷ lệ phần trăm tổng hợp
    if num_responses > 0:
        # Tính tổng số từ của cả hai danh sách
        total_words = len(all_good_words) + len(all_bad_words)
        
        # Tính tỷ lệ phần trăm từ tốt và từ xấu
        if total_words > 0:
            percentage_tot = (len(all_good_words) / total_words) * 100
            percentage_xau = 100 - percentage_tot  # Tỷ lệ xấu = 100% - tỷ lệ tốt
        else:
            percentage_tot = 0
            percentage_xau = 0
    else:
        percentage_tot = 0
        percentage_xau = 0

    # Tạo dữ liệu gộp lại cho địa điểm
    combined_data_llm = {
        "danh_sach_tu_tot": ", ".join(all_good_words),
        "danh_sach_tu_xau": ", ".join(all_bad_words),
        "percentage_tot": round(percentage_tot, 2),
        "percentage_xau": round(percentage_xau, 2)
    }

    # Cập nhật data_llm của địa điểm trong bảng locations
    cursor.execute(""" 
        UPDATE locations SET data_llm = %s WHERE id = %s
    """, (json.dumps(combined_data_llm, ensure_ascii=False, indent=4), location_id))

    conn.commit()
    export_location_to_txt(location_id)
    cursor.close()
    conn.close()

#lấy db về lưu thành file txt
def export_location_to_txt(location_id):
    conn = connect_to_database()
    cursor = conn.cursor()

    # Lấy thông tin của location_id từ database
    cursor.execute("SELECT id, name, data_llm FROM locations WHERE id = %s", (location_id,))
    location = cursor.fetchone()
    
    if location:
        location_id, name, data_llm = location

        # Đường dẫn thư mục lưu file
        output_dir = "demo/data_in"
        os.makedirs(output_dir, exist_ok=True)  

        # Tạo file txt theo location_id
        file_name = f"{output_dir}/location_{location_id}.txt"
        with open(file_name, "w", encoding="utf-8") as file:
            file.write(f"Location ID: {location_id}\n")
            file.write(f"Location Name: {name}\n")
            file.write(f"Data LLM:\n{data_llm}\n")

        # print(f"Đã lưu {file_name}")

    cursor.close()
    conn.close()

# Hàm xử lý file CSV và gọi API để lấy dữ liệu
def process_csv_file(input_file):
    df = pd.read_csv(input_file)
    df = df.dropna(subset=['user'])
    local = []
    
    # Kiểm tra cột 'comment' có tồn tại không
    if "comment" not in df.columns:
        raise ValueError("File CSV phải có cột 'comment' chứa nội dung đánh giá!")

    # Thêm cột 'data_llm' nếu chưa có
    if "data_llm" not in df.columns:
        df["data_llm"] = None

    for index, row in df.iterrows():
        location_name = row["places"]  
        user_name = row["user"]
        comment = row["comment"]

         # Kiểm tra nếu địa điểm đã có trong database, nếu chưa thì thêm vào
        conn = connect_to_database()
        cursor = conn.cursor()
        cursor.execute("SELECT id FROM locations WHERE name = %s", (location_name,))
        location = cursor.fetchone()

        location_id = save_location(location_name) if location is None else location[0]

        # # Gọi API để lấy phản hồi
        # chat = FilesChatAgent().get_workflow().compile().invoke(input={"question": comment})
        # response = chat['generation']

        for attempt in range(3):
            try:
                chat = FilesChatAgent().get_workflow().compile().invoke(input={"question": comment})
                response = chat['generation']
                break  # Thành công thì thoát khỏi vòng lặp
            except Exception as e:
                print(f"[Lỗi khi gọi Gemini API] Thử lần {attempt + 1}/3. Lỗi: {e}")
                time.sleep(60)  # Đợi 45 giây trước khi thử lại

        # Nếu sau 3 lần vẫn lỗi, bỏ qua dòng này
        if response is None:
            print(f"Bỏ qua dòng {index} vì lỗi liên tiếp khi gọi Gemini API.")
            continue

        json_string = response.replace('```json', '').replace('```', '')

        df.at[index, "data_llm"] = json_string
        local.append(json_string)

        # =============================
        try:
            response_dict = json.loads(json_string)

            danh_sach_tu_tot = response_dict.get("danh_sach_tu_tot", "")
            danh_sach_tu_xau = response_dict.get("danh_sach_tu_xau", "")

            # Xử lý khi giá trị là None
            if danh_sach_tu_tot is None:
                danh_sach_tu_tot = ""
            if danh_sach_tu_xau is None:
                danh_sach_tu_xau = ""

            # Chuyển chuỗi thành danh sách nếu cần
            if isinstance(danh_sach_tu_tot, str):
                danh_sach_tu_tot = [word.strip() for word in danh_sach_tu_tot.split(",") if word.strip()]
            if isinstance(danh_sach_tu_xau, str):
                danh_sach_tu_xau = [word.strip() for word in danh_sach_tu_xau.split(",") if word.strip()]

            # Tính tỷ lệ từ tốt/xấu
            percentage_tot, percentage_xau = calculate_percentage(danh_sach_tu_tot, danh_sach_tu_xau)

        except json.JSONDecodeError:
            # Nếu có lỗi khi chuyển đổi JSON, đặt tỷ lệ về 0
            percentage_tot = 0
            percentage_xau = 0

        save_or_update_user(location_id, user_name, json_string, percentage_tot, percentage_xau)
        
        result = {"location": location_name, "user": user_name, "response": json_string, "percentage_tot": percentage_tot, "percentage_xau": percentage_xau}
        local.append(result)
        
        update_location_data_llm(location_id)

        time.sleep(5)  # Tránh gửi request quá nhanh

    # Ghi lại vào file CSV gốc
    df.to_csv(input_file, index=False, encoding="utf-8")
    print("Dữ liệu đã được cập nhật vào cơ sở dữ liệu thành công!")
    print(f"Đã cập nhật dữ liệu vào {input_file}")
    return local

# Gọi hàm xử lý
input_file = "data/data_in/50d04f87_Trung_Nguyên_E-Coffee_71-73A6_Hung_Phu_1_2025-04-01.csv"
local = process_csv_file(input_file)
