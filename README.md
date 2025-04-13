# Phone Case Order System

Moderni telefonų dėklų užsakymo sistema su Stripe mokėjimais.

## Reikalavimai

- PHP 7.4 arba naujesnė versija
- cURL plėtinys
- JSON plėtinys
- mbstring plėtinys
- SSL sertifikatas
- Stripe paskyra

## Diegimas Hostinger hostinge

1. Sukurkite naują svetainę Hostinger valdymo skydelyje
2. Įjunkite SSL sertifikatą
3. Sukonfigūruokite PHP versiją (7.4 arba naujesnė)
4. Įkelkite failus į `public_html` katalogą
5. Sukurkite `.env` failą pagal `.env.example` pavyzdį
6. Nustatykite teisingas failų teises:
   ```bash
   chmod 600 .env
   chmod 755 .
   chmod 755 images
   chmod 644 *.php
   chmod 644 *.html
   ```
7. Patikrinkite diegimą paleisdami `check-hosting.php`

## Konfigūracija

1. Nukopijuokite `.env.example` į `.env`
2. Įveskite savo Stripe API raktus
3. Nustatykite teisingą `SITE_URL`
4. Patikrinkite ar visi reikalingi katalogai egzistuoja ir turi teisingas teises

## Mokėjimų testavimas

1. Naudokite Stripe testavimo kortelę: 4242 4242 4242 4242
2. Galiojimo data: bet kokia ateities data
3. CVC: bet kokie 3 skaitmenys
4. ZIP: bet kokie 5 skaitmenys

## Saugumas

- Visi jautrūs failai yra apsaugoti
- SSL sertifikatas yra privalomas
- Stripe API raktai saugomi `.env` faile
- Įjungtos saugumo antraštės
- Įjungtas CORS apsauga

## Klaidų šalinimas

1. Patikrinkite `error_log` failą
2. Paleiskite `check-hosting.php`
3. Patikrinkite `.env` failo nustatymus
4. Patikrinkite Stripe API raktus
5. Patikrinkite SSL sertifikatą

## Kontaktai

Jei turite klausimų ar problemų, kreipkitės į pagalbos tarnybą.

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