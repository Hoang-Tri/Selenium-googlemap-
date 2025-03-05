import os
from langchain_google_genai import GoogleGenerativeAI, GoogleGenerativeAIEmbeddings
from langchain.prompts import PromptTemplate
from langchain.schema.runnable import RunnableSequence
from langchain_community.document_loaders import TextLoader
from langchain_community.vectorstores import FAISS
from langchain.text_splitter import RecursiveCharacterTextSplitter

# Cấu hình api
os.environ["GOOGLE_API_KEY"] = "AIzaSyAa3fQtInjmMv_M9qj2MUhcKlMaizv0QvM"

llm = GoogleGenerativeAI(model="gemini-1.5-flash")

faiss_path = "faiss_index"

# Khởi tạo Embedding
embedding = GoogleGenerativeAIEmbeddings(model="models/text-embedding-004")

if os.path.exists(faiss_path):
    vectorstore = FAISS.load_local(faiss_path, embedding, allow_dangerous_deserialization=True)
    print("FAISS đã được tải thành công!")
else:
    documents = TextLoader("data.txt", encoding="utf-8").load()
    texts = RecursiveCharacterTextSplitter(chunk_size=500, chunk_overlap=50).split_documents(documents)

    vectorstore = FAISS.from_documents(texts, embedding)
    vectorstore.save_local(faiss_path)
    print("FAISS đã được tạo và lưu!")

print("Hỏi xoay quanh về vấn đề công nghệ thông tin")

# Prompt Template
template = PromptTemplate(
    input_variables=["context", "question"],
    template=(
        "Bạn là một trợ lý AI và chỉ được trả lời dựa vào thông tin sau:\n\n"
        "{context}\n\n"
        "Nếu câu hỏi không liên quan đến dữ liệu trên, hãy trả lời: "
        "'Xin lỗi, tôi không có thông tin về câu hỏi này.'\n\n"
        "Câu hỏi: {question}\n\nTrả lời:"
    )
)

# Tạo chuỗi xử lý với LangChain
chain = RunnableSequence(template | llm)

def search_documents(query):
    docs = vectorstore.similarity_search(query, k=5)
    return "\n\n".join([doc.page_content for doc in docs]) if docs else ""

def chatbot():
    print("Chatbot AI (Nhập 'exit' để thoát)")
    while True:
        user_input = input("Bạn: ")
        if user_input.lower() == "exit":
            print("Chatbot: Hẹn gặp lại!")
            break
        # Tìm kiếm nội dung liên quan
        related_text = search_documents(user_input)

        # Xử lý phản hồi
        response = (
            chain.invoke({"context": related_text, "question": user_input})
            if related_text
            else "Xin lỗi, tôi không tìm thấy dữ liệu phù hợp."
        )
        print("Chatbot:", response)

if __name__ == "__main__":
    chatbot()
