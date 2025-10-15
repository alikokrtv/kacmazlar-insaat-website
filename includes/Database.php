<?php
/**
 * Kacmazlar İnşaat CMS - Veritabanı Bağlantı Sınıfı
 */

class Database {
    private $host;
    private $dbname;
    private $username;
    private $password;
    private $charset;
    private $pdo;
    
    public function __construct() {
        $this->host = 'localhost';
        $this->dbname = 'kacmazlar_cms';
        $this->username = 'root'; // Hosting'de değiştirilecek
        $this->password = ''; // Hosting'de değiştirilecek
        $this->charset = 'utf8mb4';
        
        $this->connect();
    }
    
    private function connect() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
    
    public function getConnection() {
        return $this->pdo;
    }
    
    // Genel sorgu çalıştırma
    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
    
    // Tek satır getirme
    public function fetch($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }
    
    // Tüm satırları getirme
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    // Son eklenen ID'yi getirme
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
    
    // Satır sayısını getirme
    public function rowCount($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
    
    // Transaction başlatma
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }
    
    // Transaction commit
    public function commit() {
        return $this->pdo->commit();
    }
    
    // Transaction rollback
    public function rollback() {
        return $this->pdo->rollback();
    }
}

// Singleton pattern için
class DatabaseSingleton {
    private static $instance = null;
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
}
