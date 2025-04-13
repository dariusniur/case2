<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to check file permissions
function checkPermissions($file) {
    if (!file_exists($file)) {
        return "File not found";
    }
    return substr(sprintf('%o', fileperms($file)), -4);
}

// Function to format output
function output($message, $success = true) {
    echo ($success ? "✅" : "❌") . " $message\n";
    return $success;
}

// Check PHP version
$phpVersion = phpversion();
$phpVersionOk = version_compare($phpVersion, '7.4.0', '>=');
output("PHP Version: $phpVersion", $phpVersionOk);

// Check required PHP extensions
$requiredExtensions = ['curl', 'json', 'mbstring', 'openssl'];
foreach ($requiredExtensions as $ext) {
    $extLoaded = extension_loaded($ext);
    output("PHP extension: $ext", $extLoaded);
}

// Check if cURL can connect to Stripe
$ch = curl_init('https://api.stripe.com');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
$response = curl_exec($ch);
$curlError = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

output("Can connect to Stripe API", empty($curlError) && $httpCode === 200);

// Check file permissions
$filesToCheck = [
    '.env' => '0600',
    'create-checkout-session.php' => '0644',
    'index.php' => '0644',
    'success.php' => '0644',
    'cancel.php' => '0644'
];

foreach ($filesToCheck as $file => $requiredPerms) {
    $currentPerms = checkPermissions($file);
    $permsOk = $currentPerms === $requiredPerms;
    output("File permissions for $file: $currentPerms (required: $requiredPerms)", $permsOk);
}

// Check if .env file exists and has required variables
if (file_exists('.env')) {
    $envContent = file_get_contents('.env');
    $requiredVars = ['STRIPE_SECRET_KEY', 'STRIPE_PUBLISHABLE_KEY', 'SITE_URL'];
    foreach ($requiredVars as $var) {
        $varSet = strpos($envContent, $var) !== false;
        output("Environment variable $var is set", $varSet);
    }
} else {
    output(".env file not found", false);
}

// Check if vendor directory exists and is writable
$vendorExists = is_dir('vendor');
$vendorWritable = $vendorExists && is_writable('vendor');
output("Vendor directory exists and is writable", $vendorExists && $vendorWritable);

// Check if images directory is writable
$imagesWritable = is_dir('images') && is_writable('images');
output("Images directory is writable", $imagesWritable);

// Check if SSL is available
$sslAvailable = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
output("SSL is available", $sslAvailable);

// Check if Composer is installed
$composerVersion = shell_exec('composer --version');
$composerInstalled = !empty($composerVersion);
output("Composer is installed", $composerInstalled);

// Check if required directories exist
$requiredDirs = ['images', 'vendor'];
foreach ($requiredDirs as $dir) {
    $dirExists = is_dir($dir);
    output("Directory $dir exists", $dirExists);
} 