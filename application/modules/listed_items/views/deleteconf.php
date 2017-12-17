<h2><?= $headline ?></h2>

<?php
if (isset($flash)) {
  echo $flash;
}
?>
<!-- This section appears only there is an item_id -->
<div class="row-fluid sortable">
  <div class="box span12">
    <div class="box-header" data-original-title>
      <!-- <h2><i class="halflings-icon white edit"></i><span class="break"></span>Item Options</h2> -->
      <div class="box-icon">
        <a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
      </div>
    </div>
    <div class="box-content">

<h4>Are you sure that you want to delete the item?</h4>

<?php
// This line adds attributes into a form. No need to put <form...> manually
$attributes = array('class' => 'form-horizontal', 'id' => 'myform');
echo form_open_multipart('listed_items/delete/'.$item_url, $attributes);
?>

<filedset>
  <div class="control-group" style="height: 200px;">
<button type="submit" name="submit" class="btn btn-danger" value="delete">Yes - Delete Item</button>
<button type="submit" name="submit" value="cancel" class="btn">Cancel</button>
</fieldset>
</form>


    </div>
  </div><!--/span-->
</div><!--/row-->
