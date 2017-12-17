<?php
if (isset($flash)) {
  echo $flash;
}
$create_item_url = base_url()."listed_items/create_item";
?>
<h2>Your Listed Items</h2>
<a href="<?= $create_item_url ?>"><button class="btn btn-primary" type="submit">Add New Item</button></a>
<?php
$num_rows = $query->num_rows();
echo $pagination;
if ($num_rows > 0) {
  ?>
  <?php
  if ($num_rows == 1) {
    ?>
    <p style="margin-top: 34px;">You have <?= $num_rows ?> listed item.</p>
    <?php
  } else if ($num_rows > 1) {
    ?>
    <p style="margin-top: 34px;">You have <?= $num_rows ?> listed items.</p>
    <?php
  }
  ?>
  <div class="row-fluid sortable">
    <div class="box span12">
      <div class="box-header" data-original-title>
        <table class="table table-hover table-condensed table-responsive table-striped table-bordered bootstrap-datatable datatable">
          <thead>
            <tr>
              <th style="text-align: center;">ID</th>
              <th style="text-align: center;">Picture</th>
                <th style="text-align: center;">Item Title</th>
                <th style="text-align: center;">Listed Price</th>
                <th style="width: 15px;">Status</th>
                <th style="text-align: center;">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $this->load->module('store_items');
              foreach($query->result() as $row) {
                $item_url = $row->item_url;
                $edit_item_url = base_url()."listed_items/create_item/".$item_url;
                $picture_name = $this->store_items->_get_small_pic_by_item_url($item_url);
                $image_location = base_url()."small_pics/".$picture_name;
                $cat_url = $this->store_items->_get_cat_url_by_item_url($item_url);
                if (isset($cat_url)) {
                  $view_item_url = base_url()."$item_segments./$cat_url/$row->item_url";
                }
                $id = $row->id;
                $item_title = $row->item_title;
                $item_price = $currency_symbol.$row->item_price;
                $status = $row->status;
                if ($status == 1) {
                  $status_label = "success";
                  $status_desc = "Active";
                } else {
                  $status_label = "default";
                  $status_desc = "Inactive";
                }

              ?>
              <tr>
                <td style="text-align: center; width: 50px; padding: 50px 0;"><?= $id ?></td>
                <td style="text-align: center; width: 50px;"><?php
                if ($picture_name != "") {
                 ?>
                  <img src="<?= $image_location ?>" title="<?= $picture_name ?>" style="text-align: center;" class="img-responsive" >
                  <?php
                } else {
                   ?>
                   <p style="text-align: center; padding: 30px 0;">Not Available</p>
                   <?php
                 }
                    ?>
                </td>
                <td style="text-align: center; padding: 50px 0;"><?= $item_title ?></td>
                <td class="center" style="text-align: center; padding: 50px 0;"><?= $item_price ?></td>
                <td style="text-align: center; padding: 50px 0;">
                  <span class="label label-<?= $status_label ?>"><?= $status_desc ?></span>
                </td>
                <td class="span1" width="15%;" style="text-align: center; padding: 50px 0;">
                  <?php
                  if (isset($cat_url)) {
                    ?>
                    <a class="btn btn-default" href="<?= $view_item_url ?>">
                      <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> View
                    </a>
                    <?php
                  }
                  ?>

                  <a class="btn btn-default" href="<?= $edit_item_url ?>">
                    <span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Edit
                  </a>
                  <i class="halflings-icon white edit"></i>
                </a>
              </td>
            </tr>
            <?php
          }
             ?>

          </tbody>
        </table>
      </div>
    </div>
  </div>
  <?php
} else {
  ?>
  <p style="margin-top: 34px;">You have no items listed</p>
  <?php
}
?>
