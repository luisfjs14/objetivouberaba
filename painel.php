<?php
session_start();

if (!isset($_SESSION["responsavel_id"])) {
    die("Responsável não logado. Faça login.");
}

require_once "conexao.php";

$responsavelId = $_SESSION["responsavel_id"];

// Dados do responsável
$stmt = $pdo->prepare("SELECT nome FROM responsaveis_financeiros WHERE id = :id");
$stmt->bindParam(":id", $responsavelId);
$stmt->execute();
$responsavel = $stmt->fetch(PDO::FETCH_ASSOC);

// Lista de alunos
$stmt = $pdo->prepare("
    SELECT 
        a.id, 
        a.nome AS nome_aluno,
        a.foto,
        t.nome AS turma_nome,
        t.turno,
        t.ano_letivo
    FROM alunos a
    JOIN turmas t ON t.id = a.turma_id
    WHERE a.responsavel_financeiro_id = :id
    ORDER BY t.ano_letivo, t.nome
");
$stmt->bindParam(":id", $responsavelId);
$stmt->execute();
$alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Painel do Responsável - Colégio Objetivo Uberaba</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="icon" type="image/png" href="assets/favicon.png" />
    <style>
        body {
            font-family: Montserrat, sans-serif;
            background:#f0f0f0;
            margin:0;
            padding:0;
        }
        header, footer {
            background: #003f9c;
            color:#fff;
            padding:15px;
            text-align:center;
        }
        .containera {
            max-width: 1000px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius:6px;
        }
        h2 {
            margin-top:0;
            color:#003f9c;
        }
        .aluno-card {
            background:#fff;
            border:1px solid #ddd;
            border-radius:8px;
            box-shadow:0 2px 6px rgba(0,0,0,0.1);
            padding:15px;
            margin-bottom:15px;
            display:flex;
            align-items:center;
            flex-wrap:wrap;
        }
        .aluno-foto {
            width:70px;
            height:70px;
            border-radius:50%;
            border:2px solid #003f9c;
            object-fit:cover;
            margin-right:15px;
        }
        .aluno-info {
            display:flex;
            flex-wrap:wrap;
            flex:1;
            align-items:left;
        }
        .aluno-info div {
            margin-right:20px;
            min-width:100px;
        }
        .nome-aluno {
            color:#003f9c;
            font-weight:bold;
            font-size:16px;
        }
        .info-texto {
            color:#333;
            font-weight:500;
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
        .botao:hover {
            background:#e6c200;
        }
        .botao-sair {
            display:inline-block;
            background:#003f9c;
            color:#fff;
            padding:8px 14px;
            border-radius:50px;
            text-decoration:none;
            margin-top:20px;
        }
        .botao-sair:hover {
            background:#002f74;
        }
        @media (max-width:600px) {
            .aluno-card {
                flex-direction: column;
                align-items:flex-start;
            }
            .aluno-info {
                flex-direction: column;
                margin-top:10px;
            }
            .aluno-info div {
                margin-right:0;
                margin-bottom:5px;
            }
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
        <h2>Bem-vindo, <?php echo htmlspecialchars($responsavel['nome']); ?>!</h2>
        <h3>Seus Alunos:</h3>
        <div class="containera">
            <?php if ($alunos): ?>
                <?php foreach ($alunos as $aluno): ?>
                    <div class="aluno-card">
                        <img class="aluno-foto" src="<?php echo htmlspecialchars($aluno['foto'] ?: 'https://via.placeholder.com/70'); ?>" alt="Foto do aluno">
                        <div class="aluno-info">
                            <div class="nome-aluno"><?php echo htmlspecialchars($aluno['nome_aluno']); ?></div>
                            <div class="info-texto"><strong>Turma: </strong><?php echo htmlspecialchars($aluno['turma_nome']); ?></div>
                            <div class="info-texto"><strong>Turno: </strong><?php echo htmlspecialchars($aluno['turno']); ?></div>
                            <div class="info-texto"><strong>Ano letivo: </strong><?php echo htmlspecialchars($aluno['ano_letivo']); ?></div>
                        </div>
                        <a class="botao" href="aluno.php?id=<?php echo $aluno['id']; ?>">Visualizar</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Você não possui alunos cadastrados.</p>
            <?php endif; ?>

            <a class="botao-sair" href="logout.php">Sair</a>
        </div>
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
