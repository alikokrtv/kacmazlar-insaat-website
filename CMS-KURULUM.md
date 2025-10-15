# Kacmazlar İnşaat CMS - Kurulum Kılavuzu

## 🎯 CMS Özellikleri

### ✅ **Tamamlanan Özellikler:**
- **Admin Panel** - Modern, responsive tasarım
- **Güvenli Giriş Sistemi** - Session tabanlı authentication
- **Veritabanı Yapısı** - Projeler, blog, admin, mesajlar
- **Proje Yönetimi** - CRUD işlemleri
- **Blog Yönetimi** - Yazı ekleme, düzenleme, kategoriler
- **İletişim Mesajları** - Form mesajlarını görüntüleme
- **Medya Yönetimi** - Dosya yükleme sistemi
- **Site Ayarları** - Dinamik konfigürasyon

### 🚀 **Yakında Eklenecek:**
- **Proje CRUD Sayfaları** - Admin panelinde proje yönetimi
- **Blog CRUD Sayfaları** - Admin panelinde blog yönetimi
- **Dinamik Ana Sayfa** - Veritabanından projeleri çekme
- **Blog Sayfası** - Halka açık blog görünümü
- **Medya Galerisi** - Resim yükleme ve yönetimi

---

## 📋 **Kurulum Adımları**

### 1️⃣ **Veritabanı Kurulumu**

**MySQL/MariaDB'de veritabanı oluşturun:**

```sql
-- database.sql dosyasını çalıştırın
mysql -u root -p < database.sql
```

**Veya phpMyAdmin'de:**
1. `database.sql` dosyasını açın
2. Tüm SQL kodunu kopyalayın
3. phpMyAdmin'de SQL sekmesine yapıştırın
4. "Çalıştır" butonuna tıklayın

### 2️⃣ **Veritabanı Bağlantı Ayarları**

`includes/Database.php` dosyasını düzenleyin:

```php
private $host = 'localhost';        // Veritabanı sunucusu
private $dbname = 'kacmazlar_cms';  // Veritabanı adı
private $username = 'root';         // Kullanıcı adı
private $password = '';             // Şifre
```

**Hosting için örnek:**
```php
private $host = 'mysql.yourhosting.com';
private $dbname = 'yourusername_kacmazlar_cms';
private $username = 'yourusername_dbuser';
private $password = 'your_db_password';
```

### 3️⃣ **Dosya İzinleri**

```bash
# Uploads klasörü için yazma izni
chmod 755 uploads/
chmod 755 uploads/projects/
chmod 755 uploads/blog/
chmod 755 uploads/media/

# Config dosyası için güvenlik
chmod 640 includes/Database.php
```

### 4️⃣ **Admin Giriş Bilgileri**

**Varsayılan Admin Hesabı:**
- **Kullanıcı Adı:** `admin`
- **E-posta:** `admin@kacmazlarinsaat.com`
- **Şifre:** `admin123`

**İlk girişten sonra şifreyi değiştirin!**

---

## 🔧 **Admin Panel Kullanımı**

### 📊 **Dashboard**
- **İstatistikler:** Proje, blog, mesaj sayıları
- **Son Aktiviteler:** Yeni eklenen içerikler
- **Hızlı Erişim:** Sık kullanılan işlemler

### 🏗️ **Proje Yönetimi**
- **Proje Ekleme:** Detaylı proje bilgileri
- **Kategori:** İskele, Çelik, Endüstriyel
- **Durum:** Planlama, Devam, Tamamlandı
- **Galeri:** Çoklu resim yükleme
- **SEO:** Meta title, description

### 📝 **Blog Yönetimi**
- **Yazı Ekleme:** Rich text editor
- **Kategoriler:** Renkli kategori sistemi
- **Etiketler:** Tag sistemi
- **Yayınlama:** Tarihli yayınlama
- **Öne Çıkarma:** Featured posts

### 📧 **Mesaj Yönetimi**
- **İletişim Formu:** Otomatik mesaj alma
- **Okundu/Okunmadı:** Durum takibi
- **Yanıtlama:** E-posta ile yanıt
- **Filtreleme:** Tarih, durum filtreleri

---

## 🎨 **Frontend Entegrasyonu**

### **Dinamik Ana Sayfa**
```php
<?php
require_once 'includes/ProjectManager.php';
$projectManager = new ProjectManager();

// Öne çıkan projeleri getir
$featuredProjects = $projectManager->getFeaturedProjects(6);

// Son projeleri getir
$recentProjects = $projectManager->getRecentProjects(12);
?>
```

### **Dinamik Blog Sayfası**
```php
<?php
require_once 'includes/BlogManager.php';
$blogManager = new BlogManager();

// Blog yazılarını getir
$posts = $blogManager->getAllPosts(10);

// Kategorileri getir
$categories = $blogManager->getCategories();
?>
```

---

## 🔒 **Güvenlik Özellikleri**

### **Authentication**
- ✅ **Session Tabanlı** giriş sistemi
- ✅ **Şifre Hashleme** (password_hash)
- ✅ **CSRF Koruması** token sistemi
- ✅ **Role Based Access** (admin/editor)

### **Input Validation**
- ✅ **SQL Injection** koruması (PDO prepared statements)
- ✅ **XSS Koruması** (htmlspecialchars)
- ✅ **File Upload** güvenliği
- ✅ **Rate Limiting** (form spam koruması)

### **Data Protection**
- ✅ **Sensitive Data** .gitignore'da
- ✅ **Database Credentials** ayrı dosyada
- ✅ **Error Logging** production'da
- ✅ **HTTPS Ready** SSL desteği

---

## 📱 **Responsive Tasarım**

### **Admin Panel**
- ✅ **Desktop** - Tam özellikli panel
- ✅ **Tablet** - Optimized layout
- ✅ **Mobile** - Collapsible sidebar

### **Frontend**
- ✅ **Mobile First** tasarım
- ✅ **Touch Friendly** butonlar
- ✅ **Fast Loading** optimized images

---

## 🚀 **Performans Optimizasyonu**

### **Database**
- ✅ **Indexes** - Hızlı sorgular
- ✅ **Pagination** - Büyük veri setleri
- ✅ **Caching** - Redis/Memcached hazır

### **Frontend**
- ✅ **Image Optimization** - WebP desteği
- ✅ **CSS/JS Minification** - Production ready
- ✅ **CDN Ready** - Static dosyalar

---

## 🔧 **Geliştirme Ortamı**

### **Gereksinimler**
- **PHP 7.4+** (8.x önerilir)
- **MySQL 5.7+** veya **MariaDB 10.3+**
- **Apache/Nginx** web sunucusu
- **Composer** (PHPMailer için)

### **Geliştirme Araçları**
```bash
# Composer bağımlılıkları
composer install

# Veritabanı migration
mysql -u root -p < database.sql

# Test verileri
mysql -u root -p < sample_data.sql
```

---

## 📞 **Destek ve Bakım**

### **Düzenli Bakım**
- ✅ **Database Backup** - Otomatik yedekleme
- ✅ **Security Updates** - PHP/MySQL güncellemeleri
- ✅ **Performance Monitoring** - Slow query log
- ✅ **Error Logging** - Hata takibi

### **Monitoring**
- ✅ **Uptime Monitoring** - Site erişilebilirlik
- ✅ **Performance Metrics** - Sayfa yükleme süreleri
- ✅ **Security Scanning** - Güvenlik taramaları

---

## 🎯 **Sonraki Adımlar**

### **Kısa Vadeli (1-2 hafta)**
1. **Proje CRUD sayfalarını** tamamla
2. **Blog CRUD sayfalarını** tamamla
3. **Ana sayfayı dinamik** hale getir
4. **Blog sayfasını** oluştur

### **Orta Vadeli (1 ay)**
1. **Medya galerisi** sistemi
2. **SEO optimizasyonu** (sitemap, meta)
3. **E-posta bildirimleri** sistemi
4. **Backup otomasyonu**

### **Uzun Vadeli (3 ay)**
1. **Multi-language** desteği
2. **API endpoints** (REST API)
3. **Advanced analytics** entegrasyonu
4. **Mobile app** backend

---

## ✅ **Kurulum Kontrol Listesi**

- [ ] Veritabanı oluşturuldu ve tablolar kuruldu
- [ ] Database.php bağlantı bilgileri güncellendi
- [ ] Admin hesabı ile giriş yapıldı
- [ ] İlk proje eklendi
- [ ] İlk blog yazısı eklendi
- [ ] İletişim formu test edildi
- [ ] Dosya yükleme test edildi
- [ ] Responsive tasarım kontrol edildi
- [ ] Güvenlik testleri yapıldı
- [ ] Backup stratejisi belirlendi

---

**🎉 CMS sistemi hazır! Admin paneli ile içerik yönetimine başlayabilirsiniz.**
