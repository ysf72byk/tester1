<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="row">
  <div class="col-md-6 mx-auto">
    <div class="card card-body bg-light mt-5">
      <h2><?php echo __('login'); ?></h2>
      <p><?php echo __('please_fill_in_your_credentials_to_log_in'); ?></p>
      <form action="<?php echo URLROOT; ?>/users/login" method="post">
        <div class="form-group">
          <label for="email"><?php echo __('email'); ?>: <sup>*</sup></label>
          <input type="email" name="email" class="form-control form-control-lg <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['email']; ?>">
          <span class="invalid-feedback"><?php echo $data['email_err']; ?></span>
        </div>
        <div class="form-group">
          <label for="password"><?php echo __('password'); ?>: <sup>*</sup></label>
          <input type="password" name="password" class="form-control form-control-lg <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['password']; ?>">
          <span class="invalid-feedback"><?php echo $data['password_err']; ?></span>
        </div>
        <div class="row">
          <div class="col">
            <input type="submit" value="<?php echo __('login'); ?>" class="btn btn-success btn-block">
          </div>
          <div class="col">
            <a href="<?php echo URLROOT; ?>/users/register" class="btn btn-light btn-block"><?php echo __('no_account_register'); ?></a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
