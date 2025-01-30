<?php
/**
 * login.php
 *
 * Este script PHP é um endpoint da API para autenticar usuários.
 * Ele espera receber CPF e senha via método POST, verifica as credenciais
 * no banco de dados e, em caso de sucesso, inicia uma sessão para o usuário.
 */

// Inclui os arquivos necessários: configuração do banco de dados e funções helpers.
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/helpers/functions.php';

// Inicia a sessão PHP. Sessões são usadas para manter o estado de login do usuário.
session_start();

try {
    // Tenta conectar ao banco de dados usando a função connect_db() definida em database.php.
    $db = connect_db();

    // Verifica se o método da requisição HTTP é POST.
    // Este endpoint só aceita requisições POST para processar o login.
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Verificar o tipo de conteúdo da requisição para processar dados JSON ou de formulário.
        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

        if ($contentType === 'application/json') {
            // Se o Content-Type for 'application/json', decodifica o JSON do corpo da requisição.
            $content = trim(file_get_contents("php://input")); // Lê o corpo da requisição.
            $decoded = json_decode($content, true); // Decodifica para um array associativo.

            // Verifica se a decodificação do JSON foi bem-sucedida e resultou em um array.
            if (!is_array($decoded)) {
                // Se o formato JSON for inválido, envia uma resposta JSON de erro com código HTTP 400 (Requisição Inválida).
                send_json_response(['error' => 'Formato JSON inválido.'], 400);
                exit; // Encerra a execução.
            }

            // Valida e sanitiza CPF e senha recebidos do JSON.
            $cpf = validate_input($decoded['cpf'] ?? '');
            $password = $decoded['password'] ?? '';
        } else {
            // Se o Content-Type não for JSON, assume que são dados de formulário padrão.
            // Valida e sanitiza CPF e senha recebidos do formulário POST.
            $cpf = validate_input($_POST['cpf'] ?? '');
            $password = $_POST['password'] ?? ''; // Senha não precisa de sanitização HTML, mas trim e stripslashes são úteis.
        }

        // Valida se CPF e senha foram fornecidos.
        if (empty($cpf) || empty($password)) {
            // Se CPF ou senha estiverem faltando, envia uma resposta JSON de erro com código HTTP 400 (Requisição Inválida).
            send_json_response(['error' => 'CPF e senha são obrigatórios.'], 400);
        }

        // Prepara a query SQL para selecionar o ID e a senha hash do usuário com o CPF fornecido.
        // Novamente, prepared statements para segurança contra SQL injection.
        $stmt = $db->prepare("SELECT id, password FROM users WHERE cpf = :cpf");
        $stmt->bindParam(':cpf', $cpf, PDO::PARAM_STR); // Faz o bind do parâmetro CPF.
        $stmt->execute(); // Executa a query.
        $user = $stmt->fetch(PDO::FETCH_ASSOC); // Busca o resultado como um array associativo.

        // Verifica se um usuário foi encontrado com o CPF fornecido e se a senha fornecida corresponde à senha hash armazenada.
        if ($user && password_verify($password, $user['password'])) {
            // Se as credenciais forem válidas, inicia a sessão do usuário.
            $_SESSION['user_id'] = $user['id']; // Define 'user_id' na sessão.
            // Envia uma resposta JSON de sucesso com código HTTP 200 (OK), incluindo uma mensagem e o ID do usuário.
            send_json_response(['message' => 'Login realizado com sucesso!', 'user_id' => $user['id']], 200);
        } else {
            // Se as credenciais forem inválidas, registra uma falha de login e envia uma resposta JSON de erro com código HTTP 401 (Não Autorizado).
            log_error("Falha de login para o CPF: " . $cpf); // Log de falha de login para auditoria/segurança.
            send_json_response(['error' => 'Credenciais inválidas.'], 401);
        }
    } else {
        // Se o método da requisição não for POST, envia uma resposta JSON de erro com código HTTP 405 (Método Não Permitido).
        send_json_response(['error' => 'Método não permitido.'], 405);
    }

} catch (PDOException $e) {
    // Captura exceções PDO (erros de banco de dados).
    $errorMessage = 'Erro de banco de dados ao fazer login: ' . $e->getMessage();
    log_error($errorMessage); // Registra o erro no log.
    // Envia uma resposta JSON de erro interno do servidor com código HTTP 500.
    send_json_response(['error' => 'Erro interno do servidor ao fazer login.'], 500);
} catch (Throwable $e) {
    // Captura outras exceções não tratadas.
    $errorMessage = 'Erro inesperado ao fazer login: ' . $e->getMessage();
    log_error($errorMessage); // Registra o erro no log.
    // Envia uma resposta JSON de erro interno do servidor com código HTTP 500.
    send_json_response(['error' => 'Erro interno do servidor.'], 500);
}
?>