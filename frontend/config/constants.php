<?php
ob_start();

/*
|--------------------------------------------------------------------------
| SESSION
|--------------------------------------------------------------------------
*/

if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

/*
|--------------------------------------------------------------------------
| SITE URL
|--------------------------------------------------------------------------
*/

$protocol = "http://";
if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
    (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {
    $protocol = "https://";
}

$host = $_SERVER['HTTP_HOST'] ?? '';

if ($host) {
    // Strip port on production environments (e.g., Render's internal port :10000)
    if (stripos($host, 'localhost') === false && stripos($host, '127.0.0.1') === false) {
        $host_parts = explode(':', $host);
        $host = $host_parts[0];
    }
    
    $script = $_SERVER['SCRIPT_NAME'] ?? $_SERVER['PHP_SELF'] ?? '';
    $path = str_replace('\\', '/', dirname($script));
    
    $pos = -1;
    foreach (['/admin', '/restro', '/delivery-boy', '/frontend'] as $folder) {
        $p = stripos($path, $folder);
        if ($p !== false && ($pos === -1 || $p < $pos)) {
            $pos = $p;
        }
    }
    
    if ($pos !== -1) {
        $path = substr($path, 0, $pos);
    }
    
    $path = rtrim($path, '/') . '/';
    $dynamic_site_url = $protocol . $host . $path;
} else {
    $dynamic_site_url = 'https://online-food-ordering-tn7s.onrender.com/';
}

$base_url = getenv('SITE_URL') ?: (getenv('SITEURL') ?: $dynamic_site_url);

if (substr($base_url, -1) !== '/') {
    $base_url .= '/';
}

$current_script = $_SERVER['SCRIPT_NAME'] ?? $_SERVER['PHP_SELF'] ?? '';

if (strpos($current_script, '/admin/') !== false) {
    $base_url .= 'admin/';
} elseif (strpos($current_script, '/restro/') !== false) {
    $base_url .= 'restro/';
} elseif (strpos($current_script, '/delivery-boy/') !== false) {
    $base_url .= 'delivery-boy/';
}

define('SITEURL', $base_url);

/*
|--------------------------------------------------------------------------
| DATABASE CONFIGURATION (DYNAMICAL VIA ENVIRONMENT VARIABLES OR HARDCODED FALLBACKS)
|--------------------------------------------------------------------------
*/

// Set default fallback values
$db_host = 'reseau.proxy.rlwy.net';
$db_port = 16444;
$db_user = 'root';
$db_pass = 'lZfMAPxhtYCTVmYEjdDZfGAUGTRSYnOd';
$db_name = 'railway';

// Check for single connection URL variable (common in Render/Railway)
$mysql_url = getenv('MYSQL_URL') ?: getenv('DATABASE_URL');
if ($mysql_url) {
    $parsed_url = parse_url($mysql_url);
    if ($parsed_url) {
        if (isset($parsed_url['host'])) {
            $db_host = $parsed_url['host'];
        }
        if (isset($parsed_url['port'])) {
            $db_port = intval($parsed_url['port']);
        }
        if (isset($parsed_url['user'])) {
            $db_user = $parsed_url['user'];
        }
        if (isset($parsed_url['pass'])) {
            $db_pass = $parsed_url['pass'];
        }
        if (isset($parsed_url['path'])) {
            $db_name = ltrim($parsed_url['path'], '/');
        }
    }
} else {
    // Check individual environment variables
    $db_host = getenv('DB_HOST') ?: (getenv('MYSQLHOST') ?: $db_host);
    $db_port = getenv('DB_PORT') ?: (getenv('MYSQLPORT') ?: $db_port);
    $db_user = getenv('DB_USERNAME') ?: (getenv('MYSQLUSER') ?: $db_user);
    
    // getenv returns false if the variable is not set
    $env_pass = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : getenv('MYSQLPASSWORD');
    if ($env_pass !== false && $env_pass !== null) {
        $db_pass = $env_pass;
    }
    
    $db_name = getenv('DB_NAME') ?: (getenv('MYSQLDATABASE') ?: $db_name);
}

define('LOCALHOST', $db_host);
define('DB_PORT', intval($db_port));
define('DB_USERNAME', $db_user);
define('DB_PASSWORD', $db_pass);
define('DB_NAME', $db_name);

/*
|--------------------------------------------------------------------------
| SMTP CONFIGURATION
|--------------------------------------------------------------------------
*/

define('MAIL_HOST', 'smtp-relay.brevo.com');
define('MAIL_PORT', 2525);
define('MAIL_ENCRYPTION', 'tls');
define('MAIL_USERNAME', 'aecb2c001@smtp-brevo.com');

define(
    'MAIL_PASSWORD',
    'xsmtpsib-48b53487c091eca8966041257a7a984faec9134db389c6c6068a5387e1b5c4bc-MhABUaZcqgK9ChZS'
);

define('MAIL_FROM_EMAIL', 'utsavsarvaliya27@gmail.com');
define('MAIL_FROM_NAME', 'Pasar-kita');

define('MAIL_REPLY_TO_EMAIL', 'utsavsarvaliya27@gmail.com');

define('MAIL_TIMEOUT', 30);
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

$conn = mysqli_init();
if ($conn) {
    mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, 5);
    try {
        if (!@mysqli_real_connect($conn, LOCALHOST, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT)) {
            $conn = false;
        }
    } catch (Throwable $e) {
        $conn = false;
        $connect_error = $e->getMessage();
    }
} else {
    $conn = false;
    $connect_error = "Failed to initialize mysqli";
}

if (!$conn) {
    die(
        'Database Connection Failed: '
        . ($connect_error ?? mysqli_connect_error())
    );
}

/*
|--------------------------------------------------------------------------
| DATABASE CHARACTER SET
|--------------------------------------------------------------------------
*/

if (!mysqli_set_charset($conn, 'utf8mb4')) {
    die(
        'Error setting charset: '
        . mysqli_error($conn)
    );
}

/*
|--------------------------------------------------------------------------
| MAIL FUNCTIONS
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../vendor/autoload.php';

$mailFile = __DIR__ . '/mail.php';

if (file_exists($mailFile)) {
    require_once $mailFile;
}