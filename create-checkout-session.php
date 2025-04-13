<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content type to JSON
header('Content-Type: application/json');

// Load configuration
$configFile = __DIR__ . '/config/.env';
if (!file_exists($configFile)) {
    error_log("Error: Configuration file not found at $configFile");
    http_response_code(500);
    echo json_encode(['error' => 'Configuration error: Missing config file']);
    exit;
}

// Read configuration
$config = parse_ini_file($configFile);
if (!$config) {
    error_log("Error: Failed to parse configuration file");
    http_response_code(500);
    echo json_encode(['error' => 'Configuration error: Invalid config file']);
    exit;
}

// Check if cURL is available
if (!function_exists('curl_init')) {
    error_log("Error: cURL extension is not available");
    http_response_code(500);
    echo json_encode(['error' => 'Server configuration error: cURL extension is required']);
    exit;
}

// Get the raw POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Log received data
error_log("Received data: " . print_r($data, true));

// Validate input data
if (!$data) {
    error_log("Error: Invalid input data received");
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input data']);
    exit;
}

// Required fields
$requiredFields = ['name', 'address', 'phone', 'deliveryMethod', 'phoneModel'];
foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
        error_log("Error: Missing required field: $field");
        http_response_code(400);
        echo json_encode(['error' => "Missing required field: $field"]);
        exit;
    }
}

// Get Stripe configuration
$stripeSecretKey = $config['STRIPE_SECRET_KEY'] ?? '';
$siteUrl = $config['SITE_URL'] ?? '';

if (empty($stripeSecretKey)) {
    error_log("Error: STRIPE_SECRET_KEY not found in configuration");
    http_response_code(500);
    echo json_encode(['error' => 'Stripe configuration error: API key not found']);
    exit;
}

// Initialize cURL session
$ch = curl_init();
if (!$ch) {
    error_log("Error: Failed to initialize cURL");
    http_response_code(500);
    echo json_encode(['error' => 'Server error: Failed to initialize payment service']);
    exit;
}

// Set cURL options
curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/checkout/sessions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $stripeSecretKey,
    'Content-Type: application/x-www-form-urlencoded'
]);

// Prepare session data
$sessionData = [
    'payment_method_types[]' => 'card',
    'line_items[0][price_data][currency]' => 'eur',
    'line_items[0][price_data][product_data][name]' => 'Telefono dekoravimas',
    'line_items[0][price_data][unit_amount]' => 2000, // 20.00 EUR
    'line_items[0][quantity]' => 1,
    'mode' => 'payment',
    'success_url' => $siteUrl . '/success.html',
    'cancel_url' => $siteUrl . '/cancel.html',
    'customer_email' => $data['email'] ?? '',
    'metadata[name]' => $data['name'],
    'metadata[address]' => $data['address'],
    'metadata[phone]' => $data['phone'],
    'metadata[deliveryMethod]' => $data['deliveryMethod'],
    'metadata[phoneModel]' => $data['phoneModel']
];

// Set POST data
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($sessionData));

// Execute cURL request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Check for cURL errors
if (curl_errno($ch)) {
    error_log("cURL Error: " . curl_error($ch));
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . curl_error($ch)]);
    curl_close($ch);
    exit;
}

// Close cURL session
curl_close($ch);

// Check HTTP response code
if ($httpCode !== 200) {
    error_log("Stripe API Error: " . $response);
    http_response_code(500);
    echo json_encode(['error' => 'Payment service error: ' . $response]);
    exit;
}

// Parse response
$session = json_decode($response, true);
if (!$session || !isset($session['id'])) {
    error_log("Error: Invalid response from Stripe API");
    http_response_code(500);
    echo json_encode(['error' => 'Invalid response from payment service']);
    exit;
}

// Return session ID
echo json_encode(['id' => $session['id']]); 