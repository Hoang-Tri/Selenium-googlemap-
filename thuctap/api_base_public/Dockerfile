# Sử dụng image chính thức của Ubuntu 22.04
FROM ubuntu:22.04 AS builder


# Thiết lập biến môi trường để tránh các thông báo trong quá trình cài đặt
ENV DEBIAN_FRONTEND=noninteractive


# Cập nhật danh sách các gói và cài đặt các gói cần thiết
RUN apt-get update && \
    apt-get install -y python3 python3-pip git nano build-essential \
    libatlas-base-dev libffi-dev libcurl4-openssl-dev libssl-dev \
    pkg-config libmysqlclient-dev fonts-liberation wget && \
    wget -O google-chrome.deb https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb && \
    apt-get install -y ./google-chrome.deb && \
    rm google-chrome.deb && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*


# Thiết lập thư mục làm việc trong container
WORKDIR /_app_


# Sao chép toàn bộ mã nguồn vào container
COPY app /_app_/app
COPY chatbot /_app_/chatbot
COPY data /_app_/data
COPY demo /_app_/demo
COPY ggmap /_app_/ggmap
COPY ingestion /_app_/ingestion
COPY .env /_app_/.env
COPY demo.py /_app_/demo.py
COPY requirements.txt /_app_/requirements.txt
COPY test_datallm.py /_app_/test_datallm.py
COPY run_api.py /_app_/run_api.py
COPY chromedriver-linux64 /_app_/chromedriver-linux64
COPY chromedriver-win64 /_app_/chromedriver-win64

ENV IN_DOCKER=true

# Dùng mirror nhanh hơn khi pip install
RUN pip3 install --no-cache-dir --upgrade -r /_app_/requirements.txt


# Xóa thư mục .venv nếu có
RUN rm -rf /_app_/venv || true


# Lệnh để chạy ứng dụng
CMD ["python3", "run_api.py"]