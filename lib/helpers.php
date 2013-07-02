<?php
/*
 * A shorter name for htmlspecialchars
 */

function html_esc() {
  return call_user_func_array('htmlspecialchars', func_get_args());
}

function href($href, $relative_to = FALSE) {
  global $BASE_URL;
  if ($relative_to===FALSE) {
    $href = preg_replace('/^\/+/', '', $href);
    return "$BASE_URL/$href";
  } else {
    // relative paths unimplemented
    // we would  use http_build_url with the new path
  }
}

function nav_link($current, $href, $title) {
  $title = html_esc($title);
  $active = $current == $href ? 'active' 
    : ($href !== '/' && strpos($current, $href) !== FALSE ? 'active' : '');
  $href = href($href);
  return "<li class=\"$active\"><a href=\"$href\">$title</a></li>";
}

function redirect($href) {  
  // handle URL
  if (preg_match("/^http:\/\//", $href))
    header("Location: " . $href);

  // handle relative path
  else {
    // adapted from http://www.php.net/header
    $protocol = (@$_SERVER["HTTPS"]) ? "https" : "http";
    $host = $_SERVER["HTTP_HOST"];
    $href = href($href);
    header("Location: $protocol://$host$href");
  }

  // exit immediately since we're redirecting anyway
  exit;
}