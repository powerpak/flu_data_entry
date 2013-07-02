<?php include 'partial/header.php' ?>

  <div class="container">

    <?php if (count($in_progress_sources)): ?>
      <h2>Works in progress (<?= count($in_progress_sources) ?>)</h2>
      <?php $rows = $in_progress_sources; ?>
      <?php include 'partial/source_table.php'; ?>
    <?php endif; ?>

    <?php if (count($new_sources)): ?>
      <h2>Unopened records (<?= count($new_sources) ?>)</h2>
      <?php $rows = $new_sources; ?>
      <?php include 'partial/source_table.php'; ?>
    <?php endif; ?>
    
    <?php if (count($done_sources)): ?>
      <h2>Finished records (<?= count($done_sources) ?>)</h2>
      <?php $rows = $done_sources; ?>
      <?php include 'partial/source_table.php'; ?>
    <?php endif; ?>

  </div>
  
  <?php include 'partial/scripts.php' ?>
  <script type="text/javascript" src="<?= href('js/source-list.js') ?>"></script>
</body>
</html>