# KitOrangPeduli Deployment Guide
**Server:** 103.185.52.124 (IKS)  
**Domain:** kitorangpeduli.id  
**Date:** December 12, 2025

---

## Prerequisites

- **Server Access:** SSH as root (password: Iks123#?)
- **Database:** PostgreSQL on block storage (db-iks)
- **Storage:** S3-compatible object storage (iks-assets bucket)
- **Local Machine:** Windows with PowerShell/Git Bash

---

## 🚀 STEP 1: SSH Setup & Server Access

### On Your Windows Machine (PowerShell):

```powershell
# Generate SSH key (if you don't have one)
ssh-keygen -t rsa -b 4096 -C "kitorangpeduli@iks"
# Press Enter for default location: C:\Users\jodya\.ssh\id_rsa
# Set passphrase or press Enter to skip

# View your public key
type C:\Users\jodya\.ssh\id_rsa.pub
# Copy the entire output (starts with ssh-rsa)
```

### On the Server:

```bash
# Connect to server
ssh root@103.185.52.124
# Password: Iks123#?

# Add your public key
mkdir -p ~/.ssh
nano ~/.ssh/authorized_keys
# Paste your public key, save (Ctrl+X, Y, Enter)

# Set proper permissions
chmod 700 ~/.ssh
chmod 600 ~/.ssh/authorized_keys

# Exit and test passwordless login
exit
```

### Test Passwordless Login:

```powershell
ssh root@103.185.52.124
# Should login without password!
```

✅ **Checkpoint:** You can login to server without password

---

## 🚀 STEP 2: Install PHP 8.2 & Dependencies

### Copy the installation script to server:

```powershell
# From your local machine
scp deployment/install_server.sh root@103.185.52.124:/root/
```

### On the Server, run the installation script:

```bash
ssh root@103.185.52.124

# Make script executable and run
chmod +x /root/install_server.sh
bash /root/install_server.sh
```

**This script will install:**
- PHP 8.2 with all required extensions (pgsql, gd, curl, mbstring, xml, zip, bcmath)
- PHP 8.2 FPM
- Composer (latest)
- Node.js 18+ and npm
- Git
- Certbot (for SSL)
- Supervisor (for queue worker)

**Time:** ~5-10 minutes

✅ **Checkpoint:** PHP 8.2, Composer, Node.js installed

---

## 🚀 STEP 3: Upload Application Code

### Option A: Using Git (Recommended)

```bash
# On the server
cd /var/www
git clone https://YOUR_GIT_REPO_URL kitorangpeduli.id
cd kitorangpeduli.id
```

### Option B: Using SCP/rsync from Local Machine

```powershell
# From your local machine (PowerShell)
cd C:\Users\jodya\OneDrive\Documents\projects\kitorangpeduli.id

# Create remote directory
ssh root@103.185.52.124 "mkdir -p /var/www/kitorangpeduli.id"

# Upload files (excluding node_modules, vendor, .git)
scp -r * root@103.185.52.124:/var/www/kitorangpeduli.id/
```

✅ **Checkpoint:** Application files uploaded to `/var/www/kitorangpeduli.id`

---

## 🚀 STEP 4: Install Dependencies & Configure

### On the Server:

```bash
cd /var/www/kitorangpeduli.id

# Install PHP dependencies
/usr/bin/php8.2 /usr/local/bin/composer install --optimize-autoloader --no-dev

# Install Node dependencies and build assets
npm install
npm run build

# Copy environment file
cp deployment/.env.production .env

# IMPORTANT: Edit .env with your credentials
nano .env
```

### Edit `.env` with these values:

```env
# Application
APP_ENV=production
APP_DEBUG=false
APP_URL=https://kitorangpeduli.id

# Database - GET THESE FROM YOUR PRODUCTION POSTGRESQL
DB_CONNECTION=pgsql
DB_HOST=localhost              # Or your PostgreSQL host
DB_PORT=5432
DB_DATABASE=kitorangpeduli_db  # Your production database name
DB_USERNAME=postgres           # Your production database user
DB_PASSWORD=YOUR_DB_PASSWORD   # Your production database password

# Object Storage - GET THESE FROM IKS PANEL
AWS_ENDPOINT=https://s3.id-jak.idcloudhost.com  # Your S3 endpoint
AWS_ACCESS_KEY_ID=YOUR_ACCESS_KEY
AWS_SECRET_ACCESS_KEY=YOUR_SECRET_KEY
AWS_BUCKET=iks-assets
AWS_USE_PATH_STYLE_ENDPOINT=false
FILESYSTEM_DISK=s3

# WhatsApp (already in file from dev)
WA_URL=https://app.whacenter.com/api/send
WA_TOKEN=f68f236a401d382a5cb9b923617798b7

# OpenRouter AI (already in file from dev)
OPENROUTER_API_KEY=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
```

### Generate Application Key & Setup:

```bash
# Generate unique app key
/usr/bin/php8.2 artisan key:generate

# Run database migrations
/usr/bin/php8.2 artisan migrate --force

# Create storage symlink
/usr/bin/php8.2 artisan storage:link

# Optimize for production
/usr/bin/php8.2 artisan config:cache
/usr/bin/php8.2 artisan route:cache
/usr/bin/php8.2 artisan view:cache

# Set proper permissions
chown -R www-data:www-data /var/www/kitorangpeduli.id
chmod -R 755 /var/www/kitorangpeduli.id/storage
chmod -R 755 /var/www/kitorangpeduli.id/bootstrap/cache
```

✅ **Checkpoint:** Application configured and optimized

---

## 🚀 STEP 5: Configure Apache & SSL

### Copy Apache configuration:

```bash
# On the server
cp /var/www/kitorangpeduli.id/deployment/apache-kitorangpeduli.conf /etc/apache2/sites-available/kitorangpeduli.id.conf

# Enable required Apache modules
a2enmod rewrite proxy_fcgi setenvif ssl headers

# Enable PHP 8.2 FPM
a2enconf php8.2-fpm

# Enable the site
a2ensite kitorangpeduli.id

# Test Apache configuration
apache2ctl configtest
# Should show "Syntax OK"

# Reload Apache
systemctl reload apache2
```

### Generate SSL Certificate:

```bash
# Install Let's Encrypt certificate
certbot --apache -d kitorangpeduli.id

# Follow the prompts:
# - Enter email address
# - Agree to terms
# - Choose to redirect HTTP to HTTPS (option 2)

# Verify certificate auto-renewal
certbot renew --dry-run
```

✅ **Checkpoint:** Apache configured with SSL certificate

---

## 🚀 STEP 6: Configure Background Services

### Setup Supervisor for Queue Worker:

```bash
# Copy supervisor config
cp /var/www/kitorangpeduli.id/deployment/supervisor-kitorangpeduli.conf /etc/supervisor/conf.d/kitorangpeduli-worker.conf

# Reload supervisor
supervisorctl reread
supervisorctl update
supervisorctl start kitorangpeduli-worker:*

# Check status
supervisorctl status
```

### Setup Laravel Scheduler (Cron):

```bash
# Edit www-data crontab
crontab -u www-data -e

# Add this line:
* * * * * cd /var/www/kitorangpeduli.id && /usr/bin/php8.2 artisan schedule:run >> /dev/null 2>&1
```

✅ **Checkpoint:** Queue worker and scheduler running

---

## 🚀 STEP 7: Upload Existing Media to Object Storage

### Install AWS CLI or s3cmd on server:

```bash
# Install s3cmd
apt-get install -y s3cmd

# Configure s3cmd
s3cmd --configure
# Enter your S3 credentials from IKS panel
```

### Sync existing media files:

```bash
cd /var/www/kitorangpeduli.id

# If you have local storage/app/public files, sync them
s3cmd sync storage/app/public/ s3://iks-assets/

# Or upload from your local machine
```

✅ **Checkpoint:** Media files uploaded to object storage

---

## 🚀 STEP 8: Final Testing

### Test the application:

1. **Visit:** https://kitorangpeduli.id
2. **Admin Panel:** https://kitorangpeduli.id/admin
3. **Test file upload** (KTP images)
4. **Test AI report generation**
5. **Check queue worker logs:** `tail -f /var/www/kitorangpeduli.id/storage/logs/laravel.log`

### Verify services are running:

```bash
# Check Apache
systemctl status apache2

# Check PHP-FPM
systemctl status php8.2-fpm

# Check PostgreSQL
systemctl status postgresql

# Check Supervisor
supervisorctl status

# Check queue worker logs
tail -f /var/log/supervisor/kitorangpeduli-worker-stdout.log
```

✅ **Checkpoint:** Application fully deployed and running

---

## 🚀 STEP 9: Setup Envoy for Future Deployments

### On your local machine:

```bash
cd C:\Users\jodya\OneDrive\Documents\projects\kitorangpeduli.id

# Install Envoy
composer require laravel/envoy --dev
```

**Envoy.blade.php is already created in your project root!**

### Deploy updates in the future:

```powershell
# From your local machine
vendor\bin\envoy run deploy
```

This will automatically:
- Pull latest code from Git
- Install dependencies
- Build assets
- Run migrations
- Clear and rebuild caches
- Restart queue worker

✅ **Checkpoint:** Envoy configured for easy deployments

---

## 📊 Post-Deployment Checklist

- [ ] Application accessible at https://kitorangpeduli.id
- [ ] Admin panel login works
- [ ] Database connection successful
- [ ] File uploads work (KTP images to S3)
- [ ] AI reports generate successfully
- [ ] WhatsApp integration works
- [ ] Queue worker processing jobs
- [ ] SSL certificate valid
- [ ] Cron scheduler running

---

## 🔒 Security Recommendations

1. **Change default PostgreSQL password**
2. **Setup UFW firewall:**
   ```bash
   ufw allow 22
   ufw allow 80
   ufw allow 443
   ufw enable
   ```
3. **Disable root SSH login** (after creating sudo user)
4. **Setup fail2ban** for SSH protection
5. **Regular backups** (database + S3 files)

---

## 🆘 Troubleshooting

### Application shows 500 error:
```bash
# Check logs
tail -f /var/www/kitorangpeduli.id/storage/logs/laravel.log

# Check permissions
ls -la /var/www/kitorangpeduli.id/storage
```

### Queue not processing:
```bash
# Check supervisor status
supervisorctl status

# Restart worker
supervisorctl restart kitorangpeduli-worker:*

# Check worker logs
tail -f /var/log/supervisor/kitorangpeduli-worker-stdout.log
```

### Database connection error:
```bash
# Test PostgreSQL connection
psql -h localhost -U postgres -d kitorangpeduli_db

# Check .env database credentials
cat /var/www/kitorangpeduli.id/.env | grep DB_
```

### File upload not working:
```bash
# Test S3 connection
s3cmd ls s3://iks-assets/

# Check .env AWS credentials
cat /var/www/kitorangpeduli.id/.env | grep AWS_
```

---

## 📞 Support

- **Server IP:** 103.185.52.124
- **Domain:** kitorangpeduli.id
- **Deployment Date:** December 12, 2025

**Need help?** Check the logs first, then consult this guide.

---

**🎉 Deployment Complete! Your application is now live at https://kitorangpeduli.id**
