<?php
/**
 * get_demands.php
 *
 * Este script PHP é um endpoint da API para buscar todas as demandas do banco de dados.
 * Ele retorna as demandas em formato JSON.
 */

// Inclui os arquivos necessários: configuração do banco de dados e funções helpers.
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/helpers/functions.php';

try {
    // Tenta conectar ao banco de dados usando a função connect_db() definida em database.php.
    $db = connect_db();

    // Executa uma query SQL para selecionar todos os campos, incluindo status e secretariat_id
    $stmt = $db->query("SELECT id, category, description, latitude, longitude, status, secretariat_id FROM demands");

    // Busca todos os resultados da query como um array associativo.
    $demands = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Envia a resposta JSON com o array de demandas e código HTTP 200 (OK).
    send_json_response($demands);

} catch (PDOException $e) {
    // Captura exceções PDO (erros de banco de dados).
    $errorMessage = 'Erro de banco de dados ao buscar demandas: ' . $e->getMessage();
    log_error($errorMessage); // Registra o erro no log.
    // Envia uma resposta JSON de erro interno do servidor com código HTTP 500.
    send_json_response(['error' => 'Erro interno do servidor ao buscar demandas.'], 500);
} catch (Throwable $e) {
    // Captura outras exceções não tratadas.
    $errorMessage = 'Erro inesperado ao buscar demandas: ' . $e->getMessage();
    log_error($errorMessage); // Registra o erro no log.
    // Envia uma resposta JSON de erro interno do servidor com código HTTP 500.
    send_json_response(['error' => 'Erro interno do servidor.'], 500);
}
?>