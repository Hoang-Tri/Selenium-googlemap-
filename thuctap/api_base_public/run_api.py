import subprocess

# Chạy ứng dụng FastAPI
if __name__ == "__main__":
    subprocess.run(["uvicorn", "app.main:app", "--host", "0.0.0.0", "--port", "60074", "--workers", "2"])

#admin - quan ly dang nhap, dang ky. laravel 2024 2025css