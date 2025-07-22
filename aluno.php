<?php
session_start();
if (!isset($_SESSION["responsavel_id"])) {
    header("Location: restrita.php");
    exit();
}
require 'conexao.php';

$aluno_id = $_GET["id"];

// Buscando dados do aluno
$sql = "SELECT a.*, 
               m.nome AS mae_nome, m.cpf AS mae_cpf, m.email AS mae_email, m.whatsapp AS mae_whatsapp,
               p.nome AS pai_nome, p.cpf AS pai_cpf, p.email AS pai_email, p.whatsapp AS pai_whatsapp,
               r.nome AS resp_nome, r.cpf AS resp_cpf, r.email AS resp_email, r.whatsapp AS resp_whatsapp, r.rua AS resp_rua, r.cep AS resp_cep, r.numero AS resp_numero, r.cidade AS resp_cidade, r.uf AS resp_uf, r.bairro AS resp_bairro,
               t.nome AS turma_nome, t.ano_letivo AS ano_letivo, t.turno AS turno
        FROM alunos a
        JOIN mae m ON a.mae_id = m.id
        LEFT JOIN pai p ON a.pai_id = p.id
        JOIN responsaveis_financeiros r ON a.responsavel_financeiro_id = r.id
        JOIN turmas t ON a.turma_id = t.id
        WHERE a.id = ? AND a.responsavel_financeiro_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$aluno_id, $_SESSION["responsavel_id"]]);
$aluno = $stmt->fetch();
if (!$aluno) {
    echo "Aluno não encontrado.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Painel do Aluno - Colégio Objetivo Uberaba</title>
        <link rel="stylesheet" href="style.css" />
        <link rel="icon" type="image/png" href="assets/favicon.png" />
        <style>
            body {
                font-family: Montserrat, sans-serif;
                background:#f0f0f0;
                margin:0;
                padding:0;
            }
            .aluno-foto {
            width:70px;
            height:70px;
            border-radius:50%;
            border:2px solid #003f9c;
            object-fit:cover;
            margin-right:15px;
            }
            .separador {
            height: 40px; /* altura do espaço */
            }
            .containera {
            max-width: 1000px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius:6px;
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
        <a href="https://wa.me/5534984459808" target="_blank" class="whatsapp-button" title="Fale conosco no WhatsApp">
            <img src="https://img.icons8.com/color/48/000000/whatsapp--v1.png" alt="WhatsApp" />
        </a>
        <div class="container">
            <div class="separador"></div>
            <div class="linha-separadora"></div>
            <h2 class="titulo-secao"><?php echo htmlspecialchars($aluno["nome"]); ?></h2>
            <img class="aluno-foto" src="<?php echo htmlspecialchars($aluno['foto'] ?: 'https://via.placeholder.com/70'); ?>" alt="Foto do aluno">
            <form action="atualizar_foto.php" method="POST" enctype="multipart/form-data" style="display: flex; align-items: center; gap: 10px;">
                <input type="hidden" name="aluno_id" value="<?= $alunoId ?>">
                <label for="foto-upload" style="cursor:pointer;">Atualizar Foto</label>
                <input type="file" id="foto-upload" name="foto" accept="image/*" style="display: none;" onchange="this.form.submit()">
            </form>
            <div class="separador"></div>
            <div class="linha-separadora"></div>
            <h2 class="titulo-secao">Dados do aluno e responsáveis</h2>
            <div class="containera">
                <ul>
                    <div class="separador"></div>
                    <div class="linha-separadora"></div>
                    <h2 class="titulo-secao">Aluno</h2>
                    <li><strong>CPF: </strong> <?php echo htmlspecialchars($aluno["cpf"]); ?></li>
                    <li><strong>Data de nascimento: </strong> <?php echo htmlspecialchars($aluno["data_nascimento"]); ?></li>
                    <li><strong>Turma: </strong> <?php echo htmlspecialchars($aluno["turma_nome"]); ?> | <?php echo htmlspecialchars($aluno["turno"]); ?> - <?php echo $aluno["ano_letivo"]; ?></li>
                    <li><strong>Endereço: </strong><?php echo htmlspecialchars($aluno["rua"]); ?>, <?php echo htmlspecialchars($aluno["numero"]); ?> - <?php echo htmlspecialchars($aluno["bairro"]); ?></li>
                    <p><?php echo htmlspecialchars($aluno["cidade"]); ?> - <?php echo htmlspecialchars($aluno["uf"]); ?>, <?php echo htmlspecialchars($aluno["cep"]); ?></p>
                    <li><strong>Plano de pagamento 2025: </strong> <?php echo htmlspecialchars($aluno["parcelas_25"]); ?>x R$ <?php echo htmlspecialchars($aluno["mensalidade_25"]); ?></li>
                    <li><strong>Plano de pagamento 2026: </strong> <?php echo htmlspecialchars($aluno["parcelas_26"]); ?>x R$ <?php echo htmlspecialchars($aluno["mensalidade_26"]); ?></li>
                </ul>
                <div class="separador"></div>
                <div class="linha-separadora"></div>
                <h2 class="titulo-secao">Mãe</h2>
                <ul>
                    <li><strong>Nome: </strong><?php echo htmlspecialchars($aluno["mae_nome"]); ?></li>
                    <li><strong>CPF: </strong><?php echo htmlspecialchars($aluno["mae_cpf"]); ?></li>
                    <li><strong>Email: </strong><?php echo htmlspecialchars($aluno["mae_email"]); ?></li>
                    <li><strong>WhatsApp: </strong><?php echo htmlspecialchars($aluno["mae_whatsapp"]); ?></li>
                </ul>
                <div class="separador"></div>
                <div class="linha-separadora"></div>
                <h2 class="titulo-secao">Pai</h2>
                <ul>
                    <li><strong>Nome: </strong><?php echo htmlspecialchars($aluno["pai_nome"]); ?></li>
                    <li><strong>CPF: </strong><?php echo htmlspecialchars($aluno["pai_cpf"]); ?></li>
                    <li><strong>Email: </strong><?php echo htmlspecialchars($aluno["pai_email"]); ?></li>
                    <li><strong>WhatsApp: </strong><?php echo htmlspecialchars($aluno["pai_whatsapp"]); ?></li>
                </ul>
                <div class="separador"></div>
                <div class="linha-separadora"></div>
                <h2 class="titulo-secao">Responsável Financeiro</h2>
                <ul>
                    <li><strong>Nome: </strong><?php echo htmlspecialchars($aluno["resp_nome"]); ?></li>
                    <li><strong>CPF: </strong><?php echo htmlspecialchars($aluno["resp_cpf"]); ?></li>
                    <li><strong>Email: </strong><?php echo htmlspecialchars($aluno["resp_email"]); ?></li>
                    <li><strong>WhatsApp: </strong><?php echo htmlspecialchars($aluno["resp_whatsapp"]); ?></li>
                    <li><strong>Endereço: </strong><?php echo htmlspecialchars($aluno["resp_rua"]); ?>, <?php echo htmlspecialchars($aluno["resp_numero"]); ?> - <?php echo htmlspecialchars($aluno["resp_bairro"]); ?></li>
                    <p><?php echo htmlspecialchars($aluno["resp_cidade"]); ?> - <?php echo htmlspecialchars($aluno["resp_uf"]); ?>, <?php echo htmlspecialchars($aluno["resp_cep"]); ?></p>
                </ul>
            </div>
            <?php
            // Controle da rematrícula
            $rematricula_liberada = true; // Troque por variável ou consulta no banco
            if ($rematricula_liberada):
            ?>
            <div class="linha-separadora"></div>
            <h2 class="titulo-secao">Secretaria Virtual</h2>
            <div class="containera">
                <ul>
                    <li><strong>Declaração de Escolaridade: </strong></li>
                    <a class="botao" href="declaracao.php?id=<?php echo $aluno['id']; ?>">Baixar Declaração</a>
                    <div class="separador"></div>
                    <li><strong>Carteirinha de Estudante: </strong></li>
                    <a class="botao" href="carteirinha.php?id=<?php echo $aluno['id']; ?>">Baixar Carteirinha</a>
                    <div class="separador"></div>
                    <li><strong>Boletim Virtual: </strong></li>
                    <p>Login: <?php echo htmlspecialchars($aluno["eduxe_login"]); ?></p>
                    <p>Senha: <?php echo htmlspecialchars($aluno["eduxe_senha"]); ?></p>
                    <a href="https://grupointegrado.eduxego.com.br" target="_blank" class="botao">EduxeGO</a>
                    <div class="separador"></div>
                    <li><strong>Portão Objetivo: </strong></li>
                    <p>Login: <?php echo htmlspecialchars($aluno["portal_objetivo_login"]); ?></p>
                    <p>Senha: <?php echo htmlspecialchars($aluno["portal_objetivo_senha"]); ?></p>
                    <a href="https://objetivo.br" target="_blank" class="botao">Objetivo.br</a>
                    <div class="separador"></div>
                    <li><strong>Ocorrências/Advertências/Suspensões </strong></li>
                    <a href="aluno_ocorrencias.php?id=<?php echo $aluno['id']; ?>" class="botao">Visualizar Ocorrências</a>
                    <div class="separador"></div>
                    <a href="aluno_advertencias.php?id=<?php echo $aluno['id']; ?>" class="botao">Visualizar Advertências</a>
                    <div class="separador"></div>
                    <a href="aluno_suspensoes.php?id=<?php echo $aluno['id']; ?>" class="botao">Vizsualizar Suspensões</a>
                </ul>
            </div>
            <div class="separador"></div>
            <div class="linha-separadora"></div>
            <h2 class="titulo-secao">Rematrícula 2026</h2>
            <div class="containera">
                <ul>
                    <li><strong>Passo 1: </strong>Baixe o contrato no botão abaixo</li>
                    <a href="gerar_contrato.php?id=<?php echo $aluno["id"]; ?>" target="_blank" class="botao">📄 Acessar Contrato PDF</a><br>
                    <div class="separador"></div>
                    <li><strong>Passo 2: </strong>Leia atentamente o contrato e assine no campo indicado na última página com sua conta gov.br, usando o app ou o site. Para acessar, clique no botão abaixo</li>
                    <a href="https://assinador.iti.br/assinatura/" target="_blank" class="botao">✍ Assinar com gov.br</a><br>
                    <div class="separador"></div>
                    <li><strong>Passo 3: </strong>Envie o contrato assinado pelo WhatsApp. É fácil: clique no botão abaixo para abrir a conversa, envie a mensagem pronta e o PDF do contrato. Nosso time responderá com as instruções dos próximos passos.</li>
                    <a href="https://wa.me/5534997276716?text=Segue%20contrato%20assinado%20do%20aluno(a)%20<?php echo urlencode($aluno["nome"]); ?>" target="_blank" class="botao">📲 Enviar pelo WhatsApp</a>
                    <?php else: ?>
                    <p>A rematrícula ainda não está liberada.</p>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="separador"></div>
            <a href="painel.php" class="btn">Voltar</a>
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
