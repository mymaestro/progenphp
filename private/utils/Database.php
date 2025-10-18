<?php
/**
 * Database Helper Class
 * 
 * Simple database wrapper for PDO operations
 */

class Database {
    private $pdo;
    private $config;
    
    public function __construct($config = null) {
        $this->config = $config ?? include __DIR__ . '/../config/app.php';
        $this->connect();
    }
    
    /**
     * Establish database connection
     */
    private function connect() {
        try {
            $dbConfig = $this->config['database']['default'];
            $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset={$dbConfig['charset']}";
            
            $this->pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $dbConfig['options']);
        } catch (PDOException $e) {
            logMessage('Database connection failed: ' . $e->getMessage(), 'error');
            throw new Exception('Database connection failed');
        }
    }
    
    /**
     * Execute a query and return results
     * 
     * @param string $sql SQL query
     * @param array $params Parameters for prepared statement
     * @return array|false Query results or false on failure
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            logMessage('Query failed: ' . $e->getMessage(), 'error');
            return false;
        }
    }
    
    /**
     * Execute a query and return single row
     * 
     * @param string $sql SQL query
     * @param array $params Parameters for prepared statement
     * @return array|false Single row or false on failure
     */
    public function queryOne($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            logMessage('Query failed: ' . $e->getMessage(), 'error');
            return false;
        }
    }
    
    /**
     * Execute an insert/update/delete query
     * 
     * @param string $sql SQL query
     * @param array $params Parameters for prepared statement
     * @return int|false Number of affected rows or false on failure
     */
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            logMessage('Execute failed: ' . $e->getMessage(), 'error');
            return false;
        }
    }
    
    /**
     * Get the last inserted ID
     * 
     * @return string Last insert ID
     */
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Begin transaction
     */
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }
    
    /**
     * Commit transaction
     */
    public function commit() {
        return $this->pdo->commit();
    }
    
    /**
     * Rollback transaction
     */
    public function rollback() {
        return $this->pdo->rollback();
    }
    
    /**
     * Check if table exists
     * 
     * @param string $tableName Table name to check
     * @return bool True if exists, false otherwise
     */
    public function tableExists($tableName) {
        $sql = "SHOW TABLES LIKE ?";
        $result = $this->queryOne($sql, [$tableName]);
        return $result !== false;
    }
}