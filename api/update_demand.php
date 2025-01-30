<?php
/**
 * update_demand.php
 *
 * Este script PHP é um endpoint da API para atualizar uma demanda existente.
 * Permite atualizar o status e a secretaria da demanda.
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/helpers/functions.php';

session_start();

try {
    $db = connect_db();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Verificar se o usuário está logado (pode ajustar a autorização conforme necessário)
        if (!isset($_SESSION['user_id'])) {
            send_json_response(['error' => 'Você precisa estar logado para atualizar uma demanda.'], 401);
        }

        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

        if ($contentType === 'application/json') {
            $content = trim(file_get_contents("php://input"));
            $decoded = json_decode($content, true);

            if (!is_array($decoded)) {
                send_json_response(['error' => 'Formato JSON inválido.'], 400);
                exit;
            }

            $demand_id = validate_input($decoded['demand_id'] ?? ''); // ID da demanda é obrigatório
            $status = validate_input($decoded['status'] ?? ''); // Status é opcional
            $secretariat_id = $decoded['secretariat_id'] ?? null; // Secretariat ID é opcional
        } else {
            $demand_id = validate_input($_POST['demand_id'] ?? ''); // ID da demanda é obrigatório
            $status = validate_input($_POST['status'] ?? ''); // Status é opcional
            $secretariat_id = $_POST['secretariat_id'] ?? null; // Secretariat ID é opcional
        }

        // Validar se o ID da demanda foi fornecido
        if (empty($demand_id)) {
            send_json_response(['error' => 'O ID da demanda é obrigatório para atualização.'], 400);
        }

        // Inicializa partes da query e dados para bindParam
        $update_fields = [];
        $params = [];

        // Adicionar status à atualização se fornecido
        if (!empty($status)) {
            $update_fields[] = 'status = :status';
            $params[':status'] = $status;
        }

        // Adicionar secretariat_id à atualização se fornecido e não for nulo
        if ($secretariat_id !== null && $secretariat_id !== '') { // Permite enviar secretariat_id como 0 ou "" para remover a atribuição
            $update_fields[] = 'secretariat_id = :secretariat_id';
            $params[':secretariat_id'] = $secretariat_id;
        } elseif ($secretariat_id === '0' || $secretariat_id === '') { // Trata explicitamente para definir secretariat_id como NULL (remover atribuição)
            $update_fields[] = 'secretariat_id = NULL'; // Não precisa de bindParam para NULL
        }


        // Se não houver campos para atualizar (além do ID), retorna erro
        if (empty($update_fields)) {
            send_json_response(['error' => 'Nenhum campo para atualizar fornecido (status ou secretaria).'], 400);
        }

        // Monta a query UPDATE dinamicamente
        $sql_update = "UPDATE demands SET " . implode(', ', $update_fields) . " WHERE id = :demand_id";
        $stmt = $db->prepare($sql_update);

        // Adiciona o demand_id aos parâmetros
        $params[':demand_id'] = $demand_id;

        // Bind dos parâmetros
        foreach ($params as $key => &$val) {
            $stmt->bindParam($key, $val);
        }

        // Executa a query
        if ($stmt->execute()) {
            send_json_response(['message' => 'Demanda atualizada com sucesso!'], 200);
        } else {
            send_json_response(['error' => 'Falha ao atualizar a demanda.', 'details' => $stmt->errorInfo()], 500);
        }

    } else {
        send_json_response(['error' => 'Método não permitido.'], 405);
    }

} catch (PDOException $e) {
    $errorMessage = 'Erro de banco de dados ao atualizar demanda: ' . $e->getMessage();
    log_error($errorMessage);
    send_json_response(['error' => 'Erro interno do servidor ao atualizar demanda.'], 500);
} catch (Throwable $e) {
    $errorMessage = 'Erro inesperado ao atualizar demanda: ' . $e->getMessage();
    log_error($errorMessage);
    send_json_response(['error' => 'Erro interno do servidor.'], 500);
}
?>