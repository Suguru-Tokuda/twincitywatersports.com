<h1><?= $headline ?></h1>
<?= validation_errors("<p style='color: red;'>", "</p>") ?>

<?php
if (isset($flash)) {
  echo $flash;
}
// This section appears only there is an update_id
if (is_numeric($update_id)) { ?>
  <div class="row-fluid sortable">
    <div class="box span12">
      <div class="box-header" data-original-title>
        <h2><i class="halflings-icon white edit"></i><span class="break"></span>Account Options</h2>
        <div class="box-icon">
          <a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
        </div>
      </div>
      <div class="box-content">
        <a href="<?= base_url() ?>store_accounts/update_password/<?= $update_id ?>" ><button type="button" class="btn btn-primary">Update Password</button></a>
        <a href="<?= base_url() ?>store_accounts/deleteconf/<?= $update_id ?>" ><button type="button" class="btn btn-danger">Delete Account</button></a>
      </div>
    </div><!--/span-->
  </div><!--/row-->
  <?php
}
?>
<div class="row-fluid sortable">
  <div class="box span12">
    <div class="box-header" data-original-title>
      <h2><i class="halflings-icon white edit"></i><span class="break"></span>Account Details</h2>
      <div class="box-icon">
        <!-- <a href="#" class="btn-setting"><i class="halflings-icon white wrench"></i></a> -->
        <a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
        <!-- <a href="#" class="btn-close"><i class="halflings-icon white remove"></i></a> -->
      </div>
    </div>
    <div class="box-content">
      <?php
      $form_location = base_url()."store_accounts/create/".$update_id
      ?>
      <form class="form-horizontal" method="post" action="<?= $form_location ?>">
        <fieldset>
          <div class="control-group">
            <label class="control-label" for="typeahead">Username</label>
            <div class="controls">
              <input type="text" class="span6 typeahead" name="userName" value="<?= $userName?>">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="typeahead">First Name</label>
            <div class="controls">
              <input type="text" class="span6 typeahead" name="firstName" value="<?= $firstName?>">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="typeahead">Last Name</label>
            <div class="controls">
              <input type="text" class="span6 typeahead" name="lastName" value="<?= $lastName?>">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="typeahead">Company</label>
            <div class="controls">
              <input type="text" class="span6 typeahead" name="company" value="<?= $company?>">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="typeahead">Address 1</label>
            <div class="controls">
              <input type="text" class="span6 typeahead" name="address1" value="<?= $address1?>">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="typeahead">Address 2</label>
            <div class="controls">
              <input type="text" class="span6 typeahead" name="address2" value="<?= $address2?>">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="typeahead">City</label>
            <div class="controls">
              <input type="text" class="span6 typeahead" name="city" value="<?= $city?>">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="typeahead">State</label>
            <div class="controls">
              <input type="text" class="span6 typeahead" name="state" value="<?= $state?>">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="typeahead">Zip</label>
            <div class="controls">
              <input type="text" class="span6 typeahead" name="zip" value="<?= $zip?>">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="typeahead">Phone</label>
            <div class="controls">
              <input type="text" class="span6 typeahead" name="phone" value="<?= $phone?>">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="typeahead">Email</label>
            <div class="controls">
              <input type="text" class="span6 typeahead" name="email" value="<?= $email?>">
            </div>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn btn-primary" name="submit" value="Submit">Submit</button>
            <button type="submit" class="btn" name="submit" value="Cancel">Cancel</button>
          </div>
        </fieldset>
      </form>

    </div>
  </div><!--/span-->
