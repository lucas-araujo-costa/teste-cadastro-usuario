<?php
header('Content-Type: application/json');

$host = 'localhost';
$dbname = 'cadastro_usuarios'; 
$user = 'postgres'; 
$password = 'postgres'; 
$port = '5432';

$response = [
    'success' => false,
    'message' => 'Ocorreu um erro desconhecido.'
];

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sqlFile = 'database.sql'; 
    $sql = file_get_contents($sqlFile);

    if ($sql === false) {
        throw new Exception("Não foi possível ler o arquivo SQL.");
    }

    $pdo->exec($sql);

    function is_valid_email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    function is_valid_password($password) {
        return preg_match('/^\d{8,}$/', $password);
    }

    $nome = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['password'] ?? '');

    $errors = [];

    if (empty($nome)) {
        $errors[] = 'O campo Nome é obrigatório.';
    }
    if (empty($email) || !is_valid_email($email)) {
        $errors[] = 'O Email deve ser um endereço válido.';
    }
    if (empty($senha) || !is_valid_password($senha)) {
        $errors[] = 'A Senha deve conter pelo menos 8 dígitos numéricos.';
    }

    if (!empty($errors)) {
        $response['message'] = implode('<br>', $errors);
        echo json_encode($response);
        exit;
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuario WHERE email = :email");
    $stmt->execute([':email' => $email]);
    if ($stmt->fetchColumn() > 0) {
        $response['message'] = 'Email já cadastrado.';
        echo json_encode($response);
        exit;
    }

    $sql = "INSERT INTO usuario (nome, email, senha) VALUES (:nome, :email, :senha)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nome' => $nome,
        ':email' => $email,
        ':senha' => $senha
    ]);

    $response['success'] = true;
    $response['message'] = 'Cadastro realizado com sucesso!';

} catch (PDOException $e) {
    $response['message'] = 'Erro ao conectar com o banco de dados ou ao cadastrar usuário: ' . $e->getMessage();
} catch (Exception $e) {
    $response['message'] = 'Erro: ' . $e->getMessage();
}

$pdo = null;

echo json_encode($response);
?>
