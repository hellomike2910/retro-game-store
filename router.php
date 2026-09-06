<?php
// Router for PHP's local development server. Use start.command / start.bat.
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');
$resolved = realpath(__DIR__ . $path);
$root = realpath(__DIR__) . DIRECTORY_SEPARATOR;
$blocked = preg_match('~(?:^|/)(?:\.[^/]*|includes|tests|data|work)(?:/|$)~i', $path)
    || preg_match('~\.(?:sqlite(?:3)?|db|log|ini|bak|zip|md|command|bat|sh)$~i', $path)
    || ($resolved && $resolved !== realpath(__DIR__) && strncmp($resolved, $root, strlen($root)) !== 0);
if ($blocked) {
    http_response_code(404);
    echo 'Not found';
    return true;
}
return false;
