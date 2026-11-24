@echo off
cd /d "%~dp0"

set "PHP_CMD=php"
if exist "C:\xampp\php\php.exe" (
    set "PHP_CMD=C:\xampp\php\php.exe"
)

where php >nul 2>nul
if errorlevel 1 if not exist "C:\xampp\php\php.exe" (
    echo PHP was not found. Please install PHP or XAMPP and try again.
    pause
    exit /b 1
)

echo Starting Vehicle Management app...
echo Open: http://127.0.0.1:8000
"%PHP_CMD%" artisan serve --host 127.0.0.1 --port 8000

if errorlevel 1 (
    echo.
    echo The app did not start correctly. Check your PHP/Laravel setup.
    pause
)
