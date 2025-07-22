<?php
// Inicia sessão
session_start();

// Conexão com o banco
require_once "conexao.php";

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $senha = $_POST["senha"];

    // Busca o responsável financeiro pelo email
    $stmt = $pdo->prepare("SELECT id, nome, senha_hash, ativo FROM responsaveis_financeiros WHERE email = :email LIMIT 1");
    $stmt->bindParam(":email", $email);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        if (!$usuario["ativo"]) {
            $erro = "Este usuário está desativado.";
        } elseif (password_verify($senha, $usuario["senha_hash"])) {
            // Login bem-sucedido
            $_SESSION["responsavel_id"] = $usuario["id"];
            $_SESSION["responsavel_nome"] = $usuario["nome"];
            header("Location: painel.php");
            exit();
        } else {
            $erro = "Senha incorreta.";
        }
    } else {
        $erro = "E-mail não encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Área Restrita - Colégio Objetivo Uberaba</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="icon" type="image/png" href="assets/favicon.png" />
  <style>
    body {
      margin: 0;
      padding: 0;
      background-image:
        linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)),  /* sobreposição escura com transparência */
        url(assets/banner1.png);
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      height: 100vh;
      color: white;
    }

    .conteudo {
      text-align: center;
      padding-top: 100px;
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

      <nav class="header-center" id="navMenu">
        <ul>
          <li><a href="index.html">Home</a></li>
          <li><a href="cursos.html">Cursos</a></li>
          <li><a href="eventos.html">Eventos</a></li>
          <li><a href="contato.html">Contato</a></li>
          <li><a href="trabalhe.html">Trabalhe Conosco</a></li>
        </ul>
      </nav>

      <div class="header-right">
        <button class="menu-toggle" id="menuToggle">&#9776;</button>
        <a href="restrita.html" class="btn-restrita">Área Restrita</a>
      </div>
    </div>
  </header>

  <a href="https://wa.me/5534984459808" target="_blank" class="whatsapp-button" title="Fale conosco no WhatsApp">
    <img src="https://img.icons8.com/color/48/000000/whatsapp--v1.png" alt="WhatsApp" />
  </a>

  <main class="container" style="padding: 100px 20px;">
    <section class="area-restrita">
      <h1>Área Restrita</h1>
      <?php if(isset($erro)) echo "<p style='color:red;'>$erro</p>"; ?>
      <form method="POST" class="login-form">
        <label for="email">Login</label>
        <input type="email" id="email" name="email" placeholder="nomecompleto@objetivouberaba.com" required />

        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required />

        <button type="submit">Entrar</button>
      </form>

      <?php if (!empty($erro)): ?>
        <script>
          alert("<?= $erro ?>");
        </script>
      <?php endif; ?>
    </section>
  </main>

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

  <script src="js/script.js"></script>
</body>
</html>