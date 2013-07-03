<!DOCTYPE html>
<html>
<head>
  <title><?= isset($title) ? $title : 'flu phenos' ?></title>
  <link href="<?= href('css/bootstrap.css') ?>" rel="stylesheet"/>
  <link href="<?= href('css/bootstrap-tagmanager.css') ?>" rel="stylesheet"/>
  <link href="<?= href('css/form.css') ?>" rel="stylesheet"/>
  <link href="<?= href('css/bootstrap-responsive.css') ?>" rel="stylesheet"/>
</head>
<body>  
  <div class="navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-inner">
      <div class="container">
        <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="brand" href="<?= href('/') ?>">Flu phenotype entry</a>
        <div class="nav-collapse collapse">
          <ul class="nav">
            <?= nav_link($this->path, '/', 'List phenotypes') ?>
            <?= nav_link($this->path, '/edit', 'Enter data') ?>
            <?php if ($_SESSION['user']): ?>
            <?= nav_link($this->path, '/logout', "Logout {$this->user}") ?>
            <?php endif; ?>
          </ul>
        </div> <!--/.nav-collapse -->
      </div>
    </div>
  </div>