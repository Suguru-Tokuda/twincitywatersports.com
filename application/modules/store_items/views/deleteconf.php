<h1><?= $headline ?></h1>

<?php
if (isset($flash)) {
  echo $flash;
}
?>
<!-- This section appears only there is an update_id -->
<div class="row-fluid sortable">
  <div class="box span12">
    <div class="box-header" data-original-title>
      <h2><i class="halflings-icon white edit"></i><span class="break"></span>Item Options</h2>
      <div class="box-icon">
        <a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
      </div>
    </div>
    <div class="box-content">

<h3>Are you sure that you want to delete the item?</h3>

<?php
// This line adds attributes into a form. No need to put <form...> manually
$attributes = array('class' => 'form-horizontal', 'id' => 'myform');
echo form_open_multipart('store_items/delete/'.$update_id, $attributes);
?>

<filedset>
  <div class="control-group" style="height: 200px;">
<button type="submit" name="submit" class="btn btn-danger" value="Delete">Yes - Delete Item</button>
<button type="submit" name="submit" value="Cancel" class="btn">Cancel</button>
</fieldset>
</form>


    </div>
  </div><!--/span-->
</div><!--/row-->
