<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content type to JSON
header('Content-Type: application/json');

try {
    // Load environment variables
    $configDir = dirname(__FILE__) . '/config';
    $envFile = $configDir . '/.env';
    
    if (!file_exists($envFile)) {
        throw new Exception('Environment file not found at ' . $envFile);
    }
    
    $env = parse_ini_file($envFile);
    if (!$env) {
        throw new Exception('Failed to parse environment file');
    }
    
    if (empty($env['STRIPE_SECRET_KEY'])) {
        throw new Exception('Stripe secret key not found in environment file');
    }
    
    $stripeSecretKey = $env['STRIPE_SECRET_KEY'];
    $siteUrl = $env['SITE_URL'] ?? 'https://' . $_SERVER['HTTP_HOST'];
    
    // Get POST data
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        throw new Exception('Invalid input data');
    }
    
    // Validate required fields
    $requiredFields = ['customerName', 'customerAddress', 'customerPhone', 'phoneModel', 'deliveryMethod'];
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            throw new Exception('Missing required field: ' . $field);
        }
    }
    
    // Log received data (for debugging)
    error_log('Received data: ' . print_r($data, true));
    
    // Initialize cURL session
    $ch = curl_init();
    
    // Prepare session data
    $sessionData = [
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => 'Custom Phone Case',
                    'description' => 'Phone Model: ' . $data['phoneModel']
                ],
                'unit_amount' => 2000 // 20.00 EUR
            ],
            'quantity' => 1
        ]],
        'mode' => 'payment',
        'success_url' => $siteUrl . '/success.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => $siteUrl . '/cancel.php?session_id={CHECKOUT_SESSION_ID}',
        'metadata' => [
            'customer_name' => $data['customerName'],
            'customer_address' => $data['customerAddress'],
            'customer_phone' => $data['customerPhone'],
            'phone_model' => $data['phoneModel'],
            'delivery_method' => $data['deliveryMethod']
        ],
        'customer_email' => $data['customerEmail'] ?? null,
        'shipping_address_collection' => [
            'allowed_countries' => ['LT']
        ],
        'phone_number_collection' => [
            'enabled' => true
        ]
    ];
    
    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/checkout/sessions');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($sessionData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $stripeSecretKey,
        'Content-Type: application/json',
        'Stripe-Version: 2023-10-16'
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    // Execute cURL request
    $response = curl_exec($ch);
    
    // Check for cURL errors
    if (curl_errno($ch)) {
        throw new Exception('cURL error: ' . curl_error($ch));
    }
    
    // Get HTTP response code
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    // Close cURL session
    curl_close($ch);
    
    // Check HTTP response code
    if ($httpCode !== 200) {
        $errorResponse = json_decode($response, true);
        $errorMessage = isset($errorResponse['error']['message']) 
            ? $errorResponse['error']['message'] 
            : 'Stripe API error';
        throw new Exception($errorMessage);
    }
    
    // Decode response
    $session = json_decode($response, true);
    
    if (!isset($session['id'])) {
        throw new Exception('Invalid response from Stripe API');
    }
    
    // Log success
    error_log('Successfully created Stripe session: ' . $session['id']);
    
    // Return session ID
    echo json_encode([
        'id' => $session['id']
    ]);
    
} catch (Exception $e) {
    // Log error
    error_log('Error in create-checkout-session.php: ' . $e->getMessage());
    
    // Return error response
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
} 