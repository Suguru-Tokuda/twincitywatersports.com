<h1><?= $headline ?></h1>
<?= validation_errors("<p style='color: red;'>", "</p>") ?>

<?php
if (isset($flash)) {
  echo $flash;
}
$form_location = base_url().'comments/submit';
$this->load->module('timedate');
$this->load->module('store_accounts');
foreach($query->result() as $row) {
  $view_url = base_url()."enquiries/view/".$row->id;
  $open = $row->opened;
  if ($open == 1) {
    $icon = '<i class="icon-envelope"></i>';
  } else {
    $icon = '<i class="icon-envelope-alt" style="color: orange;"></i>';
  }
  $date_sent = $this->timedate->get_date($row->date_created, 'full');
  if ($row->sent_by == 0) {
    $sent_by = "Admin";
  } else {
    $sent_by = $this->store_accounts->_get_customer_name($row->sent_by);
  }
  $subject = $row->subject;
  $message = $row->message;
  $ranking = $row->ranking;
  ?>
  <!-- <div class="row"> -->
  <p style="margin-top: 30px;">
    <a href="<?= base_url()."enquiries/create/".$update_id ?>"><button class="btn btn-primary" type="submit">Reply</button></a>
    <!-- <a href="<?= base_url()."enquiries/" ?>"><button class="btn btn-info" type="button">Create New Comment</button></a> -->
    <a href="#myModal" role="button" class="btn btn-info" data-toggle="modal">Create New Comment</a>
    <a href="<?= base_url()."enquiries/inbox" ?>"><button class="btn btn-primary" type="submit">Back to Inbox</button></a>

    <!-- Button to trigger modal -->


    <!-- Modal -->
    <div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Create New Comment</h3>
      </div>
      <div class="modal-body">

        <form class="form-horizontal" action="<?= $form_location ?>" method="post">
          <div class="control-group">
            <label class="control-label" for="inputComment">Comment</label>
            <div class="controls">
              <textarea row="6" name="comment"></textarea>
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
          <button type="submit" name="submit" value="submit" class="btn btn-primary">Save changes</button>
        </div>
        <?php
        echo form_hidden('comment_type', 'e');
        echo form_hidden('update_id', $update_id);
        ?>
      </form>
    </div>



  </p>
  <!-- <p style="margin-top: 30px;"> -->
  <div class="row-fluid sortable">
    <div class="box span12">
      <div class="box-header" data-original-title>
        <h2><i class="halflings-icon white star"></i><span class="break"></span>Enquiry Ranking</h2>
        <div class="box-icon">
          <!-- <a href="#" class="btn-setting"><i class="halflings-icon white wrench"></i></a> -->
          <a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
          <!-- <a href="#" class="btn-close"><i class="halflings-icon white remove"></i></a> -->
        </div>
      </div>
      <div class="box-content">
        <?php
        $form_location = base_url()."enquiries/submit_ranking/".$update_id;
        ?>
        <form class="form-horizontal" method="post" action="<?= $form_location ?>">
          <fieldset>

            <div class="control-group">
              <label class="control-label" for="typeahead">Ranking </label>
              <div class="controls">
                <?php
                if ($ranking > 0) {
                  unset($options['']);
                }
                echo form_dropdown('ranking', $options, $ranking);
                ?>
              </div>
            </div>

            <div class="form-actions">
              <button type="submit" class="btn btn-primary" name="submit" value="submit">Rate</button>
            </div>
          </fieldset>
        </form>
      </div>
    </div>
  </div>




  <!-- </p> -->
  <!-- </div> -->
  <div class="row-fluid sortable">
    <div class="box span12">
      <div class="box-header" data-original-title>
        <h2><i class="halflings-icon white edit"></i><span class="break"></span>Enquiry Details</h2>
        <div class="box-icon">
          <!-- <a href="#" class="btn-setting"><i class="halflings-icon white wrench"></i></a> -->
          <a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
          <!-- <a href="#" class="btn-close"><i class="halflings-icon white remove"></i></a> -->
        </div>
      </div>
      <div class="box-content">

        <table class="table table-striped table-bordered bootstrap-datatable">
          <tbody>
            <tr>
              <td style="font-weight: bold;">Date Sent</td><td><?= $date_sent?></td>
            </tr>
            <tr>
              <td style="font-weight: bold;">Sent by</td><td><?= $sent_by ?></td>
            </tr>
            <tr>
              <td style="font-weight: bold;">Subject</td><td><?= $subject ?></td>
            </tr>
            <tr>
              <td style="font-weight: bold; vertical-align: top;">Message</td>
              <td style="vertical-align: top;"><?= nl2br($message) ?></td>
            </tr>
            <?php
          }
          ?>
        </tbody>

      </table>

    </div>
  </div>

  <?php
  echo Modules::run('comments/_draw_comments', 'e', $update_id);
  ?>
