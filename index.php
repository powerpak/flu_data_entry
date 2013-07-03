<?php

if (!require_once('config.php')) { die('Must cp config.dist.php config.php && vi config.php'); }
if (!file_exists('.htaccess')) { die('Must cp setup.htaccess .htaccess && vi .htaccess'); }

require_once('lib/glue.php');
require_once('lib/init.php');

$urls = array(
  '/' => 'index',
  '/login' => 'login',
  '/logout' => 'logout',
  '/edit' => 'edit',
  '/edit/(?P<id>\d+)?' => 'edit'
);

class login extends Controller {
  function GET() {
    if (isset($_SESSION['user'])) { redirect('/'); }
    include 'lib/views/login.php';
  }
  function POST() {
    if (isset($_POST['user'])) {
      $_SESSION['user'] = $_POST['user'];
      redirect('/');
    }
    include 'lib/views/login.php';
  }
}

class logout extends Controller {
  function GET() {
    unset($_SESSION['user']);
    redirect('/login');
  }
}

class index extends Controller {
  function GET() {
    $in_progress_sources = Model::factory('Source')->where('review_by', $this->user)
      ->where('reviewed', 1)->find_many();
    $new_sources = Model::factory('Source')->where('review_by', $this->user)
      ->where_null('reviewed')->find_many();
    $done_sources = Model::factory('Source')->where('review_by', $this->user)
      ->where('reviewed', 2)->find_many();
    include 'lib/views/list.php';
  }
}

class edit extends Controller {
  function GET($vars) {
    $id = isset($vars['id']) ? $vars['id'] : NULL;
    if ($id !== NULL) {
      $source = Model::factory('Source')->find_one($id);
      $phenotypes = array();
      foreach ($source->phenotypes()->find_many() as $phenotype) {
        array_push($phenotypes, $phenotype->as_array());
      }
    }
    include 'lib/views/pheno_form.php';
  }
  
  function POST() {
    $source = Source::save_from_form($_POST);
    if ($_POST['next_source']) {
      $next_source = Model::factory('Source')->where_null('reviewed')->find_one();
      redirect("/edit/{$next_source->id}");
    } elseif ($_POST['done']) {
      redirect("/");
    } else {
      redirect("/edit/{$source->id}");
    }
  }
}

glue::stick($urls, $BASE_URL, array(array_merge($G, array('base_url'=>$BASE_URL))));