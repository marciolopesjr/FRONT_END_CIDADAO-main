<?php
/**
 * health.php
 *
 * Este script PHP é um endpoint de health check para a API.
 * Ele simplesmente retorna um status HTTP 200 OK e um JSON indicando que a API está online.
 * É útil para monitoramento e verificar se a API está respondendo.
 */

// Define o código de resposta HTTP para 200 (OK).
http_response_code(200);
// Define o header Content-Type para application/json.
header('Content-Type: application/json');
// Envia uma resposta JSON simples com o status 'OK'.
echo json_encode(['status' => 'OK']);
?>