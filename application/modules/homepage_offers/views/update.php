<h1><?= $headline ?></h1>
<?= validation_errors("<p style='color: red;'>", "</p>") ?>
<?php
if (isset($flash)) {
  echo $flash;
}
?>
<div class="row-fluid sortable">
  <div class="box span12">
    <div class="box-header" data-original-title>
      <h2><i class="halflings-icon white edit"></i><span class="break"></span>New Offer Option for <?= $block_title ?></h2>
      <div class="box-icon">
        <!-- <a href="#" class="btn-setting"><i class="halflings-icon white wrench"></i></a> -->
        <a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
        <!-- <a href="#" class="btn-close"><i class="halflings-icon white remove"></i></a> -->
      </div>
    </div>
    <div class="box-content">
      <p>Submit an Item. When you are finished adding new offers, press "Finished".</p>
      <?php
      $form_location = base_url()."homepage_offers/submit/".$update_id;
      ?>
      <form class="form-horizontal" method="post" action="<?= $form_location ?>">
        <fieldset>
          <div class="control-group">
            <label class="control-label" for="typeahead">New Offer </label>
            <div class="controls">
              <!-- <input type="text" class="span6" name="item_id" placeholder="Enter an item ID here"> -->
              <div class="control-group">
                <div class="controls">
                  <?php
                  echo form_dropdown('item_id', $options, $update_id);
                  ?>
                </div>
                </div>
            </div>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn btn-primary" name="submit" value="Submit">Submit</button>
            <button type="submit" class="btn" name="submit" value="Finished">Finished</button>
          </div>
        </fieldset>
      </form>

    </div>
  </div><!--/span-->
</div><!--/row-->
<!-- -->
<?php
if ($num_rows > 0) {
  ?>
  <div class="row-fluid sortable">
    <div class="box span12">
      <div class="box-header" data-original-title>
        <h2><i class="halflings-icon white edit"></i><span class="break"></span>Existing Offers</h2>
        <div class="box-icon">
          <a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
          <a href="#" class="btn-close"><i class="halflings-icon white remove"></i></a>
        </div>
      </div>
      <div class="box-content">
        <table class="table table-striped table-bordered bootstrap-datatable datatable">
          <thead>
            <tr>
              <th>Count</th>
              <th>Item</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $count = 0;
            $this->load->module('store_items');
            foreach ($query->result() as $row) {
              $count++;
              $delete_url = base_url()."homepage_offers/delete/".$row->id;
              $item_title = $this->store_items->get_item_title_by_id($row->item_id);
              ?>
              <tr>
                <td><?= $count ?></td>
                <td class="center"><?= $item_title ?></td>
                <td class="center">
                  <a class="btn btn-danger" href="<?= $delete_url ?>">
                    <i class="halflings-icon white trash"></i>Remove Item
                  </a>
                </td>
              </tr>
              <?php
            }
            ?>
            <tbody>
            </table>
          </div>
        </div>
      </div>
      <?php
    }
    ?>
