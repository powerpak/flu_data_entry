<?php
  
require_once('idiorm.php');
require_once('dakota.php');
require_once('helpers.php');

// display errors and warnings but not notices
ini_set("display_errors", TRUE);
error_reporting(E_ALL ^ E_NOTICE);

// enable sessions, restricting cookie to $BASE_URL
session_set_cookie_params(0, $BASE_URL);
session_start();

// Global variables to be inserted into the controller
$G = array();

ORM::configure($DATABASE['connection']);
ORM::configure('username', $DATABASE['user']);
ORM::configure('password', $DATABASE['password']);

foreach (glob("lib/models/*.php") as $filename) {
  require_once($filename);
}

$base_url = preg_quote($BASE_URL, '/');
// require authentication for most pages
if (!preg_match("/^$base_url\\/log(in|out)/", $_SERVER['REQUEST_URI'])) {
  if (!isset($_SESSION["user"])) { redirect("login"); }
  
  // if authenticated, load the current user's information into a global
  // right now that's just a name.
  $G["user"] = $_SESSION["user"];
}