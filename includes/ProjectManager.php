<?php
/**
 * Kacmazlar İnşaat CMS - Proje Yönetimi Sınıfı
 */

require_once 'includes/Database.php';

class ProjectManager {
    private $db;
    
    public function __construct() {
        $this->db = DatabaseSingleton::getInstance();
    }
    
    // Tüm projeleri getir
    public function getAllProjects($limit = null, $offset = 0, $category = null, $status = null) {
        $sql = "SELECT p.*, a.full_name as created_by_name 
                FROM projects p 
                LEFT JOIN admins a ON p.created_by = a.id";
        
        $conditions = [];
        $params = [];
        
        if ($category) {
            $conditions[] = "p.category = ?";
            $params[] = $category;
        }
        
        if ($status) {
            $conditions[] = "p.status = ?";
            $params[] = $status;
        }
        
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $sql .= " ORDER BY p.sort_order ASC, p.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    // Tek proje getir
    public function getProject($id) {
        $sql = "SELECT p.*, a.full_name as created_by_name 
                FROM projects p 
                LEFT JOIN admins a ON p.created_by = a.id 
                WHERE p.id = ?";
        return $this->db->fetch($sql, [$id]);
    }
    
    // Slug ile proje getir
    public function getProjectBySlug($slug) {
        $sql = "SELECT p.*, a.full_name as created_by_name 
                FROM projects p 
                LEFT JOIN admins a ON p.created_by = a.id 
                WHERE p.slug = ? AND p.is_published = 1";
        return $this->db->fetch($sql, [$slug]);
    }
    
    // Proje ekle
    public function createProject($data) {
        $sql = "INSERT INTO projects (
                    title, slug, short_description, description, category, 
                    location, start_date, end_date, area, client, status, 
                    featured_image, gallery_images, meta_title, meta_description, 
                    is_featured, is_published, sort_order, created_by
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['title'],
            $data['slug'],
            $data['short_description'],
            $data['description'],
            $data['category'],
            $data['location'],
            $data['start_date'],
            $data['end_date'],
            $data['area'],
            $data['client'],
            $data['status'],
            $data['featured_image'],
            json_encode($data['gallery_images']),
            $data['meta_title'],
            $data['meta_description'],
            $data['is_featured'] ? 1 : 0,
            $data['is_published'] ? 1 : 0,
            $data['sort_order'],
            $data['created_by']
        ];
        
        $this->db->query($sql, $params);
        return $this->db->lastInsertId();
    }
    
    // Proje güncelle
    public function updateProject($id, $data) {
        $sql = "UPDATE projects SET 
                    title = ?, slug = ?, short_description = ?, description = ?, 
                    category = ?, location = ?, start_date = ?, end_date = ?, 
                    area = ?, client = ?, status = ?, featured_image = ?, 
                    gallery_images = ?, meta_title = ?, meta_description = ?, 
                    is_featured = ?, is_published = ?, sort_order = ?
                WHERE id = ?";
        
        $params = [
            $data['title'],
            $data['slug'],
            $data['short_description'],
            $data['description'],
            $data['category'],
            $data['location'],
            $data['start_date'],
            $data['end_date'],
            $data['area'],
            $data['client'],
            $data['status'],
            $data['featured_image'],
            json_encode($data['gallery_images']),
            $data['meta_title'],
            $data['meta_description'],
            $data['is_featured'] ? 1 : 0,
            $data['is_published'] ? 1 : 0,
            $data['sort_order'],
            $id
        ];
        
        return $this->db->query($sql, $params);
    }
    
    // Proje sil
    public function deleteProject($id) {
        $sql = "DELETE FROM projects WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }
    
    // Slug oluştur
    public function generateSlug($title, $excludeId = null) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $slug = trim($slug, '-');
        
        $originalSlug = $slug;
        $counter = 1;
        
        while (true) {
            $sql = "SELECT id FROM projects WHERE slug = ?";
            $params = [$slug];
            
            if ($excludeId) {
                $sql .= " AND id != ?";
                $params[] = $excludeId;
            }
            
            $existing = $this->db->fetch($sql, $params);
            
            if (!$existing) {
                break;
            }
            
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    // Öne çıkan projeleri getir
    public function getFeaturedProjects($limit = 3) {
        $sql = "SELECT * FROM projects 
                WHERE is_featured = 1 AND is_published = 1 
                ORDER BY sort_order ASC, created_at DESC 
                LIMIT ?";
        return $this->db->fetchAll($sql, [$limit]);
    }
    
    // Kategoriye göre projeleri getir
    public function getProjectsByCategory($category, $limit = null) {
        $sql = "SELECT * FROM projects 
                WHERE category = ? AND is_published = 1 
                ORDER BY sort_order ASC, created_at DESC";
        
        $params = [$category];
        
        if ($limit) {
            $sql .= " LIMIT ?";
            $params[] = $limit;
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    // Proje sayısını getir
    public function getProjectCount($category = null, $status = null) {
        $sql = "SELECT COUNT(*) as count FROM projects";
        $conditions = [];
        $params = [];
        
        if ($category) {
            $conditions[] = "category = ?";
            $params[] = $category;
        }
        
        if ($status) {
            $conditions[] = "status = ?";
            $params[] = $status;
        }
        
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $result = $this->db->fetch($sql, $params);
        return $result['count'];
    }
}
