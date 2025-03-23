from chatbot.utils.llm import LLM  # noqa: I001
from chatbot.utils.answer_generator import AnswerGenerator
from chatbot.utils.no_answer_handler import NoAnswerHandler

from langgraph.graph import END, StateGraph, START
from chatbot.utils.graph_state import GraphState
from typing import Dict, Any

from app.config import settings


class FilesChatAgent:
    def __init__(self) -> None:
        self.llm = LLM().get_llm(settings.LLM_NAME)  # Khởi tạo mô hình ngôn ngữ
        self.answer_generator = AnswerGenerator(self.llm)  # Bộ tạo câu trả lời
        self.no_answer_handler = NoAnswerHandler(self.llm)  # Xử lý trường hợp không có câu trả lời

    def generate(self, state: GraphState) -> Dict[str, Any]:
        question = state["question"]
        generation = self.answer_generator.get_chain().invoke({"question": question})
        return {"generation": generation}

    def decide_to_generate(self, state: GraphState) -> Dict[str, Any]:
        filtered_documents = state["question"]

        if not filtered_documents:
            return "no_document"
        else:
            return "generate"

    def handle_no_answer(self, state: GraphState) -> Dict[str, Any]:
        question = state["question"]
        generation = self.no_answer_handler.get_chain().invoke({"question": question})
        return {"generation": generation}

    def get_workflow(self):
        workflow = StateGraph(GraphState)

        workflow.add_node("generate", self.generate)
        workflow.add_node("handle_no_answer", self.handle_no_answer)

        workflow.add_edge(START, "generate")
        # workflow.add_conditional_edges(
        #     "generate",
        #     self.decide_to_generate,  
        #     {
        #         "no_document": "handle_no_answer",
        #         "generate": "generate",  
        #     },
        # )
        workflow.add_edge("generate", END)

        return workflow
