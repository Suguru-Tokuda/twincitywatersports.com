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
        <h2><i class="halflings-icon white edit"></i><span class="break"></span>Item Options</h2>
        <div class="box-icon">
          <a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
        </div>
      </div>
      <?php
      // Check if the item has an image. Upload image icon appears only there is NO image.
      if ($update_id != "") {
        ?>
        <div class="box-content">
          <a href="<?= base_url() ?>store_items/upload_image/<?= $update_id ?>" ><button type="button" class="btn btn-primary">Manage Images</button></a>
          <a href="<?= base_url() ?>store_items/deleteconf/<?= $update_id ?>" ><button type="button" class="btn btn-danger">Delete Item</button></a>
          <a href="<?= base_url() ?>store_items/view/<?= $update_id?>" ><button type="button" class="btn btn-default">View Item In Shop</button></a>
        </div>
        <?php
      }
      ?>
    </div><!--/span-->
  </div><!--/row-->
  <?php
}
?>
<div class="row-fluid sortable">
  <div class="box span12">
    <div class="box-header" data-original-title>
      <h2><i class="halflings-icon white edit"></i><span class="break"></span>Item Details</h2>
      <div class="box-icon">
        <!-- <a href="#" class="btn-setting"><i class="halflings-icon white wrench"></i></a> -->
        <a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
        <!-- <a href="#" class="btn-close"><i class="halflings-icon white remove"></i></a> -->
      </div>
    </div>
    <div class="box-content">
      <?php
      $form_location = base_url()."store_items/create/".$update_id;
      ?>
      <form class="form-horizontal" method="post" action="<?= $form_location ?>">
        <fieldset>
          <div class="control-group">
            <label class="control-label" for="typeahead">Item Title </label>
            <div class="controls">
              <input type="text" class="span6 typeahead" name="item_title" value="<?= $item_title ?>">
            </div>
          </div>

          <div class="control-group">
            <label class="control-label" for="typeahead">Item Price </label>
            <div class="controls">
              <input type="text" class="span6 typeahead" name="item_price" value="<?= $item_price ?>">
            </div>
          </div>

          <div class="control-group">
            <label class="control-label" for="typeahead">Was Price <span style="color: green;">(optional) </label>
              <div class="controls">
                <input type="text" class="span6 typeahead" name="was_price" value="<?= $was_price ?>">
              </div>
            </div>

            <!-- categories - dropdown or checkbox -->
            <div class="control-group">
              <label class="col-md-3 control-label" for="textinput">Categories</label>
              <div class="controls" style="margin-top: 8px;">
                <?php
                $this->load->module('store_categories');
                foreach($categories_options as $key => $value) {
                  $this->load->module('listed_items');
                  $cat_title = $this->store_categories->_get_cat_title_by_id($value);
                  if ($update_id != "") {
                    $checked = $this->listed_items->_check_for_category($update_id, $value);
                  } else {
                    $checked = "";
                  }
                  ?>
                  <input type="checkbox" name="categories[]" value="<?= $value ?>" <?= $checked ?>> <?= ucwords($cat_title) ?>
                  <?php
                }
                ?>
              </div>
            </div>

            <div class="control-group">
              <label class="control-label" for="typeahead">Satus </label>
              <div class="controls">
                <?php
                if (!isset($status)) {
                  $status = '';
                }
                $additional_dd_code = 'id="status"';
                $options = array(
                  '' => 'Please select...',
                  '1' => 'Active',
                  '0' => 'Inactive',
                );
                echo form_dropdown('status', $options, $status, $additional_dd_code);
                ?>
              </div>
            </div>

            <div class="control-group" >
              <label class="control-label" for="textinput">City (Location)</label>
              <div class="controls">
                <input id="textinput" name="city" value="<?= $city ?>" type="text" placeholder="Enter city" class="form-control input-md">
              </div>
            </div>

            <div class="control-group">
              <label class="control-label" for="textinput">State</label>
              <div class="controls">
                <?php
                $state_key = array_search($state, $states);
                $selection = $state;
                echo form_dropdown('state', $states, $state_key, 'class="form-control"');
                ?>
              </div>
            </div>


            <div class="control-group hidden-phone">
              <label class="control-label" for="textarea2">Item Description</label>
              <div class="controls">
                <textarea type="text" class="cleditor" id="textarea2" rows="3" name="item_description" >
                  <?php
                  echo $item_description;
                  ?>
                </textarea>
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

  </div><!--/row-->
