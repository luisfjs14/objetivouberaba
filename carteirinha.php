<?php
require_once "conexao.php";

// Recebe o ID do aluno via GET
$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID do aluno não informado.");
}

// Busca aluno e turma pelo ID
$stmt = $pdo->prepare("
    SELECT
        a.nome AS nome_aluno,
        a.data_nascimento,
        a.portal_objetivo_login AS matricula,
        a.foto,
        t.nome AS turma
    FROM alunos a
    JOIN turmas t ON t.id = a.turma_id
    WHERE a.id = :id
");
$stmt->execute([':id' => $id]);
$aluno = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$aluno) {
    die("Aluno não encontrado.");
}

function formatarData($data) {
    return date('d/m/Y', strtotime($data));
}

// Caminho da foto
$foto = $aluno['foto'] && file_exists($aluno['foto'])
    ? $aluno['foto']
    : 'assets/foto_padrao.jpg'; // imagem padrão

// Caminho dos logos da escola
$logo_escola = 'assets/logoobjetivouberabaamarela.png'; // Exemplo: logo principal
$logo_30_anos = 'assets/30anos.png'; // Exemplo: logo Uberaba / 30 anos
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Carteirinha de estudante - Colégio Objetivo Uberaba</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="icon" type="image/png" href="assets/favicon.png" />
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f3f3f3;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
      margin: 0;
      min-height: 100vh;
      box-sizing: border-box; /* Garante que padding e border sejam incluídos na largura/altura */
    }

    .card {
      width: 720px; /* Largura padrão para desktop */
      height: 400px; /* Altura padrão para desktop */
      /* Gradiente de azul para um azul mais claro cobrindo todo o card */
      background: linear-gradient(to right, #003f9c, #6495ED); /* Azul escuro para Azul mais claro (Cornflower Blue) */
      border: none;
      border-radius: 10px;
      position: relative;
      overflow: hidden;
      display: flex;
      flex-direction: row; /* Layout em linha para desktop */
      box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    }

    .left {
      padding: 30px;
      width: 65%; /* Largura padrão para desktop */
      display: flex;
      flex-direction: column;
      justify-content: flex-start;
      color: #FFD700; /* Cor do texto principal alterada para amarelo */
      position: relative;
      z-index: 1;
    }

    .logo-container { /* Container para as duas logos */
        display: flex;
        align-items: center;
        gap: 5px; /* Espaçamento entre as logos */
        position: absolute; /* Posicionamento absoluto para as logos */
        top: 20px; /* Ajuste conforme necessário */
        left: 30px; /* Ajuste conforme necessário */
        z-index: 2; /* Garante que as logos fiquem acima */
    }

    .school-logo { /* Estilo para o logo principal "Objetivo" */
        height: 35px; /* Altura menor para o logo principal */
        width: auto;
    }

    .thirty-years-logo { /* Estilo para o logo de "30 anos" ou "Uberaba" */
        height: 45px; /* Altura um pouco maior, ou ajuste conforme a proporção desejada */
        width: auto;
    }

    .left h2 {
      color: #FFD700; /* Alterado para amarelo */
      margin: 90px 0 10px 0; /* Ajustar margem para dar espaço aos logos */
      font-size: 20px;
    }

    .left h3 {
      margin: 5px 0;
      font-size: 16px;
      font-weight: normal;
      color: #FFD700; /* Alterado para amarelo */
    }

    .highlight {
      /* Fundo do "CARTEIRINHA DE ESTUDANTE" */
      background: #003f9c; /* Cor sólida azul para o destaque */
      color: #FFD700; /* Texto em amarelo */
      display: inline-block;
      padding: 4px 10px;
      margin-top: 10px;
      font-weight: bold;
      border-radius: 3px;
      z-index: 1;
    }

    .photo-section { /* Seção da foto */
      width: 35%; /* Largura padrão para desktop */
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      /* Borda esquerda para separar do lado esquerdo, como na imagem de referência */
      padding: 20px;
      z-index: 1;
    }

    .photo-section img {
      width: 160px;
      height: 200px;
      object-fit: cover;
      /* Borda da foto em azul escuro */
      border: 3px solid #003f9c;
      border-radius: 5px;
    }

    .validade {
      margin-top: 20px;
      font-size: 14px;
      color: #333; /* Cor do texto da validade */
      /* No exemplo da imagem, a validade não tem um fundo próprio, então removemos */
      /* background: rgba(0, 0, 0, 0.2); */
      /* padding: 5px 10px; */
      /* border-radius: 3px; */
    }

    .back-button {
        display: inline-block;
        margin-top: 20px;
        padding: 10px 15px;
        background-color: #fdd700;
        color: #003f9c;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        transition: background-color 0.3s ease;
    }

    .back-button:hover {
        background-color: #e6c500;
    }

    /* ========================================= */
    /* RESPONSIVIDADE PARA TELAS MENORES (CELULAR - CARTÃOZINHO) */
    /* ========================================= */
    @media (max-width: 580px) { /* Ponto de quebra ajustado para um layout menor, mas ainda em linha */
      body {
        padding: 10px; /* Reduz o padding do body */
      }

      .card {
        width: 95%; /* Ocupa quase toda a largura da tela */
        height: 250px; /* Altura fixa menor para o "cartãozinho" */
        /* flex-direction já é row por padrão para desktop, mantemos */
        font-size: 0.7em; /* Reduz a fonte base para todo o card */
      }

      .left {
        width: 65%; /* Mantém a proporção de largura */
        padding: 15px 10px; /* Ajusta o padding para celular */
        /* text-align: left; /* Mantém alinhamento à esquerda */
      }

      .logo-container {
        position: absolute; /* Mantém o posicionamento absoluto */
        top: 10px; /* Ajusta para o topo menor */
        left: 10px; /* Ajusta para a esquerda menor */
        gap: 3px; /* Reduz o espaçamento entre as logos */
      }

      .school-logo {
        height: 20px; /* Logos menores */
      }

      .thirty-years-logo {
        height: 25px; /* Logos menores */
      }

      .left h2 {
        margin: 45px 0 5px 0; /* Ajusta a margem superior para o espaço das logos menores */
        font-size: 13px; /* Diminui o tamanho da fonte do nome */
      }

      .left h3 {
        margin: 3px 0;
        font-size: 10px; /* Diminui o tamanho da fonte dos detalhes */
      }

      .highlight {
        padding: 3px 6px;
        margin-top: 5px;
        font-size: 10px; /* Diminui o tamanho da fonte do highlight */
      }

      .photo-section {
        width: 35%; /* Mantém a proporção de largura */
        padding: 10px; /* Reduz o padding da seção da foto */

      }

      .photo-section img {
        width: 80px; /* Diminui o tamanho da foto */
        height: 100px; /* Diminui o tamanho da foto */
        border-width: 2px; /* Borda da foto mais fina */
      }

      .validade {
        margin-top: 10px;
        font-size: 10px; /* Diminui o tamanho da fonte da validade */
      }

      .back-button {
        padding: 6px 10px;
        font-size: 12px;
        margin-top: 15px;
        width: auto; /* Deixa o botão se ajustar ao conteúdo */
      }
    }

    @media (max-width: 380px) { /* Para celulares muito pequenos */
        .card {
            height: 200px; /* Ajuste ainda menor */
        }
        .left h2 {
            font-size: 11px;
            margin: 40px 0 3px 0;
        }
        .left h3 {
            font-size: 9px;
            margin: 2px 0;
        }
        .highlight {
            font-size: 9px;
            padding: 2px 5px;
        }
        .photo-section img {
            width: 65px;
            height: 80px;
        }
        .validade {
            font-size: 9px;
            margin-top: 8px;
        }
        .back-button {
            font-size: 10px;
            padding: 5px 8px;
            margin-top: 10px;
        }
    }

  </style>
</head>
<body>
  <div class="card">
    <div class="left">
      <div class="logo-container">
        <img src="<?= $logo_escola ?>" alt="Logo da Escola" class="school-logo">
        <img src="<?= $logo_30_anos ?>" alt="Logo 30 Anos" class="thirty-years-logo">
      </div>
      <br><br><br>
      <span class="highlight">CARTEIRINHA DE ESTUDANTE</span>
      <h2>NOME: <?= strtoupper($aluno["nome_aluno"]) ?></h2>
      <h3>TURMA: <?= $aluno["turma"] ?></h3>
      <h3>DATA NASCIMENTO: <?= formatarData($aluno["data_nascimento"]) ?></h3>
      <h3>MATRÍCULA: <?= $aluno["matricula"] ?></h3>
    </div>

    <div class="photo-section">
      <img src="<?= $foto ?>" alt="Foto do aluno">
      <br><br><br><br>
      <div class="validade">Validade: 31/12/2025</div>
    </div>
  </div>
</body>
</html>