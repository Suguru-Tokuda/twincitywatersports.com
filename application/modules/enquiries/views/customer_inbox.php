<h2>Your <?= $folder_type ?></h2>
<?php
if (isset($flash)) {
  echo $flash;
}
$create_message_url = base_url()."yourmessages/create";
?>
<p style="margin-top: 30px;">
  <a href="<?= $create_message_url ?>"><button class="btn btn-primary" type="submit">Compose Message</button></a>
</p>

<?php
$this->load->module('enquiries');
$num_row = $this->enquiries->count_where('sent_to', $customer_id);
if ($num_row > 0) {
  ?>


  <table class="table table-striped table-bordered bootstrap-datatable datatable">
    <thead>
      <tr style="background-color: #666; color: white;">
        <th>&nbsp;</th>
        <th>Date Sent</th>
        <th>Sent By</th>
        <th>Subject</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $this->load->module('site_settings');
      $this->load->module('store_accounts');
      $this->load->module('timedate');
      $team_name = $this->site_settings->_get_support_team_name();

      foreach($query->result() as $row) {

        $view_url = base_url()."yourmessages/view/".$row->code;

        $customer_data['firstName'] = $row->firstName;
        $customer_data['lastName'] = $row->lastName;
        $customer_data['company'] = $row->company;
        $open = $row->opened;

        if ($open == 1) {
          $icon = '<span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>';
        } else {
          $icon = '<span style="color: orange;" class="glyphicon glyphicon-envelope" aria-hidden="true"></span>';
        }
        $date_sent = $this->timedate->get_date($row->date_created, 'datepicker_us');
        if ($row->sent_by == 0) {
          $sent_by = $team_name;
        } else {
          $sent_by = $this->store_accounts->_get_customer_name($row->sent_by);
          // $sent_by = $firstName." ".$lastName;
        }
        ?>
        <tr>
          <td class="span1" width="20"><?= $icon ?></td>
          <td width="100"><?= $date_sent ?></td>
          <td width="200"><?= $sent_by ?></td>
          <td><?= $row->subject ?></td>
          <td class="span1" width="20">
            <a class="btn btn-default" href="<?= $view_url ?>">
              <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> View
            </a>
          </td>
        </tr>
        <?php
      }
      ?>
    </tbody>
  </table>

  <?php
} else {
  echo "<h3>You have no messages</h3>";
}
?>
