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
c:\xampp\php\php.exe -S localhost:8000 router.php
pause
