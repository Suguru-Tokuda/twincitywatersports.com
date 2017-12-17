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

  <title><?php
  if (isset($item_title)) {
    echo $item_title;
  } else {
    echo "Twin Cities Cable Park";
  }
  ?></title>
  <!-- Bootstrap core CSS -->
  <link href="<?php echo base_url(); ?>dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
  <link href="<?php echo base_url(); ?>css/ie10-viewport-bug-workaround.css" rel="stylesheet">
  <!-- Custom styles for this template -->
  <link href="<?php echo base_url(); ?>css/jumbotron.css" rel="stylesheet">
  <link href="<?php echo base_url(); ?>css/panel.css" rel="stylesheet">
  <link href="<?php echo base_url(); ?>css/custom.css" rel="stylesheet">
  <link href="<?php echo base_url(); ?>css/Footer-with-button-logo.css" rel="stylesheet">
  <link href="<?php echo base_url(); ?>css/font-awesome-4.7.0/css/font-awesome.css" rel="stylesheet">
  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>

</head>

<body>
  <?php
  // isseet = determines if the value is NOT NULL
  if (isset($sort_this)) {
    // Whenever there is a change in sorting, this gets kicked on.
    require_once('sort_pictures_code.php');
  }
  ?>
  <nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="<?= base_url() ?>">Home</a>
      </div>
      <div id="navbar" class="navbar-collapse collapse">
        <?php
        echo Modules::run('store_categories/_draw_top_nav');
        $form_location = base_url().'store_items/search';
        ?>
        <div class="col-sm-3 col-md-3">
          <form class="navbar-form" role="search" action="<?= $form_location ?>" method="post">
            <div class="input-group">
              <input type="text" class="form-control" placeholder="Search" name="searchKeywords" >
              <div class="input-group-btn">
                <button class="btn btn-default" name="submit" type="submit" value="submit"><i class="glyphicon glyphicon-search"></i></button>
              </div>
            </div>
          </form>
        </div>
        <?php
        $signup_url = base_url()."youraccount/start";
        $login_url = base_url()."youraccount/login";
        $this->load->module('site_security');
        $user_id = $this->site_security->_get_user_id();
        if ($user_id == "") {
          ?>
          <ul class="nav navbar-nav navbar-right">
            <li><a href="<?= $signup_url ?>"><span class="glyphicon glyphicon-user"></span> Sign Up</a></li>
            <li><a href="<?= $login_url ?>"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
          </ul>
          <?php
        } else if ($user_id > 0) {
          include('customer_dropdown.php');
        }
        ?>
      </div>
    </div>
  </nav><?php
  if (isset($page_content)) {
    if ($page_url == "") { // means it's in homepage
      require_once('carousel.php');
    }
  }
  ?><div class="container">
    <div class="container" style="min-height: 650px;"><?php
    if (isset($page_content)) {
      // echo ($page_content);
      if (!isset($page_url)) {
        $page_url = 'homepage';
      }
      if ($page_url == "") {
        // this lines loads 'content_homepage.php'
        require_once('content_homepage.php');
      } elseif ($page_url == "contactus") {
        // load up a contact form
        echo Modules::run('contact/_drow_form');
      }
    } else if (isset($view_file)) {
      $this->load->view($view_module.'/'.$view_file);
    }
    ?>
  </div>
</div>

<footer id="myFooter" style="position: relative;">
  <div class="container">
    <div class="row">
      <div class="col-sm-3">
        <h2 class="logo"><a href="#"> LOGO </a></h2>
      </div>
      <div class="col-sm-2">
        <h5>Navigation</h5>
        <ul>
          <li><a href="<?= base_url() ?>">Home</a></li>
          <?php
          if ($user_id == 0) {
            ?>
            <li><a href="<?= base_url()?>youraccount/start">Sign up</a></li>
            <?php
          }
          ?>
        </ul>
      </div>
      <div class="col-sm-2">
        <h5>About us</h5>
        <ul>
          <li><a href="<?= base_url()."aboutus" ?>">Company Information</a></li>
          <li><a href="<?= base_url()."contactus" ?>">Contact us</a></li>
        </ul>
      </div>
      <div class="col-sm-3">
        <div class="social-networks">
          <a href="#" class="twitter"><i class="fa fa-twitter"></i></a>
          <a href="#" class="facebook"><i class="fa fa-facebook"></i></a>
          <a href="#" class="google"><i class="fa fa-google-plus"></i></a>
        </div>
        <!-- <button type="button" class="btn btn-default">Contact us</button> -->
      </div>
    </div>
  </div>
  <div class="footer-copyright">
    <p>© <?= $our_company ?> </p>
  </div>
</footer>



<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> -->
<script>window.jQuery || document.write('<script src="<?php echo base_url(); ?>assets/js/vendor/jquery.min.js"><\/script>')</script>
<script src="<?php echo base_url(); ?>dist/js/bootstrap.min.js"></script>
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="<?php echo base_url(); ?>js/ie10-viewport-bug-workaround.js"></script>
</body>
</html>
