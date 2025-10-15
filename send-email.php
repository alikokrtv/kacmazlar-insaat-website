<?php
/**
 * Kacmazlar İnşaat - İletişim Formu E-posta Gönderici
 * PHPMailer kullanarak güvenli e-posta gönderimi
 */

// Hata raporlamayı aç (geliştirme ortamında)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Üretimde 0 olmalı
ini_set('log_errors', 1);

// CORS ve güvenlik başlıkları
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');

// Sadece POST isteklerine izin ver
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Sadece POST istekleri kabul edilir.'
    ]);
    exit;
}

// Autoloader ve konfigürasyon
require_once __DIR__ . '/vendor/autoload.php';
$config = require_once __DIR__ . '/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Güvenli veri temizleme fonksiyonu
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * E-posta validasyonu
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Telefon numarası temizleme
 */
function sanitize_phone($phone) {
    return preg_replace('/[^0-9+\s\-\(\)]/', '', $phone);
}

// Form verilerini al ve temizle
$name = isset($_POST['name']) ? sanitize_input($_POST['name']) : '';
$email = isset($_POST['email']) ? sanitize_input($_POST['email']) : '';
$phone = isset($_POST['phone']) ? sanitize_phone($_POST['phone']) : '';
$message = isset($_POST['message']) ? sanitize_input($_POST['message']) : '';

// Validasyon
$errors = [];

if (empty($name) || strlen($name) < 2) {
    $errors[] = 'Ad soyad en az 2 karakter olmalıdır.';
}

if (empty($email) || !validate_email($email)) {
    $errors[] = 'Geçerli bir e-posta adresi giriniz.';
}

if (empty($phone) || strlen($phone) < 10) {
    $errors[] = 'Geçerli bir telefon numarası giriniz.';
}

if (empty($message) || strlen($message) < 10) {
    $errors[] = 'Mesaj en az 10 karakter olmalıdır.';
}

// Hata varsa dön
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => implode(' ', $errors),
        'errors' => $errors
    ]);
    exit;
}

// Basit spam koruması - honeypot (isteğe bağlı)
if (isset($_POST['website']) && !empty($_POST['website'])) {
    // Bot detected
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Spam tespit edildi.'
    ]);
    exit;
}

// Rate limiting - Basit IP bazlı (isteğe bağlı)
$ip = $_SERVER['REMOTE_ADDR'];
$rate_limit_file = sys_get_temp_dir() . '/contact_form_' . md5($ip) . '.txt';

if (file_exists($rate_limit_file)) {
    $last_submit = (int)file_get_contents($rate_limit_file);
    $time_passed = time() - $last_submit;
    
    if ($time_passed < 60) { // 1 dakika
        http_response_code(429);
        echo json_encode([
            'success' => false,
            'message' => 'Çok fazla istek. Lütfen ' . (60 - $time_passed) . ' saniye bekleyiniz.'
        ]);
        exit;
    }
}

// PHPMailer ile e-posta gönder
try {
    $mail = new PHPMailer(true);
    
    // SMTP ayarları
    $mail->isSMTP();
    $mail->Host = $config['smtp_host'];
    $mail->SMTPAuth = true;
    $mail->Username = $config['smtp_username'];
    $mail->Password = $config['smtp_password'];
    $mail->SMTPSecure = $config['smtp_secure'];
    $mail->Port = $config['smtp_port'];
    $mail->CharSet = 'UTF-8';
    
    // Debug ayarı (üretimde 0 olmalı)
    $mail->SMTPDebug = SMTP::DEBUG_OFF;
    
    // Gönderen ve alıcı bilgileri
    $mail->setFrom($config['from_email'], $config['from_name']);
    $mail->addAddress($config['to_email'], $config['to_name']);
    $mail->addReplyTo($email, $name);
    
    // E-posta içeriği
    $mail->isHTML(true);
    $mail->Subject = $config['subject_prefix'] . $name . ' - Yeni İletişim Formu Mesajı';
    
    // HTML içerik
    $mail->Body = "
    <!DOCTYPE html>
    <html lang='tr'>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #004E89; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
            .content { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; }
            .field { margin-bottom: 15px; padding: 10px; background: white; border-left: 3px solid #FF6B35; }
            .label { font-weight: bold; color: #004E89; }
            .value { margin-top: 5px; }
            .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Yeni İletişim Formu Mesajı</h2>
            </div>
            <div class='content'>
                <div class='field'>
                    <div class='label'>👤 Ad Soyad:</div>
                    <div class='value'>" . htmlspecialchars($name) . "</div>
                </div>
                <div class='field'>
                    <div class='label'>📧 E-posta:</div>
                    <div class='value'>" . htmlspecialchars($email) . "</div>
                </div>
                <div class='field'>
                    <div class='label'>📱 Telefon:</div>
                    <div class='value'>" . htmlspecialchars($phone) . "</div>
                </div>
                <div class='field'>
                    <div class='label'>💬 Mesaj:</div>
                    <div class='value'>" . nl2br(htmlspecialchars($message)) . "</div>
                </div>
                <div class='field'>
                    <div class='label'>🕒 Gönderim Zamanı:</div>
                    <div class='value'>" . date('d.m.Y H:i:s') . "</div>
                </div>
                <div class='field'>
                    <div class='label'>🌐 IP Adresi:</div>
                    <div class='value'>" . htmlspecialchars($ip) . "</div>
                </div>
            </div>
            <div class='footer'>
                <p>Bu mesaj www.kacmazlarinsaat.com iletişim formundan gönderilmiştir.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Düz metin alternatifi
    $mail->AltBody = "
    Yeni İletişim Formu Mesajı
    
    Ad Soyad: $name
    E-posta: $email
    Telefon: $phone
    
    Mesaj:
    $message
    
    Gönderim Zamanı: " . date('d.m.Y H:i:s') . "
    IP Adresi: $ip
    ";
    
    // E-postayı gönder
    $mail->send();
    
    // Rate limit dosyasını güncelle
    file_put_contents($rate_limit_file, time());
    
    // Başarılı yanıt
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Mesajınız başarıyla gönderildi. En kısa sürede size dönüş yapılacaktır.'
    ]);
    
} catch (Exception $e) {
    // Hata loglama
    error_log("PHPMailer Error: " . $mail->ErrorInfo);
    
    // Kullanıcıya genel hata mesajı
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'E-posta gönderilirken bir hata oluştu. Lütfen daha sonra tekrar deneyin veya doğrudan telefon ile iletişime geçin.',
        'error' => 'SMTP Error' // Detayları üretimde gösterme
    ]);
}

