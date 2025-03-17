from langchain_community.document_loaders import DirectoryLoader, TextLoader
from langchain.text_splitter import CharacterTextSplitter
from langchain_huggingface import HuggingFaceEmbeddings
from langchain_community.vectorstores import FAISS

loader = DirectoryLoader("Documents", glob="*.txt", loader_cls=TextLoader, loader_kwargs={"encoding": "utf-8"})
documents = loader.load()

text_splitter = CharacterTextSplitter(chunk_size = 500, chunk_overlap = 50)
texts = text_splitter.split_documents(documents)

# tao embedding su dung HuggingFaceEmbeddings
embedding = HuggingFaceEmbeddings(model_name="all-MiniLM-L6-v2")
#faiss
vectorstore = FAISS.from_documents(texts, embedding)
#luu faiss vao index_faiss
vectorstore.save_local("index_faiss")
new_vectorstore = FAISS.load_local("index_faiss", embedding, allow_dangerous_deserialization=True)

#search
query = "what is langchain faiss?"
results = new_vectorstore.similarity_search(query, k = 3)

for i, res in enumerate(results):
    print(f"Results {i+1}: {res.page_content}\n")