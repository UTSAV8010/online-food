```php
<?php

/*
|--------------------------------------------------------------------------
| Output Buffering
|--------------------------------------------------------------------------
*/
ob_start();

/*
|--------------------------------------------------------------------------
| Session Configuration
|--------------------------------------------------------------------------
*/
$sessionLifetime = 60 * 60 * 24; // 24 hours
$secureCookie = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

$cookieParams = session_get_cookie_params();

session_set_cookie_params([
    'lifetime' => $sessionLifetime,
    'path'     => $cookieParams['path'] ?? '/',
    'domain'   => $cookieParams['domain'] ?? '',
    'secure'   => $secureCookie,
    'httponly' => true,
    'samesite' => 'Lax',
]);

ini_set('session.gc_maxlifetime', (string)$sessionLifetime);
ini_set('session.cookie_lifetime', (string)$sessionLifetime);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| Site URL
|--------------------------------------------------------------------------
*/
define(
    'SITEURL',
    getenv('SITEURL') ?: 'https://online-food-ordering-tn7s.onrender.com/'
);

/*
|--------------------------------------------------------------------------
| Database Configuration
|--------------------------------------------------------------------------
| Use Railway Public Networking credentials.
| Set these variables in Render Environment Variables.
|--------------------------------------------------------------------------
*/

define('DB_HOST', getenv('MYSQLHOST') ?: 'YOUR_RAILWAY_PUBLIC_HOST');
define('DB_PORT', (int)(getenv('MYSQLPORT') ?: 3306));

define('DB_USERNAME', getenv('MYSQLUSER') ?: 'root');
define('DB_PASSWORD', getenv('MYSQLPASSWORD') ?: 'YOUR_DATABASE_PASSWORD');

define('DB_NAME', getenv('MYSQLDATABASE') ?: 'railway');

/*
|--------------------------------------------------------------------------
| SMTP Mail Configuration
|--------------------------------------------------------------------------
*/

define('MAIL_HOST', getenv('MAIL_HOST') ?: 'smtp.gmail.com');
define('MAIL_PORT', (int)(getenv('MAIL_PORT') ?: 587));

define('MAIL_ENCRYPTION', getenv('MAIL_ENCRYPTION') ?: 'tls');

define(
    'MAIL_USERNAME',
    getenv('MAIL_USERNAME') ?: 'your-email@gmail.com'
);

define(
    'MAIL_PASSWORD',
    getenv('MAIL_PASSWORD') ?: 'your-app-password'
);

define(
    'MAIL_FROM_EMAIL',
    getenv('MAIL_FROM_EMAIL') ?: 'your-email@gmail.com'
);

define(
    'MAIL_FROM_NAME',
    getenv('MAIL_FROM_NAME') ?: 'Pasar-kita'
);

define(
    'MAIL_REPLY_TO_EMAIL',
    getenv('MAIL_REPLY_TO_EMAIL') ?: 'your-email@gmail.com'
);

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

define(
    'RECEIVE_UPI_ID',
    getenv('RECEIVE_UPI_ID') ?: 'yourupi@oksbi'
);

define(
    'RECEIVE_UPI_NAME',
    getenv('RECEIVE_UPI_NAME') ?: 'Pasar-kita Online Foods'
);

/*
|--------------------------------------------------------------------------
| Google Maps API
|--------------------------------------------------------------------------
*/

define(
    'GOOGLE_MAPS_API_KEY',
    getenv('GOOGLE_MAPS_API_KEY') ?: ''
);

/*
|--------------------------------------------------------------------------
| Database Connection
|--------------------------------------------------------------------------
*/

// mysqli_report(MYSQLI_REPORT_OFF);

// $conn = mysqli_connect(
//     DB_HOST,
//     DB_USERNAME,
//     DB_PASSWORD,
//     DB_NAME,
//     DB_PORT
// );

// if (!$conn) {
//     die(
//         "Database Connection Failed: " .
//         mysqli_connect_error()
//     );
// }

mysqli_set_charset($conn, 'utf8mb4');

/*
|--------------------------------------------------------------------------
| Mail Functions
|--------------------------------------------------------------------------
*/

$mailFile = __DIR__ . '/mail.php';

if (file_exists($mailFile)) {
    require_once $mailFile;
}
?>
```
