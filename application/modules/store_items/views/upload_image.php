<style>
#file {
  opacity: 0;
  width: 100%;
  height: 100%;
  position: absolute;
  cursor: pointer;
}
div.uploadBtn {
  width: 115px;
  height: 40px;
  background: url('https://lh6.googleusercontent.com/-dqTIJRTqEAQ/UJaofTQm3hI/AAAAAAAABHo/w7ruR1SOIsA/s157/upload.png');
  position: relative;
  background-size: 100%;
  cursor: pointer;
}

.imageUploadedOrNot {
  display: none;
}

img#blankImg {
  max-width: 20%;
}
</style>

<h1><?= $headline ?></h1>
<div class="row-fluid sortable">
  <div class="box span12">
    <div class="box-header" data-original-title>
      <h2><i class="halflings-icon white tag"></i><span class="break"></span>Upload Image</h2>
      <div class="box-icon">
        <a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
        <a href="#" class="btn-close"><i class="halflings-icon white remove"></i></a>
      </div>
    </div>

    <div class="box-content">
      <?php
      $update_id = $this->uri->segment(3);
      if (!isset($update_id)) {
        $update_id = "";
      }
      if (isset($flash)) {
        echo $flash;
      }
      if (isset($error)) {
        foreach ($error as $value) {
          echo $value;
        }
      }
      ?>
      <?php
      if ($num_rows < 10) { // shows if the item has only less than 10 pictures
        ?>
        <div class="imageUploadedOrNot">
          <h3>Here's the display of your image: </h3>
          <img src="#" id="blankImg">
        </div>
        <?php echo form_open_multipart('store_items/do_upload/'.$update_id);?>
        <div class="form-group">
          <label for="input">Image</label>
          <!-- <div class="uploadBtn"> -->
            <input type="file" name="userfile" id="file">
          <!-- </div> -->
        </div>
        <div class="form-actions">
          <button type="submit" name="submit" class="btn btn-primary" value="upload">Upload</button>
          <button type="submit" name="submit" class="btn" value="cancel">Cancel</button>
        </div>
      </form>

    </div>
  </div>
</div>
<?php
} else {
?>
<form action="<?= base_url().'store_items/create_item/'.$update_id ?>" method="post">
  <button class="btn btn-primary" type="submit" >To Manage Item Page</button>
</form>
<?php
}
?>

<?php
if ($num_rows > 0) {
?>
<h4>Uploaded Pictures</h4>
<p><?= $num_rows ?> pictures for the item (max 10 pictures) - <strong>You can drag and change the oder</strong></p>
<!-- pictures -->
<ul id="sortlist" class="list-group" style="margin-top: 30px; list-style: none;">
  <?php
  $this->load->module('store_categories');
  foreach($query->result() as $row) {
    $delete_image_url = base_url()."/store_items/delete_image/".$row->id;
    $picture_location = base_url()."/small_pics/".$row->picture_name;
    $view_item_url = base_url()."/store_categories/view/".$row->id;
    $priority = $row->priority;
    ?>
    <li class="sort list-group-item" style="height: 200px;" id="<?= $row->id?>">
      <img src="<?= $picture_location ?>" title="<?= $row->picture_name ?>" class="img-responsive"><br>
      <?php
      echo anchor(base_url().'store_items/delete_image/'.$update_id.'/'.$row->id, 'Remove');
      ?>
    </li>
    <?php
  }
  ?>
</ul>
<?php
}
?>
</div>
</div>
</div>


<script>
$(function() {
  $("#file").change(function() {
    var reader = new FileReader();
    reader.onload = function(image) {
      $('.imageUploadedOrNot').show(0);
      $('#blankImg').attr('src', image.target.result);
    }
    reader.readAsDataURL(this.files[0]); // this refers to $('#file')
  });
});
</script>
