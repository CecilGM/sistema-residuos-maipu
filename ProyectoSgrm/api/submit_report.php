<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../models/UserModel.php';
include_once '../models/ReportModel.php';

$database = new Database();
$db = $database->getConnection();

$userModel = new UserModel($db);
$reportModel = new ReportModel($db);

$data = json_decode(file_get_contents("php://input"));

if(
    !empty($data->nombre) &&
    !empty($data->email) &&
    !empty($data->ubicacion) &&
    !empty($data->direccion_especifica) &&
    !empty($data->descripcion)
) {
    $usuario_id = $userModel->findOrCreateUser(
        $data->nombre,
        $data->email,
        $data->telefono ?? '',
        $data->direccion ?? ''
    );
    
    if($usuario_id) {
        $report_data = array(
            "usuario_id" => $usuario_id,
            "ubicacion" => $data->ubicacion,
            "direccion_especifica" => $data->direccion_especifica,
            "descripcion" => $data->descripcion,
            "fotos" => $data->fotos ?? ''
        );
        
        $numero_seguimiento = $reportModel->createReport($report_data);
        
        if($numero_seguimiento) {
            http_response_code(201);
            echo json_encode(array(
                "message" => "Reporte creado exitosamente",
                "numero_seguimiento" => $numero_seguimiento,
                "success" => true
            ));
        } else {
            http_response_code(503);
            echo json_encode(array(
                "message" => "No se pudo crear el reporte",
                "success" => false
            ));
        }
    } else {
        http_response_code(503);
        echo json_encode(array(
            "message" => "No se pudo crear/find el usuario",
            "success" => false
        ));
    }
} else {
    http_response_code(400);
    echo json_encode(array(
        "message" => "Datos incompletos. Todos los campos son requeridos.",
        "success" => false
    ));
}
?>