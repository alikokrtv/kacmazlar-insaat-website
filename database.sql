-- Kacmazlar İnşaat CMS Veritabanı Yapısı
-- SQLite uyumlu

-- Admin kullanıcıları tablosu
CREATE TABLE IF NOT EXISTS admins (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role VARCHAR(20) DEFAULT 'editor' CHECK (role IN ('admin', 'editor')),
    is_active BOOLEAN DEFAULT 1,
    last_login DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Projeler tablosu
CREATE TABLE IF NOT EXISTS projects (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    short_description TEXT,
    description TEXT,
    category VARCHAR(20) NOT NULL CHECK (category IN ('iskele', 'celik', 'endustriyel')),
    location VARCHAR(100),
    start_date DATE,
    end_date DATE,
    area VARCHAR(50),
    client VARCHAR(100),
    status VARCHAR(20) DEFAULT 'planlama' CHECK (status IN ('planlama', 'devam', 'tamamlandi')),
    featured_image VARCHAR(255),
    gallery_images TEXT, -- JSON as TEXT in SQLite
    meta_title VARCHAR(200),
    meta_description TEXT,
    is_featured BOOLEAN DEFAULT 0,
    is_published BOOLEAN DEFAULT 1,
    sort_order INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER,
    FOREIGN KEY (created_by) REFERENCES admins(id) ON DELETE SET NULL
);

-- Blog kategorileri tablosu
CREATE TABLE IF NOT EXISTS blog_categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    color VARCHAR(7) DEFAULT '#004E89',
    is_active BOOLEAN DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Blog yazıları tablosu
CREATE TABLE IF NOT EXISTS blog_posts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    excerpt TEXT,
    content TEXT NOT NULL,
    featured_image VARCHAR(255),
    category_id INTEGER,
    tags TEXT, -- JSON as TEXT in SQLite
    meta_title VARCHAR(200),
    meta_description TEXT,
    is_published BOOLEAN DEFAULT 0,
    is_featured BOOLEAN DEFAULT 0,
    view_count INTEGER DEFAULT 0,
    published_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER,
    FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES admins(id) ON DELETE SET NULL
);

-- Site ayarları tablosu
CREATE TABLE IF NOT EXISTS site_settings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type VARCHAR(20) DEFAULT 'text' CHECK (setting_type IN ('text', 'textarea', 'image', 'boolean', 'json')),
    description TEXT,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- İletişim mesajları tablosu
CREATE TABLE IF NOT EXISTS contact_messages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(200),
    message TEXT NOT NULL,
    ip_address VARCHAR(45),
    is_read BOOLEAN DEFAULT 0,
    replied_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Medya dosyaları tablosu
CREATE TABLE IF NOT EXISTS media_files (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INTEGER NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    file_type VARCHAR(20) NOT NULL CHECK (file_type IN ('image', 'document', 'video')),
    alt_text VARCHAR(200),
    description TEXT,
    uploaded_by INTEGER,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES admins(id) ON DELETE SET NULL
);

-- Varsayılan admin kullanıcısı (şifre: admin123)
INSERT OR IGNORE INTO admins (username, email, password, full_name, role) VALUES 
('admin', 'admin@kacmazlarinsaat.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Site Yöneticisi', 'admin');

-- Varsayılan blog kategorileri
INSERT OR IGNORE INTO blog_categories (name, slug, description, color) VALUES 
('İskele Sistemleri', 'iskele-sistemleri', 'İskele kurulumu ve sistemleri hakkında yazılar', '#FF6B35'),
('Çelik Konstrüksiyon', 'celik-konstruksiyon', 'Çelik yapı ve konstrüksiyon projeleri', '#004E89'),
('Güvenlik', 'guvenlik', 'İSG ve güvenlik standartları', '#28a745'),
('Teknoloji', 'teknoloji', 'Sektördeki yeni teknolojiler', '#6f42c1');

-- Varsayılan site ayarları
INSERT OR IGNORE INTO site_settings (setting_key, setting_value, setting_type, description) VALUES 
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
INSERT OR IGNORE INTO projects (title, slug, short_description, description, category, location, start_date, end_date, area, client, status, featured_image, is_featured, is_published) VALUES 
('Facade İskele Sistemi', 'facade-iskele-sistemi', 'Yüksek katlı bina facade iskelesi kurulumu', 'Bu projede, İstanbul''da bulunan 15 katlı bir binanın facade iskelesi kurulumunu gerçekleştirdik. Proje, binanın dış cephe yenileme çalışmaları için gerekli olan güvenli çalışma platformunu sağlamak amacıyla hayata geçirildi.', 'iskele', 'İstanbul', '2024-03-01', '2024-06-01', '2.500 m²', 'Özel Müşteri', 'tamamlandi', 'project1.jpg', 1, 1),
('Çelik Bina Konstrüksiyonu', 'celik-bina-konstruksiyonu', 'Çelik taşıyıcı sistem ve çatı montajı', 'Bu projede, Ankara''da bulunan endüstriyel bir tesisin çelik konstrüksiyon yapısını tasarladık ve montajını gerçekleştirdik. Proje, 3.200 m² alanda çelik taşıyıcı sistem ve çatı montajını içermektedir.', 'celik', 'Ankara', '2024-01-01', '2024-05-01', '3.200 m²', 'Endüstriyel Müşteri', 'tamamlandi', 'project2.jpg', 1, 1),
('Ringlock İskele Sistemi', 'ringlock-iskele-sistemi', 'CE belgeli güvenli iskele kurulumu', 'Modern ringlock iskele sistemi ile güvenli çalışma platformu oluşturduk. Sistem, yüksek dayanıklılık ve kolay montaj özellikleri ile öne çıkıyor.', 'iskele', 'İzmir', '2024-02-01', '2024-04-01', '1.800 m²', 'İnşaat Firması', 'tamamlandi', 'project3.jpg', 0, 1);

-- Örnek blog yazıları
INSERT OR IGNORE INTO blog_posts (title, slug, excerpt, content, category_id, is_published, is_featured, published_at) VALUES 
('İskele Güvenliği ve İSG Standartları', 'iskele-guvenligi-ve-isg-standartlari', 'İskele kurulumunda güvenlik standartları ve İSG kuralları hakkında detaylı bilgiler.', 'İskele sistemlerinde güvenlik, çalışanların hayatı için kritik önem taşır. Bu yazıda, iskele kurulumunda dikkat edilmesi gereken güvenlik standartlarını ele alıyoruz...', 1, 1, 1, datetime('now')),
('Çelik Konstrüksiyonun Avantajları', 'celik-konstruksiyonun-avantajlari', 'Çelik yapıların geleneksel yapılara göre sağladığı avantajları keşfedin.', 'Çelik konstrüksiyon, modern inşaat sektöründe giderek daha fazla tercih edilen bir yapım tekniğidir. Bu yazıda çelik yapıların avantajlarını detaylı olarak inceliyoruz...', 2, 1, 0, datetime('now'));

-- İndeksler (performans için)
CREATE INDEX IF NOT EXISTS idx_projects_category ON projects(category);
CREATE INDEX IF NOT EXISTS idx_projects_status ON projects(status);
CREATE INDEX IF NOT EXISTS idx_projects_published ON projects(is_published);
CREATE INDEX IF NOT EXISTS idx_projects_featured ON projects(is_featured);
CREATE INDEX IF NOT EXISTS idx_blog_posts_category ON blog_posts(category_id);
CREATE INDEX IF NOT EXISTS idx_blog_posts_published ON blog_posts(is_published);
CREATE INDEX IF NOT EXISTS idx_blog_posts_featured ON blog_posts(is_featured);
CREATE INDEX IF NOT EXISTS idx_blog_posts_published_at ON blog_posts(published_at);
CREATE INDEX IF NOT EXISTS idx_contact_messages_read ON contact_messages(is_read);
CREATE INDEX IF NOT EXISTS idx_contact_messages_created ON contact_messages(created_at);