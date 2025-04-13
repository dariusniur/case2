<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content type to JSON
header('Content-Type: application/json');

// Get the raw POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Log received data
error_log("Received data: " . print_r($data, true));

// Validate input data
if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input data']);
    exit;
}

// Required fields
$requiredFields = ['name', 'address', 'phone', 'deliveryMethod', 'phoneModel'];
foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode(['error' => "Missing required field: $field"]);
        exit;
    }
}

// Get Stripe secret key from environment variable
$stripeSecretKey = getenv('STRIPE_SECRET_KEY');
if (!$stripeSecretKey) {
    error_log("Error: STRIPE_SECRET_KEY not found in environment variables");
    http_response_code(500);
    echo json_encode(['error' => 'Stripe configuration error']);
    exit;
}

// Initialize cURL session
$ch = curl_init();

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
    'success_url' => 'https://deklink.lt/success.html',
    'cancel_url' => 'https://deklink.lt/cancel.html',
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
    echo json_encode(['error' => 'Server error occurred']);
    curl_close($ch);
    exit;
}

// Close cURL session
curl_close($ch);

// Check HTTP response code
if ($httpCode !== 200) {
    error_log("Stripe API Error: " . $response);
    http_response_code(500);
    echo json_encode(['error' => 'Payment service error']);
    exit;
}

// Parse response
$session = json_decode($response, true);

// Return session ID
echo json_encode(['id' => $session['id']]); 