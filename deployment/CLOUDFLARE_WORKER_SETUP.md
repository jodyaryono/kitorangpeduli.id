# OpenRouter Cloudflare Worker Proxy - Setup Guide

## Masalah
Production server IP (103.185.52.124) di-BLOCK oleh OpenRouter, semua model return HTTP 500 Internal Server Error.

## Solusi
Gunakan Cloudflare Worker sebagai proxy untuk bypass IP blocking (100% GRATIS).

---

## Step 1: Deploy Cloudflare Worker

### 1.1 Login Cloudflare
1. Buka https://dash.cloudflare.com
2. Login dengan akun Cloudflare (atau daftar gratis)

### 1.2 Create Worker
1. Klik menu **"Workers & Pages"** di sidebar kiri
2. Klik tombol **"Create Application"**
3. Pilih **"Create Worker"**
4. Beri nama worker: `openrouter-proxy` (atau nama bebas)
5. Klik **"Deploy"**

### 1.3 Edit Worker Code
1. Setelah deploy, klik tombol **"Edit Code"**
2. **HAPUS SEMUA** code default yang ada
3. Copy paste code dari file: `deployment/cloudflare-worker-openrouter-proxy.js`
4. Klik **"Save and Deploy"**

### 1.4 Copy Worker URL
Setelah deploy sukses, copy URL worker Anda, contoh:
```
https://openrouter-proxy.your-username.workers.dev
```

---

## Step 2: Update Laravel Configuration

### 2.1 Update .env Production
SSH ke server production dan edit file .env:

```bash
ssh kitorangpeduli
nano /var/www/kitorangpeduli.id/.env
```

Tambahkan baris ini:
```env
# OpenRouter via Cloudflare Worker Proxy
OPENROUTER_API_URL=https://openrouter-proxy.your-username.workers.dev
```

**PENTING**: Ganti `your-username` dengan URL worker Anda yang sebenarnya!

### 2.2 Clear Config Cache
```bash
cd /var/www/kitorangpeduli.id
php artisan config:clear
php artisan config:cache
```

---

## Step 3: Test dari Production Server

Test apakah proxy sudah jalan:

```bash
curl -X POST 'https://openrouter-proxy.your-username.workers.dev' \
  --header 'Content-Type: application/json' \
  --header 'Authorization: Bearer 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' \
  --data '{
    "model": "google/gemini-2.5-flash",
    "messages": [{"role": "user", "content": "Hello, test proxy!"}],
    "max_tokens": 50
  }'
```

Jika berhasil, Anda akan dapat response JSON dengan content AI.

---

## Rekomendasi Model OpenRouter (Berbayar, Terbaik untuk Report Generation)

Setelah proxy jalan, pilih salah satu model ini:

### 1. **DeepSeek Chat V3** (RECOMMENDED - SUPER MURAH)
```php
private string $model = 'deepseek/deepseek-chat-v3';
```
- 💰 **Cost**: $0.0000002/prompt, $0.00000088/completion
- ⚡ **Kecepatan**: Sangat cepat
- 🎯 **Cocok**: Advanced reasoning, data analysis, report generation
- 📊 **Context**: 163K tokens

**Estimasi cost untuk 1000 reports**: ~$0.50 - $1.00

### 2. **OpenAI GPT-4o Mini** (BALANCE - Speed & Quality)
```php
private string $model = 'openai/gpt-4o-mini';
```
- 💰 **Cost**: $0.00000015/prompt, $0.0000006/completion
- ⚡ **Kecepatan**: Cepat
- 🎯 **Cocok**: Smart analysis, well-structured reports
- 📊 **Context**: 128K tokens

**Estimasi cost untuk 1000 reports**: ~$1.00 - $2.00

### 3. **Qwen 2.5 72B** (POWERFUL - Best Value)
```php
private string $model = 'qwen/qwen-2.5-72b-instruct';
```
- 💰 **Cost**: $0.00000007/prompt, $0.00000026/completion
- ⚡ **Kecepatan**: Medium
- 🎯 **Cocok**: Complex data analysis, multilingual
- 📊 **Context**: 32K tokens

**Estimasi cost untuk 1000 reports**: ~$0.80 - $1.50

### 4. **Anthropic Claude 3.5 Haiku** (PREMIUM - Most Accurate)
```php
private string $model = 'anthropic/claude-3.5-haiku';
```
- 💰 **Cost**: $0.0000008/prompt, $0.000004/completion
- ⚡ **Kecepatan**: Sangat cepat
- 🎯 **Cocok**: Accurate analysis, nuanced reports
- 📊 **Context**: 200K tokens

**Estimasi cost untuk 1000 reports**: ~$3.00 - $5.00

---

## Cara Ganti Model

Edit file: `app/Services/GeminiReportService.php`

```php
class GeminiReportService
{
    private string $apiKey;
    private string $model = 'deepseek/deepseek-chat-v3'; // <-- Ganti di sini
    private string $apiUrl;
```

Lalu deploy ke production:
```bash
scp app/Services/GeminiReportService.php kitorangpeduli:/var/www/kitorangpeduli.id/app/Services/
ssh kitorangpeduli "cd /var/www/kitorangpeduli.id && php artisan config:clear"
```

---

## Troubleshooting

### Proxy return error 401
- Cek apakah Authorization header di-forward dengan benar
- Pastikan OPENROUTER_API_KEY di .env masih valid

### Proxy return error 500
- Cek Cloudflare Worker logs di dashboard
- Pastikan script worker ter-deploy dengan benar

### Laravel masih call https://openrouter.ai langsung
- Pastikan `php artisan config:cache` sudah dijalankan
- Pastikan .env production ada OPENROUTER_API_URL

---

## Monitoring & Limits

### Cloudflare Worker Limits (Free Tier)
- ✅ 100,000 requests/day
- ✅ 10ms CPU time per request
- ✅ Unlimited bandwidth

### OpenRouter API Rate Limits
Tergantung model yang dipilih, check: https://openrouter.ai/docs#limits

---

## Contact Support

Jika masih ada masalah setelah setup proxy:

**OpenRouter Support**:
- Email: support@openrouter.ai
- Discord: https://discord.gg/openrouter

**Cloudflare Support**:
- Docs: https://developers.cloudflare.com/workers/
- Community: https://community.cloudflare.com/

---

**Created**: 2025-01-11
**Status**: Ready to deploy ✅
