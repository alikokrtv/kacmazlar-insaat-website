<?php
/**
 * E-posta Yapılandırması - Örnek Dosya
 * Bu dosyayı 'config.php' olarak kopyalayın ve kendi bilgilerinizi girin
 */

return [
    // SMTP Sunucu Ayarları
    'smtp_host' => 'smtp.example.com',  // SMTP sunucu adresi (örn: smtp.gmail.com, mail.kacmazlarinsaat.com)
    'smtp_port' => 587,                  // SMTP port (genellikle 587 veya 465)
    'smtp_secure' => 'tls',              // Güvenlik protokolü: 'tls' veya 'ssl'
    
    // SMTP Kimlik Doğrulama
    'smtp_username' => 'info@kacmazlarinsaat.com',  // SMTP kullanıcı adı (genellikle e-posta adresi)
    'smtp_password' => 'BURAYA_SIFRE_GIRINIZ',      // SMTP şifresi
    
    // E-posta Ayarları
    'from_email' => 'info@kacmazlarinsaat.com',     // Gönderen e-posta adresi
    'from_name' => 'Kacmazlar İnşaat',              // Gönderen ismi
    'to_email' => 'info@kacmazlarinsaat.com',       // Alıcı e-posta adresi (formdan gelen mesajlar buraya gelecek)
    'to_name' => 'Kacmazlar İnşaat',                // Alıcı ismi
    
    // E-posta Şablonu
    'subject_prefix' => '[Web Sitesi İletişim] ',   // E-posta konu ön eki
    
    // Güvenlik
    'recaptcha_enabled' => false,                    // reCAPTCHA kullanılsın mı? (şimdilik false)
    'allowed_origins' => [                           // İzin verilen domainler
        'http://localhost:8080',
        'https://www.kacmazlarinsaat.com',
        'https://kacmazlarinsaat.com'
    ]
];

