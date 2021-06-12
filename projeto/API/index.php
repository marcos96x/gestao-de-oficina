<?php
@session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);
require_once './GenericFuncions.php';

if (!isset($_POST['action'])) {
    send_error();
}

$acao = $_POST['action'];
if (function_exists($acao)) {
    $acao();
} else {
    send_error(403, 'Método / Action inválido');
}

// inicio auth
function loginAdmin()
{
    if (isset($_POST['login']) && !empty($_POST['login']) && isset($_POST['senha']) && !empty($_POST['senha'])) {
        $login = addslashes(strip_tags($_POST['login']));
        $senha = addslashes(strip_tags($_POST['senha']));

        $senha = md5($senha);
        require_once "./DB.php";
        $res = $db->prepare("SELECT usuario_id FROM usuario WHERE usuario_login = :user AND usuario_senha = :pass");
        $res->bindParam(":user", $login, PDO::PARAM_STR);
        $res->bindParam(":pass", $senha, PDO::PARAM_STR);
        $res->execute();

        $row = $res->fetch();
        unset($db);
        if (isset($row[0])) {
            $usuario = [
                "id" => $row['usuario_id'],
            ];
            $_SESSION['__USER__'] = $usuario;
            send_success();
        } else {
            send_error(401, 'Login ou senha incorretos');
        }
    } else {
        send_error(401, 'Login e Senha obrigatórios');
    }
}

function logoutAdmin()
{
    unset($_SESSION['__USER__']);
    send_success();
}

function autenticacao()
{
    if (!isset($_SESSION['__USER__'])) {
        send_error(401, 'Usuário não está autenticado');
    } else {
        send_success();
    }
}

// fim auth

// inicio peças
function getPecas() {
    require_once "./DB.php";
    $res = $db->prepare("
        SELECT peca_id, peca_nome, peca_estoque_qtd_min, peca_estoque_qtd_atual, peca_referencia, categoria_peca_nome,
        IF(peca_estoque_qtd_atual < peca_estoque_qtd_min, 'danger', '') AS peca_text_estoque
        FROM peca
        JOIN vinculo_categoria_peca ON vinculo_categoria_peca_peca = peca_id
        JOIN categoria_peca ON vinculo_categoria_peca_categoria = categoria_peca_id
        ORDER BY peca_id DESC
    ");
    $row = $res->fetchAll();
    if (isset($row[0])) {
        send_success(200, $row);
    }
    send_error(404);
}

function salvaPeca() {
    require_once "./DB.php";
    $nome = addslashes(strip_tags($_POST['nome']));
    $estoque_qtd_min = intval(addslashes(strip_tags($_POST['estoque_qtd_min'])));
    $estoque_qtd_atual = intval(addslashes(strip_tags($_POST['estoque_qtd_atual'])));
    $referencia = addslashes(strip_tags($_POST['referencia']));
    if (empty($nome)) {
        send_error(403, 'Nome da peça obrigatório');
    }
    $data = [
        'nome' => $nome,
        'estoque_qtd_min' => $estoque_qtd_min,
        'estoque_qtd_atual' => $estoque_qtd_atual,
        'referencia' => $referencia,
    ];      
    $res = $db->prepare("INSERT INTO peca (peca_nome, peca_estoque_qtd_min, peca_estoque_qtd_atual, peca_referencia) VALUES (:nome, :estoque_qtd_min, :estoque_qtd_atual, :referencia)");
    $res->execute($data);

    unset($db);
    send_success();
}

function editaPeca() {
    require_once "./DB.php";
    $id = intval(addslashes(strip_tags($_POST['id'])));
    $nome = addslashes(strip_tags($_POST['nome']));
    $estoque_qtd_min = intval(addslashes(strip_tags($_POST['estoque_qtd_min'])));
    $estoque_qtd_atual = intval(addslashes(strip_tags($_POST['estoque_qtd_atual'])));
    $referencia = addslashes(strip_tags($_POST['referencia']));
    if (empty($nome) || $id == 0) {
        send_error(403, 'Nome da peça e ID obrigatório');
    }
    $data = [
        'id' => $id,
        'nome' => $nome,
        'estoque_qtd_min' => $estoque_qtd_min,
        'estoque_qtd_atual' => $estoque_qtd_atual,
        'referencia' => $referencia,
    ];      
    $res = $db->prepare("UPDATE peca SET peca_nome = :nome, peca_estoque_qtd_min = :estoque_qtd_min, peca_estoque_qtd_atual = :estoque_qtd_atual, peca_referencia = :referencia WHERE peca_id = :id");
    $res->bindParam(":id", $data['id'], PDO::PARAM_STR);
    $res->bindParam(":nome", $data['nome'], PDO::PARAM_STR);
    $res->bindParam(":estoque_qtd_min", $data['estoque_qtd_min'], PDO::PARAM_STR);
    $res->bindParam(":estoque_qtd_atual", $data['estoque_qtd_atual'], PDO::PARAM_STR);
    $res->bindParam(":referencia", $data['referencia'], PDO::PARAM_STR);
    $res->execute();

    unset($db);
    send_success();
}

function addCategoriaPeca() {
    require_once "./DB.php";
    $peca = intval(addslashes(strip_tags($_POST['peca'])));
    $categoria = intval(addslashes(strip_tags($_POST['categoria'])));
    if (empty($categoria) || empty($peca) || $categoria == 0 || $peca == 0) {
        send_error(403, 'Categoria / Peca obrigatório');
    }
    $data = [
        'categoria' => $categoria,
        'peca' => $peca
    ];      
    $res = $db->prepare("INSERT INTO vinculo_categoria_peca (vinculo_categoria_peca_peca, vinculo_categoria_peca_categoria) VALUES (:peca, :categoria)");
    $res->execute($data);

    unset($db);
    send_success();
}
function removeCategoriaPeca() {
    require_once "./DB.php";
    $id = intval($_POST['id']);
    if ($id == 0) {
       send_error(400, 'ID obrigatório');
    }

    $res = $db->prepare("DELETE FROM vinculo_categoria_peca WHERE vinculo_categoria_peca_id = :id");
    $res->bindParam(":id", $id, PDO::PARAM_STR);
    $res->execute();
    unset($db);
    send_success();
}

function removePeca()
{
    require_once "./DB.php";
    $id = intval($_POST['id']);
    if ($id == 0) {
       send_error(400, 'ID obrigatório');
    }

    $res = $db->prepare("DELETE FROM peca WHERE peca_id = :id");
    $res->bindParam(":id", $id, PDO::PARAM_STR);
    $res->execute();
    $res = $db->prepare("DELETE FROM vinculo_categoria_peca WHERE vinculo_categoria_peca_peca = :id");
    $res->bindParam(":id", $id, PDO::PARAM_STR);
    $res->execute();
    unset($db);
    send_success();
}
// fim peças
// inicio categoria
function getCategorias() {
    require_once "./DB.php";
    $res = $db->prepare("
        SELECT categoria_peca_nome, categoria_peca_id, 
        SELECT(COUNT(*) FROM vinculo_categoria_peca WHERE vinculo_categoria_peca_categoria = categoria_id) AS categoria_peca_qtd_pecas
        FROM categoria_peca
        ORDER BY categoria_peca_id DESC
    ");
    $row = $res->fetchAll();
    if (isset($row[0])) {
        send_success(200, $row);
    }
    send_error(404);
}
function salvaCategoria() {
    require_once "./DB.php";
    $nome = addslashes(strip_tags($_POST['nome']));
    if (empty($nome)) {
        send_error(403, 'Nome da categoria obrigatório');
    }
    $data = [
        'nome' => $nome
    ];      
    $res = $db->prepare("INSERT INTO categoria_peca (categoria_peca_nome) VALUES (:nome)");
    $res->execute($data);

    unset($db);
    send_success();
}

function editaCategoria() {
    require_once "./DB.php";
    $id = intval(addslashes(strip_tags($_POST['id'])));
    $nome = addslashes(strip_tags($_POST['nome']));
    if (empty($nome) || $id == 0) {
        send_error(403, 'Nome da categoria e ID obrigatório');
    }
    $data = [
        'id' => $id,
        'nome' => $nome,
    ];      
    $res = $db->prepare("UPDATE categoria_peca SET categoria_peca_nome = :nome WHERE categoria_peca_id = :id");
    $res->bindParam(":id", $data['id'], PDO::PARAM_STR);
    $res->bindParam(":nome", $data['nome'], PDO::PARAM_STR);
    $res->execute();

    unset($db);
    send_success();
}

function removeCategoria() {
    require_once "./DB.php";
    $id = intval($_POST['id']);
    if ($id == 0) {
       send_error(400, 'ID obrigatório');
    }
    $res = $db->prepare("DELETE FROM categoria_peca WHERE categoria_peca_id = :id");
    $res->bindParam(":id", $id, PDO::PARAM_STR);
    $res->execute();
    unset($db);
    send_success();
}

// fim categoria

// inicio usuário
function getUsuarios() {
    require_once "./DB.php";
    $res = $db->prepare("
        SELECT usuario_id, usuario_nome, usuario_login
        FROM usuario
        ORDER BY usuario_id DESC
    ");
    $row = $res->fetchAll();
    if (isset($row[0])) {
        send_success(200, $row);
    }
    send_error(404);
}
function salvaUsuario() {
    require_once "./DB.php";
    $nome = addslashes(strip_tags($_POST['nome']));
    $login = addslashes(strip_tags($_POST['login']));
    $senha = addslashes(strip_tags($_POST['senha']));
    if (empty($nome) || empty($login) || empty($senha)) {
        send_error(403, 'Nome, login e senha obrigatório');
    }
    $data = [
        'nome' => $nome,
        'login' => $login,
        'senha' => md5($senha),
    ];      
    $res = $db->prepare("INSERT INTO usuario (usuario_nome, usuario_login, usuario_senha) VALUES (:nome, :login, :senha)");
    $res->execute($data);

    unset($db);
    send_success();
}

function editaUsuario() {
    require_once "./DB.php";
    $id = intval(addslashes(strip_tags($_POST['id'])));
    $nome = addslashes(strip_tags($_POST['nome']));
    $login = addslashes(strip_tags($_POST['login']));
    $senha = addslashes(strip_tags($_POST['senha']));
    if (empty($nome) || $id == 0 || empty($login)) {
        send_error(403, 'Nome, login e ID obrigatório');
    }
    $data = [
        'id' => $id,
        'nome' => $nome,
        'login' => $login,
        'senha' => md5($senha),
    ];      
    $set = "usuario_nome = :nome, usuario_login = :login";
    if(!empty($senha)) {
        $set .= ", usuario_senha = :senha";
    }
    $res = $db->prepare("UPDATE usuario SET $set WHERE usuario_id = :id");
    $res->bindParam(":id", $data['id'], PDO::PARAM_STR);
    $res->bindParam(":nome", $data['nome'], PDO::PARAM_STR);
    $res->bindParam(":login", $data['login'], PDO::PARAM_STR);
    $res->bindParam(":senha", $data['senha'], PDO::PARAM_STR);
    $res->execute();

    unset($db);
    send_success();
}

function removeUsuario() {
    require_once "./DB.php";
    $id = intval($_POST['id']);
    if ($id == 0) {
       send_error(400, 'ID obrigatório');
    }
    $res = $db->prepare("DELETE FROM usuario WHERE usuario_id = :id");
    $res->bindParam(":id", $id, PDO::PARAM_STR);
    $res->execute();
    unset($db);
    send_success();
}
// fim usuario