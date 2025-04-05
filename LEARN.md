# Learn about Scoop 💬

Welcome to **Scoop**! This document will guide you through understanding how the project works and how to contribute. Whether you're a developer or a user, you'll find useful information here.

---

## 🚀 Project Overview

**Scoop** is a PHP/MySQL marketplace that connects buyers and sellers directly via WhatsApp, without the need for a shopping cart. It’s designed to be simple, user-friendly, and efficient.

Key Features:
- **Product showcase**: Display products with images, descriptions, and prices.
- **WhatsApp integration**: Sellers and buyers can contact each other directly via WhatsApp.
- **Admin panel**: Manage products, listings, and updates.
- **Search**: Users can search for products by category or keyword.

---

## 📖 How It Works

1. **Frontend**:
   - The website showcases products with images, descriptions, and prices.
   - The "WhatsApp" button allows users to contact the seller directly, bypassing the need for a cart system.

2. **Backend**:
   - Written in **PHP** and **MySQL**, the backend handles product listings and user interactions.
   - The database (`scoopbd`) stores product data, and admins can update listings through the admin panel.

3. **Database**:
   - The database file `scoopbd.sql` is provided in the root directory.
   - It includes tables for managing product information and user data (if needed).

---

## 🛠️ Setting Up the Project

To set up **Scoop** on your local machine, follow these steps:

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/yourusername/scoop.git
   cd scoop
   ```

2. **Import the Database**:
   - Import `scoopbd.sql` into your MySQL database using phpMyAdmin or MySQL CLI.
   - This will create the `scoopbd` database with necessary tables.

3. **Configure the Database Connection**:
   - Edit the `/includes/config.php` file and configure the following:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     define('DB_NAME', 'scoopbd');
     ```

4. **Launch the Site**:
   - Host the project on a local server (like **XAMPP**, **MAMP**, or **WAMP**).
   - The site will be available at `localhost/scoop`.

---

## 🤝 Contributing

We welcome contributions to **Scoop**! If you'd like to help improve the project, follow these steps:

1. **Fork the Repository**: Create your own fork of the project to make changes.
2. **Create a Branch**: Always create a new branch for your changes.
   ```bash
   git checkout -b my-feature
   ```
3. **Make Changes**: Implement your changes or add new features.
4. **Create a Pull Request**: Once you’re done, open a pull request with a description of your changes.

---

## 📄 License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

---

## 💡 Additional Resources

- [PHP Official Documentation](https://www.php.net/docs.php)
- [MySQL Official Documentation](https://dev.mysql.com/doc/)
- [WhatsApp API Documentation](https://www.twilio.com/docs/whatsapp)

---

Happy coding! 👨‍💻👩‍💻
