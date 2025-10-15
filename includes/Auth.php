<?php
/**
 * Kacmazlar İnşaat CMS - Admin Authentication Sınıfı
 */

require_once 'Database.php';

class Auth {
    private $db;
    
    public function __construct() {
        $this->db = DatabaseSingleton::getInstance();
    }
    
    // Admin girişi
    public function login($username, $password) {
        $sql = "SELECT id, username, email, password, full_name, role, is_active FROM admins WHERE (username = ? OR email = ?) AND is_active = 1";
        $admin = $this->db->fetch($sql, [$username, $username]);
        
        if ($admin && password_verify($password, $admin['password'])) {
            // Son giriş zamanını güncelle
            $this->db->query("UPDATE admins SET last_login = NOW() WHERE id = ?", [$admin['id']]);
            
            // Session başlat
            session_start();
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['admin_name'] = $admin['full_name'];
            $_SESSION['admin_role'] = $admin['role'];
            $_SESSION['admin_logged_in'] = true;
            
            return true;
        }
        
        return false;
    }
    
    // Admin çıkışı
    public function logout() {
        session_start();
        session_destroy();
        return true;
    }
    
    // Giriş kontrolü
    public function isLoggedIn() {
        session_start();
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }
    
    // Admin bilgilerini getir
    public function getCurrentAdmin() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['admin_id'],
            'username' => $_SESSION['admin_username'],
            'email' => $_SESSION['admin_email'],
            'full_name' => $_SESSION['admin_name'],
            'role' => $_SESSION['admin_role']
        ];
    }
    
    // Admin yetki kontrolü
    public function hasRole($requiredRole) {
        $admin = $this->getCurrentAdmin();
        if (!$admin) return false;
        
        $roles = ['editor' => 1, 'admin' => 2];
        return $roles[$admin['role']] >= $roles[$requiredRole];
    }
    
    // Şifre değiştirme
    public function changePassword($adminId, $currentPassword, $newPassword) {
        $sql = "SELECT password FROM admins WHERE id = ?";
        $admin = $this->db->fetch($sql, [$adminId]);
        
        if ($admin && password_verify($currentPassword, $admin['password'])) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $sql = "UPDATE admins SET password = ? WHERE id = ?";
            $this->db->query($sql, [$hashedPassword, $adminId]);
            return true;
        }
        
        return false;
    }
    
    // Yeni admin ekleme (sadece admin yetkisi)
    public function createAdmin($username, $email, $password, $fullName, $role = 'editor') {
        if (!$this->hasRole('admin')) {
            return false;
        }
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO admins (username, email, password, full_name, role) VALUES (?, ?, ?, ?, ?)";
        
        try {
            $this->db->query($sql, [$username, $email, $hashedPassword, $fullName, $role]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // CSRF token oluşturma
    public function generateCSRFToken() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        return $token;
    }
    
    // CSRF token doğrulama
    public function verifyCSRFToken($token) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}
