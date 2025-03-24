import requests

def call_chat_ingestion_api(place: str, api_key: str):
    url = "http://localhost:60074/base/chat-ingestion/"  # Thay đổi nếu server chạy ở port khác
    headers = {
        "accept": "application/json",
        "API-Key": api_key
    }
    data = {
        "Place": place
    }

    try:
        response = requests.post(url, data=data, headers=headers)
        response.raise_for_status()  # Ném lỗi nếu HTTP status là lỗi

        result = response.json()
        return result["data"]

    except requests.exceptions.HTTPError as http_err:
        print(f"HTTP error: {http_err}")
    except Exception as err:
        print(f"Lỗi khác: {err}")
def main():
    api_key = "gnqAYAVeDMR7dzocBfH5j89O4oXUPpEa" 
    place = input("Nhập tên địa điểm cần hỏi: ")

    result = call_chat_ingestion_api(place, api_key)

    if result:
        print("Phản hồi từ chatbot:")
        print(result)

if __name__ == "__main__":
    main()