import tkinter as tk
from tkinter import messagebox

def on_click(button_text):
    current_text = entry.get()

    if button_text == "C":
        entry.delete(0, tk.END)
    elif button_text == "=":
        try:
            result = eval(current_text)
            entry.delete(0, tk.END)
            entry.insert(tk.END, str(result))
        except Exception as e:
            messagebox.showerror("Hata", "Geçersiz İşlem")
            entry.delete(0, tk.END)
    else:
        entry.insert(tk.END, button_text)

# Ana pencereyi oluştur
root = tk.Tk()
root.title("Modern Hesap Makinesi")
root.geometry("400x600")
root.configure(bg="#2E2E2E")

# Giriş alanı
entry = tk.Entry(root, font=("Arial", 24), borderwidth=2, relief="solid", justify="right", bg="#3B3B3B", fg="white")
entry.pack(pady=20, padx=10, fill="x")

# Düğme çerçevesi
button_frame = tk.Frame(root, bg="#2E2E2E")
button_frame.pack(pady=10, padx=10)

# Düğmeler
buttons = [
    '7', '8', '9', '/',
    '4', '5', '6', '*',
    '1', '2', '3', '-',
    'C', '0', '=', '+'
]

row_val = 0
col_val = 0

for button in buttons:
    action = lambda x=button: on_click(x)
    tk.Button(button_frame, text=button, font=("Arial", 18), bg="#505050", fg="white", height=2, width=4, command=action).grid(row=row_val, column=col_val, padx=5, pady=5)
    col_val += 1
    if col_val > 3:
        col_val = 0
        row_val += 1

# Pencereyi çalıştır
root.mainloop()