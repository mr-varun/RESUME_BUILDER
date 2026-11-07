<?php
require_once __DIR__ . '/includes/boot.php';
Auth::logout();
Helpers::redirect(APP_URL . '/index.php');
