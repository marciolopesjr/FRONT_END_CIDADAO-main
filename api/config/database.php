<?php
/**
 * database.php
 *
 * Este arquivo contém a função para conectar ao banco de dados MySQL usando PDO.
 * Eu decidi encapsular a lógica de conexão em uma função para facilitar a reutilização
 * em todos os scripts da API que precisam acessar o banco de dados.
 */

/**
 * Função para estabelecer uma conexão com o banco de dados MySQL.
 *
 * @return PDO|null Retorna uma instância PDO em caso de sucesso ou null em caso de falha.
 *              Em caso de falha, a função também encerra a execução do script e envia
 *              uma resposta JSON de erro com código HTTP 500.
 */
function connect_db() {
    // Configurações do banco de dados.
    // Para segurança e flexibilidade, em um ambiente de produção, essas configurações
    // deveriam vir de variáveis de ambiente, não hardcoded aqui.
    $host = 'localhost'; // Endereço do servidor MySQL. Em desenvolvimento, 'localhost' geralmente funciona.
    $dbname = 'u271084294_cidadao'; // Nome do banco de dados.
    $user = 'u271084294_teste'; // Usuário do banco de dados.
    $pass = 'E$JfNQUy9md^zr4'; // Senha do banco de dados.

    try {
        // Tenta criar uma nova conexão PDO.
        // Estou usando UTF8 para garantir que caracteres especiais sejam tratados corretamente.
        $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);

        // Configura o PDO para lançar exceções em caso de erros.
        // Isso é importante para que possamos capturar e tratar erros de banco de dados de forma adequada.
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Se a conexão foi bem-sucedida, retorna a instância PDO.
        return $db;
    } catch (PDOException $e) {
        // Se ocorrer uma PDOException (erro de conexão), eu vou:
        // 1. Definir o código de resposta HTTP para 500 (Erro Interno do Servidor).
        http_response_code(500);
        // 2. Enviar uma resposta JSON com uma mensagem de erro.
        echo json_encode(['error' => 'Erro ao conectar com o banco de dados: ' . $e->getMessage()]);
        // 3. Encerrar a execução do script. É crucial encerrar aqui para evitar que o script continue
        //    e tente executar operações no banco de dados sem uma conexão válida.
        exit;
    }
}
?>