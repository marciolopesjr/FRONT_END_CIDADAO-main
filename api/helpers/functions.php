<?php
/**
 * functions.php
 *
 * Este arquivo contém funções utilitárias e helpers que são usadas em vários
 * scripts da API. Eu criei este arquivo para manter o código DRY (Don't Repeat Yourself)
 * e para organizar funções comuns em um único lugar.
 */

/**
 * Função para validar e sanitizar dados de entrada.
 * Esta função é essencial para proteger a aplicação contra ataques XSS e outras
 * vulnerabilidades relacionadas à entrada de dados do usuário.
 *
 * @param string $data Dado de entrada a ser validado.
 * @return string Dado validado e sanitizado.
 */
function validate_input($data) {
    // Remove espaços em branco no início e no final da string.
    $data = trim($data);
    // Remove barras invertidas adicionadas por `stripslashes()`.
    $data = stripslashes($data);
    // Converte caracteres especiais HTML para entidades HTML.
    // Isso ajuda a prevenir ataques XSS, escapando caracteres que poderiam ser interpretados como HTML ou JavaScript.
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Função para enviar uma resposta JSON padronizada.
 * Esta função facilita a criação de respostas consistentes para a API,
 * incluindo a definição do código de status HTTP e o header Content-Type.
 *
 * @param array $data Dados a serem enviados como JSON.
 * @param int $http_code Código de status HTTP a ser retornado (padrão: 200 OK).
 */
function send_json_response($data, $http_code = 200) {
    // Define o código de status HTTP.
    http_response_code($http_code);
    // Define o header Content-Type para application/json, indicando que a resposta é JSON.
    header('Content-Type: application/json');
    // Codifica os dados PHP para JSON e os imprime na saída.
    echo json_encode($data);
    // Encerra a execução do script após enviar a resposta.
    exit;
}

/**
 * Função para registrar mensagens de erro em um arquivo de log.
 * Logging de erros é crucial para monitorar a aplicação, diagnosticar problemas
 * e garantir que erros inesperados sejam registrados para análise posterior.
 *
 * @param string $message Mensagem de erro a ser logada.
 */
function log_error($message) {
    // Formata a mensagem de erro com a data e hora atual.
    $logMessage = "[" . date("Y-m-d H:i:s") . "] " . $message . "\n";
    // Usa a função error_log() para escrever a mensagem no arquivo de log.
    // Tipo 3 indica que a mensagem será escrita em um arquivo especificado no terceiro parâmetro.
    // O caminho do arquivo de log é definido como '../logs/error.log', relativo ao diretório deste script.
    error_log($logMessage, 3, __DIR__ . '/../logs/error.log');
}
?>