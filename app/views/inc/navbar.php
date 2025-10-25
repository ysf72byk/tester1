<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-3">
  <div class="container">
    <a class="navbar-brand" href="<?php echo URLROOT; ?>"><?php echo SITENAME; ?></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarsExampleDefault">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item">
          <a class="nav-link" href="<?php echo URLROOT; ?>"><?php echo __('home'); ?></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo URLROOT; ?>/pages/about"><?php echo __('about'); ?></a>
        </li>
      </ul>

      <ul class="navbar-nav ml-auto">
        <?php if(isset($_SESSION['user_id'])) : ?>
        <li class="nav-item">
          <a class="nav-link" href="#"><?php echo __('welcome'); ?> <?php echo $_SESSION['user_name']; ?></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo URLROOT; ?>/users/logout"><?php echo __('logout'); ?></a>
        </li>
        <?php else : ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo URLROOT; ?>/users/register"><?php echo __('register'); ?></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo URLROOT; ?>/users/login"><?php echo __('login'); ?></a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
