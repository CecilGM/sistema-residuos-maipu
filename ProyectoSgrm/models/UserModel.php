<?php
class UserModel {
    private $conn;
    private $table_name = "usuarios";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function findOrCreateUser($nombre, $email, $telefono = '', $direccion = '') {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['id'];
        } else {
            $query = "INSERT INTO " . $this->table_name . " (nombre, email, telefono, direccion) 
                     VALUES (:nombre, :email, :telefono, :direccion)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":nombre", $nombre);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":telefono", $telefono);
            $stmt->bindParam(":direccion", $direccion);
            
            if($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
        }
        return false;
    }
}
?>