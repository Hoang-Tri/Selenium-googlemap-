from chatbot.utils.retriever import Retriever
from chatbot.utils.llm import LLM  # noqa: I001
from chatbot.utils.feedback_generator import FeedBackGenerator
from langgraph.graph import END, StateGraph, START
from chatbot.utils.graph_state import GraphState
from typing import Dict, Any

# from app.config import settings
from app.ai_config import settings



class FilesChatAgent:
    def __init__(self) -> None:

        self.llm = LLM().get_llm(settings.AI)  # Khởi tạo mô hình ngôn ngữ
        self.answer_generator = FeedBackGenerator(self.llm)  # Bộ tạo câu trả lời

    def generate(self, state: GraphState) -> Dict[str, Any]:
        question = state["question"]
        generation = self.answer_generator.get_chain().invoke({"question": question})
        return {"generation": generation}

    def get_workflow(self):
        """
        Thiết lập luồng xử lý của chatbot, bao gồm các bước tìm kiếm, đánh giá và tạo câu trả lời.

        Returns:
            StateGraph: Đồ thị trạng thái của quy trình chatbot.
        """
        workflow = StateGraph(GraphState)

        workflow.add_node("generate", self.generate)  # Bước tạo câu trả lời

        workflow.add_edge(START, "generate")

        workflow.add_edge("generate", END)

        return workflow
