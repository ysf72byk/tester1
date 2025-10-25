<?php require APPROOT . '/app/views/inc/header.php'; ?>
<div class="row">
  <div class="col-md-6 mx-auto">
    <div class="card card-body bg-light mt-5">
      <h2><?php echo __('create_an_account'); ?></h2>
      <p><?php echo __('please_fill_out_this_form_to_register_with_us'); ?></p>
      <form action="<?php echo URLROOT; ?>/users/register" method="post">
        <div class="form-group">
          <label for="name"><?php echo __('name'); ?>: <sup>*</sup></label>
          <input type="text" name="name" class="form-control form-control-lg <?php echo (!empty($data['name_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['name']; ?>">
          <span class="invalid-feedback"><?php echo $data['name_err']; ?></span>
        </div>
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
        <div class="form-group">
          <label for="confirm_password"><?php echo __('confirm_password'); ?>: <sup>*</sup></label>
          <input type="password" name="confirm_password" class="form-control form-control-lg <?php echo (!empty($data['confirm_password_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['confirm_password']; ?>">
          <span class="invalid-feedback"><?php echo $data['confirm_password_err']; ?></span>
        </div>

        <div class="row">
          <div class="col">
            <input type="submit" value="<?php echo __('register'); ?>" class="btn btn-success btn-block">
          </div>
          <div class="col">
            <a href="<?php echo URLROOT; ?>/users/login" class="btn btn-light btn-block"><?php echo __('have_an_account_login'); ?></a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<?php require APPROOT . '/app/views/inc/footer.php'; ?>
