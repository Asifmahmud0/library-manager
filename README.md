# Library Manager Plugin

A WordPress plugin to manage a library of books using a custom database table, REST API, and a React-based Admin Dashboard.

## 📦 Features
- **Custom Database Table:** Stores books independently from standard WP Posts.
- **REST API:** Secure endpoints for CRUD operations.
- **React Admin UI:** Single Page Application (SPA) dashboard to manage books.
- **Secure:** Uses Nonces, Prepared Statements, and Capability Checks.

## 🚀 Installation Steps
1. Download the `library-manager.zip` file.
2. Go to your WordPress Admin > **Plugins** > **Add New** > **Upload Plugin**.
3. Upload the ZIP file and click **Install Now**.
4. Click **Activate**.
5. A new menu item **"Library Manager"** will appear in the sidebar.

## 🛠 How to Build the React App (Development)
If you wish to modify the React source code:

1. Ensure `Node.js` and `npm` are installed.
2. Navigate to the plugin directory in your terminal:
   ```bash
   cd wp-content/plugins/library-manager


🔌 REST API Documentation
Base URL: /wp-json/library/v1
Method  	         Endpoint	           Description	          Access
GET	/books	Retrieve list of books	                              Public
GET	/books/{id}	Retrieve single book details	            Public
POST	/books	Create a new book	                        Admin only
PUT	/books/{id}	Update an existing book	                Admin only
DELETE	/books/{id}	Delete a book	                        Admin only


🗄️ Database Schema
Table: wp_library_books (Prefix may vary)
Column	Type	Description
id	BIGINT (PK)	Auto-increment ID
title	VARCHAR(255)	Book Title
description	LONGTEXT	Book Description
author	VARCHAR(255)	Author Name
publication_year	INT	Year of Release
status	VARCHAR(20)	available, borrowed, unavailable
created_at	DATETIME	Creation Timestamp