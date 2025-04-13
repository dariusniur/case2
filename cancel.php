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
    
    // Get session ID from query parameter
    $sessionId = $_GET['session_id'] ?? null;
    if (!$sessionId) {
        throw new Exception('No session ID provided');
    }
    
    // Initialize cURL session
    $ch = curl_init();
    
    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/checkout/sessions/' . $sessionId);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $stripeSecretKey
    ]);
    
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
        throw new Exception('Stripe API error: ' . $response);
    }
    
    // Decode response
    $session = json_decode($response, true);
    
    // Return session data
    echo json_encode($session);
    
} catch (Exception $e) {
    // Log error
    error_log('Error in cancel.php: ' . $e->getMessage());
    
    // Return error response
    http_response_code(500);
    echo json_encode([
        'error' => [
            'message' => $e->getMessage()
        ]
    ]);
} 