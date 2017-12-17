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
      <table class="table table-striped table-bordered">
        <?php
        $this->load->module('timedate');
        foreach($query->result() as $row) {
          $date_created = $this->timedate->get_date($row->date_created, 'full');
          ?>
          <tr>
            <td><?php
            echo "<i>$date_created</i><br><br>";
            echo nl2br("$row->comment");
            ?>
          </td>
        </tr>
        <?php
      }
      ?>
    </table>

  </div>
</div>
</div>
