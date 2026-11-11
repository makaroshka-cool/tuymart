<?php
require __DIR__ . '/customer-auth.php';
unset($_SESSION['customer']);
header('Location: ../index.html');
exit;