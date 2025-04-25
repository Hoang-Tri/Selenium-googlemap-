import time, json, os, ast
import pandas as pd
import mysql.connector
from chatbot.services.chatbot_api_agent_comment import FilesChatAgent

# Hàm kết nối đến cơ sở dữ liệu MySQL
def connect_to_database():
    host = "localhost"
    if os.getenv("IN_DOCKER") == "true":
        host = "host.docker.internal"

    return mysql.connector.connect(
        host=host,  
        user="root",      
        password="",       
        database="db_googlemap" 
    )
# Hàm lưu thông tin địa điểm vào bảng locations
def save_location(name, address=None, scraped_date=None):
    conn = connect_to_database()
    cursor = conn.cursor()
    cursor.execute(''' 
        INSERT INTO locations (name, address, scraped_date) 
        VALUES (%s, %s, %s)
    ''', (name, address, scraped_date))
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

    total_words = len(danh_sach_tu_tot) + len(danh_sach_tu_xau)

    if total_words > 0:
        percentage_tot = (len(danh_sach_tu_tot) / total_words) * 100
        percentage_xau = 100 - percentage_tot
    else:
        percentage_tot = 0
        percentage_xau = 0

    return percentage_tot, percentage_xau

# Hàm lưu thông tin người dùng vào bảng users
def save_or_update_user(location_id, user_review, data_llm, danh_sach_tu_tot, danh_sach_tu_xau, percentage_tot, percentage_xau, star=None, creat_date=None):
    conn = connect_to_database()
    cursor = conn.cursor(buffered=True)

    data = {
        "danh_sach_tu_tot": str(danh_sach_tu_tot),
        "danh_sach_tu_xau": str(danh_sach_tu_xau),
        "GPT": {
            "phan_tram_tot": str(round(percentage_tot, 2)),
            "phan_tram_xau": str(round(percentage_xau, 2)),
        },
    }

    data_llm_with_percentage = json.dumps(data, ensure_ascii=False, indent=4)

    # Kiểm tra xem review đã tồn tại chưa
    cursor.execute("SELECT id FROM users_review WHERE location_id = %s AND user_review = %s", (location_id, user_review))
    user_review_entry = cursor.fetchone()

    if user_review_entry is None:
        cursor.execute(''' 
            INSERT INTO users_review (location_id, user_review, data_llm, star, creat_date) 
            VALUES (%s, %s, %s, %s, %s)
        ''', (location_id, user_review, data_llm_with_percentage, star, creat_date))
    else:
        cursor.execute(''' 
            UPDATE users_review 
            SET data_llm = %s, star = %s, creat_date = %s
            WHERE id = %s
        ''', (data_llm_with_percentage, star, creat_date, user_review_entry[0]))
    
    conn.commit()
    cursor.close()
    conn.close()


# Cập nhật data_llm trong bảng locations
def update_location_data_llm(location_id):
    conn = connect_to_database()
    cursor = conn.cursor()

    cursor.execute("SELECT data_llm FROM users_review WHERE location_id = %s", (location_id,))
    rows = cursor.fetchall()

    tong_tu_tot, tong_tu_xau = [], []

    for (data_llm,) in rows:
        try:
            data = json.loads(data_llm)
            
            tu_tot = ast.literal_eval(data.get("danh_sach_tu_tot", "[]"))
            tu_xau = ast.literal_eval(data.get("danh_sach_tu_xau", "[]"))

            tong_tu_tot.extend(tu_tot)
            tong_tu_xau.extend(tu_xau)
        except Exception as e:
            print(f"Lỗi JSON hoặc chuyển đổi danh sách: {e}")

    percentage_tot, percentage_xau = calculate_percentage(tong_tu_tot, tong_tu_xau)

    # Loại bỏ từ trùng lặp nếu cần
    data = {
        "danh_sach_tu_tot": str(tong_tu_tot),
        "danh_sach_tu_xau": str(tong_tu_xau),
        "GPT": {
            "phan_tram_tot": str(round(percentage_tot, 2)),
            "phan_tram_xau": str(round(percentage_xau, 2)),
        },
    }

    location_data_llm = json.dumps(data, ensure_ascii=False, indent=4)

    cursor.execute("UPDATE locations SET data_llm = %s WHERE id = %s", (location_data_llm, location_id))

    conn.commit()
    export_location_to_txt(location_id)
    cursor.close()
    conn.close()

#lấy db về lưu thành file txt
def export_location_to_txt(location_id):
    conn = connect_to_database()
    cursor = conn.cursor()

    cursor.execute("SELECT id, name, address, data_llm FROM locations WHERE id = %s", (location_id,))
    location = cursor.fetchone()

    if not location:
        print(f"Không tìm thấy địa điểm có ID: {location_id}")
        cursor.close()
        conn.close()
        return

    loc_id, name, address, data_llm = location

    try:
        parsed_data_llm = json.loads(data_llm)
    except Exception as e:
        parsed_data_llm = {"Lỗi": f"Không thể phân tích JSON: {e}"}

    output_dir = "demo/data_in"
    os.makedirs(output_dir, exist_ok=True)
    file_path = os.path.join(output_dir, f"location_{loc_id}.txt")

    with open(file_path, "w", encoding="utf-8") as file:
        file.write(f"Location ID: {loc_id}\n")
        file.write(f"Location Name: {name}\n")
        file.write(f"Address: {address}\n\n")
        file.write("Data LLM:\n")
        file.write(json.dumps(parsed_data_llm, ensure_ascii=False, indent=4))

    cursor.close()
    conn.close()


# Hàm xử lý file CSV và gọi API để lấy dữ liệu
def process_csv_file(input_file):
    df = pd.read_csv(input_file)
    df = df.dropna(subset=['user'])
    local = []
    
    # Kiểm tra cột 'comment' và 'address' có tồn tại không
    if "comment" not in df.columns:
        raise ValueError("File CSV phải có cột 'comment' chứa nội dung đánh giá!")
    if "address" not in df.columns:
        raise ValueError("File CSV phải có cột 'address' chứa địa chỉ!")

    # Thêm cột 'data_llm' nếu chưa có
    if "data_llm" not in df.columns:
        # df["data_llm"] = None
        df["data_llm"] = pd.Series(dtype="object")

    for index, row in df.iterrows():
        location_name = row["places"]  
        user_name = row["user"]
        star = row["star"] if "star" in row else None
        creat_date = row["creat_date"] if "creat_date" in row else None
        comment = row["comment"]
        address = row["address"]  
        scraped_date = row["scraped_date"] if "scraped_date" in row else None 

        # Kiểm tra nếu địa điểm đã có trong database, nếu chưa thì thêm vào
        conn = connect_to_database()
        cursor = conn.cursor()
        cursor.execute("SELECT id FROM locations WHERE name = %s", (location_name,))
        location = cursor.fetchone()

        location_id = save_location(location_name, address, scraped_date) if location is None else location[0]

        for attempt in range(3):
            try:
                chat = FilesChatAgent().get_workflow().compile().invoke(input={"question": comment})
                response = chat['generation']
                break  
            except Exception as e:
                print(f"[Lỗi khi gọi Gemini API] Thử lần {attempt + 1}/3. Lỗi: {e}")
                time.sleep(60)  

        # Nếu sau 3 lần vẫn lỗi, bỏ qua dòng này
        if response is None:
            print(f"Bỏ qua dòng {index} vì lỗi liên tiếp khi gọi Gemini API.")
            continue

        json_string = response.replace('```json', '').replace('```', '')

        df["data_llm"] = df["data_llm"].astype("object")
        df.at[index, "data_llm"] = json_string
        local.append(json_string)

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
            percentage_tot = 0
            percentage_xau = 0

        save_or_update_user(
            location_id, user_name, json_string,
            danh_sach_tu_tot, danh_sach_tu_xau,
            percentage_tot, percentage_xau,
            star, creat_date
            )
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
# input_file = "data/data_in/6856d99c_Trường_Phổ_Thông_FPT_Cần_Thơ_2025-04-06.csv"
# local = process_csv_file(input_file)
