<h1>Manage Homepage Offers</h1>
<?php
if (isset($flash)) {
  echo $flash;
}
$create_item_url = base_url()."homepage_blocks/create";
$categories_manage_url = base_url()."homepage_blocks/manage";
$this->uri->segment(3);
?>
<p style="margin-top: 30px;">
  <a href="<?= $create_item_url ?>"><button class="btn btn-primary" type="submit">Create New Homepage Offer Block</button></a>
    <a href="<?= $categories_manage_url ?>"><button class="btn btn-primary" type="submit">Homepage Offers Top</button></a>
  <div class="row-fluid sortable">
    <div class="box span12">
      <div class="box-header" data-original-title>
        <h2><i class="halflings-icon white align-justify"></i><span class="break"></span>Exisiting Homepage Offers</h2>
        <div class="box-icon">
          <a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
          <a href="#" class="btn-close"><i class="halflings-icon white remove"></i></a>
        </div>
      </div>
      <div class="box-content">
        <?php
        echo Modules::run('homepage_blocks/_draw_sortable_list');
         ?>
      </div>
    </div>
  </div>
