@echo off
echo ========================================
echo Starting Portfolio Website with Docker
echo ========================================
echo.

echo Checking Docker...
docker --version >nul 2>&1
if errorlevel 1 (
    echo ERROR: Docker is not installed or not in PATH
    echo Please install Docker Desktop from https://www.docker.com/products/docker-desktop
    pause
    exit /b 1
)

echo Docker found!
echo.

echo Building and starting containers...
docker-compose up -d --build

if errorlevel 1 (
    echo.
    echo ERROR: Failed to start containers
    echo Check Docker Desktop is running
    pause
    exit /b 1
)

echo.
echo ========================================
echo Containers started successfully!
echo ========================================
echo.
echo Website URL: http://localhost:8080
echo Admin Panel: http://localhost:8080/admin/login.php
echo.
echo Username: admin
echo Password: Admin@2024
echo.
echo To view logs: docker-compose logs -f
echo To stop: docker-compose down
echo.
pause


