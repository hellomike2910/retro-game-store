@echo off
cd /d "%~dp0"
echo RETRO_GAME_STORE_START
if not defined PORT set "PORT=8080"
if defined PHP_BIN goto check
where php >nul 2>nul
if %errorlevel% equ 0 (
    set "PHP_BIN=php"
) else (
    set "PHP_BIN=C:\xampp\php\php.exe"
)
:check
"%PHP_BIN%" -r "if (PHP_VERSION_ID < 80100 || !extension_loaded('pdo_sqlite')) {fwrite(STDERR, 'Can PHP 8.1+ va PDO SQLite.'); exit(1);}"
if errorlevel 1 exit /b 1
"%PHP_BIN%" includes\check-server.php "%PORT%"
set "SHOP_STATUS=%ERRORLEVEL%"
if "%SHOP_STATUS%"=="10" exit /b 0
if not "%SHOP_STATUS%"=="0" exit /b %SHOP_STATUS%
echo Mo http://127.0.0.1:%PORT% trong trinh duyet. Nhan Ctrl+C de dung.
"%PHP_BIN%" -S 127.0.0.1:%PORT% -t "%CD%" "%CD%\router.php"
