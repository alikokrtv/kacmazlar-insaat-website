<?php
/**
 * Kacmazlar İnşaat CMS - Logout Sayfası
 */

require_once 'includes/Auth.php';

$auth = new Auth();
$auth->logout();

header('Location: login.php?message=logged_out');
exit;
