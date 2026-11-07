<?php
if (!defined('ROOT_PATH')) define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/config/config.php';
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/app/CSRF.php';
require_once ROOT_PATH . '/app/Auth.php';
require_once ROOT_PATH . '/app/Helpers.php';
require_once ROOT_PATH . '/app/Document.php';

Auth::startSession();
