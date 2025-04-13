<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deklink - Pasidaryk savo iPhone dėklą</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <?php 
    $configPath = dirname(__FILE__) . '/config/stripe-config.php';
    if (file_exists($configPath)) {
        include $configPath;
    } else {
        error_log('Stripe config file not found at ' . $configPath);
    }
    ?>
</head>
<body>
<script>
// Initialize Stripe
const stripe = Stripe(stripePublishableKey);

// Handle form submission
document.getElementById('orderForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    // Get form data
    const formData = {
        customerName: document.getElementById('customerName').value,
        customerAddress: document.getElementById('customerAddress').value,
        customerPhone: document.getElementById('customerPhone').value,
        phoneModel: document.getElementById('phoneModel').value,
        deliveryMethod: document.querySelector('input[name="deliveryMethod"]:checked').value
    };
    
    try {
        // Create checkout session
        const response = await fetch('/create-checkout-session.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });
        
        // Log response status
        console.log('Response status:', response.status);
        
        // Check response
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Server response:', errorText);
            throw new Error('Serverio klaida. Bandykite dar kartą.');
        }
        
        // Parse response
        const data = await response.json();
        
        // Check session ID
        if (!data.id) {
            console.error('No session ID in response:', data);
            throw new Error('Neteisingas serverio atsakymas.');
        }
        
        // Log session ID
        console.log('Received session ID:', data.id);
        
        // Redirect to Stripe checkout
        const result = await stripe.redirectToCheckout({
            sessionId: data.id
        });
        
        // Handle redirect error
        if (result.error) {
            throw new Error(result.error.message);
        }
        
    } catch (error) {
        console.error('Error:', error);
        alert('Įvyko klaida: ' + error.message);
    }
});

// Handle delivery option selection
document.querySelectorAll('.delivery-option').forEach(option => {
    option.addEventListener('click', () => {
        // Remove selected class from all options
        document.querySelectorAll('.delivery-option').forEach(opt => {
            opt.classList.remove('border-pink-500', 'bg-pink-50');
        });
        
        // Add selected class to clicked option
        option.classList.add('border-pink-500', 'bg-pink-50');
        
        // Check the radio input
        const radio = option.querySelector('input[type="radio"]');
        radio.checked = true;
    });
});

// Form validation
const form = document.getElementById('orderForm');
const submitButton = form.querySelector('button[type="submit"]');

function validateForm() {
    const requiredFields = [
        'customerName',
        'customerAddress',
        'customerPhone',
        'phoneModel'
    ];
    
    const allFieldsFilled = requiredFields.every(field => {
        const element = document.getElementById(field);
        return element && element.value.trim() !== '';
    });
    
    const deliveryMethodSelected = document.querySelector('input[name="deliveryMethod"]:checked');
    
    submitButton.disabled = !(allFieldsFilled && deliveryMethodSelected);
}

// Add validation listeners
form.querySelectorAll('input, select').forEach(element => {
    element.addEventListener('input', validateForm);
    element.addEventListener('change', validateForm);
});

// Initial validation
validateForm();
</script>

<div>
    <label class="block text-sm font-medium text-gray-700">Vardas Pavardė</label>
    <input type="text" id="customerName" name="customerName" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500">
</div>
<div>
    <label class="block text-sm font-medium text-gray-700">Adresas</label>
    <input type="text" id="customerAddress" name="customerAddress" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500">
</div>
<div>
    <label class="block text-sm font-medium text-gray-700">Telefono numeris</label>
    <input type="tel" id="customerPhone" name="customerPhone" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500">
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 mb-2">Pristatymo būdas</label>
    <div class="grid grid-cols-3 gap-4">
        <label class="delivery-option p-4 rounded-lg border hover:border-pink-500 cursor-pointer">
            <input type="radio" name="deliveryMethod" value="Omniva" class="hidden" required>
            <i class="fas fa-box text-2xl mb-2"></i>
            <span class="text-sm">Omniva</span>
        </label>
        <label class="delivery-option p-4 rounded-lg border hover:border-pink-500 cursor-pointer">
            <input type="radio" name="deliveryMethod" value="LT Express" class="hidden" required>
            <i class="fas fa-truck text-2xl mb-2"></i>
            <span class="text-sm">LT Express</span>
        </label>
        <label class="delivery-option p-4 rounded-lg border hover:border-pink-500 cursor-pointer">
            <input type="radio" name="deliveryMethod" value="DPD" class="hidden" required>
            <i class="fas fa-box-open text-2xl mb-2"></i>
            <span class="text-sm">DPD</span>
        </label>
    </div>
</div>

<div>
    <label class="block text-sm font-medium text-gray-700">Telefono modelis</label>
    <select id="phoneModel" name="phoneModel" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500">
        <option value="">Pasirinkite modelį</option>
        <option value="iPhone 6s">iPhone 6s</option>
        <option value="iPhone 7">iPhone 7</option>
        <option value="iPhone 8">iPhone 8</option>
        <option value="iPhone X">iPhone X</option>
        <option value="iPhone 11">iPhone 11</option>
        <option value="iPhone 12">iPhone 12</option>
        <option value="iPhone 13">iPhone 13</option>
        <option value="iPhone 14">iPhone 14</option>
        <option value="iPhone 15">iPhone 15</option>
        <option value="iPhone 15 Pro">iPhone 15 Pro</option>
        <option value="iPhone 15 Pro Max">iPhone 15 Pro Max</option>
    </select>
</div> 
</body>
</html> 