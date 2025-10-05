<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../models/ReportModel.php';

$database = new Database();
$db = $database->getConnection();

$reportModel = new ReportModel($db);

$email = isset($_GET['email']) ? $_GET['email'] : '';

if(!empty($email)) {
    $stmt = $reportModel->getReportsByEmail($email);
    $num = $stmt->rowCount();
    
    if($num > 0) {
        $reports_arr = array();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $report_item = array(
                "id" => $row['id'],
                "numero_seguimiento" => $row['numero_seguimiento'],
                "usuario_nombre" => $row['usuario_nombre'],
                "ubicacion" => $row['ubicacion'],
                "direccion_especifica" => $row['direccion_especifica'],
                "descripcion" => $row['descripcion'],
                "estado" => $row['estado'],
                "fecha_creacion" => $row['fecha_creacion'],
                "fotos" => $row['fotos']
            );
            array_push($reports_arr, $report_item);
        }
        
        http_response_code(200);
        echo json_encode($reports_arr);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "No se encontraron reportes para este email"));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Email requerido"));
}
?>