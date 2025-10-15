# Kacmazlar İnşaat - Profesyonel Web Sitesi & CMS

[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue.svg)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange.svg)](https://mysql.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

## 🏗️ Proje Hakkında

Kacmazlar İnşaat için geliştirilmiş profesyonel web sitesi ve içerik yönetim sistemi (CMS). Çelik konstrüksiyon, iskele sistemleri ve kalıp sistemleri alanında uzmanlaşmış firma için özel olarak tasarlanmıştır.

## ✨ Özellikler

### 🌐 Frontend
- **Responsive Tasarım** - Tüm cihazlarda mükemmel görünüm
- **Modern UI/UX** - Kullanıcı dostu arayüz
- **SEO Optimizasyonu** - Arama motorları için optimize edilmiş
- **Hızlı Yükleme** - Optimize edilmiş performans
- **PDF Katalog** - Online katalog görüntüleyici
- **Proje Galerisi** - Detaylı proje sayfaları

### 🎛️ Admin Panel (CMS)
- **Güvenli Giriş Sistemi** - Session tabanlı authentication
- **Proje Yönetimi** - CRUD işlemleri ile proje yönetimi
- **Blog Sistemi** - Yazı ekleme, düzenleme, kategoriler
- **İletişim Mesajları** - Form mesajlarını yönetme
- **Medya Yönetimi** - Dosya yükleme ve organizasyon
- **Site Ayarları** - Dinamik konfigürasyon

### 🔒 Güvenlik
- **SQL Injection Koruması** - PDO prepared statements
- **XSS Koruması** - Input sanitization
- **CSRF Token Sistemi** - Form güvenliği
- **Şifre Hashleme** - Güvenli şifre saklama
- **Session Yönetimi** - Güvenli oturum kontrolü

## 🚀 Kurulum

### Gereksinimler
- PHP 7.4 veya üzeri
- MySQL 5.7 veya üzeri
- Apache/Nginx web sunucusu
- Composer (PHPMailer için)

### Adım 1: Repository'yi Klonlayın
```bash
git clone https://github.com/yourusername/kacmazlar-insaat-website.git
cd kacmazlar-insaat-website
```

### Adım 2: Veritabanını Kurun
```bash
mysql -u root -p < database.sql
```

### Adım 3: Bağlantı Ayarlarını Yapın
`includes/Database.php` dosyasını düzenleyin:
```php
private $host = 'localhost';
private $dbname = 'kacmazlar_cms';
private $username = 'your_username';
private $password = 'your_password';
```

### Adım 4: Composer Bağımlılıklarını Yükleyin
```bash
composer install
```

### Adım 5: Dosya İzinlerini Ayarlayın
```bash
chmod 755 uploads/
chmod 640 includes/Database.php
```

## 📱 Kullanım

### Admin Paneli
- **URL:** `http://yourdomain.com/login.php`
- **Varsayılan Giriş:** `admin` / `admin123`

### Ana Site
- **URL:** `http://yourdomain.com/`
- **Responsive tasarım** ile tüm cihazlarda çalışır

## 📁 Proje Yapısı

```
kacmazlar-insaat-website/
├── admin.php                 # Admin dashboard
├── login.php                 # Admin giriş sayfası
├── index.html               # Ana sayfa
├── katalog.html             # PDF katalog sayfası
├── proje-*.html             # Proje detay sayfaları
├── css/
│   └── style.css           # Ana CSS dosyası
├── js/
│   └── main.js             # JavaScript dosyası
├── images/                 # Görseller
├── includes/               # PHP sınıfları
│   ├── Database.php        # Veritabanı bağlantısı
│   ├── Auth.php            # Authentication sistemi
│   ├── ProjectManager.php  # Proje yönetimi
│   └── BlogManager.php     # Blog yönetimi
├── vendor/                 # Composer bağımlılıkları
├── database.sql            # Veritabanı yapısı
├── config.php              # E-posta ayarları
├── send-email.php          # E-posta gönderici
└── README.md               # Bu dosya
```

## 🎨 Teknolojiler

### Frontend
- **HTML5** - Semantic markup
- **CSS3** - Modern styling, Flexbox, Grid
- **JavaScript (ES6+)** - Vanilla JS, modern features
- **Font Awesome** - İkonlar
- **PDF.js** - PDF görüntüleyici

### Backend
- **PHP 7.4+** - Server-side scripting
- **MySQL 5.7+** - Veritabanı
- **PDO** - Veritabanı erişimi
- **PHPMailer** - E-posta gönderimi
- **Composer** - Bağımlılık yönetimi

### Güvenlik
- **Password Hashing** - Güvenli şifre saklama
- **Prepared Statements** - SQL injection koruması
- **CSRF Tokens** - Cross-site request forgery koruması
- **Input Validation** - Veri doğrulama ve sanitization

## 📊 Veritabanı Yapısı

### Ana Tablolar
- **`admins`** - Admin kullanıcıları
- **`projects`** - Proje bilgileri
- **`blog_posts`** - Blog yazıları
- **`blog_categories`** - Blog kategorileri
- **`contact_messages`** - İletişim mesajları
- **`site_settings`** - Site ayarları
- **`media_files`** - Medya dosyaları

## 🔧 Geliştirme

### Yerel Geliştirme Ortamı
```bash
# PHP built-in server
php -S localhost:8080

# Veya Apache/Nginx ile
# Virtual host kurulumu gerekli
```

### Veritabanı Migration
```bash
# Yeni migration çalıştırma
mysql -u root -p < database.sql
```

## 📈 Performans

### Optimizasyonlar
- **Image Optimization** - WebP desteği
- **CSS/JS Minification** - Production ready
- **Database Indexing** - Hızlı sorgular
- **Caching** - Redis/Memcached hazır

### Monitoring
- **Error Logging** - Hata takibi
- **Performance Metrics** - Yükleme süreleri
- **Uptime Monitoring** - Site erişilebilirlik

## 🤝 Katkıda Bulunma

1. Fork yapın
2. Feature branch oluşturun (`git checkout -b feature/amazing-feature`)
3. Commit yapın (`git commit -m 'Add amazing feature'`)
4. Push yapın (`git push origin feature/amazing-feature`)
5. Pull Request oluşturun

## 📄 Lisans

Bu proje MIT lisansı altında lisanslanmıştır. Detaylar için [LICENSE](LICENSE) dosyasına bakın.

## 📞 İletişim

- **Website:** [kacmazlarinsaat.com](https://kacmazlarinsaat.com)
- **E-posta:** info@kacmazlarinsaat.com
- **Telefon:** +90 543 239 25 50

## 🙏 Teşekkürler

- **Font Awesome** - İkonlar için
- **PDF.js** - PDF görüntüleyici için
- **PHPMailer** - E-posta gönderimi için
- **Composer** - Bağımlılık yönetimi için

---

**⭐ Bu projeyi beğendiyseniz yıldız vermeyi unutmayın!**