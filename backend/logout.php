<?php
// /autoposteg/backend/logout.php
session_start();
require __DIR__ . '/config.php';

// Destruir sesión completamente
session_unset();
session_destroy();

// Redirigir al login según BASE_PATH
auth_header: 
header('Location: ' . BASE_PATH . '/login');
exit;
