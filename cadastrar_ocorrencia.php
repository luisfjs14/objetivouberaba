<?php
session_start();
if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit;
}
require_once "conexao.php";

if (!isset($_GET["aluno"])) {
    die("Aluno não especificado.");
}

$alunoId = (int)$_GET["aluno"];
$msg = "";

// Buscar dados do aluno para exibir o nome
$stmtAluno = $pdo->prepare("SELECT nome FROM alunos WHERE id = :id");
$stmtAluno->execute([":id" => $alunoId]);
$aluno = $stmtAluno->fetch(PDO::FETCH_ASSOC);
if (!$aluno) {
    die("Aluno não encontrado.");
}

// Processa o cadastro
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titulo = trim($_POST["titulo"]);
    $descricao = trim($_POST["descricao"]);
    $data = $_POST["data"];

    if ($titulo && $data) {
        $stmt = $pdo->prepare("
            INSERT INTO ocorrencias (aluno_id, titulo, descricao, data_registro, registrado_por)
            VALUES (:aluno, :titulo, :descricao, :data, :admin)
        ");
        $stmt->execute([
            ":aluno" => $alunoId,
            ":titulo" => $titulo,
            ":descricao" => $descricao,
            ":data" => $data,
            ":admin" => $_SESSION["admin_id"]
        ]);
        header("Location: admin_painel.php?mensagem=ocorrencia_sucesso");
        exit;
    } else {
        $msg = "Preencha todos os campos obrigatórios.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Cadastrar ocorrências - Colégio Objetivo Uberaba</title>
        <link rel="stylesheet" href="style.css" />
        <link rel="icon" type="image/png" href="assets/favicon.png" />
        <style>
            body { font-family: Arial, sans-serif; }
            .card {
                border: 1px solid #ccc;
                padding: 10px;
                margin: 10px 0;
                border-radius: 25px;
            }
            .titulo {
                font-weight: bold;
                color: #003f9c;
            }
            .separador {
            height: 40px; /* altura do espaço */
            }
            .botao {
            display:inline-block;
            background:#fdd700;
            color:#003f9c;
            padding:6px 12px;
            border-radius:50px;
            text-decoration:none;
            font-weight:bold;
            }
        </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-left">
                <a href="index.html">
                <img src="assets/logoobjetivouberabaamarela.png" alt="Logo Colégio Objetivo" class="logo" />
                </a>
                <img src="assets/30anos.png" alt="30 anos" class="logo-aniversario" />
            </div>
        </div>
    </header>
    <div class="container">
        <div class="separador"></div>
        <div class="linha-separadora"></div>
        <h2 class="titulo-secao">Cadastrar Ocorrência</h2>
        <p><strong>Aluno:</strong> <?= htmlspecialchars($aluno["nome"]) ?></p>
        <?php if($msg) echo "<p style='color:red;'>$msg</p>"; ?>
        <form method="post">
            <label><strong>Título*</strong></label><br>
            <input type="text" name="titulo" required><br><br>

            <label><strong>Descrição*</strong></label><br>
            <textarea name="descricao" rows="4" cols="50" required></textarea><br><br>

            <label><strong>Data*</strong></label><br>
            <input type="date" name="data" required><br><br>

            <button type="submit" class="botao">Salvar Ocorrência</button>
        </form>
        <div class="separador"></div>
        <a href="admin_painel.php" class="btn">Voltar</a>
    </div>
    <footer class="rodape">
        <div class="rodape-container">
            <div class="rodape-esquerda">
                <img src="assets/logoobjetivouberabaamarela.png" class="logo" />
                <p>Atendimentos:<br>Segunda a sexta, das 8h às 18h</p>
                <p>
                <br>
                Unidade Uberaba<br>Ensino Infantil | Ensino Fundamental | Ensino Médio | Pré Vestibular <br> (34) 3333-2576
                </p>
                <p>CNPJ: 33.520.135/0001-37</p>
                <div class="redes-sociais">
                <a href="https://www.instagram.com/objetivouberaba/"><img src="assets/instagram.webp" alt="Instagram" /></a>
                <a href="https://www.facebook.com/objetivointegradouberaba/?locale=pt_BR"><img src="https://img.icons8.com/color/48/facebook-new.png" alt="Facebook" /></a>
                </div>
                <a href="#" class="politica-link">Política de Privacidade</a>
            </div>
            <div class="rodape-direita">
                <img src="assets/grupointegrado.png" class="logo-integrado" />
                <p class="versao">v1.69.0</p>
            </div>
        </div>
        <div class="rodape-copy">
            <p>Objetivo Uberaba © 2025 | Todos os direitos reservados</p>
            <p>Made by Luis F. de J. Soares</p>
        </div>
    </footer>
</body>
</html>
