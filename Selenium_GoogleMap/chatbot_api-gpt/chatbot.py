import google.generativeai as genai

# Thay API Key từ Google AI Studio
genai.configure(api_key="AIzaSyAEpdaCoy8k8zJ4NA0jQp8TitPDZIpWMf4")

def chat_with_gemini(prompt):
    model = genai.GenerativeModel("gemini-pro")  # Chọn model Gemini Pro
    response = model.generate_content(prompt)
    return response.text  # Lấy nội dung phản hồi

if __name__ == "__main__":
    while True:
        user_input = input("You: ")
        if user_input.lower() in ["quit", "exit", "bye"]:
            break

        responses = chat_with_gemini(user_input)
        print("Chat: ", responses)


