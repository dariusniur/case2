# Deklink - Custom Phone Case Shop

This is a custom phone case shop with Stripe integration for secure payments.

## Features

- Modern, responsive design using Tailwind CSS
- Custom iPhone case ordering system
- Stripe payment integration
- Image upload functionality
- Multiple delivery options
- Support for various iPhone models

## Requirements

- PHP 7.4 or newer
- Composer
- Stripe account with API keys

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/deklink.git
cd deklink
```

2. Install dependencies:
```bash
composer install
```

3. Create `.env` file from example:
```bash
cp .env.example .env
```

4. Update `.env` with your Stripe API keys:
```
STRIPE_SECRET_KEY=your_stripe_secret_key
STRIPE_PUBLISHABLE_KEY=your_stripe_publishable_key
SITE_URL=your_site_url
```

5. Set file permissions:
```bash
chmod +x set-permissions.sh
./set-permissions.sh
```

6. Check requirements:
```bash
php check-requirements.php
```

## Development

To run the development server:

```bash
php -S localhost:8000
```

Then visit http://localhost:8000 in your browser.

## Testing

To test the Stripe integration, use these test card numbers:

- Success: 4242 4242 4242 4242
- Decline: 4000 0000 0000 0002

Use any future expiry date and any 3-digit CVC.

## Deployment

1. Upload all files to your hosting server
2. Run `composer install` on the server
3. Set up `.env` with production values
4. Run `set-permissions.sh` to set correct file permissions
5. Run `check-requirements.php` to verify everything is set up correctly

## Support

If you have any questions or issues, please contact support@deklink.lt

## Directory Structure

```
.
├── index.html              # Main landing page
├── create-checkout-session.php  # Stripe payment processing
├── success.html           # Success page
├── cancel.html            # Cancel page
├── composer.json          # PHP dependencies
├── README.md              # Documentation
└── images/                # Image assets
    ├── logo.png
    ├── hero-case.png
    ├── 1.png
    ├── 2.png
    ├── 3.png
    └── 4.png
```

## License

MIT 