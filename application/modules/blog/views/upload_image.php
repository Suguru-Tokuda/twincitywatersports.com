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
      if (isset($error)) {
        foreach ($error as $value) {
          echo $value;
        }
      }
      ?>
      <?php
      // This line adds attributes into a form. No need to put <form...> manually
      $attributes = array('class' => 'form-horizontal', 'id' => 'myform');
      echo form_open_multipart('blog/do_upload/'.$update_id, $attributes);
      ?>
      <!-- <form class="form-horizontal"> -->
      <p style="margin-top: 24px;">Please choose a file from your computer and then press "Upload".</p>
      <fieldset>
        <div class="control-group" style="height: 200px;">
          <label class="control-label" for="fileInput">File input</label>
          <div class="controls">
            <input type="file" class="input-file uniform_on" name="userfile" id="fileInput">
          </div>
        </div>
        <div class="form-actions">
          <button type="submit" name="submit" class="btn btn-primary" value="Upload">Upload</button>
          <button type="submit" name="submit" class="btn" value="Cancel">Cancel</button>
        </div>
      </fieldset>
    </form>



  </div>
</div><!--/span-->
</div><!--/row-->
