<?php
$form_location = base_url().'youraccount/submit';
?>
<h1>Create Account</h1>
<?php
echo validation_errors("<p style='color: red;'>", "</p>");
?>
<form class="form-horizontal" action="<?= $form_location ?>" method="post">
  <fieldset>

    <!-- Form Name -->
    <legend>Please submit your details using the form below</legend>

    <!-- Text input-->
    <div class="form-group">
      <label class="col-md-4 control-label" for="textinput">Username</label>
      <div class="col-md-4">
        <input id="textinput" name="userName" value="<?= $userName ?>" type="text" placeholder="Enter your username of choice here" class="form-control input-md" required="">
      </div>
    </div>

    <!-- Text input-->
    <div class="form-group">
      <label class="col-md-4 control-label" for="textinput">E-mail</label>
      <div class="col-md-4">
        <input id="textinput" name="email" value="<?= $email ?>" type="text" placeholder="Enter your contact email address" class="form-control input-md">
      </div>
    </div>

    <!-- Text input-->
    <div class="form-group">
      <label class="col-md-4 control-label" for="textinput">Password</label>
      <div class="col-md-4">
        <input id="textinput" name="password" value="<?= $password ?>" type="password" placeholder="Enter your password of your choice" class="form-control input-md">
      </div>
    </div>

    <!-- Text input-->
    <div class="form-group">
      <label class="col-md-4 control-label" for="textinput">Confirm Password</label>
      <div class="col-md-4">
        <input id="textinput" name="confirmPassword" value="<?= $confirmPassword ?>" type="password" placeholder="Please repeat your password here" class="form-control input-md">
      </div>
    </div>

    <!-- Button -->
    <div class="form-group">
      <label class="col-md-4 control-label" for="singlebutton">Create user?</label>
      <div class="col-md-4">
        <button id="singlebutton" name="submit" value="submit" class="btn btn-primary">Yes</button>
      </div>
    </div>

  </fieldset>
</form>
