import os
from langchain_google_genai import GoogleGenerativeAI
from langchain.prompts import PromptTemplate
from langchain.schema.runnable import RunnableSequence

os.environ["GOOGLE_API_KEY"] = "AIzaSyAa3fQtInjmMv_M9qj2MUhcKlMaizv0QvM"

llm = GoogleGenerativeAI(model="gemini-1.5-flash")

template = PromptTemplate(
    input_variables=["question"],
    template="Bạn là một trợ lý AI. Hãy lặp lại câu hỏi và đưa ra câu trả lời chi tiết.\n\nCâu hỏi: {question}\n\nTrả lời:"
)
# Tạo chuỗi xử lý với LangChain
chain = RunnableSequence(template | llm)

def chatbot():
    print("Chatbot AI (Nhập 'exit' để thoát)")
    while True:
        user_input = input("Bạn: ")
        if user_input.lower() == "exit":
            print("Chatbot: Hẹn gặp lại!")
            break
        response = chain.invoke({"question": user_input})
        print("Chatbot:", response)

if __name__ == "__main__":
    chatbot()
