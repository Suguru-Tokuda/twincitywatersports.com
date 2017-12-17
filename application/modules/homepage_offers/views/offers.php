<?php
$this->load->module('small_pics');
foreach ($query->result() as $row) {
  $item_id = $row->item_id;
  $picture_name = $this->small_pics->get_index_small_pic_name_id_by_item_id($item_id);
  $item_title = $row->item_title;
  // $small_pic = $row->small_pic;
  $item_price = number_format($row->item_price, 2);
  $item_price = str_replace('.00', '', $item_price);

  $was_price = $row->was_price;
  $small_pic_path = base_url()."small_pics/".$picture_name;
  $item_page = base_url()."$item_segments./$row->cat_url/$row->item_url";
  ?>
  <div class="col-md-3 col-sm-3 col-xs-6" >
    <div class="offer offer-<?= $theme ?>" style="min-height: 350px;">
      <div class="shape">
        <div class="shape-text">
          <span class="glyphicon glyphicon-star" aria-hidden="true" style="font-size: 1.4em;"></span>
        </div>
      </div>
      <div class="offer-content">
        <h3 class="lead">
          <?= $currency_symbol.$item_price ?>
        </h3>
        <?php
        if ($picture_name != "") {
          ?>
          <a href="<?= $item_page ?>" ><img src="<?= $small_pic_path ?>" title="<?= $row->item_title ?>" class="img-responsive"  ></a>
          <?php
        } else {
          ?>
          <p>No picture available</p>
          <?php
        }
        ?>
        <p>
          <a href="<?= $item_page ?>"><?= $item_title ?></a>
        </p>
      </div>
    </div>
  </div>
  <?php
}
?>
