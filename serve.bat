@echo off
title ORLMS Development Server
echo ============================================
echo   ORLMS - Development Server
echo   http://localhost:8000
echo ============================================
echo.
echo Starting PHP server...
echo Press Ctrl+C to stop the server.
echo.
IF EXIST "C:\xampp2\php\php.exe" (
    C:\xampp2\php\php.exe -S localhost:8000 router.php
) ELSE IF EXIST "C:\xampp\php\php.exe" (
    C:\xampp\php\php.exe -S localhost:8000 router.php
) ELSE (
    php -S localhost:8000 router.php
)
pause
