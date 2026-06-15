<?php
ob_start();

if (session_status() === PHP_SESSION_NONE) {
    @session_start();
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
*/

define('LOCALHOST', 'mainline.proxy.rlwy.net');
define('DB_PORT', 22086);

define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'dYfEiQBLkcHAGCLmVWRRsREehaxXHorC');
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
define('MAIL_PASSWORD', 'vedmjmfeekiwpdmw');

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

define('GOOGLE_MAPS_API_KEY', 'AIzaSyDYSBlQ9HF7MqndLVihj3QTJKh6tHbBOUQ');

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
        'Database Connection Failed: ' .
        mysqli_connect_error()
    );
}

/*
|--------------------------------------------------------------------------
| DATABASE CHARACTER SET
|--------------------------------------------------------------------------
*/

if (!mysqli_set_charset($conn, 'utf8mb4')) {
    die(
        'Error setting charset: ' .
        mysqli_error($conn)
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
