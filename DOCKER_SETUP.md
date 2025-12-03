# Docker Setup Guide

This guide will help you run the Portfolio Website using Docker and Docker Compose.

## Prerequisites

1. **Docker Desktop** (for Windows/Mac) or **Docker Engine** (for Linux)
   - Download: https://www.docker.com/products/docker-desktop
   - Install and start Docker Desktop

2. **Docker Compose** (usually included with Docker Desktop)

## Quick Start

### Step 1: Navigate to Project Directory
```bash
cd newport-master
```

### Step 2: Start Services
```bash
docker-compose up -d
```

This will:
- Build the PHP Apache container
- Start MySQL container
- Automatically run database setup script
- Make the site available at `http://localhost:8080`

### Step 3: Access Your Website

**Frontend:**
- URL: `http://localhost:8080/`

**Admin Panel:**
- URL: `http://localhost:8080/admin/login.php`
- Username: `admin`
- Password: `Admin@2024`

## Docker Services

### Web Server (PHP + Apache)
- **Container:** `portfolio_web`
- **Port:** `8080` (mapped to container port 80)
- **Service Name:** `web`

### MySQL Database
- **Container:** `portfolio_mysql`
- **Port:** `3306` (mapped to host)
- **Service Name:** `mysql`
- **Database:** `fuaditme_portfolio_db`
- **User:** `fuaditme_portfolio_user`
- **Password:** `Portfolio@2024`
- **Root Password:** `rootpassword`

## Docker Commands

### Start Services
```bash
docker-compose up -d
```

### Stop Services
```bash
docker-compose down
```

### View Logs
```bash
# All services
docker-compose logs

# Specific service
docker-compose logs web
docker-compose logs mysql
```

### Access MySQL Container
```bash
docker exec -it portfolio_mysql mysql -u fuaditme_portfolio_user -p
# Password: Portfolio@2024
```

### Access PHP Container
```bash
docker exec -it portfolio_web bash
```

### Rebuild Containers
```bash
docker-compose up -d --build
```

### Stop and Remove Everything (including volumes)
```bash
docker-compose down -v
```

## Configuration

### Database Connection
The `config.php` file is automatically configured to use:
- **Host:** `mysql` (Docker service name)
- **Database:** `fuaditme_portfolio_db`
- **User:** `fuaditme_portfolio_user`
- **Password:** `Portfolio@2024`

### Port Configuration
- **Web:** `8080` (change in docker-compose.yml if needed)
- **MySQL:** `3306` (exposed for external access if needed)

To change ports, edit `docker-compose.yml`:
```yaml
ports:
  - "YOUR_PORT:80"  # For web
  - "YOUR_PORT:3306" # For MySQL
```

## Troubleshooting

### Port Already in Use
If port 8080 is already in use:
1. Edit `docker-compose.yml`
2. Change `"8080:80"` to `"YOUR_PORT:80"`
3. Restart: `docker-compose down && docker-compose up -d`

### Database Connection Error
1. Check if MySQL container is running:
   ```bash
   docker ps
   ```
2. Check MySQL logs:
   ```bash
   docker-compose logs mysql
   ```
3. Wait for MySQL to be healthy (healthcheck passes)

### Can't Access Website
1. Check if web container is running:
   ```bash
   docker ps
   ```
2. Check web logs:
   ```bash
   docker-compose logs web
   ```
3. Verify port mapping in `docker-compose.yml`

### Database Not Initialized
If database tables are missing:
1. Access MySQL container:
   ```bash
   docker exec -it portfolio_mysql mysql -u root -prootpassword
   ```
2. Run setup script manually:
   ```sql
   source /docker-entrypoint-initdb.d/setup_database.sql
   ```

### Reset Everything
To start fresh:
```bash
docker-compose down -v
docker-compose up -d
```

## File Structure in Docker

```
/var/www/html/          # Application root (mapped to project directory)
├── index.php
├── admin/
├── includes/
└── ...
```

## Environment Variables

You can override database settings using environment variables in `docker-compose.yml`:

```yaml
web:
  environment:
    - DB_HOST=mysql
    - DB_USER=fuaditme_portfolio_user
    - DB_PASS=Portfolio@2024
    - DB_NAME=fuaditme_portfolio_db
```

## Production Considerations

For production deployment:

1. **Change Default Passwords:**
   - Update MySQL root password
   - Update database user password
   - Update admin panel password

2. **Use Environment Files:**
   - Create `.env` file for sensitive data
   - Use `env_file` in docker-compose.yml

3. **Enable HTTPS:**
   - Add reverse proxy (nginx)
   - Use SSL certificates

4. **Backup Database:**
   ```bash
   docker exec portfolio_mysql mysqldump -u root -prootpassword fuaditme_portfolio_db > backup.sql
   ```

5. **Resource Limits:**
   Add to docker-compose.yml:
   ```yaml
   deploy:
     resources:
       limits:
         cpus: '1'
         memory: 512M
   ```

## Admin Panel Access

- **URL:** `http://localhost:8080/admin/login.php`
- **Username:** `admin`
- **Password:** `Admin@2024`

⚠️ **Change password immediately after first login!**

## Support

If you encounter issues:
1. Check container logs: `docker-compose logs`
2. Verify containers are running: `docker ps`
3. Check port availability
4. Review error messages in logs

---

**Your site is now running in Docker!** 🐳


