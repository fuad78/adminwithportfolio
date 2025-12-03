# Portfolio Website - PHP/MySQL Version

A complete portfolio website converted from React/TypeScript to PHP/JavaScript with MySQL database and a comprehensive admin panel.

## Features

- **Frontend Pages:**
  - Home page with profile, skills carousel, and contact info
  - About page with skills showcase
  - Services page
  - Projects portfolio
  - Blog section
  - Contact form

- **Admin Panel:**
  - Secure login system
  - Dashboard with statistics
  - Manage Home section (profile, introduction, skills)
  - Manage About section
  - Manage Services (add, edit, delete)
  - Manage Projects (add, edit, delete)
  - Manage Blog posts (add, edit, delete)
  - View and manage contact form submissions

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- PHP MySQLi extension enabled

### Setup Steps

1. **Database Setup:**
   ```bash
   # Login to MySQL
   mysql -u root -p
   
   # Import the database
   mysql -u root -p < database.sql
   ```
   
   Or manually:
   - Create a database named `portfolio_db`
   - Import the `database.sql` file

2. **Configure Database Connection:**
   Edit `config.php` and update the database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'portfolio_db');
   ```

3. **Set Up Web Server:**
   - Place the project files in your web server directory (e.g., `htdocs`, `www`, or `public_html`)
   - Ensure Apache/Nginx is configured to serve PHP files
   - Make sure mod_rewrite is enabled (for clean URLs)

4. **Default Admin Credentials:**
   - Username: `admin`
   - Password: `admin123`
   
   **⚠️ IMPORTANT:** Change the default password immediately after first login!

   To change the password, you have two options:
   
   **Option 1: Use the setup script (Recommended)**
   - Navigate to: `http://localhost/newport-master/setup_password.php`
   - Enter username and new password
   - Click "Set Password"
   
   **Option 2: Use SQL (Advanced)**
   ```sql
   -- Generate hash first using PHP: password_hash('your_new_password', PASSWORD_DEFAULT)
   UPDATE admin_users SET password = '$2y$10$...your_generated_hash...' WHERE username = 'admin';
   ```

5. **Access the Website:**
   - Frontend: `http://localhost/newport-master/` or your domain
   - Admin Panel: `http://localhost/newport-master/admin/login.php`

6. **Configure SMTP (for password reset emails):**
   - Set the following environment variables or edit `config.php`:
     - `SMTP_HOST`
     - `SMTP_PORT` (default `587`)
     - `SMTP_USERNAME`
     - `SMTP_PASSWORD`
     - `SMTP_ENCRYPTION` (`tls` or `ssl`)
     - `MAIL_FROM_ADDRESS`
     - `MAIL_FROM_NAME`
   - Docker users can add these under `web.environment` in `docker-compose.yml`
   - Once configured, admins can use the "Forgot password" flow to receive reset links via email

## File Structure

```
newport-master/
├── admin/              # Admin panel files
│   ├── index.php       # Admin dashboard
│   ├── login.php       # Admin login
│   ├── logout.php      # Admin logout
│   ├── home.php        # Manage home section
│   ├── about.php       # Manage about section
│   ├── services.php    # Manage services
│   ├── projects.php    # Manage projects
│   ├── blog.php        # Manage blog posts
│   └── contacts.php    # View contact submissions
├── includes/           # Shared includes
│   ├── header.php      # Site header
│   └── footer.php      # Site footer
├── index.php           # Home page
├── about.php           # About page
├── services.php        # Services page
├── projects.php        # Projects page
├── blog.php            # Blog page
├── contact.php         # Contact page
├── config.php          # Database configuration
└── database.sql        # Database schema
```

## Usage

### Admin Panel

1. **Login:**
   - Navigate to `/admin/login.php`
   - Use default credentials: `admin` / `admin123`
   - Change password after first login!

2. **Manage Content:**
   - Use the dashboard to navigate to different sections
   - Each section allows you to add, edit, or delete content
   - Changes are saved immediately to the database

3. **Contact Submissions:**
   - View all contact form submissions in `/admin/contacts.php`
   - Update status (new, read, replied)
   - Delete submissions
   - View full message details

### Frontend

- All pages are publicly accessible
- Contact form saves submissions to the database
- Content is dynamically loaded from the database

## Security Notes

1. **Change Default Password:** Immediately change the default admin password
2. **Database Security:** Use strong database passwords
3. **File Permissions:** Set appropriate file permissions (644 for files, 755 for directories)
4. **HTTPS:** Use HTTPS in production
5. **Input Validation:** All inputs are sanitized, but consider additional validation for production

## Customization

- **Styling:** Uses Tailwind CSS via CDN. You can customize styles in each PHP file
- **Database:** All content is stored in MySQL and can be edited via admin panel
- **Images:** Update image URLs in the admin panel or directly in the database

## Troubleshooting

1. **Database Connection Error:**
   - Check `config.php` database credentials
   - Ensure MySQL service is running
   - Verify database exists

2. **Page Not Found:**
   - Check web server configuration
   - Verify file paths are correct
   - Check .htaccess if using Apache

3. **Admin Login Not Working:**
   - Verify database has admin user
   - Check password hash in database
   - Clear browser cookies/session

4. **Contact Form Not Saving:**
   - Check database connection
   - Verify `contact_submissions` table exists
   - Check PHP error logs

## Support

For issues or questions, check:
- PHP error logs
- MySQL error logs
- Browser console for JavaScript errors

## License

This project is converted from a React portfolio template. Use as needed for your portfolio website.

