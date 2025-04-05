# Scoop 🛍️📱
**A simple PHP/MySQL marketplace connecting buyers and sellers via WhatsApp.**  
No cart — just click, chat, and deal! 💬🤝

![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?logo=php)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?logo=mysql)

---

## ✨ Features
- 🖼️ Product showcase with image, description, and price
- 📲 Direct contact via WhatsApp button
- 🧰 Admin panel for managing product listings
- 🔍 Lightweight search by category or keyword

---

## 🛠️ Installation

1. **📥 Clone the repository**  
   ```bash
   git clone https://github.com/yourusername/scoop.git
   cd scoop
   ```

2. **🗄️ Import the database**
   - File: `scoopbd.sql` (located in the root folder)
   - Import it using phpMyAdmin or command line  
     It will create the database `scoopbd`

3. **⚙️ Configure the database connection**  
   Edit the file `/includes/config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'scoopbd');
   ```

4. ✅ **Done!**  
   Host the folder on your local server (like XAMPP, MAMP...) — no build or compilation needed.

---

## 🗂️ Project Structure
```
/scoopbd.sql         # MySQL export file
/includes/           # DB config and utility functions
/admin/              # Admin dashboard
/uploads/            # Product images
index.php            # Main product listing
```

---

## 🌐 Live Demo
🔗 [Add your demo link here]

---

> 💡 “No cart, no complexity — just real people making real deals.” ✨

---

## 🤝 Contributions
👥 Pull requests are welcome! For major changes, please open an issue first.

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

Created by: **Dayifour** and **Doubafly**.
