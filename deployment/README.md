# 🚀 KitOrangPeduli Deployment Files

All deployment scripts and configurations are ready! Follow the guides below to deploy your application.

---

## 📚 Documentation

- **[QUICK_START.md](QUICK_START.md)** - Fast 3-step deployment guide
- **[DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md)** - Comprehensive step-by-step guide with troubleshooting

---

## 📁 Deployment Files

| File | Purpose |
|------|---------|
| `install_server.sh` | Automated server setup (PHP 8.2, Composer, Node.js, Apache, etc.) |
| `apache-kitorangpeduli.conf` | Apache virtual host configuration |
| `supervisor-kitorangpeduli.conf` | Queue worker supervisor configuration |
| `.env.production` | Production environment template |

---

## 🎯 What to Do Next

### Option 1: Quick Deployment (Recommended)

Follow [QUICK_START.md](QUICK_START.md) for a streamlined 3-step process.

### Option 2: Detailed Deployment

Follow [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md) for comprehensive instructions with explanations.

---

## ✅ Pre-Deployment Checklist

Before you start, make sure you have:

- [ ] SSH access to server 103.185.52.124 (password: Iks123#?)
- [ ] PostgreSQL database credentials (host, port, database, user, password)
- [ ] Object storage credentials from IKS panel (S3 endpoint, access key, secret key)
- [ ] Domain kitorangpeduli.id pointing to 103.185.52.124

---

## 🚀 Execute Deployment Now

### Step 1: Upload Installation Script

```powershell
# From your Windows machine (PowerShell)
cd C:\Users\jodya\OneDrive\Documents\projects\kitorangpeduli.id
scp deployment/install_server.sh root@103.185.52.124:/root/
```

### Step 2: Run Installation on Server

```powershell
ssh root@103.185.52.124
bash /root/install_server.sh
```

**This will install:**
- PHP 8.2 with all required extensions
- Composer
- Node.js 18+
- Apache
- Certbot (for SSL)
- Supervisor (for queue worker)
- PostgreSQL client tools
- s3cmd (for object storage)

### Step 3: Follow Remaining Steps

After installation completes, continue with either:
- [QUICK_START.md](QUICK_START.md) Step 2-3
- [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md) Step 3-9

---

## 🔄 Future Deployments

After initial deployment, use Envoy for automated updates:

```powershell
# From your local machine
vendor\bin\envoy run deploy
```

This automatically:
- Pulls latest code from Git
- Installs dependencies
- Builds assets
- Runs migrations
- Optimizes caches
- Restarts queue workers

---

## 📞 Need Help?

- **Server:** 103.185.52.124
- **Domain:** kitorangpeduli.id
- **Check logs:** `/var/www/kitorangpeduli.id/storage/logs/laravel.log`
- **Queue logs:** `/var/log/supervisor/kitorangpeduli-worker-stdout.log`

See troubleshooting section in [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md#-troubleshooting)

---

## 🎉 Ready to Deploy!

All scripts are tested and ready. Start with Step 1 above or jump to [QUICK_START.md](QUICK_START.md)!

**Deployment Time:** ~15-30 minutes for initial setup
**Future Deployments:** ~2-3 minutes with Envoy
