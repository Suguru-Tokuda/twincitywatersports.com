<?php
$form_location = current_url();
?>

<div class="row">
  <div class="col-md-8">
    <h2><?= $headline ?></h2>
    <?= validation_errors("<p style='color: red;'>", "</p>") ?>
    <form action="<?= $form_location ?>" method="post" >
      <?php
      if ($code == "") {
        ?>
        <div class="form-group"style="margin-top: 24px;">
          <label for="subject">Subject</label>
          <input type="text" name="subject" value="<?= $subject ?>" class="form-control" id="subject" placeholder="Enter a subject here">
        </div>
        <?php
      } else {
        ?>
          <div class="form-group" style="margin-top: 24px;">
            <label>Original Subject</label>
            <p><?= $subject ?></p>
          </div>
        <?php
        echo form_hidden('subject', $subject);
      }
      ?>

      <div class="form-group">
        <label for="message">Password</label>
        <textarea name="message" class="form-control" rows="6" placeholder="Enter your message here"><?= $message ?></textarea>
      </div>

      <div class="checkbox">
        <label>
          <input type="checkbox" name="urgent" value="1"<?php
          if ($urgent == 1) {
            echo " checked";
          }
          ?>> Urgent
        </label>
      </div>
      <button type="submit" name="submit" value="submit" class="btn btn-primary">Send</button>
      <button type="submit" name="submit" value="cancel" class="btn btn-default">Cancel</button>
      <?php
      echo form_hidden('token', $token);
      ?>
    </form>

  </div>
</div>
