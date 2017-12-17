<h1>Manage Items</h1>

<?php
if (isset($flash)) {
  echo $flash;
}
$create_item_url = base_url()."store_items/create";
?><p style="margin-top: 30px;">
  <a href="<?= $create_item_url ?>"><button class="btn btn-primary" type="submit">Add New Item</button></a>

  <div class="row-fluid sortable">
    <div class="box span12">
      <div class="box-header" data-original-title>
        <h2><i class="halflings-icon white tag"></i><span class="break"></span>Items Inventory</h2>
        <div class="box-icon">
          <a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
          <a href="#" class="btn-close"><i class="halflings-icon white remove"></i></a>
        </div>
      </div>
      <div class="box-content">
        <table class="table table-striped table-bordered bootstrap-datatable datatable">
          <thead>
            <tr>
              <th>Item Title</th>
              <th>User ID</th>
              <th>Price</th>
              <th>Was Price</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach($query->result() as $row) {
              $edit_item_url = base_url()."store_items/create/".$row->id;
              $view_item_url = base_url()."store_items/view/".$row->id;
              if ($row->user_id == 0) {
                $userName = "Admin";
              } else {
                $userName = $row->userName;
              }
              $status = $row->status;
              $status_string = "";
              if ($status == 1) {
                $status_label = "success";
                $status_desc = "Active";
              } else {
                $status_label = "default";
                $status_desc = "Inactive";
              }
              ?>
              <tr>
                <td><?= $row->item_title ?></td>
                <td><?= $userName ?></td>
                <td class="center"><?= $row->item_price ?></td>
                <td class="center"><?= $row->was_price ?></td>
                <td>
                  <span class="label label-<?= $status_label ?>"><?= $status_desc ?></span>
                </td>
                <td class="center">
                  <a class="btn btn-success" href="<?= $view_item_url ?>">
                    <i class="halflings-icon white zoom-in"></i>
                  </a>
                  <a class="btn btn-info" href="<?= $edit_item_url ?>">
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
