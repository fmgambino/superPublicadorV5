<?php
session_start();

// Duración máxima de inactividad (10 segundos)
define('MAX_INACTIVITY', 5);

// Validar autenticación
if (!isset($_SESSION['user_id'])) {
    header('Location: /autoposteg/login');
    exit;
}

// Logout automático tras inactividad
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > MAX_INACTIVITY)) {
    session_unset();
    session_destroy();
    header('Location: /autoposteg/login?error=timeout');
    exit;
}

// Actualizar marca de actividad
$_SESSION['last_activity'] = time();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <base href="/autoposteg/" />
  <meta name="theme-color" content="#000000" />
  <link rel="manifest" href="manifest.json" />
  <link rel="stylesheet" href="assets/css/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="icon" href="images/favicon" type="image/png" />
  <title>Super-Publicador Facebook V5.0 by Electrónica Gambino</title>

  <!-- Facebook SDK -->
  <script async defer crossorigin="anonymous" src="https://connect.facebook.net/es_LA/sdk.js"></script>
  <script>
    window.fbAsyncInit = function() {
      FB.init({
        appId      : 'YOUR_APP_ID',
        cookie     : true,
        xfbml      : true,
        version    : 'v15.0'
      });
      FB.getLoginStatus(function(response) {
        statusChangeCallback(response);
      });
    };

    function statusChangeCallback(response) {
      const fbModal  = document.getElementById('facebookModal');
      const tutModal = document.getElementById('tutorialModal');
      if (response.status === 'connected') {
        fbModal.classList.add('hidden');
        tutModal.classList.remove('hidden');
      } else {
        fbModal.classList.remove('hidden');
      }
    }

    function openFacebookLogin() {
      FB.login(function(response) {
        if (response.authResponse) {
          location.reload();
        }
      }, {scope: 'public_profile,email'});
    }
  </script>
</head>
<body class="dark-mode">

  <!-- Pantalla de carga -->
  <div id="loader"><div class="spinner"></div></div>

  <!-- Popup instalación PWA -->
  <div id="installBanner" class="install-popup hidden">
    <p id="installMessage">¿Deseas instalar esta app en tu escritorio?</p>
    <button id="installBtn">Instalar</button>
    <button onclick="dismissInstall()">Cancelar</button>
  </div>

  <header class="navbar">
    <img id="logo" data-logo data-logo-light="images/superpublicador_ligth.png" data-logo-dark="images/superpublicador_black.png" src="images/logo-dark.png" alt="Logo" />
    <ul class="desktop-menu">
      <li><a href="#nosotros">Nosotros</a></li>
      <li><a href="#contacto">Contacto</a></li>
      <li><a href="#planes">Planes</a></li>
      <li><a href="#" id="linkTutoriales" class="btn-tutorial">Tutoriales</a></li>
    </ul>
    <div class="controls">
      <button id="langToggle" class="icon-button tooltip" title="Cambiar idioma">🌐<span class="tooltiptext" id="langTooltip">Idioma: Español</span></button>
      <button id="themeToggle" class="icon-button" title="Modo claro/oscuro">☀️</button>
      <a href="backend/logout.php" class="icon-button" title="Cerrar sesión"><i class="fa fa-sign-out-alt"></i></a>
    </div>
    <button class="menu-toggle" onclick="toggleMenu()">☰</button>
  </header>

  <nav id="menu" class="menu">
    <a href="#nosotros">Nosotros</a>
    <a href="#contacto">Contacto</a>
    <a href="#planes">Planes</a>
    <a href="#" id="linkTutorialesMobile" class="btn-tutorial">Tutoriales</a>
  </nav>

  <div class="iframe-wrapper">
    <iframe id="toolFrame" src="https://v2.fewfeed.com/tool/auto-post-fb-group" frameborder="0" style="min-height: 800px;"></iframe>
    <div class="iframe-overlay"></div>
  </div>

  <!-- Facebook Login Modal -->
  <div id="facebookModal" class="modal hidden">
    <div class="modal-content">
      <span class="close" onclick="document.getElementById('facebookModal').classList.add('hidden')">&times;</span>
      <div style="text-align: center">
        <p style="font-size: 36px; color: red;">&#9888;</p>
        <h2>Debes iniciar sesión en Facebook</h2>
        <p>Para usar esta herramienta, primero debes iniciar sesión en Facebook. Si ya lo hiciste, recarga.</p>
        <button class="btn-facebook" onclick="openFacebookLogin()">Ir a Facebook</button>
      </div>
    </div>
  </div>

  <!-- Tutoriales Modal -->
  <div id="tutorialModal" class="modal hidden">
    <div class="modal-content tutorial-container">
      <span class="close" onclick="document.getElementById('tutorialModal').classList.add('hidden')">&times;</span>
      <h2 style="text-align: center;">🎓 Guías paso a paso</h2>
      <div class="tutorial-videos">
        <div class="tutorial-item">
          <h3>📌 Tutorial Nº1: Cómo Publicar en tus Grupos de Facebook</h3>
          <video controls><source src="css/videos/tutorial01_ComoPublicarAutoGrupos.mp4" type="video/mp4"></video>
        </div>
        <div class="tutorial-item">
          <h3>🛠️ Tutorial Nº2: Cómo responder automáticamente a los comentarios</h3>
          <video controls><source src="css/videos/tutorial02_ComoResponderComentarios.mp4" type="video/mp4"></video>
        </div>
      </div>
    </div>
  </div>

  <!-- Popup de descarga de extensión -->
  <div id="extensionModal" class="modal hidden">
    <div class="modal-content">
      <span class="close" onclick="closeExtensionModal()">&times;</span>
      <div style="text-align: center">
        <h2>Instala nuestra extensión para Google Chrome</h2>
        <p>Para continuar, por favor descarga e instala nuestra extensión en tu navegador.</p>
        <a href="assets/files/superPublicadorEG.zip" class="btn-facebook" download>Descargar Extensión Chrome</a>
        <button onclick="leerInstrucciones()" class="btn-voice">🔊 Escuchar instrucciones</button>
        <div style="margin-top: 20px">
          <video controls width="100%" style="max-width: 400px">
            <source src="https://www.youtube.com/watch?v=Z3XfiatWazI" type="video/mp4" />
            Tu navegador no soporta video.
          </video>
        </div>
      </div>
    </div>
  </div>

  <footer>
    <p id="welcomeMessage">Bienvenido a la herramienta automática de publicación.</p>
    Powered by <a href="https://electronicagambino.com" target="_blank">Electrónica Gambino</a>
  </footer>

  <button class="floating-button subscribe-button" onclick="openPlansModal()">Suscribirse</button>
  <a href="https://wa.me/+5491128715389" target="_blank" class="floating-button whatsapp-button">💬</a>

  <div id="plansModal" class="modal hidden" onclick="closePlansModal(event)">
    <div class="modal-content" onclick="event.stopPropagation()">
      <span class="close" onclick="closePlansModal()">&times;</span>
      <h2>Elige tu plan</h2>
      <div class="plan">
        <h3>Plan Bronce - $7,99/mes</h3>
        <p>30 días de AutoPostEG + 30 días de "Wasapbot"</p>
        <a href="https://tusitio.com/checkout-bronce" class="btn-plan" target="_blank">Adquirir</a>
      </div>
      <div class="plan">
        <h3>Plan Platinum - $19,99/mes</h3>
        <p>90 días de AutoPostEG + 90 días de "Wasapbot"</p>
        <a href="https://tusitio.com/checkout-platinum" class="btn-plan" target="_blank">Adquirir</a>
      </div>
      <div class="plan">
        <h3>Plan Oro - $49,99/mes</h3>
        <p>365 días de AutoPostEG + 365 días de "Wasapbot"</p>
        <a href="https://tusitio.com/checkout-oro" class="btn-plan" target="_blank">Adquirir</a>
      </div>
    </div>
  </div>

  <script>
// Detecta si la extensión está instalada en Chrome
function detectExtension() {
  const extensionId = 'cfkbmndbnhbodakfmceoanpmlplbgllh'; // reemplaza con el ID real de la extensión
  if (window.chrome && chrome.runtime && chrome.runtime.sendMessage) {
    chrome.runtime.sendMessage(extensionId, {ping: true}, response => {
      if (chrome.runtime.lastError || !response) {
        document.getElementById('extensionModal').classList.remove('hidden');
      }
    });
  } else {
    // No es Chrome o API no disponible
    document.getElementById('extensionModal').classList.remove('hidden');
  }
}

function closeExtensionModal() {
  document.getElementById('extensionModal').classList.add('hidden');
}

window.addEventListener('load', detectExtension);
</script>
<script src="assets/js/app.js"></script>
</body>
</html>
