# Kacmazlar İnşaat - Web Sitesi Kurulum Kılavuzu

## 📋 Gereksinimler

- **PHP 7.4 veya üzeri**
- **Composer** (PHP bağımlılık yöneticisi)
- **Bir SMTP e-posta sunucusu** (cPanel Mail, Gmail, SendGrid, vb.)
- **Web sunucusu** (Apache, Nginx, vb.)

## 🚀 Kurulum Adımları

### 1. Dosyaları Sunucuya Yükleyin

Tüm dosyaları web sunucunuzun root dizinine yükleyin (genellikle `public_html` veya `www`):

```bash
# FTP/SFTP ile yükleyin veya cPanel File Manager kullanın
```

### 2. Composer Bağımlılıklarını Yükleyin

SSH erişiminiz varsa:

```bash
cd /path/to/your/website
php composer.phar install
```

Ya da cPanel Terminal üzerinden:

```bash
composer install
```

### 3. E-posta Yapılandırması

**a) Config dosyasını oluşturun:**

```bash
cp config.sample.php config.php
```

**b) `config.php` dosyasını düzenleyin:**

```php
return [
    // SMTP Sunucu Ayarları
    'smtp_host' => 'mail.kacmazlarinsaat.com',  // Hosting SMTP adresi
    'smtp_port' => 587,                          // Port (587 veya 465)
    'smtp_secure' => 'tls',                      // 'tls' veya 'ssl'
    
    // Kimlik Doğrulama
    'smtp_username' => 'info@kacmazlarinsaat.com',
    'smtp_password' => 'GERCEK_SIFRE_BURAYA',
    
    // E-posta Ayarları
    'from_email' => 'info@kacmazlarinsaat.com',
    'from_name' => 'Kacmazlar İnşaat',
    'to_email' => 'info@kacmazlarinsaat.com',  // Mesajların geleceği adres
    'to_name' => 'Kacmazlar İnşaat',
];
```

### 4. İletişim Bilgilerini Güncelleyin

`index.html` dosyasındaki iletişim bilgilerini güncelleyin:

- **Telefon:** Satır 399
- **E-posta:** Satır 404
- **Adres:** Satır 394
- **Çalışma Saatleri:** Satır 409

### 5. Domain Ayarları

**a) `sitemap.xml` dosyasını güncelleyin:**

```xml
<loc>https://www.kacmazlarinsaat.com/</loc>
```

**b) `robots.txt` dosyasını güncelleyin:**

```
Sitemap: https://www.kacmazlarinsaat.com/sitemap.xml
```

**c) `.htaccess` dosyasında HTTPS yönlendirmesini aktif edin (SSL sertifikanız varsa):**

```apache
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### 6. Dosya İzinlerini Ayarlayın

```bash
# Config dosyası güvenliği
chmod 640 config.php

# Vendor dizini
chmod 755 vendor/

# Genel dizinler
chmod 755 css/ js/ images/
chmod 644 *.html *.php
```

## 🔧 SMTP Ayarları - Yaygın Hosting Sağlayıcıları

### cPanel / WHM

```php
'smtp_host' => 'mail.yourdomain.com',
'smtp_port' => 587,
'smtp_secure' => 'tls',
'smtp_username' => 'info@yourdomain.com',
'smtp_password' => 'your_password',
```

### Gmail (Test için - Production'da önerilmez)

```php
'smtp_host' => 'smtp.gmail.com',
'smtp_port' => 587,
'smtp_secure' => 'tls',
'smtp_username' => 'your-email@gmail.com',
'smtp_password' => 'app_specific_password', // 2FA etkinse App Password kullanın
```

**Not:** Gmail için [App Passwords](https://myaccount.google.com/apppasswords) oluşturmanız gerekir.

### Office 365 / Outlook

```php
'smtp_host' => 'smtp.office365.com',
'smtp_port' => 587,
'smtp_secure' => 'tls',
```

## 🧪 Test Etme

### 1. Yerel Test (PHP Built-in Server)

```bash
cd /path/to/website
php -S localhost:8080
```

Tarayıcıda: `http://localhost:8080`

### 2. Form Testi

1. İletişim formunu doldurun
2. "Gönder" butonuna tıklayın
3. Başarı/hata mesajını kontrol edin
4. E-posta kutunuzu kontrol edin

### 3. Debug Modu

Sorun yaşıyorsanız, `send-email.php` dosyasında debug modunu açın:

```php
// Satır 5-6
ini_set('display_errors', 1); // 0'dan 1'e çevirin
```

Ve SMTP debug seviyesini artırın:

```php
// Satır 120
$mail->SMTPDebug = SMTP::DEBUG_SERVER; // DEBUG_OFF'dan DEBUG_SERVER'a çevirin
```

## 🔒 Güvenlik Önerileri

1. **`config.php` dosyasını asla Git'e commit etmeyin**
2. **Güçlü SMTP şifreleri kullanın**
3. **HTTPS kullanın** (SSL sertifikası yükleyin)
4. **PHP ve Composer'ı güncel tutun**
5. **Düzenli yedeklemeler alın**

## 🐛 Sorun Giderme

### E-posta Gönderilmiyor

1. **SMTP bilgilerini kontrol edin:**
   - Host, port, kullanıcı adı, şifre doğru mu?
   - SSL/TLS ayarı doğru mu?

2. **Sunucu güvenlik duvarını kontrol edin:**
   - Port 587 veya 465 açık mı?

3. **PHP hata loglarını kontrol edin:**
   ```bash
   tail -f /var/log/apache2/error.log
   # veya
   tail -f /home/username/logs/error_log
   ```

4. **SMTP debug açın ve hatayı görün**

### "Composer not found" Hatası

Composer'ı yükleyin:

```bash
curl -sS https://getcomposer.org/installer | php
php composer.phar install
```

### Rate Limiting Sorunları

`send-email.php` dosyasında rate limit süresini değiştirin (satır 100):

```php
if ($time_passed < 60) { // 60 saniye -> istediğiniz süreyi girin
```

## 📞 Destek

Sorun yaşıyorsanız:

1. Error loglarını kontrol edin
2. SMTP sağlayıcınızın dökümanını okuyun
3. `config.sample.php` ile karşılaştırın

## ✅ Canlıya Alma Kontrol Listesi

- [ ] Composer bağımlılıkları yüklendi
- [ ] `config.php` oluşturuldu ve SMTP bilgileri girildi
- [ ] İletişim bilgileri güncellendi (telefon, e-posta, adres)
- [ ] `sitemap.xml` ve `robots.txt` domain'e göre güncellendi
- [ ] `.htaccess` HTTPS yönlendirmesi aktif edildi (SSL varsa)
- [ ] Dosya izinleri ayarlandı
- [ ] İletişim formu test edildi
- [ ] E-posta alımı doğrulandı
- [ ] Tüm sayfalar test edildi (Ana sayfa, Katalog)
- [ ] Mobil uyumluluk kontrol edildi
- [ ] SSL sertifikası yüklendi ve çalışıyor
- [ ] Google Analytics eklendi (isteğe bağlı)
- [ ] Favicon ve logo görünüyor

## 📝 Notlar

- **PHP 8.x** ile test edilmiştir
- **PHPMailer 6.11.1** versiyonu kullanılmaktadır
- Güvenlik güncellemeleri için `composer update` düzenli çalıştırın

