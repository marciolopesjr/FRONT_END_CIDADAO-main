<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/helpers/functions.php';

session_start();

try {
    $db = connect_db();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_SESSION['user_id'])) {
            send_json_response(['error' => 'Você precisa estar logado para criar uma demanda.'], 401);
        }

        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

        if ($contentType === 'application/json') {
            $content = trim(file_get_contents("php://input"));
            $decoded = json_decode($content, true);

            if (!is_array($decoded)) {
                send_json_response(['error' => 'Formato JSON inválido.'], 400);
                exit;
            }

            $category = validate_input($decoded['category'] ?? '');
            $description = validate_input($decoded['description'] ?? '');
            $latitude = $decoded['latitude'] ?? null;
            $longitude = $decoded['longitude'] ?? null;
            // Novos campos: status e secretariat_id (opcionais por enquanto)
            $status = validate_input($decoded['status'] ?? ''); // Pega o status do JSON, se fornecido
            $secretariat_id = $decoded['secretariat_id'] ?? null; // Pega o secretariat_id do JSON, se fornecido
        } else {
            $category = validate_input($_POST['category'] ?? '');
            $description = validate_input($_POST['description'] ?? '');
            $latitude = $_POST['latitude'] ?? null;
            $longitude = $_POST['longitude'] ?? null;
            // Novos campos: status e secretariat_id (opcionais por enquanto)
            $status = validate_input($_POST['status'] ?? ''); // Pega o status do POST, se fornecido
            $secretariat_id = $_POST['secretariat_id'] ?? null; // Pega o secretariat_id do POST, se fornecido
        }

        if (empty($category) || empty($description) || $latitude === null || $longitude === null) {
            send_json_response(['error' => 'Por favor, forneça categoria, descrição e localização da demanda.'], 400);
        }

        // Define um status padrão para novas demandas (ex: "Nova", "Pendente", etc.)
        $default_status = "Nova"; // Você pode escolher um status inicial diferente
        if (empty($status)) { // Se o status não foi fornecido na requisição, usa o padrão
            $status = $default_status;
        }

        $stmt = $db->prepare("INSERT INTO demands (user_id, category, description, latitude, longitude, status, secretariat_id) VALUES (:user_id, :category, :description, :latitude, :longitude, :status, :secretariat_id)");
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':category', $category, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':latitude', $latitude);
        $stmt->bindParam(':longitude', $longitude);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR); // Bind do status
        $stmt->bindParam(':secretariat_id', $secretariat_id, PDO::PARAM_INT); // Bind do secretariat_id (pode ser NULL)
        $stmt->execute();

        send_json_response(['message' => 'Demanda criada com sucesso!'], 201);
    } else {
        send_json_response(['error' => 'Método não permitido.'], 405);
    }

} catch (PDOException $e) {
    $errorMessage = 'Erro de banco de dados ao criar demanda: ' . $e->getMessage();
    log_error($errorMessage);
    send_json_response(['error' => 'Erro interno do servidor ao criar demanda.'], 500);
} catch (Throwable $e) {
    $errorMessage = 'Erro inesperado ao criar demanda: ' . $e->getMessage();
    log_error($errorMessage);
    send_json_response(['error' => 'Erro interno do servidor.'], 500);
}
?>