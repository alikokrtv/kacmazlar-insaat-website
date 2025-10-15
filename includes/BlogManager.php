<?php
/**
 * Kacmazlar İnşaat CMS - Blog Yönetimi Sınıfı
 */

require_once 'includes/Database.php';

class BlogManager {
    private $db;
    
    public function __construct() {
        $this->db = DatabaseSingleton::getInstance();
    }
    
    // Tüm blog yazılarını getir
    public function getAllPosts($limit = null, $offset = 0, $categoryId = null, $published = null) {
        $sql = "SELECT bp.*, bc.name as category_name, bc.color as category_color, a.full_name as author_name
                FROM blog_posts bp 
                LEFT JOIN blog_categories bc ON bp.category_id = bc.id
                LEFT JOIN admins a ON bp.created_by = a.id";
        
        $conditions = [];
        $params = [];
        
        if ($categoryId) {
            $conditions[] = "bp.category_id = ?";
            $params[] = $categoryId;
        }
        
        if ($published !== null) {
            $conditions[] = "bp.is_published = ?";
            $params[] = $published ? 1 : 0;
        }
        
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $sql .= " ORDER BY bp.published_at DESC, bp.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    // Tek blog yazısı getir
    public function getPost($id) {
        $sql = "SELECT bp.*, bc.name as category_name, bc.color as category_color, a.full_name as author_name
                FROM blog_posts bp 
                LEFT JOIN blog_categories bc ON bp.category_id = bc.id
                LEFT JOIN admins a ON bp.created_by = a.id
                WHERE bp.id = ?";
        return $this->db->fetch($sql, [$id]);
    }
    
    // Slug ile blog yazısı getir
    public function getPostBySlug($slug) {
        $sql = "SELECT bp.*, bc.name as category_name, bc.color as category_color, a.full_name as author_name
                FROM blog_posts bp 
                LEFT JOIN blog_categories bc ON bp.category_id = bc.id
                LEFT JOIN admins a ON bp.created_by = a.id
                WHERE bp.slug = ? AND bp.is_published = 1";
        return $this->db->fetch($sql, [$slug]);
    }
    
    // Blog yazısı ekle
    public function createPost($data) {
        $sql = "INSERT INTO blog_posts (
                    title, slug, excerpt, content, featured_image, category_id, 
                    tags, meta_title, meta_description, is_published, is_featured, 
                    published_at, created_by
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['title'],
            $data['slug'],
            $data['excerpt'],
            $data['content'],
            $data['featured_image'],
            $data['category_id'],
            json_encode($data['tags']),
            $data['meta_title'],
            $data['meta_description'],
            $data['is_published'] ? 1 : 0,
            $data['is_featured'] ? 1 : 0,
            $data['is_published'] ? ($data['published_at'] ?? date('Y-m-d H:i:s')) : null,
            $data['created_by']
        ];
        
        $this->db->query($sql, $params);
        return $this->db->lastInsertId();
    }
    
    // Blog yazısı güncelle
    public function updatePost($id, $data) {
        $sql = "UPDATE blog_posts SET 
                    title = ?, slug = ?, excerpt = ?, content = ?, featured_image = ?, 
                    category_id = ?, tags = ?, meta_title = ?, meta_description = ?, 
                    is_published = ?, is_featured = ?, published_at = ?
                WHERE id = ?";
        
        $params = [
            $data['title'],
            $data['slug'],
            $data['excerpt'],
            $data['content'],
            $data['featured_image'],
            $data['category_id'],
            json_encode($data['tags']),
            $data['meta_title'],
            $data['meta_description'],
            $data['is_published'] ? 1 : 0,
            $data['is_featured'] ? 1 : 0,
            $data['is_published'] ? ($data['published_at'] ?? date('Y-m-d H:i:s')) : null,
            $id
        ];
        
        return $this->db->query($sql, $params);
    }
    
    // Blog yazısı sil
    public function deletePost($id) {
        $sql = "DELETE FROM blog_posts WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }
    
    // Slug oluştur
    public function generateSlug($title, $excludeId = null) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $slug = trim($slug, '-');
        
        $originalSlug = $slug;
        $counter = 1;
        
        while (true) {
            $sql = "SELECT id FROM blog_posts WHERE slug = ?";
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
    
    // Öne çıkan blog yazılarını getir
    public function getFeaturedPosts($limit = 3) {
        $sql = "SELECT bp.*, bc.name as category_name, bc.color as category_color
                FROM blog_posts bp 
                LEFT JOIN blog_categories bc ON bp.category_id = bc.id
                WHERE bp.is_featured = 1 AND bp.is_published = 1 
                ORDER BY bp.published_at DESC 
                LIMIT ?";
        return $this->db->fetchAll($sql, [$limit]);
    }
    
    // Son blog yazılarını getir
    public function getRecentPosts($limit = 5) {
        $sql = "SELECT bp.*, bc.name as category_name, bc.color as category_color
                FROM blog_posts bp 
                LEFT JOIN blog_categories bc ON bp.category_id = bc.id
                WHERE bp.is_published = 1 
                ORDER BY bp.published_at DESC 
                LIMIT ?";
        return $this->db->fetchAll($sql, [$limit]);
    }
    
    // Kategoriye göre blog yazılarını getir
    public function getPostsByCategory($categoryId, $limit = null) {
        $sql = "SELECT bp.*, bc.name as category_name, bc.color as category_color
                FROM blog_posts bp 
                LEFT JOIN blog_categories bc ON bp.category_id = bc.id
                WHERE bp.category_id = ? AND bp.is_published = 1 
                ORDER BY bp.published_at DESC";
        
        $params = [$categoryId];
        
        if ($limit) {
            $sql .= " LIMIT ?";
            $params[] = $limit;
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    // Blog yazısı görüntülenme sayısını artır
    public function incrementViewCount($id) {
        $sql = "UPDATE blog_posts SET view_count = view_count + 1 WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }
    
    // Blog yazısı sayısını getir
    public function getPostCount($categoryId = null, $published = null) {
        $sql = "SELECT COUNT(*) as count FROM blog_posts";
        $conditions = [];
        $params = [];
        
        if ($categoryId) {
            $conditions[] = "category_id = ?";
            $params[] = $categoryId;
        }
        
        if ($published !== null) {
            $conditions[] = "is_published = ?";
            $params[] = $published ? 1 : 0;
        }
        
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $result = $this->db->fetch($sql, $params);
        return $result['count'];
    }
    
    // Blog kategorilerini getir
    public function getCategories() {
        $sql = "SELECT * FROM blog_categories WHERE is_active = 1 ORDER BY name ASC";
        return $this->db->fetchAll($sql);
    }
    
    // Blog kategorisi ekle
    public function createCategory($data) {
        $sql = "INSERT INTO blog_categories (name, slug, description, color) VALUES (?, ?, ?, ?)";
        $params = [$data['name'], $data['slug'], $data['description'], $data['color']];
        
        $this->db->query($sql, $params);
        return $this->db->lastInsertId();
    }
    
    // Blog kategorisi güncelle
    public function updateCategory($id, $data) {
        $sql = "UPDATE blog_categories SET name = ?, slug = ?, description = ?, color = ? WHERE id = ?";
        $params = [$data['name'], $data['slug'], $data['description'], $data['color'], $id];
        
        return $this->db->query($sql, $params);
    }
    
    // Blog kategorisi sil
    public function deleteCategory($id) {
        $sql = "DELETE FROM blog_categories WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }
}
