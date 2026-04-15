@echo off
echo ===================================================
echo    Starting Grow Your Crops India Application
echo ===================================================
echo.

:: Start Python Data Engine
echo [1/2] Starting Python Data Engine (Port 5000)...
start "Python Data Engine" cmd /k "cd src_python && python app.py"

:: Start PHP Web Server
echo [2/2] Starting PHP Web Server (Port 8000)...
start "PHP Web Server" cmd /k "cd public && php -S localhost:8000"

echo.
echo Both servers have been successfully launched in separate windows!
echo Keep those windows open while you are using the application.
echo.
echo Opening your browser to http://localhost:8000 ...

:: Wait a brief moment to ensure servers initialize
timeout /t 3 /nobreak > nul
start http://localhost:8000
