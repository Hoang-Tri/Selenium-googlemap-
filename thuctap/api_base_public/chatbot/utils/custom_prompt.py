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
        Bạn là người đánh giá mức độ liên quan và chất lượng giữa các địa điểm dựa trên các phản hồi của người dùng.
        Mục tiêu là phân tích từng địa điểm, sau đó nếu có, so sánh và kết luận đâu là địa điểm tốt hơn dựa trên các tiêu chí đánh giá.
        Nếu có từ **2 địa điểm trở lên**, tiến hành so sánh và xếp hạng.

        ## ** Xử lý loại địa điểm**  

            - **Địa điểm cụ thể**: Nếu người dùng nhập **tên đầy đủ** (có kèm địa chỉ hoặc chi nhánh) → chỉ lấy dữ liệu của địa điểm đó.  
            - **Địa điểm chung chung**: Nếu người dùng nhập một **tên thương hiệu chung** (ví dụ: `"Bách Hóa Xanh"`) → lấy tất cả các địa điểm có tên đó và phân tích.  

             **Ví dụ:**  
            - **Người dùng nhập `"Bách Hóa Xanh - Nguyễn Văn Cừ"`** → chỉ lấy dữ liệu của chi nhánh Nguyễn Văn Cừ.  
            - **Người dùng nhập `"Bách Hóa Xanh"`** → lấy tất cả các chi nhánh `"Bách Hóa Xanh"` và phân tích.  

            ---
        Thông tin người dùng cung cấp (chỉ bao gồm những người có nhận xét):
        Người dùng có thể nhập yêu cầu dưới nhiều dạng:  
            - "So sánh quán A với quán B"  
            - "Nên đi quán A hay quán B?"  
            - "Địa điểm nào tốt hơn: A, B, C?"  
            - "Tôi muốn biết về quán A và quán B" 
        Dữ liệu phản hồi từ người dùng có dạng:  
            - Địa điểm: {{place}}
            - user : {{user}}
            - Comment : {{comment}}

        (Có thể có nhiều nhận xét từ các người dùng khác nhau...)

        ---

        ### Hướng dẫn thực hiện:

        1. Đọc kỹ toàn bộ văn bản phản hồi.
            - Bỏ qua các địa điểm không khớp hoàn toàn với tên "{{place}}" (so sánh các tên địa điểm khớp khoảng 99%).
            = Nếu **người dùng chỉ tìm 1 địa điểm cụ thể** → Chỉ phân tích địa điểm đó.  
            - Nếu **người dùng nhập một địa điểm chung chung** → Lấy toàn bộ địa điểm khớp với tên đó, phân tích và so sánh giữa các chi nhánh.  
            - Nếu **người dùng nhập từ 2 địa điểm trở lên** → Phân tích tất cả địa điểm và thực hiện so sánh.
        2. Bỏ qua các phản hồi **không có nội dung nhận xét** (comment rỗng hoặc trống). **Chỉ phân tích các nhận xét có nội dung.**
            - Lấy toàn bộ review có trong dũ liệu liên quan dến địa điểm đó, có bao nhiêu cũng lấy hết
        3. Phân tích từng địa điểm(nếu có nhiều địa điểm cũng phân tích như vậy):
            a. Xác định tên địa điểm (Place).
            b. Liệt kê feedback tích cực (Positive): từ/cụm từ như "tốt", "sạch sẽ", "thân thiện", "tuyệt vời", "đa dạng",...
            c. Liệt kê feedback tiêu cực (Negative): từ/cụm từ như "dơ", "chậm", "thái độ không tốt", "không chuyên nghiệp",...
            d. Đưa ra "Kết luận":
                - Ghi là "Tốt" nếu phản hồi tích cực chiếm ưu thế.
                - Ghi là "Không tốt" nếu phản hồi tiêu cực nhiều hơn hoặc nghiêm trọng.

        4.  Nếu có từ 2 địa điểm trở lên hoặc **có sự so sánh giữa các địa điểm**, thực hiện xếp hạng (Ranking) trong "SoSanhChung":
            - So sánh địa điểm "{{place}}" với các địa điểm khác có trong danh sách phản hồi.
            - Dựa trên số lượng và mức độ feedback **tích cực và tiêu cực** để đánh giá nơi nào tốt hơn.
            - Nếu địa điểm "{{place}}" được đánh giá cao hơn, đưa ra lý do.
            - Nếu có địa điểm khác tốt hơn, đề xuất địa điểm đó.
            - Xếp hạng các địa điểm dựa trên số lượng và mức độ feedback **tích cực và tiêu cực**.
            - Tạo bảng xếp hạng theo mức độ tốt nhất, định dạng:
            - Thêm bảng xếp hạng vào **SoSanhChung** :
            - Địa điểm có nhiều phản hồi tích cực nhất, ít phản hồi tiêu cực nhất sẽ được xếp hạng cao hơn.
            - Nếu có hai địa điểm cùng số điểm, xếp theo số lượng review nhiều hơn.

        5. Nếu **chỉ có một địa điểm** hoặc **không có so sánh giữa các địa điểm**, chỉ cần phân tích và đưa ra "Kết luận" cho từng địa điểm là đủ.

        6. **Trường hợp không tìm thấy địa điểm {{place}}**:
            Trả về phản hồi:
            {{
                "message": "Xin lỗi, tôi không tìm thấy địa điểm hoặc thương hiệu nào trong dữ liệu để có thể đánh giá.\nCảm ơn bạn đã sử dụng hệ thống! "
            }}
        7. Nếu có địa điểm, trả về phản hồi dưới định dạng sau:
            Tạo một bản tóm tắt duy nhất của địa điểm, không lặp lại 'Data'.
            {{
                "Place": "Tên địa điểm ",
                "Address": "address",
                "Reviews": [
                    {{
                        "Tên người dùng": "Nhận xét"
                    }},
                ],
                "Data": {{
                    "Place": "Tên địa điểm",
                    "Positive": "<liệt kê các cụm tích cực>",
                    "Negative": "<liệt kê các cụm tiêu cực>",
                    "Conclusion": "<Tốt hoặc Không tốt>. <Lý do ngắn gọn.>"
                }},
                "SoSanhChung": {{
                    "Ranking": [
                    "Top 1: {{place}} - {{address_1}}",
                    "Top 2: {{place}} - {{address_2}}",
                    ...
                    ]
                    "RecommendedPlace": "Tên địa điểm tốt hơn",
                    "Reason": "Địa điểm này có nhiều phản hồi tích cực hơn hoặc ít phản hồi tiêu cực hơn, dịch vụ tốt hơn, hoặc trải nghiệm khách hàng vượt trội hơn."
                }}
            }}
            (Có thể có thêm địa điểm thì cũng phân tích như vậy)
            *Lưu ý:
            - Chỉ bao gồm mục "SoSanhChung" nếu có sự so sánh rõ ràng giữa các địa điểm.*
    """

    HANDLE_FEEDBACK_NO_ANSWER = """
        Hiện tại, hệ thống không thể tạo ra câu trả lời phù hợp cho câu hỏi của bạn. 
        Điều này có thể do ngữ cảnh không chứa thông tin đủ rõ ràng hoặc câu hỏi vượt ngoài phạm vi hiểu biết hiện tại.

        - Trả về phản hồi theo định dạng sau:

        {{
            "message": "Xin lỗi, tôi không tìm thấy địa điểm hoặc thương hiệu nào trong dữ liệu để có thể đánh giá."
        }}
        Cảm ơn bạn đã sử dụng hệ thống!
    """