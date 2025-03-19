import os
from dotenv import load_dotenv
from langchain_google_genai import GoogleGenerativeAI
from langchain.prompts import PromptTemplate
from langchain.schema.runnable import RunnableSequence

load_dotenv()

llm = GoogleGenerativeAI(model="gemini-1.5-flash", api_key=os.getenv("GOOGLE_API_KEY"))

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
