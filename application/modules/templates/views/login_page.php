<?php
$first_bit = $this->uri->segment(1);
$form_location = base_url().$first_bit.'/submit_login';
if ($first_bit == "youraccount") {
  $label = "Sign in";
} else {
  $label = "Admin Login";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
  <meta name="description" content="">
  <meta name="author" content="">
  <link rel="icon" href="<?php echo base_url(); ?>/favicon.ico">
  <title>Login</title>
  <link href="<?php echo base_url(); ?>dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?php echo base_url(); ?>css/ie10-viewport-bug-workaround.css" rel="stylesheet">
  <link href="<?php echo base_url(); ?>css/jumbotron.css" rel="stylesheet">
  <link href="<?php echo base_url(); ?>css/panel.css" rel="stylesheet">
</head>
<body>
  <div class="container">
    <div class="row">
      <div class="col-md-4 col-md-offset-4">
        <form class="form-signin" action="<?= $form_location ?>" method="post">
          <h2 class="form-signin-heading"><?= $label ?></h2>
          <label for="inputEmail" class="sr-only">Username or Email</label>
          <input type="text" id="inputEmail" name="userName" value"<?= $userName ?>" class="form-control" placeholder="Email address" required autofocus>
          <label for="inputPassword" class="sr-only">Password</label>
          <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" required>
          <div class="checkbox">
            <?php
            if ($first_bit == "youraccount") { ?>
              <label>
                <input type="checkbox" name="remember" value="remember-me"> Remember me
              </label>
              <?php
            }
            ?>
          </div>
          <button name="submit" value="submit" class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
        </form>
        <?php
        echo validation_errors("<p style='color: red;'>", "</p>");
        ?>
      </div>
    </div>
  </div> <!-- /container -->

  <!-- Bootstrap core JavaScript
  ================================================== -->
  <!-- Placed at the end of the document so the pages load faster -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="<?php echo base_url(); ?>assets/js/vendor/jquery.min.js"><\/script>')</script>
  <script src="<?php echo base_url(); ?>dist/js/bootstrap.min.js"></script>
  <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
  <script src="<?php echo base_url(); ?>js/ie10-viewport-bug-workaround.js"></script>
</body>
</html>
