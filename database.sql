-- Kacmazlar İnşaat CMS Veritabanı Yapısı
-- MySQL 5.7+ uyumlu

CREATE DATABASE IF NOT EXISTS kacmazlar_cms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE kacmazlar_cms;

-- Admin kullanıcıları tablosu
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'editor') DEFAULT 'editor',
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Projeler tablosu
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    short_description TEXT,
    description LONGTEXT,
    category ENUM('iskele', 'celik', 'endustriyel') NOT NULL,
    location VARCHAR(100),
    start_date DATE,
    end_date DATE,
    area VARCHAR(50),
    client VARCHAR(100),
    status ENUM('planlama', 'devam', 'tamamlandi') DEFAULT 'planlama',
    featured_image VARCHAR(255),
    gallery_images JSON,
    meta_title VARCHAR(200),
    meta_description TEXT,
    is_featured BOOLEAN DEFAULT FALSE,
    is_published BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    FOREIGN KEY (created_by) REFERENCES admins(id) ON DELETE SET NULL
);

-- Blog kategorileri tablosu
CREATE TABLE blog_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    color VARCHAR(7) DEFAULT '#004E89',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Blog yazıları tablosu
CREATE TABLE blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    excerpt TEXT,
    content LONGTEXT NOT NULL,
    featured_image VARCHAR(255),
    category_id INT,
    tags JSON,
    meta_title VARCHAR(200),
    meta_description TEXT,
    is_published BOOLEAN DEFAULT FALSE,
    is_featured BOOLEAN DEFAULT FALSE,
    view_count INT DEFAULT 0,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES admins(id) ON DELETE SET NULL
);

-- Site ayarları tablosu
CREATE TABLE site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value LONGTEXT,
    setting_type ENUM('text', 'textarea', 'image', 'boolean', 'json') DEFAULT 'text',
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- İletişim mesajları tablosu
CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(200),
    message TEXT NOT NULL,
    ip_address VARCHAR(45),
    is_read BOOLEAN DEFAULT FALSE,
    replied_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Medya dosyaları tablosu
CREATE TABLE media_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    file_type ENUM('image', 'document', 'video') NOT NULL,
    alt_text VARCHAR(200),
    description TEXT,
    uploaded_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES admins(id) ON DELETE SET NULL
);

-- Varsayılan admin kullanıcısı (şifre: admin123)
INSERT INTO admins (username, email, password, full_name, role) VALUES 
('admin', 'admin@kacmazlarinsaat.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Site Yöneticisi', 'admin');

-- Varsayılan blog kategorileri
INSERT INTO blog_categories (name, slug, description, color) VALUES 
('İskele Sistemleri', 'iskele-sistemleri', 'İskele kurulumu ve sistemleri hakkında yazılar', '#FF6B35'),
('Çelik Konstrüksiyon', 'celik-konstruksiyon', 'Çelik yapı ve konstrüksiyon projeleri', '#004E89'),
('Güvenlik', 'guvenlik', 'İSG ve güvenlik standartları', '#28a745'),
('Teknoloji', 'teknoloji', 'Sektördeki yeni teknolojiler', '#6f42c1');

-- Varsayılan site ayarları
INSERT INTO site_settings (setting_key, setting_value, setting_type, description) VALUES 
('site_title', 'Kacmazlar İnşaat', 'text', 'Site başlığı'),
('site_description', 'Çelik konstrüksiyon, iskele ve kalıp sistemlerinde profesyonel çözümler', 'textarea', 'Site açıklaması'),
('contact_phone', '+90 543 239 25 50', 'text', 'İletişim telefonu'),
('contact_email', 'info@kacmazlarinsaat.com', 'text', 'İletişim e-postası'),
('contact_address', 'İstanbul, Türkiye', 'text', 'İletişim adresi'),
('social_facebook', '', 'text', 'Facebook sayfası URL'),
('social_twitter', '', 'text', 'Twitter hesabı URL'),
('social_instagram', '', 'text', 'Instagram hesabı URL'),
('social_linkedin', '', 'text', 'LinkedIn sayfası URL'),
('google_analytics', '', 'text', 'Google Analytics kodu'),
('meta_keywords', 'çelik konstrüksiyon, iskele sistemleri, kalıp sistemleri', 'text', 'Meta keywords');

-- Örnek proje verileri
INSERT INTO projects (title, slug, short_description, description, category, location, start_date, end_date, area, client, status, featured_image, is_featured, is_published) VALUES 
('Facade İskele Sistemi', 'facade-iskele-sistemi', 'Yüksek katlı bina facade iskelesi kurulumu', 'Bu projede, İstanbul\'da bulunan 15 katlı bir binanın facade iskelesi kurulumunu gerçekleştirdik. Proje, binanın dış cephe yenileme çalışmaları için gerekli olan güvenli çalışma platformunu sağlamak amacıyla hayata geçirildi.', 'iskele', 'İstanbul', '2024-03-01', '2024-06-01', '2.500 m²', 'Özel Müşteri', 'tamamlandi', 'project1.jpg', TRUE, TRUE),
('Çelik Bina Konstrüksiyonu', 'celik-bina-konstruksiyonu', 'Çelik taşıyıcı sistem ve çatı montajı', 'Bu projede, Ankara\'da bulunan endüstriyel bir tesisin çelik konstrüksiyon yapısını tasarladık ve montajını gerçekleştirdik. Proje, 3.200 m² alanda çelik taşıyıcı sistem ve çatı montajını içermektedir.', 'celik', 'Ankara', '2024-01-01', '2024-05-01', '3.200 m²', 'Endüstriyel Müşteri', 'tamamlandi', 'project2.jpg', TRUE, TRUE),
('Ringlock İskele Sistemi', 'ringlock-iskele-sistemi', 'CE belgeli güvenli iskele kurulumu', 'Modern ringlock iskele sistemi ile güvenli çalışma platformu oluşturduk. Sistem, yüksek dayanıklılık ve kolay montaj özellikleri ile öne çıkıyor.', 'iskele', 'İzmir', '2024-02-01', '2024-04-01', '1.800 m²', 'İnşaat Firması', 'tamamlandi', 'project3.jpg', FALSE, TRUE);

-- Örnek blog yazıları
INSERT INTO blog_posts (title, slug, excerpt, content, category_id, is_published, is_featured, published_at) VALUES 
('İskele Güvenliği ve İSG Standartları', 'iskele-guvenligi-ve-isg-standartlari', 'İskele kurulumunda güvenlik standartları ve İSG kuralları hakkında detaylı bilgiler.', 'İskele sistemlerinde güvenlik, çalışanların hayatı için kritik önem taşır. Bu yazıda, iskele kurulumunda dikkat edilmesi gereken güvenlik standartlarını ele alıyoruz...', 1, TRUE, TRUE, NOW()),
('Çelik Konstrüksiyonun Avantajları', 'celik-konstruksiyonun-avantajlari', 'Çelik yapıların geleneksel yapılara göre sağladığı avantajları keşfedin.', 'Çelik konstrüksiyon, modern inşaat sektöründe giderek daha fazla tercih edilen bir yapım tekniğidir. Bu yazıda çelik yapıların avantajlarını detaylı olarak inceliyoruz...', 2, TRUE, FALSE, NOW());

-- İndeksler (performans için)
CREATE INDEX idx_projects_category ON projects(category);
CREATE INDEX idx_projects_status ON projects(status);
CREATE INDEX idx_projects_published ON projects(is_published);
CREATE INDEX idx_projects_featured ON projects(is_featured);
CREATE INDEX idx_blog_posts_category ON blog_posts(category_id);
CREATE INDEX idx_blog_posts_published ON blog_posts(is_published);
CREATE INDEX idx_blog_posts_featured ON blog_posts(is_featured);
CREATE INDEX idx_blog_posts_published_at ON blog_posts(published_at);
CREATE INDEX idx_contact_messages_read ON contact_messages(is_read);
CREATE INDEX idx_contact_messages_created ON contact_messages(created_at);
