import google.generativeai as genai
import tkinter as tk
from tkinter import scrolledtext
from dotenv import load_dotenv
import os

load_dotenv() 

genai.configure(api_key=os.getenv("GOOGLE_API_KEY"))

def chat_with_gemini(prompt):
    try:
        model = genai.GenerativeModel("gemini-1.5-pro")
        response = model.generate_content(prompt)
        return response.text
    except Exception as e:
        return f"Error: {e}"

def send_message():
    user_input = entry.get()
    if user_input.strip():
        chat_box.insert(tk.END, f"You: {user_input}\n", "user")
        entry.delete(0, tk.END)
        response = chat_with_gemini(user_input)
        chat_box.insert(tk.END, f"Bot: {response}\n", "bot")
        chat_box.yview(tk.END)

root = tk.Tk()
root.title("Gemini Chatbot")
root.geometry("500x600")

chat_box = scrolledtext.ScrolledText(root, wrap=tk.WORD, width=60, height=25)
chat_box.pack(padx=10, pady=10)
chat_box.tag_config("user", foreground="blue")
chat_box.tag_config("bot", foreground="black")
chat_box.configure(bg="#CCCCCC")

entry = tk.Entry(root, width=50  )
entry.pack(padx=10, pady=5)

send_button = tk.Button(root, text="Send", command=send_message)
send_button.pack(pady=5)
send_button.configure(bg="#CCCCCC")

root.mainloop()

