#!/bin/bash

# Set permissions for .env file
if [ -f .env ]; then
    chmod 600 .env
    echo "✅ Set .env permissions to 600"
else
    echo "❌ .env file not found"
fi

# Set permissions for PHP files
for file in *.php; do
    if [ -f "$file" ]; then
        chmod 644 "$file"
        echo "✅ Set $file permissions to 644"
    fi
done

# Set permissions for HTML files
for file in *.html; do
    if [ -f "$file" ]; then
        chmod 644 "$file"
        echo "✅ Set $file permissions to 644"
    fi
done

# Set permissions for .htaccess
if [ -f .htaccess ]; then
    chmod 644 .htaccess
    echo "✅ Set .htaccess permissions to 644"
else
    echo "❌ .htaccess file not found"
fi

# Set permissions for vendor directory
if [ -d vendor ]; then
    chmod 755 vendor
    echo "✅ Set vendor directory permissions to 755"
else
    echo "❌ vendor directory not found"
fi

# Set permissions for images directory
if [ -d images ]; then
    chmod 755 images
    find images -type f -exec chmod 644 {} \;
    echo "✅ Set images directory permissions to 755 and files to 644"
else
    echo "❌ images directory not found"
fi

echo "✅ Permissions setup complete" 