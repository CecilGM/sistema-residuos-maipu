<?php
class ReportModel {
    private $conn;
    private $table_name = "reportes";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createReport($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                 (usuario_id, numero_seguimiento, ubicacion, direccion_especifica, descripcion, fotos) 
                 VALUES (:usuario_id, :numero_seguimiento, :ubicacion, :direccion_especifica, :descripcion, :fotos)";
        
        $stmt = $this->conn->prepare($query);
        
        $numero_seguimiento = "SGRM-" . date('Y-m-d') . "-" . sprintf("%04d", rand(1, 9999));
        
        $stmt->bindParam(":usuario_id", $data['usuario_id']);
        $stmt->bindParam(":numero_seguimiento", $numero_seguimiento);
        $stmt->bindParam(":ubicacion", $data['ubicacion']);
        $stmt->bindParam(":direccion_especifica", $data['direccion_especifica']);
        $stmt->bindParam(":descripcion", $data['descripcion']);
        $stmt->bindParam(":fotos", $data['fotos']);
        
        if($stmt->execute()) {
            return $numero_seguimiento;
        }
        return false;
    }

    public function getReportsByEmail($email) {
        $query = "SELECT r.*, u.nombre as usuario_nombre 
                 FROM reportes r 
                 JOIN usuarios u ON r.usuario_id = u.id 
                 WHERE u.email = :email 
                 ORDER BY r.fecha_creacion DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        
        return $stmt;
    }
}
?>