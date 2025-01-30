<?php
/**
 * test_db.php
 *
 * Este script PHP é usado para testar a conexão com o banco de dados MySQL.
 * Ele inclui o arquivo de configuração do banco de dados e chama a função connect_db().
 * Se a conexão for bem-sucedida, exibe uma mensagem de sucesso.
 * **IMPORTANTE: Este script deve ser usado apenas para testes e removido ou protegido
 * em ambientes de produção.**
 */

// Inclui o arquivo de configuração do banco de dados.
require_once 'config/database.php';

// Tenta conectar ao banco de dados usando a função connect_db().
$db = connect_db();

// Verifica se a conexão foi estabelecida com sucesso.
// Se $db não for null (ou false em alguns casos de erro, mas connect_db() já trata erros),
// assume que a conexão foi bem-sucedida.
if ($db) {
    // Se a conexão foi bem-sucedida, exibe uma mensagem de sucesso.
    echo "Conexão com o MySQL estabelecida com sucesso!";
} else {
    // Se a conexão falhou, a função connect_db() já terá enviado uma resposta de erro JSON e encerrado o script.
    // Portanto, não precisamos fazer nada aqui em caso de falha, além do que já está em connect_db().
    // (Em versões anteriores, eu poderia ter colocado uma mensagem de falha aqui, mas agora a função já cuida disso).
}
?>