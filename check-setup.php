<?php
echo "Checking system requirements...\n\n";

// Check PHP version
$requiredVersion = '7.4.0';
$currentVersion = PHP_VERSION;
echo "PHP Version: $currentVersion\n";
if (version_compare($currentVersion, $requiredVersion, '>=')) {
    echo "✓ PHP version meets requirements\n";
} else {
    echo "✗ PHP version is too old. Required: $requiredVersion\n";
}

// Check if vendor directory exists
if (is_dir('vendor')) {
    echo "✓ Vendor directory exists\n";
    
    // Check if required packages are installed
    $requiredPackages = [
        'stripe/stripe-php',
        'vlucas/phpdotenv'
    ];
    
    foreach ($requiredPackages as $package) {
        if (file_exists("vendor/$package")) {
            echo "✓ Package $package is installed\n";
        } else {
            echo "✗ Package $package is missing\n";
        }
    }
} else {
    echo "✗ Vendor directory is missing. Run 'composer install'\n";
}

// Check .env file
if (file_exists('.env')) {
    echo "✓ .env file exists\n";
    
    // Check if .env is readable
    if (is_readable('.env')) {
        echo "✓ .env file is readable\n";
        
        // Check if .env contains required variables
        $envContent = file_get_contents('.env');
        $requiredVars = ['STRIPE_SECRET_KEY', 'STRIPE_PUBLISHABLE_KEY'];
        foreach ($requiredVars as $var) {
            if (strpos($envContent, $var) !== false) {
                echo "✓ $var is set in .env\n";
            } else {
                echo "✗ $var is missing in .env\n";
            }
        }
    } else {
        echo "✗ .env file is not readable\n";
    }
} else {
    echo "✗ .env file is missing\n";
}

// Check file permissions
echo "\nChecking file permissions...\n";
$filesToCheck = [
    '.env' => 600,
    'vendor' => 755,
    'create-checkout-session.php' => 644
];

foreach ($filesToCheck as $file => $expectedPermission) {
    if (file_exists($file)) {
        $currentPermission = substr(sprintf('%o', fileperms($file)), -3);
        if ($currentPermission == $expectedPermission) {
            echo "✓ $file has correct permissions ($currentPermission)\n";
        } else {
            echo "✗ $file has incorrect permissions ($currentPermission, expected $expectedPermission)\n";
        }
    }
}

// Check if Stripe can be initialized
try {
    require_once 'vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    \Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
    echo "\n✓ Stripe initialized successfully\n";
} catch (Exception $e) {
    echo "\n✗ Error initializing Stripe: " . $e->getMessage() . "\n";
}

echo "\nCheck complete!\n"; 