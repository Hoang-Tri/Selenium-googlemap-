from chatbot.utils.retriever import Retriever
from chatbot.utils.llm import LLM  # noqa: I001
from chatbot.utils.answer_generator import AnswerGenerator
from chatbot.utils.no_answer_handler import NoAnswerHandler

from langgraph.graph import END, StateGraph, START
from chatbot.utils.graph_state import GraphState
from typing import Dict, Any

# from app.config import settings
from app.ai_config import settings


class FilesChatAgent:
    def __init__(self,path_vector_store) -> None:
        self.retriever = Retriever(settings.AI).set_retriever(path_vector_store)  # Khởi tạo trình tìm kiếm tài liệu
        self.llm = LLM().get_llm(settings.AI)  # Khởi tạo mô hình ngôn ngữ
        self.answer_generator = AnswerGenerator(self.llm)  # Bộ tạo câu trả lời

    def retrieve(self, state: GraphState) -> Dict[str, Any]:
        """
        Tìm kiếm các tài liệu liên quan đến câu hỏi.

        Args:
            state (GraphState): Trạng thái hiện tại chứa câu hỏi.

        Returns:
            dict: Chứa danh sách tài liệu và câu hỏi.
        """
        question = state["question"]
        documents = self.retriever.get_documents(question, int(settings.NUM_DOC))
        return {"documents": documents, "question": question}
    def generate(self, state: GraphState) -> Dict[str, Any]:
        question = state["question"]
        documents = state["documents"]
        context = "\n\n".join(doc.page_content for doc in documents)  # Ghép nội dung các tài liệu thành một đoạn văn
        generation = self.answer_generator.get_chain().invoke({"question": question, "context": context})
        return {"generation": generation}

    def get_workflow(self):
        """
        Thiết lập luồng xử lý của chatbot, bao gồm các bước tìm kiếm, đánh giá và tạo câu trả lời.

        Returns:
            StateGraph: Đồ thị trạng thái của quy trình chatbot.
        """
        workflow = StateGraph(GraphState)

        workflow.add_node("retrieve", self.retrieve)  # Bước tìm kiếm tài liệu
        workflow.add_node("generate", self.generate)  # Bước tạo câu trả lời

        workflow.add_edge(START, "retrieve")
        workflow.add_edge("retrieve", "generate")


        workflow.add_edge("generate", END)

        return workflow
