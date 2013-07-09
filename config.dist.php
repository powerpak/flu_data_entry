<?php

// COPY TO config.php BEFORE USING

// Configure this to the root of where the app lives in the URL path hierarchy
// $BASE_URL = '/sub/folder');
$BASE_URL = '/flu_data_entry';

// Database connection settings
$DATABASE = array(
  'connection' => 'mysql:host=127.0.0.1;dbname=flu_pheno;charset=utf8',
  'user' => 'root',
  'password' => '',
  'session_model' => 'Session'
);