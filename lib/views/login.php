<?php include 'partial/header.php' ?>

  <div class="container">

    <h1>Select user</h1>
    
    <p>Please select your name from the following dropdown.</p>

    <form action="<?= href('/login') ?>" class="form-horizontal" method="post">
      <div class="control-group">
        <label class="control-label" for="user">User</label>
        <div class="controls">
          <select name="user">
            <option>Ted</option>
            <option>Ruhana</option>
          </select>
        </div>
      </div>
      <div class="control-group">
        <div class="controls">
          <label class="checkbox">
            <input type="checkbox"> Remember me
          </label>
          <button type="submit" class="btn">Sign in</button>
        </div>
      </div>
    </form>

  </div>
  
  <?php include 'partial/scripts.php' ?>
</body>
</html>