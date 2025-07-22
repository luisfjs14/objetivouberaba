<?php
session_start();
if (!isset($_SESSION["responsavel_id"])) {
    header("Location: restrita.php");
    exit;
}
require_once "conexao.php";

if (!isset($_GET["id"])) {
    die("Aluno não especificado.");
}
$alunoId = (int)$_GET["id"];

// Validar se o aluno pertence ao responsável
$stmt = $pdo->prepare("
    SELECT a.nome
    FROM alunos a
    WHERE a.id = :id AND a.responsavel_financeiro_id = :rid
");
$stmt->execute([
    ":id" => $alunoId,
    ":rid" => $_SESSION["responsavel_id"]
]);
$aluno = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$aluno) {
    die("Aluno não encontrado ou acesso não permitido.");
}

// Buscar ocorrências
$stmt = $pdo->prepare("
    SELECT o.titulo, o.descricao, o.data_registro, ad.nome AS funcionario
    FROM ocorrencias o
    JOIN administradores ad ON ad.id = o.registrado_por
    WHERE o.aluno_id = :id
    ORDER BY o.data_registro DESC
");
$stmt->execute([":id" => $alunoId]);
$ocorrencias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Ocorrências - Colégio Objetivo Uberaba</title>
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
    <a href="https://wa.me/5534984459808" target="_blank" class="whatsapp-button" title="Fale conosco no WhatsApp">
        <img src="https://img.icons8.com/color/48/000000/whatsapp--v1.png" alt="WhatsApp" />
    </a>
    <div class="container">
        <div class="separador"></div>
        <div class="linha-separadora"></div>
        <h2 class="titulo-secao">Ocorrências de <?=htmlspecialchars($aluno["nome"])?></h2>
        <?php if (empty($ocorrencias)): ?>
            <p>Este aluno não possui nenhuma ocorrência registrada.</p>
        <?php else: ?>
            <?php foreach ($ocorrencias as $o): ?>
                <div class="card">
                    <div class="titulo"><?=htmlspecialchars($o["titulo"])?></div>
                    <div><?=nl2br(htmlspecialchars($o["descricao"]))?></div>
                    <small>
                        Data: <?=date("d/m/Y", strtotime($o["data_registro"]))?><br>
                        Registrado por: <?=htmlspecialchars($o["funcionario"])?>
                    </small>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <div class="separador"></div>
        <a href="painel.php" class="btn">Voltar ao Painel</a>
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
