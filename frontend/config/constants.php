<?php

// Starting the Session
ob_start();

$sessionLifetime = 60 * 60 * 24; // 24 hours
$secureCookie = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

$cookieParams = session_get_cookie_params();
$cookieParams = array(
    'lifetime' => $sessionLifetime,
    'path' => $cookieParams['path'] ?? '/',
    'domain' => $cookieParams['domain'] ?? '',
    'secure' => $secureCookie,
    'httponly' => true,
    'samesite' => 'Lax',
);

ini_set('session.gc_maxlifetime', (string) $sessionLifetime);
ini_set('session.cookie_lifetime', (string) $sessionLifetime);

session_set_cookie_params($cookieParams);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| Site URL Configuration
|--------------------------------------------------------------------------
|
| Localhost:
| define('SITEURL', 'http://localhost/r-management/');
|
| Render:
|
*/

define('SITEURL', 'https://online-food-ordering-tn7s.onrender.com/');

/*
|--------------------------------------------------------------------------
| Railway Database Configuration
|--------------------------------------------------------------------------
|
| IMPORTANT:
| If Render cannot connect using mysql.railway.internal,
| replace LOCALHOST with your Railway public host.
|
*/

define('LOCALHOST', 'mysql.railway.internal');
define('DB_PORT', 3306);

define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'dYfEiQBLkcHAGCLmVWRRsREehaxXHorC');
define('DB_NAME', 'railway');

/*
|--------------------------------------------------------------------------
| SMTP Mail Configuration
|--------------------------------------------------------------------------
*/

define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_ENCRYPTION', 'tls');

define('MAIL_USERNAME', 'utsavsarvaliya27@gmail.com');
define('MAIL_PASSWORD', 'vedmjmfeekiwpdmw');

define('MAIL_FROM_EMAIL', 'utsavsarvaliya27@gmail.com');
define('MAIL_FROM_NAME', 'Pasar-kita');

define('MAIL_REPLY_TO_EMAIL', 'utsavsarvaliya27@gmail.com');

define('MAIL_TIMEOUT', 20);
define('MAIL_VERIFY_PEER', false);

define('APP_OTP_TTL_SECONDS', 60);

/*
|--------------------------------------------------------------------------
| Inventory Settings
|--------------------------------------------------------------------------
*/

$low_stock_threshold = 3;

/*
|--------------------------------------------------------------------------
| Payment Gateway Settings
|--------------------------------------------------------------------------
*/

define('RECEIVE_UPI_ID', 'utsavsarvaliya27@oksbi');
define('RECEIVE_UPI_NAME', 'Pasar-kita Online Foods');

/*
|--------------------------------------------------------------------------
| Google Maps API
|--------------------------------------------------------------------------
*/

define('GOOGLE_MAPS_API_KEY', 'AIzaSyDYSBlQ9HF7MqndLVihj3QTJKh6tHbBOUQ');

/*
|--------------------------------------------------------------------------
| Database Connection
|--------------------------------------------------------------------------
*/

$conn = mysqli_connect(
    LOCALHOST,
    DB_USERNAME,
    DB_PASSWORD,
    DB_NAME,
    DB_PORT
);

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

/*
|--------------------------------------------------------------------------
| Mail Functions
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/mail.php';

?>