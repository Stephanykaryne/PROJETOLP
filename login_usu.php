<?php
session_start();

// Já logado? Vai direto para o menu
if (isset($_SESSION['usuario'])) {
    header("Location: menu.php");
    exit();
}

include 'conexao.php';

$erro = "";

if (isset($_POST['entrar'])) {

    $email = trim($_POST['nome']  ?? "");
    $senha = trim($_POST['senha'] ?? "");

    if ($email === "" || $senha === "") {

        $erro = "Preencha todos os campos.";

    } else {

        $stmt = $conexao->prepare("SELECT email, senha FROM usuario WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {

            $usuario = $resultado->fetch_assoc();

            if (password_verify($senha, $usuario['senha'])) {

                session_regenerate_id(true);

                // Variáveis que o menu.php e as demais páginas esperam
                $_SESSION['usuario'] = $usuario['email'];
                $_SESSION['nome']    = $usuario['email'];
                $_SESSION['tipo']    = "bibliotecario";

                $stmt->close();
                header("Location: menu.php");
                exit();

            } else {
                $erro = "Email ou senha incorretos.";
            }

        } else {
            $erro = "Email ou senha incorretos.";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&display=swap');

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --bg:      #0d0d0d;
      --surface: #161616;
      --border:  #222;
      --accent:  #6366f1;
      --accent2: #a78bfa;
      --danger:  #ef4444;
      --text:    #f0f0f0;
      --muted:   #555;
    }

    body {
      font-family: 'Sora', sans-serif;
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background-image:
        radial-gradient(ellipse at 10% 0%,  rgba(99,102,241,.12) 0%, transparent 55%),
        radial-gradient(ellipse at 90% 90%, rgba(167,139,250,.08) 0%, transparent 50%);
      padding: 32px 16px;
    }

    .card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 48px 40px;
      width: 100%;
      max-width: 420px;
      box-shadow: 0 25px 60px rgba(0,0,0,.5);
    }

    .logo {
      font-size: 13px;
      font-weight: 700;
      letter-spacing: 4px;
      text-transform: uppercase;
      color: var(--accent);
      margin-bottom: 28px;
    }

    h1 { font-size: 26px; font-weight: 700; color: var(--text); margin-bottom: 6px; }
    .sub { font-size: 13px; color: var(--muted); margin-bottom: 32px; }

    label {
      display: block;
      font-size: 12px;
      font-weight: 600;
      letter-spacing: 1px;
      text-transform: uppercase;
      color: #666;
      margin-bottom: 6px;
    }

    input {
      width: 100%;
      padding: 12px 14px;
      background: #1e1e1e;
      border: 1px solid #2e2e2e;
      border-radius: 8px;
      color: var(--text);
      font-family: 'Sora', sans-serif;
      font-size: 14px;
      margin-bottom: 20px;
      transition: border-color .2s;
      outline: none;
    }

    input:focus { border-color: var(--accent); }

    /* Campo de senha com botão de mostrar/ocultar */
    .password-field { position: relative; }
    .password-field input { padding-right: 46px; }

    .btn-view {
      position: absolute;
      right: 8px;
      top: 6px;
      background: none;
      border: none;
      font-size: 18px;
      cursor: pointer;
      padding: 6px;
      transition: transform .2s;
    }

    .btn-view:hover { transform: scale(1.15); }

    /* Link esqueci minha senha */
    .esqueci {
      display: block;
      text-align: right;
      font-size: 12px;
      color: #818cf8;
      text-decoration: none;
      margin-top: -12px;
      margin-bottom: 22px;
      transition: color .2s;
    }

    .esqueci:hover { color: var(--accent2); text-decoration: underline; }

    .btn-entrar {
      width: 100%;
      padding: 13px;
      background: var(--accent);
      color: #fff;
      font-family: 'Sora', sans-serif;
      font-size: 14px;
      font-weight: 600;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background .2s, transform .1s;
      margin-bottom: 16px;
    }

    .btn-entrar:hover  { background: #4f52d9; }
    .btn-entrar:active { transform: scale(.98); }

    .btn-cadastro {
      display: block;
      width: 100%;
      padding: 12px;
      background: transparent;
      border: 1px solid var(--border);
      border-radius: 8px;
      color: #aaa;
      font-family: 'Sora', sans-serif;
      font-size: 14px;
      font-weight: 500;
      text-align: center;
      text-decoration: none;
      transition: border-color .2s, color .2s;
    }

    .btn-cadastro:hover { border-color: var(--accent); color: var(--accent); }

    .msg-erro {
      background: rgba(239,68,68,.12);
      border: 1px solid rgba(239,68,68,.3);
      color: #f87171;
      font-size: 13px;
      padding: 11px 14px;
      border-radius: 8px;
      margin-bottom: 20px;
    }

    .divider { border: none; border-top: 1px solid var(--border); margin: 20px 0; }
  </style>
</head>
<body>

<div class="card">

  <div class="logo">&#9632; Sistema</div>
  <h1>Bem-vindo</h1>
  <p class="sub">Faça login para continuar</p>

  <?php if ($erro): ?>
    <div class="msg-erro">⚠️ <?= htmlspecialchars($erro) ?></div>
  <?php endif; ?>

  <form method="POST" action="" autocomplete="off">

    <label for="nome">Email</label>
    <input type="text" id="nome" name="nome"
           placeholder="seu@email.com"
           value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>"
           required autofocus>

    <label for="senha">Senha</label>
    <div class="password-field">
      <input type="password" id="senha" name="senha"
             placeholder="••••••••" required>
      <button type="button" class="btn-view" onclick="mostrarSenha()" aria-label="Mostrar senha">👁️</button>
    </div>

    <a href="esquecer_senha.php" class="esqueci">Esqueci minha senha</a>

    <button type="submit" name="entrar" class="btn-entrar">Entrar</button>

  </form>

  <hr class="divider">

  <a href="cadastro_usu.php" class="btn-cadastro">Não tenho conta — Cadastrar</a>

</div>

<script>
  function mostrarSenha() {
    const campo = document.getElementById('senha');
    const botao = document.querySelector('.btn-view');

    if (campo.type === 'password') {
      campo.type = 'text';
      botao.textContent = '👁️‍🗨️';
    } else {
      campo.type = 'password';
      botao.textContent = '👁️';
    }
  }
</script>

</body>
</html>
