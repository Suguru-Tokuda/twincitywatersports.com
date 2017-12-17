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
      <h2><i class="halflings-icon white edit"></i><span class="break"></span>Blog Entry Details</h2>
      <div class="box-icon">
        <!-- <a href="#" class="btn-setting"><i class="halflings-icon white wrench"></i></a> -->
        <a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
        <!-- <a href="#" class="btn-close"><i class="halflings-icon white remove"></i></a> -->
      </div>
    </div>
    <div class="box-content">

      <?php
      $form_location = base_url()."blog/create/".$update_id;
      ?>
      <form class="form-horizontal" method="post" action="<?= $form_location ?>">
        <fieldset>
          <div class="control-group">
            <label class="control-label" for="typeahead">Date published </label>
            <div class="controls">
              <input type="text" class="input-xlarge datepicker" name="date_published" value="<?= $date_published ?>" >
            </div>
          </div>

          <div class="control-group">
            <label class="control-label" for="typeahead">Blog Entry Title </label>
            <div class="controls">
              <input type="text" class="span6 typeahead" name="blog_title" value="<?= $blog_title ?>">
            </div>
          </div>

          <div class="control-group hidden_phone">
            <label class="control-label" for="textarea">Blog Entry Keywords </label>
            <div class="controls">
              <textarea rows="3" class="span6" name="blog_keywords"><?php
              echo $blog_keywords;
              ?></textarea>
            </div>
          </div>

          <div class="control-group hidden_phone">
            <label class="control-label" for="textarea2">Blog Entry Description</label>
            <div class="controls">
              <textarea class="span6" id="textarea2" rows="3" name="blog_description"><?php
              echo $blog_description;
              ?></textarea>
            </div>
          </div>

          <div class="control-group hidden_phone">
            <label class="control-label" for="textarea3">Blog Entry Content</label>
            <div class="controls">
              <textarea class="cleditor" id="textarea3" rows="3" name="blog_content"><?php
              echo $blog_content;
              ?></textarea>
            </div>
          </div>

          <div class="control-group">
            <label class="control-label" for="typeahead">Author </label>
            <div class="controls">
              <input type="text" class="span6 typeahead" name="author" value="<?= $author ?>">
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
<?php
// This section appears only there is an update_id
if (is_numeric($update_id)) { ?>
  <div class="row-fluid sortable">
    <div class="box span12">
      <div class="box-header" data-original-title>
        <h2><i class="halflings-icon white edit"></i><span class="break"></span>Blog Options</h2>
        <div class="box-icon">
          <a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
        </div>
      </div>
      <div class="box-content">
        <?php
        // Check if the item has an image. Upload image icon appears only there is NO image.
        if ($picture == "") {
          ?>
          <a href="<?= base_url() ?>blog/upload_image/<?= $update_id ?>" ><button type="button" class="btn btn-primary">Upload Image</button></a>
          <?php
        } else {
          ?>
          <a href="<?= base_url() ?>blog/delete_image/<?= $update_id ?>" ><button type="button" class="btn btn-danger">Delete Image</button></a>
          <?php
        }
        if ($update_id > 2) { ?>

          <a href="<?= base_url() ?>blog/deleteconf/<?= $update_id ?>" ><button type="button" class="btn btn-danger">Delete Blog Entry</button></a>
          <?php
        }
        ?>
        <a href="<?= base_url().$blog_url ?>" ><button type="button" class="btn btn-default">View Blog Entry</button></a>

      </div>
    </div><!--/span-->
  </div><!--/row-->
  <?php
}
if ($picture != "") {
  ?>
  <div class="row-fluid sortable">
    <div class="box span12">
      <div class="box-header" data-original-title>
        <h2><i class="halflings-icon white edit"></i><span class="break"></span>Blog Image</h2>
        <div class="box-icon">
          <a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
        </div>
      </div>
      <div class="box-content">
        <img src="<?= base_url() ?>blog_pics/<?= $picture ?>">
      </div>
    </div><!--/span-->
  </div><!--/row-->
  <?php
}
?>
