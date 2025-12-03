# Portfolio Website - Complete Setup Guide

## 🚀 Quick Setup for Windows

### Step 1: Install Requirements
1. **XAMPP/WAMP/MAMP** (includes PHP and MySQL)
   - Download from: https://www.apachefriends.org/ (XAMPP)
   - Install and start Apache and MySQL services

2. **Verify PHP is working**
   - Open browser: `http://localhost`
   - You should see XAMPP dashboard

### Step 2: Setup Database

#### Option A: Using MySQL Command Line (Recommended)

1. Open **Command Prompt** or **PowerShell** as Administrator
2. Navigate to MySQL bin directory (usually `C:\xampp\mysql\bin`)
3. Login as root:
   ```bash
   mysql -u root -p
   ```
   (Press Enter if no password, or enter your MySQL root password)

4. Run the setup script:
   ```sql
   source C:\Users\Fuad System Engineer\Desktop\newport-master\newport-master\setup_database.sql
   ```
   
   OR copy and paste the entire contents of `setup_database.sql` into MySQL command line

#### Option B: Using phpMyAdmin (Easier)

1. Open browser: `http://localhost/phpmyadmin`
2. Click on **SQL** tab
3. Copy entire contents of `setup_database.sql` file
4. Paste into SQL textarea
5. Click **Go**

### Step 3: Place Files in Web Server

1. Copy the entire `newport-master` folder to:
   - **XAMPP**: `C:\xampp\htdocs\newport-master`
   - **WAMP**: `C:\wamp64\www\newport-master`
   - **MAMP**: `C:\MAMP\htdocs\newport-master`

2. Or if you want it in root:
   - Copy all files from `newport-master` folder to `htdocs` folder
   - Access via: `http://localhost/`

### Step 4: Access Your Website

**Frontend (Public):**
- URL: `http://localhost/newport-master/`
- Or: `http://localhost/` (if files are in root)

**Admin Panel:**
- URL: `http://localhost/newport-master/admin/login.php`
- Or: `http://localhost/admin/login.php` (if files are in root)

---

## 🔐 Admin Panel Credentials

**Username:** `admin`  
**Password:** `Admin@2024`

⚠️ **IMPORTANT:** Change this password immediately after first login!

To change password:
1. Login to admin panel
2. Go to: `http://localhost/newport-master/setup_password.php`
3. Enter new password
4. Click "Set Password"

---

## 📊 Database Credentials

**Database Name:** `portfolio_db`  
**Database User:** `portfolio_user`  
**Database Password:** `Portfolio@2024`

These are configured in `config.php`. If you changed them during setup, update `config.php` accordingly.

---

## ✅ Verify Everything Works

1. **Check Database Connection:**
   - Open: `http://localhost/newport-master/`
   - If you see the homepage, database is connected!

2. **Test Admin Panel:**
   - Go to: `http://localhost/newport-master/admin/login.php`
   - Login with: `admin` / `Admin@2024`
   - You should see the dashboard

3. **Test Adding Content:**
   - Go to "Blog" section
   - Click "Add New Blog Post"
   - Fill in the form and submit
   - Check if it appears on: `http://localhost/newport-master/blog.php`

4. **Test Adding Projects:**
   - Go to "Projects" section
   - Click "Add New Project"
   - Fill in the form and submit
   - Check if it appears on: `http://localhost/newport-master/projects.php`

---

## 🛠️ Troubleshooting

### Database Connection Error
- Check if MySQL service is running (XAMPP Control Panel)
- Verify credentials in `config.php` match `setup_database.sql`
- Make sure database `portfolio_db` exists

### Page Not Found (404)
- Check file paths in browser address bar
- Verify files are in correct `htdocs` folder
- Try: `http://localhost/newport-master/index.php`

### Admin Login Not Working
- Verify admin user exists in database:
  ```sql
  SELECT * FROM portfolio_db.admin_users;
  ```
- Reset password using `setup_password.php`

### Can't Add Blog/Projects
- Check database connection
- Verify tables exist: `blog_posts`, `projects`
- Check PHP error logs in XAMPP

---

## 📝 Admin Panel Features

✅ **Home Section** - Edit profile, name, title, introduction, skills  
✅ **About Section** - Edit about page content  
✅ **Services** - Add, edit, delete services  
✅ **Projects** - Add, edit, delete projects  
✅ **Blog** - Add, edit, delete blog posts  
✅ **Contact Submissions** - View all contact form submissions, mark as read/replied

---

## 🌐 Making Site Live (Production)

1. **Get Web Hosting** (with PHP and MySQL support)
2. **Upload Files** via FTP/cPanel File Manager
3. **Create Database** in hosting control panel
4. **Import Database:**
   - Export from local: `mysqldump -u portfolio_user -p portfolio_db > portfolio_backup.sql`
   - Import to hosting database
5. **Update `config.php`** with hosting database credentials
6. **Set Permissions:**
   - Files: 644
   - Folders: 755
7. **Change Admin Password** immediately!

---

## 📞 Support

If you encounter issues:
1. Check PHP error logs
2. Check MySQL error logs
3. Verify all files are uploaded correctly
4. Ensure database tables exist

---

## 🎉 You're All Set!

Your portfolio website is now ready! You can:
- Add/edit content through admin panel
- View contact form submissions
- Customize all sections
- Add unlimited blog posts and projects

**Admin Panel URL:** `http://localhost/newport-master/admin/login.php`  
**Username:** `admin`  
**Password:** `Admin@2024`



