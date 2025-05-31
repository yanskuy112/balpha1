<?php
// Database configuration for Vercel
class Database {
    private $host;
    private $username;
    private $password;
    private $database;
    private $connection;
    
    public function __construct() {
        // Vercel environment variables - prioritize $_ENV then getenv()
        $this->host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?? '';
        $this->username = $_ENV['DB_USERNAME'] ?? getenv('DB_USERNAME') ?? '';
        $this->password = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?? '';
        $this->database = $_ENV['DB_DATABASE'] ?? getenv('DB_DATABASE') ?? '';
        
        // Fallback values for development (remove in production)
        if (empty($this->host)) {
            error_log("Warning: Database environment variables not set");
        }
    }
    
    public function connect() {
        // Skip connection if credentials are missing
        if (empty($this->host) || empty($this->username) || empty($this->password) || empty($this->database)) {
            throw new Exception("Database credentials not configured");
        }
        
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->database};charset=utf8mb4;port=3306";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
                PDO::ATTR_TIMEOUT => 5, // 5 second timeout
            ];
            
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
            return $this->connection;
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }
    
    public function getConnection() {
        if ($this->connection === null) {
            $this->connect();
        }
        return $this->connection;
    }
    
    // Create table if not exists
    public function createTable() {
        $sql = "CREATE TABLE IF NOT EXISTS kegiatan_harian (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tanggal DATE NOT NULL,
            waktu TIME NOT NULL,
            kategori VARCHAR(100) NOT NULL,
            catatan TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        try {
            $this->getConnection()->exec($sql);
            return true;
        } catch (PDOException $e) {
            error_log("Table creation failed: " . $e->getMessage());
            return false;
        }
    }
}

// Initialize database connection
$pdo = null;
$use_database = false;

try {
    $db = new Database();
    $pdo = $db->getConnection();
    $db->createTable();
    $use_database = true;
} catch (Exception $e) {
    // Fallback to session storage if database fails
    $use_database = false;
    error_log("Database initialization failed, using session storage: " . $e->getMessage());
}
?>