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

// Check vendor directory
$vendorExists = is_dir('vendor');
output("Vendor directory exists", $vendorExists);

// Check .env file
$envExists = file_exists('.env');
$envPerms = checkPermissions('.env');
$envPermsOk = $envExists && $envPerms === '0600';
output(".env file permissions: " . ($envExists ? $envPerms : 'file not found'), $envPermsOk);

// Check required PHP extensions
$requiredExtensions = ['curl', 'json', 'mbstring'];
foreach ($requiredExtensions as $ext) {
    $extLoaded = extension_loaded($ext);
    output("PHP extension: $ext", $extLoaded);
}

// Check if Composer is installed
$composerVersion = shell_exec('composer --version');
$composerInstalled = !empty($composerVersion);
output("Composer installed", $composerInstalled);

// Check write permissions for required directories
$directories = ['.', 'vendor'];
foreach ($directories as $dir) {
    if (is_dir($dir)) {
        $writable = is_writable($dir);
        output("Directory $dir is writable", $writable);
    }
}

// Check if Stripe keys are set in .env
if ($envExists) {
    $envContent = file_get_contents('.env');
    $stripeSecretKeySet = strpos($envContent, 'STRIPE_SECRET_KEY') !== false;
    $stripePublishableKeySet = strpos($envContent, 'STRIPE_PUBLISHABLE_KEY') !== false;
    output("Stripe Secret Key is set", $stripeSecretKeySet);
    output("Stripe Publishable Key is set", $stripePublishableKeySet);
}

// Check if cURL can connect to Stripe
$ch = curl_init('https://api.stripe.com');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$curlError = curl_error($ch);
curl_close($ch);
output("Can connect to Stripe API", empty($curlError)); 