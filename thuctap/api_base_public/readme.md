
-----------------------DOCKER------------------------ 
docker build -t demo_api_base_public .

docker run -d --restart always -v e:/Student/ThucTap/thuctap/DOCKER_DEMO/demo/data_in:/_app_/demo/data_in --name demo_api_base_public -p 55007:60074 demo_api_base_public


docker save -o demo_api_base_public.tar demo_api_base_public
docker load -i demo_api_base_public.tar
docker exec -it demo_api_base_public bash

------------------------README----------------------------
Hệ thống đánh giá thương hiệu dựa trên dữ liệu google maps

Mô tả: Đây là một webb giúp người dùng có thể tìm kiếm và đánh giá khách quan về một địa điểm nào đó thông qua các chỉ số và biểu đồ trên giao diện đơn giản và sử dụng API nôi bộ với xác thực API-Key.

Tính năng: Đánh giá một địa điểm cụ thể hoặc so sánh giữa hai địa điểm

Hướng dẫn sử dụng:
    - Đăng nhập hoặc đăng ký bằng email
    - Trang chủ, nhập tên một địa điểm và nhấn nút gửi để xem thông tin chi tiết
    - Trang so sánh, nhập hai địa điểm để hệ thống đưa ra các chỉ số và kết luận

Cài đặt:
    git clone (Backend) https://github.com/Hoang-Tri/Selenium-googlemap-/tree/master/thuctap/api_base_public
    git clone (Frontend ) https://github.com/Hoang-Tri/Selenium-googlemap-/tree/master/thuctap/website_data/laravel_12_base

    pip install -r requirements.txt


"- Cấu hình web: T
- Chỉnh sửa thông tin User: T
- Phân quyền web: T
- Đóng gói docker: F
- Cấu hình LLM: F
- API docs: T
----
Tự động chạy dữ liệu: T
So sanh 2 địa điểm: T"