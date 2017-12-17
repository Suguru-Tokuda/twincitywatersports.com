<?php
echo Modules::run('templates/_draw_breadcrumbs', $breadcrumbs_data);
if (isset($flash)) {
  echo $flash;
}


?>
<div class="row">
  <div class="col-md-4" style="margin-top: 24px;">
    <?php
    if ($pics_query->num_rows() == 1) {
      foreach ($pics_query->result() as $row) {
        $picture_name = $row->picture_name;
        $picture_location = $picture_location = base_url()."/big_pics/".$picture_name;
      }
      ?>
      <img class="d-block img-fluid" src="<?= $picture_location ?>" title="<?= $picture_name ?>" width="100%">
      <?php
    }
    else if ($pics_query->num_rows() > 0) {
      ?>
      <div id="my-slider" class="carousel slide" data-ride="carousel"  data-interval="10000">
        <ol class="carousel-indicators">
          <?php
          $counter1 = 0;
          foreach($pics_query->result() as $row) {
            if ($counter1 == 0) {
              ?>
              <li data-target="#my-slider" data-slide-to="0" class="active" ></li>
              <?php
            } else if ($counter1 > 0) {
              ?>
              <li data-target="#my-slider" data-slide-to="<?= $counter1 ?>"></li>
              <?php
            }
            $counter1++;
          }
          ?>
        </ol>

        <div class="carousel-inner" role="listbox">
          <?php
          $counter2 = 0;
          foreach($pics_query->result() as $row) {
            $picture_name = $row->picture_name;
            $picture_location = base_url()."/big_pics/".$picture_name;
            if ($counter2 == 0) {
              ?>
              <div class="item active">
                <img class="d-block img-fluid" src="<?= $picture_location ?>" title="<?= $picture_name ?>" width="100%">
              </div>
              <?php
            } else if ($counter2 > 0) {
              ?>
              <div class="item">
                <img class="d-block img-fluid" src="<?= $picture_location ?>" title="<?= $picture_name ?>" width="100%">
              </div>
              <?php
            }
            $counter2++;
          }
          ?>
        </div>
        <a class="left carousel-control" href="#my-slider" role="button" data-slide="prev">
          <span class="glyphicon glyphicon-chevron-left" aria-hiddne="true"></span>
          <span class="sr-only">Previous</span>
        </a>
        <a class="right carousel-control" href="#my-slider" role="button" data-slide="next">
          <span class="glyphicon glyphicon-chevron-right" aria-hiddne="true"></span>
          <span class="sr-only">Previous</span>
        </a>
      </div>
      <?php
    } else {
      ?>
      <p>No pictures available for this product</p>
      <?php
    }
    ?>
    <!-- <img src="<?= base_url() ?>/big_pics/<?= $big_pic ?>" class="img-responsive" alt="<?= $item_title ?>"> -->
  </div>
  <div class="col-md-5">
    <h1><?= $item_title ?></h1>
    <h3>Price: <?= $item_price_desc ?></h3>
    <h5>Posted on: <?= $date_made ?></h5>
    <div style="clear: both;">
      <?= nl2br($item_description) ?>
    </div>
  </div>
  <div class="col-md-3">

    <?= Modules::run('store_items/_draw_contact_sellter', $update_id) ?>

    <!-- <?= Modules::run('cart/_draw_add_to_cart', $update_id) ?> -->
  </div>
</div>
