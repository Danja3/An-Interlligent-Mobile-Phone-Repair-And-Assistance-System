<?php
// Local environment configuration (XAMPP defaults).
declare(strict_types=1);

// --- Database ---
const DB_HOST = '127.0.0.1';
const DB_NAME = 'techassist';
const DB_USER = 'root';
const DB_PASS = '';
const DB_CHARSET = 'utf8mb4';

// --- App ---
// Base URL path the app is served from (no trailing slash).
//   Apache (htdocs/techassist):   '/techassist'
//   PHP built-in server at root:  ''
const BASE_URL = '/techassist';

// Where uploaded technician photos live (filesystem + public path).
const UPLOAD_DIR = __DIR__ . '/../uploads';
const UPLOAD_URL = BASE_URL . '/uploads';

const APP_NAME = 'TechAssist';
