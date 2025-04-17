<?php
require_once 'config/functions.php';

session_start();
session_unset();
session_destroy();

redirect('login.php');
?>