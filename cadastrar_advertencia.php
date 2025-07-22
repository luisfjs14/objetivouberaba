<?php
session_start();
    if (!isset($_SESSION["admin_id"])) {
        header("Location: admin_login.php");
        exit;
    }

    require_once "conexao.php";
    require_once 'dompdf/autoload.inc.php';
    use Dompdf\Dompdf;

    if (!isset($_GET["aluno"])) {
        die("Aluno não especificado.");
    }

    $alunoId = (int)$_GET["aluno"];
    $msg = "";

    // Buscar dados do aluno e turma
    $stmtAluno = $pdo->prepare("SELECT a.nome, a.data_nascimento, rf.nome AS responsavel_nome, t.nome AS turma, t.turno FROM alunos a JOIN responsaveis_financeiros rf ON rf.id = a.responsavel_financeiro_id LEFT JOIN turmas t ON t.id = a.turma_id WHERE a.id = :id");
    $stmtAluno->execute([':id' => $alunoId]);
    $aluno = $stmtAluno->fetch(PDO::FETCH_ASSOC);
    if (!$aluno) {
        die("Aluno não encontrado.");
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $titulo = trim($_POST["titulo"]);
        $motivo = trim($_POST["motivo"]);
        $data = $_POST["data"];

        if ($titulo && $motivo && $data) {
            // Gerar nome do arquivo PDF
        $nomeOriginal = $aluno['nome'];
            $nomeConvertido = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $nomeOriginal);
            $nomeSanitizado = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $nomeConvertido));
            $dataFormatada = date('d-m-Y', strtotime($data));
            $nomeArquivoPdf = "advertencia_{$nomeSanitizado}_{$dataFormatada}.pdf";
            $caminhoSalvar = __DIR__ . "/documentos/advertencias/" . $nomeArquivoPdf;
            // Gerar imagem base64 para cabeçalho e rodapé
            $imgCabecalho = base64_encode(file_get_contents("assets/cabecalho.png"));
            // HTML do PDF
            $html = '
            <html>
                <head>
                    <style>
                        @page {
                            margin: 160px 50px 120px 50px;
                        }
                        body {
                            font-family: DejaVu Sans, sans-serif;
                            font-size: 13px;
                            color: #000;
                            line-height: 1.5;
                        }
                        header {
                        position: fixed;
                        top: -130px;
                        left: 0;
                        right: 0;
                        text-align: center;
                        }
                        footer {
                        position: fixed;
                        bottom: -100px;
                        left: 0;
                        right: 0;
                        text-align: center;
                        }
                        .logo {
                            height: 60px;
                            vertical-align: middle;
                            margin: 0 20px;
                        }
                        .titulo {
                            text-align: center;
                            font-size: 16px;
                            font-weight: bold;
                            margin-bottom: 30px;
                            text-transform: uppercase;
                        }
                        .linha {
                            margin-bottom: 10px;
                        }
                        .label {
                            font-weight: bold;
                        }
                        .motivo {
                            margin-top: 20px;
                            border: 1px solid #ccc;
                            padding: 10px;
                            min-height: 80px;
                        }
                        .assinaturas {
                            margin-top: 80px;
                            display: flex;
                            justify-content: space-between;
                            font-size: 12px;
                        }
                        .assinaturas div {
                            width: 45%;
                            text-align: center;
                        }
                        .assinaturas hr {
                            margin-bottom: 5px;
                        }
                        .data-topo {
                            text-align: right;
                            font-size: 12px;
                        }
                        .cabecalho-img {
                        width: 100%;
                        height: auto;
                        }

                        .rodape-img {
                        width: 100%;
                        height: auto;
                        }
                    </style>
                </head>
                <body>
                    <header>
                        <img src="data:image/png;base64,' . $imgCabecalho . '" style="width:100%; height:auto;">
                    </header>
                    <footer>
                        CNPJ: 33.520.135/0001-37 • (34) 3333-2576 • Uberaba/MG<br>
                        Ensino Infantil | Fundamental | Médio | Pré-vestibular<br>
                        <strong>Grupo Integrado</strong> • www.objetivointegradouberaba.com
                    </footer>
                    <div class="data-topo">' . date("d/m/Y") . '</div>
                    <br><br>
                    <div class="titulo">Advertência Disciplinar</div>
                    <div class="linha"><span class="label">Nome do(a) Aluno(a):</span> ' . htmlspecialchars($aluno["nome"]) . '</div>
                    <div class="linha"><span class="label">Turma:</span> ' . (!empty($aluno['turma']) ? $aluno['turma'] : '______') . '</div>
                    <div class="linha"><span class="label">Data:</span> ' . date('d/m/Y', strtotime($data)) . '</div>
                    <div class="linha"><span class="label">Aula:</span> __________</div>
                    <p>Serve a presente como à finalidade de aplicar-lhe a pena de advertência disciplinar, em razão da seguinte ocorrência:</p>
                    <div class="motivo">' . nl2br(htmlspecialchars($motivo)) . '. Alegamos que tal atitude do aluno é ilegal, infringindo as normas internas do Colégio.</div>
                    <div class="assinaturas">
                        <div>
                            <hr>
                            Assinatura do Responsável
                        </div>
                        <br><br><br>
                        <div>
                            <hr>
                            Coordenação
                        </div>
                    </div>
                </body>
            </html>';

        // Gerar PDF
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        file_put_contents($caminhoSalvar, $dompdf->output());

        // Salvar no banco
        $stmt = $pdo->prepare("INSERT INTO advertencias (aluno_id, titulo, motivo, data_registro, arquivo_pdf, registrado_por) VALUES (:aluno, :titulo, :motivo, :data, :arquivo, :admin)");
        $stmt->execute([
            ":aluno" => $alunoId,
            ":titulo" => $titulo,
            ":motivo" => $motivo,
            ":data" => $data,
            ":arquivo" => $nomeArquivoPdf,
            ":admin" => $_SESSION["admin_id"]
        ]);

        $output = $dompdf->output();
        file_put_contents("documentos/advertencias/" . $nomeArquivo, $output); // Salva no servidor

        $dompdf->stream($nomeArquivoPdf, ["Attachment" => true]);

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
        <title>Cadastrar advertência - Colégio Objetivo Uberaba</title>
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
            <h2 class="titulo-secao">Cadastrar Advertência</h2>
            <p><strong>Aluno:</strong> <?= htmlspecialchars($aluno['nome']) ?></p>
            <?php if($msg) echo "<p style='color:red;'>$msg</p>"; ?>
            <form method="post">
                <label>Título*</label><br>
                <input type="text" name="titulo" required><br><br>

                <label>Motivo*</label><br>
                <textarea name="motivo" rows="4" required></textarea><br><br>

                <label>Data*</label><br>
                <input type="date" name="data" required><br><br>

                <button type="submit" class="botao">Salvar e Gerar PDF</button>
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
