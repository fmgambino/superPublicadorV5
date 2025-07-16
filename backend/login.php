<?php
// /xampp/htdocs/autoposteg/backend/login.php
session_start();
require __DIR__ . '/config.php';

// Si ya está autenticado, redirige al dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: /autoposteg/dashboard');
    exit;
}

$error = $_GET['error'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        header('Location: login?error=campos');
        exit;
    }

    try {
        $stmt = $pdo->prepare(
            "SELECT id, first_name, password_hash, subscription_type
             FROM users WHERE email = ?"
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['first_name'];
            $_SESSION['role']      = $user['subscription_type'];
            header('Location: /autoposteg/dashboard');
            exit;
        } else {
            header('Location: login?error=invalid');
            exit;
        }
    } catch (PDOException $e) {
        header('Location: login?error=db');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <base href="/autoposteg/">
  <title data-i18n="login">Iniciar sesión - SuperPublicador</title>
  <link rel="stylesheet" href="assets/css/auth.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <div class="container">
    <div class="form-container sign-in-container">
      <form action="backend/login.php" method="POST">
        <h1 data-i18n="login">Iniciar sesión</h1>
        <input type="email" name="email" placeholder="Correo electrónico" required>
        <div class="password-container">
          <input type="password" id="password" name="password" placeholder="Contraseña" required>
          <button type="button" id="togglePassword" class="toggle-password" aria-label="Mostrar contraseña">
            <i id="toggleIcon" class="fa fa-eye"></i>
          </button>
        </div>
        <div class="options">
          <label for="remember">
            <input type="checkbox" id="remember" name="remember">
            <span data-i18n="remember">Recuérdame</span>
          </label>
          <a href="register" class="forgot-password" data-i18n="createAccount">¿Nuevo aquí? Regístrate</a>
        </div>
        <button type="submit" class="btn" data-i18n="login">Ingresar</button>
      </form>
      <?php if ($error): ?>
        <script>
          const messages = {
            campos: 'Por favor, completa todos los campos.',
            invalid: 'Correo o contraseña incorrectos.',
            db: 'Error de conexión con la base de datos.'
          };
          Swal.fire({ icon: 'error', title: '❌', text: messages['<?= addslashes($error) ?>'] || 'Ha ocurrido un error.' });
        </script>
      <?php endif; ?>
    </div>
    <div class="overlay-container">
      <div class="overlay">
        <div class="top-controls">
          <label class="switch" title="Cambiar tema">
            <input type="checkbox" id="themeSwitcher"><span class="slider"></span>
          </label>
          <button class="lang-toggle" onclick="toggleLanguage()" title="Cambiar idioma">🇪🇸/🇺🇸</button>
        </div>
        <div class="overlay-panel overlay-right">
          <img class="logo" src="images/logo-light.png" alt="Logo">
          <h1 data-i18n="createAccount">¿Nuevo aquí?</h1>
          <p data-i18n="subtitle">Crea una cuenta para comenzar a publicar automáticamente</p>
          <a href="/autoposteg/register"><button class="ghost" data-i18n="register">Registrarse</button></a>
        </div>
      </div>
    </div>
  </div>
  <div class="auth-footer">
    <span data-i18n="footer">Bienvenido a la herramienta automática de publicación.</span><br>
    <span data-i18n="powered">Powered by</span> <a href="https://electronicagambino.com" target="_blank">Electrónica Gambino</a>
  </div>
  <script src="assets/js/auth.js"></script>
  <script>
    document.getElementById('togglePassword').addEventListener('click', function() {
      const pwd = document.getElementById('password');
      const icon = document.getElementById('toggleIcon');
      if (pwd.type === 'password') {
        pwd.type = 'text'; icon.classList.replace('fa-eye','fa-eye-slash'); this.setAttribute('aria-label','Ocultar contraseña');
      } else {
        pwd.type = 'password'; icon.classList.replace('fa-eye-slash','fa-eye'); this.setAttribute('aria-label','Mostrar contraseña');
      }
    });
  </script>
</body>
</html>
