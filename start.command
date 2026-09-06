#!/bin/sh
set -eu
cd "$(CDPATH= cd -- "$(dirname -- "$0")" && pwd)"
printf '%s\n' 'RETRO_GAME_STORE_START'
shop_php="${PHP_BIN:-}"
if [ -z "$shop_php" ]; then shop_php="$(command -v php || true)"; fi
if [ -z "$shop_php" ] && [ -x /Applications/XAMPP/xamppfiles/bin/php ]; then
    shop_php=/Applications/XAMPP/xamppfiles/bin/php
fi
if [ -z "$shop_php" ]; then
    printf '%s\n' 'Không tìm thấy PHP. Cần PHP 8.1+ với PDO SQLite (có trong XAMPP).'
    exit 1
fi
"$shop_php" -r 'if (PHP_VERSION_ID < 80100 || !extension_loaded("pdo_sqlite")) {fwrite(STDERR, "Cần PHP 8.1 trở lên và tiện ích PDO SQLite.\n"); exit(1);}'
shop_port="${PORT:-8080}"
shop_status=0
"$shop_php" includes/check-server.php "$shop_port" || shop_status=$?
if [ "$shop_status" -eq 10 ]; then exit 0; fi
if [ "$shop_status" -ne 0 ]; then exit "$shop_status"; fi
printf 'Mở http://127.0.0.1:%s trong trình duyệt. Nhấn Ctrl+C để dừng.\n' "$shop_port"
exec "$shop_php" -S "127.0.0.1:$shop_port" -t "$PWD" "$PWD/router.php"
