@echo off
cd /d "%~dp0"
echo.
echo Surprise gift server
echo Open on this computer: http://127.0.0.1:8080/admin/
echo Phone on the same Wi-Fi: use this PC's IP address, port 8080
echo.
"C:\xampp\php\php.exe" -d upload_max_filesize=48M -d post_max_size=80M -d max_file_uploads=40 -S 0.0.0.0:8080 router.php
