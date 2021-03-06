<div class="row">
  <div class="col-md-10 col-md-offset-1">
    <table class="table table-striped table-bordered" style="margin-top: 36px;">
      <?php
      $grand_total = 0;
      foreach ($query->result() as $row) {
        $sub_total = $row->price * $row->item_qty;
        $sub_total_desc = number_format($sub_total, 2);
        $grand_total += $sub_total;
        ?>
        <tr style="margin-top: 12px;">
          <td class="col-md-2">
            <?php
            if ($row->small_pic != '') {
              // echo $row->small_pic;

              ?>
              <img src="<?= base_url() ?>small_pics/<?= $row->small_pic ?>" title="<?= base_url() ?>small_pics/<?= $row->small_pic ?>">
              <?php
            } else {
              echo "No image preview available";
            }
            ?>
          </td>
          <td class="col-md-8">
            Item Number: <?= $row->item_id ?><br>
            <b><?= $row->item_title ?></b><br>
            Item Price: <?= $currency_symbol.$row->price ?><br><br>
            QUANTITY: <?= $row->item_qty ?><br><br>
            <?php
            echo anchor('store_basket/remove/'.$row->id, "Remove");
            ?>
          </td>
          <td class="col-md-2"><?= $currency_symbol.$sub_total_desc ?></td>
        </tr>
        <?php
      }
      ?>
      <tr>
        <tr style="margin-top: 12px;">
          <td class="col-md-2">
            &nbsp;
          </td>
          <td class="col-md-8">
            Shipping:
            <?php
            $grand_total = $grand_total + $shipping;
            ?>
          </td>
          <td class="col-md-2"><?= $currency_symbol.$shipping ?></td>
        </tr>
        <tr>
          <td colspan="2" style="font-weight: bold; text-align: right;">Total</td>
          <td style="font-weight: bold;"><?= $currency_symbol.number_format($grand_total, 2) ?></td>
        </tr>
      </table>
    </div>
  </div>
