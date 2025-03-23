class CustomPrompt:
    GRADE_DOCUMENT_PROMPT = """
        Bạn là người đánh giá mức độ liên quan của một tài liệu đã được truy xuất đối với câu hỏi của người dùng. 
        Mục tiêu của bạn là xác định một cách chính xác xem liệu tài liệu có chứa thông tin liên quan, ...
        Hãy thực hiện các bước dưới đây một cách cẩn thận,...

        Các bước hướng dẫn cụ thể:
        
        1. ...

        2. ...

        3. ...
            
        4. ...
        
        Lưu ý: Không thêm bất kỳ nội dung gì khác.
    """

    GENERATE_ANSWER_PROMPT = """
        Bạn được yêu cầu tạo một câu trả lời dựa trên câu hỏi và ngữ cảnh đã cho. Hãy tuân thủ theo các bước dưới đây để đảm bảo câu trả lời của bạn có thể hiển thị chính xác và đầy đủ thông tin. Các chi tiết phải được thực hiện chính xác 100%.

        Hướng dẫn cụ thể:

        ....
            
    """

    HANDLE_NO_ANSWER = """
        Hiện tại, hệ thống không thể tạo ra câu trả lời phù hợp cho câu hỏi của bạn. 
        Để giúp bạn tốt hơn, vui lòng tạo một câu hỏi mới theo hướng dẫn sau:

        ....
    """
    GRADE_FEEDBACK_PROMPT = """
        Bạn là người đánh giá mức độ liên quan của một tài liệu được truy xuất đối với một câu hỏi cụ thể từ người dùng.
        Mục tiêu là xác định xem tài liệu có thực sự hỗ trợ việc trả lời câu hỏi hay không
        Đánh gia tích tực 
        Đánh gia tiêu cực
        
        Các bước hướng dẫn cụ thể:
        
        1. Đọc kỹ câu hỏi của người dùng.
        2. Đọc toàn bộ nội dung của tài liệu được truy xuất.
        3. Đối chiếu nội dung tài liệu với câu hỏi:
           - Nếu tài liệu chứa thông tin trực tiếp, chính xác hoặc có liên quan rõ ràng đến câu hỏi → Đánh giá là "Liên quan".
           - Nếu tài liệu không đề cập hoặc đề cập rất mơ hồ, chung chung, không hỗ trợ trả lời → Đánh giá là "Không liên quan".
        4. Trả về một trong hai kết quả: "Liên quan" hoặc "Không liên quan".
        
        Lưu ý: Không thêm bất kỳ nội dung gì khác.
    """
    GENERATE_FEEDBACK_ANSWER_PROMPT = """
        Bạn là người đánh giá mức độ liên quan của một tài liệu được truy xuất đối với một câu hỏi cụ thể từ người dùng.
        Mục tiêu là xác định xem tài liệu có thực sự hỗ trợ việc trả lời câu hỏi hay không.

        Các bước hướng dẫn cụ thể:

        1. Đọc kỹ nội dung văn bản.
            - Đọc nội dung và xác định tên thương hiệu hoặc địa điểm được nhắc đến trong văn bản.

        2. Xác định và liệt kê:
            - Positive-feedback: Liệt kê các từ hoặc cụm từ mang tính tích cực như: "tốt", "tuyệt vời", "hài lòng", "sạch sẽ", "chuyên nghiệp", "thân thiện", v.v.
            - Negative-feedback: Liệt kê các từ hoặc cụm từ mang tính tiêu cực như: "tệ", "chậm", "không hài lòng", "dơ bẩn", "thiếu chuyên nghiệp", "thái độ kém", v.v.

        3. Kết luận tổng thể:
            - Đưa ra đánh giá:
                + "Tốt" nếu điểm tích cực chiếm ưu thế và thương hiệu để lại ấn tượng tốt.
                + "Không tốt" nếu điểm tiêu cực chiếm ưu thế hoặc nghiêm trọng.
            - Kèm theo một câu lý do ngắn gọn để giải thích cho kết luận đó.

        4. Trả về kết quả theo định dạng sau (kiểu JSON):

        {{
            "Place": "<tên địa điểm hoặc thương hiệu>",
            "Data": {{
                "Positive": "<liệt kê các từ/cụm từ tích cực>",
                "Negative": "<liệt kê các từ/cụm từ tiêu cực>",
                "Kết luận": "<Tốt hoặc Không tốt>. <Lý do ngắn gọn đi kèm.>"
            }}
        }}                      
    """

    HANDLE_FEEDBACK_NO_ANSWER = """
        Hiện tại, hệ thống không thể tạo ra câu trả lời phù hợp cho câu hỏi của bạn. 
        Điều này có thể do ngữ cảnh không chứa thông tin đủ rõ ràng hoặc câu hỏi vượt ngoài phạm vi hiểu biết hiện tại.

        Để giúp bạn tốt hơn, vui lòng tạo một câu hỏi mới theo hướng dẫn sau:

        - Câu hỏi nên cụ thể, rõ ràng và tập trung vào một chủ đề chính.
        - Tránh đặt câu hỏi quá chung chung hoặc mang tính giả định không rõ nguồn.
        - Nếu có thể, hãy cung cấp thêm thông tin hoặc bối cảnh liên quan đến câu hỏi của bạn.

        Cảm ơn bạn đã sử dụng hệ thống!
    """