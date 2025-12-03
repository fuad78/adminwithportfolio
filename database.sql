-- Database Schema for Portfolio Website
CREATE DATABASE IF NOT EXISTS portfolio_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE portfolio_db;

-- Admin Users Table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Home Section Content
CREATE TABLE IF NOT EXISTS home_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profile_image VARCHAR(255) DEFAULT 'https://i.postimg.cc/CKXsRKMZ/IMG-20240726-180920.jpg',
    name VARCHAR(100) DEFAULT 'KM Fuad Hasan',
    title VARCHAR(200) DEFAULT 'System Administrator & DevOps Engineer',
    introduction TEXT,
    email VARCHAR(100) DEFAULT 'fuadxeonbd@gmail.com',
    phone VARCHAR(20) DEFAULT '01872841507',
    location VARCHAR(100) DEFAULT 'Dhaka, Bangladesh',
    cv_link VARCHAR(500) DEFAULT 'https://drive.google.com/file/d/1BzjPG9y8S8yC9QGx6wo0h0Ch3a44rSv4/view?usp=sharing',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Skills Table
CREATE TABLE IF NOT EXISTS skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(50) DEFAULT 'Server',
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- About Section Content
CREATE TABLE IF NOT EXISTS about_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) DEFAULT 'About Me',
    subtitle VARCHAR(300) DEFAULT 'Experienced System Administrator and DevOps Engineer',
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Services Table
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    icon VARCHAR(50) DEFAULT 'Server',
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Projects Table
CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    image VARCHAR(500),
    tags VARCHAR(500),
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Blog Posts Table
CREATE TABLE IF NOT EXISTS blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    excerpt TEXT,
    content TEXT,
    image VARCHAR(500),
    author VARCHAR(100) DEFAULT 'KM Fuad Hasan',
    date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Contact Submissions Table
CREATE TABLE IF NOT EXISTS contact_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Social Media Links
CREATE TABLE IF NOT EXISTS social_links (
    id INT AUTO_INCREMENT PRIMARY KEY,
    platform VARCHAR(50) NOT NULL,
    url VARCHAR(500) NOT NULL,
    icon VARCHAR(50) NOT NULL,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Default Admin User (password: admin123 - change this!)
-- Default password hash for 'admin123' - generated with PHP password_hash()
-- To generate a new hash, use: password_hash('your_password', PASSWORD_DEFAULT)
-- Or use setup_password.php script to set/reset password
INSERT INTO admin_users (username, password, email) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@example.com');

-- Insert Default Home Content
INSERT INTO home_content (introduction) VALUES 
('I have extensive experience managing VPS configurations and hosting applications at XeonBD, ensuring smooth operations for clients. I also specialize in hardware troubleshooting to maintain optimal performance and reliability.');

-- Insert Default Skills
INSERT INTO skills (name, description, icon, display_order) VALUES
('Server Management', 'Expert in Linux/Unix systems, server management, and infrastructure optimization', 'Server', 1),
('Technical Support', 'Specialized in hardware troubleshooting and performance optimization', 'Award', 2),
('DevOps Engineering', 'Proficient in CI/CD, Docker, Kubernetes, and cloud platforms', 'Terminal', 3),
('Cloud Technologies', NULL, 'Cloud', 4),
('Docker', NULL, 'Box', 5),
('Kubernetes', NULL, 'Cpu', 6),
('GitHub Actions', NULL, 'GitPullRequest', 7),
('Python programming', NULL, 'Code2', 8),
('C Programming', NULL, 'Code2', 9);

-- Insert Default About Content
INSERT INTO about_content (description) VALUES 
('As a Support Engineer at XeonBD, I specialize in system administration and DevOps practices, ensuring seamless operations for client-facing systems. My expertise includes managing VPS configurations, hosting applications, and optimizing infrastructure for efficiency, security, and reliability.');

-- Insert Default Services
INSERT INTO services (title, description, icon, display_order) VALUES
('Server Management', 'Configuration and optimization of Dell Supermicro servers for web hosting services.', 'Server', 1),
('Cloud Infrastructure', 'Deployment and management of private cloud infrastructure using VMware ESXi.', 'Cloud', 2),
('System Security', 'Implementation of firewall and security protocols for network protection.', 'Shield', 3),
('High Availability Setup', 'Configuration of clustering and load balancing for seamless service uptime.', 'Settings', 4);

-- Insert Default Projects
INSERT INTO projects (title, description, image, tags, display_order) VALUES
('Server Setup for Web Hosting', "Configured and optimized Dell Supermicro servers for XeonBD's web hosting services.", 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&q=80&w=800', 'Ubuntu,LAMP Stack,Server Management', 1),
('Virtualization Infrastructure', 'Deployed private cloud infrastructure using VMware ESXi on Dell Supermicro hardware.', 'https://images.unsplash.com/photo-1597852074816-d933c7d2b988?auto=format&fit=crop&q=80&w=800', 'VMware,Cloud,Virtualization', 2),
('Network Configuration', 'Managed enterprise network services including DNS, DHCP, and Active Directory.', 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&q=80&w=800', 'Networking,Security,Infrastructure', 3),
('Load Balancing Setup', 'Implemented high-availability environment with clustering and load balancing.', 'https://i.postimg.cc/httHfTH2/images.png', 'HAProxy,High Availability,Load Balancing', 4),
('AWS Cloud Infrastructure Setup', 'Architected and deployed scalable and secure cloud infrastructure on AWS to support business applications.', 'https://i.postimg.cc/Pr4ZyhZN/aws.png', 'AWS,Cloud Infrastructure,Scalability', 5);

-- Insert Default Blog Posts
INSERT INTO blog_posts (title, excerpt, content, image, date) VALUES
('Optimizing Server Performance', 'Learn about the best practices for optimizing Dell Supermicro server performance...', 'Detailed article about optimizing server performance...', 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&q=80&w=800', '2024-03-15'),
('Cloud Migration Strategies', 'A comprehensive guide to migrating your infrastructure to the cloud...', 'Detailed insights on cloud migration strategies...', 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&q=80&w=800', '2024-03-10'),
('Security Best Practices', 'Essential security measures for protecting your infrastructure...', 'Important security measures and best practices...', 'https://images.unsplash.com/photo-1555949963-aa79dcee981c?auto=format&fit=crop&q=80&w=800', '2024-03-05');

-- Insert Default Social Links
INSERT INTO social_links (platform, url, icon, display_order) VALUES
('GitHub', 'https://github.com/fuad78', 'Github', 1),
('Facebook', 'https://www.facebook.com/profile.php?id=100008623637751', 'Facebook', 2),
('Instagram', 'https://www.instagram.com/fuad_127078/', 'Instagram', 3),
('LinkedIn', 'https://www.linkedin.com/in/%EC%9C%A4%ED%98%9C-%EA%B9%80-480631265/', 'Linkedin', 4);

