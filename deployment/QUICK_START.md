# KitOrangPeduli - Quick Start Deployment

## 📋 What You'll Need Before Starting

1. **Database Credentials** (from your production PostgreSQL):
   - Host (usually `localhost` if on same server)
   - Port (default: `5432`)
   - Database name
   - Username  
   - Password

2. **Object Storage Credentials** (from IKS Control Panel):
   - S3 Endpoint URL
   - Access Key ID
   - Secret Access Key
   - Bucket name: `iks-assets`

---

## 🚀 Deployment in 3 Simple Steps

### STEP 1: Run Installation Script on Server

```powershell
# From your Windows machine
ssh root@103.185.52.124

# Download and run installation script
cd /root
# Upload the install_server.sh script from deployment folder
# Or copy-paste the content

bash install_server.sh
```

**This installs:** PHP 8.2, Composer, Node.js, Apache, Certbot, Supervisor

---

### STEP 2: Upload Application & Configure

```powershell
# From your local machine
cd C:\Users\jodya\OneDrive\Documents\projects\kitorangpeduli.id

# Upload files to server
scp -r * root@103.185.52.124:/var/www/kitorangpeduli.id/
```

**On the server:**
```bash
cd /var/www/kitorangpeduli.id

# Install dependencies
/usr/bin/php8.2 /usr/local/bin/composer install --no-dev
npm install && npm run build

# Configure environment
cp deployment/.env.production .env
nano .env  # Edit DB and S3 credentials

# Setup application
/usr/bin/php8.2 artisan key:generate
/usr/bin/php8.2 artisan migrate --force
/usr/bin/php8.2 artisan storage:link
/usr/bin/php8.2 artisan optimize

# Set permissions
chown -R www-data:www-data /var/www/kitorangpeduli.id
chmod -R 755 storage bootstrap/cache
```

---

### STEP 3: Configure Apache & Start Services

```bash
# Copy Apache config
cp /var/www/kitorangpeduli.id/deployment/apache-kitorangpeduli.conf /etc/apache2/sites-available/kitorangpeduli.id.conf

# Enable site and modules
a2enmod rewrite proxy_fcgi setenvif ssl headers
a2enconf php8.2-fpm
a2ensite kitorangpeduli.id
systemctl reload apache2

# Setup SSL
certbot --apache -d kitorangpeduli.id

# Setup queue worker
cp /var/www/kitorangpeduli.id/deployment/supervisor-kitorangpeduli.conf /etc/supervisor/conf.d/kitorangpeduli-worker.conf
supervisorctl reread
supervisorctl update
supervisorctl start kitorangpeduli-worker:*

# Setup cron
crontab -u www-data -e
# Add: * * * * * cd /var/www/kitorangpeduli.id && /usr/bin/php8.2 artisan schedule:run >> /dev/null 2>&1
```

---

## ✅ Verify Deployment

Visit: **https://kitorangpeduli.id**

Admin Panel: **https://kitorangpeduli.id/admin**

---

## 🔄 Future Deployments (Using Envoy)

After initial setup, deploy updates simply by running:

```powershell
# From your local machine
vendor\bin\envoy run deploy
```

That's it! Envoy will automatically:
- Pull latest code
- Install dependencies  
- Build assets
- Run migrations
- Clear caches
- Restart services

---

## 📁 Files Created

All deployment files are in the `/deployment` folder:

- `DEPLOYMENT_GUIDE.md` - Full detailed guide
- `QUICK_START.md` - This file
- `install_server.sh` - Server installation script
- `apache-kitorangpeduli.conf` - Apache virtual host
- `supervisor-kitorangpeduli.conf` - Queue worker config
- `.env.production` - Production environment template

Plus: `Envoy.blade.php` in project root for automated deployments

---

**Need help?** See the full [DEPLOYMENT_GUIDE.md](./DEPLOYMENT_GUIDE.md) for detailed instructions and troubleshooting.
