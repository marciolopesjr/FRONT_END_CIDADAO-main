<?php
/**
 * register.php
 *
 * Este script PHP é um endpoint da API para registrar novos usuários no sistema.
 * Ele espera receber dados do usuário via método POST, valida esses dados,
 * hashea a senha e insere o novo usuário no banco de dados.
 */

// Inclui os arquivos necessários: configuração do banco de dados e funções helpers.
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/helpers/functions.php';

try {
    // Tenta conectar ao banco de dados usando a função connect_db() definida em database.php.
    $db = connect_db();

    // Verifica se o método da requisição HTTP é POST.
    // Este endpoint só aceita requisições POST para registrar novos usuários.
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Determina o tipo de conteúdo da requisição para processar dados JSON ou de formulário.
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

            // Valida e sanitiza os dados do usuário recebidos do JSON.
            // Se a chave não existir no array $decoded, usa uma string vazia como valor padrão para evitar erros.
            $name = validate_input($decoded['name'] ?? '');
            $email = validate_input($decoded['email'] ?? '');
            $cpf = validate_input($decoded['cpf'] ?? '');
            $phone = validate_input($decoded['phone'] ?? '');
            $address = validate_input($decoded['address'] ?? '');
            $password = $decoded['password'] ?? ''; // Senha precisa ser validada separadamente, mas não sanitizada com htmlspecialchars.
        } else {
            // Se o Content-Type não for JSON, assume que são dados de formulário padrão.
            // Valida e sanitiza os dados do usuário recebidos do formulário POST.
            $name = validate_input($_POST['name'] ?? '');
            $email = validate_input($_POST['email'] ?? '');
            $cpf = validate_input($_POST['cpf'] ?? '');
            $phone = validate_input($_POST['phone'] ?? '');
            $address = validate_input($_POST['address'] ?? '');
            $password = $_POST['password'] ?? ''; // Senha não precisa de sanitização HTML, mas trim e stripslashes são úteis.
        }

        // Valida se os campos obrigatórios (nome, email, CPF e senha) foram fornecidos.
        if (empty($name) || empty($email) || empty($cpf) || empty($password)) {
            // Se algum campo obrigatório estiver faltando, envia uma resposta JSON de erro com código HTTP 400 (Requisição Inválida).
            send_json_response(['error' => 'Por favor, preencha todos os campos obrigatórios.'], 400);
        }

        // Hashea a senha usando password_hash().
        // PASSWORD_DEFAULT usa um algoritmo de hash forte e seguro (atualmente bcrypt).
        // É importante hashear a senha antes de armazená-la no banco de dados por segurança.
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepara a query SQL para inserir um novo usuário na tabela 'users'.
        // Usando prepared statements para prevenir SQL injection.
        $stmt = $db->prepare("INSERT INTO users (name, email, cpf, phone, address, password) VALUES (:name, :email, :cpf, :phone, :address, :password)");

        // Faz o bind dos parâmetros com os valores correspondentes.
        $stmt->bindParam(':name', $name, PDO::PARAM_STR); // Nome do usuário.
        $stmt->bindParam(':email', $email, PDO::PARAM_STR); // Email do usuário.
        $stmt->bindParam(':cpf', $cpf, PDO::PARAM_STR); // CPF do usuário.
        $stmt->bindParam(':phone', $phone, PDO::PARAM_STR); // Telefone do usuário.
        $stmt->bindParam(':address', $address, PDO::PARAM_STR); // Endereço do usuário.
        $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR); // Senha hasheada.

        // Executa a query preparada.
        $stmt->execute();

        // Se o registro for bem-sucedido, envia uma resposta JSON de sucesso com código HTTP 201 (Criado).
        send_json_response(['message' => 'Registro realizado com sucesso!'], 201);
    } else {
        // Se o método da requisição não for POST, envia uma resposta JSON de erro com código HTTP 405 (Método Não Permitido).
        send_json_response(['error' => 'Método não permitido.'], 405);
    }

} catch (PDOException $e) {
    // Captura exceções PDO (erros de banco de dados).
    if ($e->getCode() == '23000' && strpos($e->getMessage(), 'Duplicate entry') !== false) {
        // Se o código do erro for '23000' (código de erro para violação de constraint no MySQL)
        // e a mensagem de erro contiver 'Duplicate entry', trata erros de duplicidade de email ou CPF.
        if (strpos($e->getMessage(), 'users.email_UNIQUE') !== false) {
            // Erro de email duplicado.
            send_json_response(['error' => 'Este email já está cadastrado.'], 400);
        } elseif (strpos($e->getMessage(), 'users.cpf_UNIQUE') !== false) {
            // Erro de CPF duplicado.
            send_json_response(['error' => 'Este CPF já está cadastrado.'], 400);
        } else {
            // Outro erro de constraint desconhecido. Registra o erro e envia uma mensagem de erro genérica.
            $errorMessage = 'Erro de constraint ao registrar usuário: ' . $e->getMessage();
            log_error($errorMessage); // Registra o erro no log.
            send_json_response(['error' => 'Erro interno do servidor ao registrar usuário.'], 500);
        }
    } else {
        // Outro erro de banco de dados não relacionado a duplicidade. Registra o erro e envia uma mensagem de erro genérica.
        $errorMessage = 'Erro de banco de dados ao registrar usuário: ' . $e->getMessage();
        log_error($errorMessage); // Registra o erro no log.
        send_json_response(['error' => 'Erro interno do servidor ao registrar usuário.'], 500);
    }
} catch (Throwable $e) {
    // Captura outras exceções não tratadas.
    $errorMessage = 'Erro inesperado ao registrar usuário: ' . $e->getMessage();
    log_error($errorMessage); // Registra o erro no log.
    // Envia uma resposta JSON de erro interno do servidor com código HTTP 500.
    send_json_response(['error' => 'Erro interno do servidor.'], 500);
}
?>