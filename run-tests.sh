#!/bin/bash

# Test runner script for Testimonials package

echo "Running Testimonials Package Tests..."
echo "====================================="

# Install dependencies if needed
if [ ! -d "vendor" ]; then
    echo "Installing dependencies..."
    composer install
fi

# Run PHPUnit tests
echo "Running PHPUnit tests..."
./vendor/bin/phpunit

echo "Tests completed!"
