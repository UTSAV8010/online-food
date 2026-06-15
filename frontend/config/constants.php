
<?php
ob_start();

/*
|--------------------------------------------------------------------------
| SESSION CONFIGURATION
|--------------------------------------------------------------------------
*/

if (session_status() === PHP_SESSION_NONE) {

    $sessionLifetime = 60 * 60 * 24; // 24 hours

    ini_set('session.gc_maxlifetime', $sessionLifetime);
    ini_set('session.cookie_lifetime', $sessionLifetime);

    session_set_cookie_params([
        'lifetime' => $sessionLifetime,
        'path'     => '/',
        'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    session_start();
}

/*
|--------------------------------------------------------------------------
| SITE URL
|--------------------------------------------------------------------------
*/

define('SITEURL', 'https://online-food-ordering-tn7s.onrender.com/');

/*
|--------------------------------------------------------------------------
| DATABASE CONFIGURATION
|--------------------------------------------------------------------------
|
| IMPORTANT:
| Replace these with your Railway PUBLIC database details.
|
*/

define('LOCALHOST', 'YOUR_RAILWAY_PUBLIC_HOST');
define('DB_PORT', 3306);

define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'YOUR_RAILWAY_PASSWORD');
define('DB_NAME', 'railway');

/*
|--------------------------------------------------------------------------
| SMTP CONFIGURATION
|--------------------------------------------------------------------------
*/

define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_ENCRYPTION', 'tls');

define('MAIL_USERNAME', 'utsavsarvaliya27@gmail.com');
define('MAIL_PASSWORD', 'YOUR_GMAIL_APP_PASSWORD');

define('MAIL_FROM_EMAIL', MAIL_USERNAME);
define('MAIL_FROM_NAME', 'Pasar-kita');

define('MAIL_REPLY_TO_EMAIL', MAIL_USERNAME);

define('MAIL_TIMEOUT', 20);
define('MAIL_VERIFY_PEER', false);

define('APP_OTP_TTL_SECONDS', 60);

/*
|--------------------------------------------------------------------------
| INVENTORY SETTINGS
|--------------------------------------------------------------------------
*/

$low_stock_threshold = 3;

/*
|--------------------------------------------------------------------------
| PAYMENT SETTINGS
|--------------------------------------------------------------------------
*/

define('RECEIVE_UPI_ID', 'utsavsarvaliya27@oksbi');
define('RECEIVE_UPI_NAME', 'Pasar-kita Online Foods');

/*
|--------------------------------------------------------------------------
| GOOGLE MAPS API
|--------------------------------------------------------------------------
*/

define('GOOGLE_MAPS_API_KEY', 'YOUR_GOOGLE_MAPS_API_KEY');

/*
|--------------------------------------------------------------------------
| DATABASE CONNECTION
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
    die(
        '<h2>Database Connection Failed</h2>' .
        '<p>' . mysqli_connect_error() . '</p>'
    );
}

/*
|--------------------------------------------------------------------------
| DATABASE CHARSET
|--------------------------------------------------------------------------
*/

if (!mysqli_set_charset($conn, 'utf8mb4')) {
    die(
        '<h2>Charset Error</h2>' .
        '<p>' . mysqli_error($conn) . '</p>'
    );
}

/*
|--------------------------------------------------------------------------
| MAIL FUNCTIONS
|--------------------------------------------------------------------------
*/

$mailFile = __DIR__ . '/mail.php';

if (file_exists($mailFile)) {
    require_once $mailFile;
}
