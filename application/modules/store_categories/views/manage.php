<h1>Manage Categories</h1>
<?php
if (isset($flash)) {
  echo $flash;
}
$create_item_url = base_url()."store_categories/create";
$categories_manage_url = base_url()."store_categories/manage";
$this->uri->segment(3);
?>
<p style="margin-top: 30px;">
  <a href="<?= $create_item_url ?>"><button class="btn btn-primary" type="submit">Add New Category</button></a>
  <?php
  if ($parent_cat_id > 0) {
    ?>
    <a href="<?= $categories_manage_url ?>"><button class="btn btn-primary" type="submit">Categories Top</button></a>
    <?php
  }
  ?>
  <div class="row-fluid sortable">
    <div class="box span12">
      <div class="box-header" data-original-title>
        <h2><i class="halflings-icon white align-justify"></i><span class="break"></span>Exisiting Categories</h2>
        <div class="box-icon">
          <a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
          <a href="#" class="btn-close"><i class="halflings-icon white remove"></i></a>
        </div>
      </div>

      <div class="box-content">
        <?php
        echo Modules::run('store_categories/_draw_sortable_list', $parent_cat_id);
         ?>
      </div>
    </div>
  </div>
