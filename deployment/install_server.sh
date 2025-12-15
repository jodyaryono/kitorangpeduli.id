#!/bin/bash

###############################################################################
# KitOrangPeduli Server Installation Script
# Server: 103.185.52.124 (IKS)
# Date: December 12, 2025
#
# This script installs PHP 8.2, Composer, Node.js, and all required dependencies
# WITHOUT modifying or removing any existing software on the server.
###############################################################################

set -e  # Exit on error

echo "========================================="
echo "KitOrangPeduli Server Installation"
echo "========================================="
echo ""

# Color codes for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_info() {
    echo -e "${YELLOW}→ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

# Check if running as root
if [[ $EUID -ne 0 ]]; then
   print_error "This script must be run as root"
   exit 1
fi

print_success "Running as root"

# Update package lists
print_info "Updating package lists..."
apt-get update -qq
print_success "Package lists updated"

# Install software-properties-common (for add-apt-repository)
print_info "Installing prerequisites..."
apt-get install -y -qq software-properties-common apt-transport-https ca-certificates curl gnupg2 lsb-release
print_success "Prerequisites installed"

# Add ondrej/php PPA for PHP 8.2 (does not affect existing PHP)
print_info "Adding ondrej/php PPA repository..."
if ! grep -q "ondrej/php" /etc/apt/sources.list.d/*; then
    add-apt-repository -y ppa:ondrej/php
    apt-get update -qq
    print_success "PHP PPA added"
else
    print_info "PHP PPA already exists, skipping..."
fi

# Install PHP 8.2 and extensions
print_info "Installing PHP 8.2 with extensions..."
apt-get install -y -qq \
    php8.2 \
    php8.2-fpm \
    php8.2-cli \
    php8.2-common \
    php8.2-pgsql \
    php8.2-gd \
    php8.2-curl \
    php8.2-mbstring \
    php8.2-xml \
    php8.2-zip \
    php8.2-bcmath \
    php8.2-intl \
    php8.2-readline

print_success "PHP 8.2 installed"

# Verify PHP 8.2 installation
PHP_VERSION=$(/usr/bin/php8.2 -v | head -n 1)
print_success "PHP Version: $PHP_VERSION"

# Install Composer
print_info "Installing Composer..."
if [ ! -f /usr/local/bin/composer ]; then
    cd /tmp
    curl -sS https://getcomposer.org/installer -o composer-setup.php
    php8.2 composer-setup.php --install-dir=/usr/local/bin --filename=composer
    rm composer-setup.php
    print_success "Composer installed"
else
    print_info "Composer already installed, updating..."
    /usr/local/bin/composer self-update
    print_success "Composer updated"
fi

COMPOSER_VERSION=$(/usr/local/bin/composer --version --no-ansi | head -n 1)
print_success "Composer Version: $COMPOSER_VERSION"

# Install Node.js 18+ (if not already installed)
print_info "Checking Node.js installation..."
if ! command -v node &> /dev/null; then
    print_info "Installing Node.js 18..."
    curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
    apt-get install -y -qq nodejs
    print_success "Node.js installed"
else
    NODE_VERSION=$(node -v)
    print_info "Node.js already installed: $NODE_VERSION"
fi

NODE_VERSION=$(node -v)
NPM_VERSION=$(npm -v)
print_success "Node.js Version: $NODE_VERSION"
print_success "npm Version: $NPM_VERSION"

# Install Git (if not already installed)
print_info "Checking Git installation..."
if ! command -v git &> /dev/null; then
    apt-get install -y -qq git
    print_success "Git installed"
else
    GIT_VERSION=$(git --version)
    print_info "Git already installed: $GIT_VERSION"
fi

# Install Apache (if not already installed)
print_info "Checking Apache installation..."
if ! command -v apache2 &> /dev/null; then
    apt-get install -y -qq apache2
    print_success "Apache2 installed"
else
    print_info "Apache2 already installed"
fi

# Install Certbot for Let's Encrypt SSL
print_info "Installing Certbot..."
if ! command -v certbot &> /dev/null; then
    apt-get install -y -qq certbot python3-certbot-apache
    print_success "Certbot installed"
else
    print_info "Certbot already installed"
fi

# Install Supervisor for queue worker management
print_info "Installing Supervisor..."
if ! command -v supervisorctl &> /dev/null; then
    apt-get install -y -qq supervisor
    systemctl enable supervisor
    systemctl start supervisor
    print_success "Supervisor installed and started"
else
    print_info "Supervisor already installed"
fi

# Install PostgreSQL client tools (if not already installed)
print_info "Checking PostgreSQL client tools..."
if ! command -v psql &> /dev/null; then
    apt-get install -y -qq postgresql-client
    print_success "PostgreSQL client installed"
else
    PSQL_VERSION=$(psql --version)
    print_info "PostgreSQL client already installed: $PSQL_VERSION"
fi

# Install s3cmd for object storage management
print_info "Installing s3cmd..."
if ! command -v s3cmd &> /dev/null; then
    apt-get install -y -qq s3cmd
    print_success "s3cmd installed"
else
    print_info "s3cmd already installed"
fi

# Install unzip (needed for Composer)
print_info "Installing unzip..."
apt-get install -y -qq unzip
print_success "unzip installed"

# Create application directory
print_info "Creating application directory..."
mkdir -p /var/www/kitorangpeduli.id
chown -R www-data:www-data /var/www/kitorangpeduli.id
print_success "Application directory created"

# Display installation summary
echo ""
echo "========================================="
echo "Installation Summary"
echo "========================================="
echo ""
/usr/bin/php8.2 -v | head -n 1
/usr/local/bin/composer --version --no-ansi | head -n 1
node -v | xargs -I {} echo "Node.js {}"
npm -v | xargs -I {} echo "npm {}"
git --version
apache2 -v | head -n 1
supervisorctl version
psql --version
echo ""
print_success "All components installed successfully!"
echo ""
echo "========================================="
echo "Next Steps:"
echo "========================================="
echo "1. Upload your application code to /var/www/kitorangpeduli.id"
echo "2. Run: cd /var/www/kitorangpeduli.id"
echo "3. Run: /usr/bin/php8.2 /usr/local/bin/composer install --no-dev"
echo "4. Run: npm install && npm run build"
echo "5. Configure .env file with production settings"
echo "6. Configure Apache virtual host"
echo "7. Setup SSL with certbot"
echo ""
print_success "Installation complete!"
