<h1><?= $block_title ?></h1>
<?= $pagination ?>
<?= $showing_statement ?>
<div class="row">
  <?php
  foreach ($query->result() as $row) {
    $small_pic_path = base_url()."small_pics/".$row->small_pic;
    $item_page = base_url()."$item_segments./$row->cat_url/$row->item_url";
    ?>
    <div class="col-md-2 img-thumbnail" style="margin: 5px; height: 300px;" >
      <a href="<?= $item_page ?>" ><img src="<?= $small_pic_path ?>" title="<?= $row->item_title ?>" class="img-responsive"  ></a>
      <h6><a href="<?= $item_page ?>" ><?= $row->item_title ?></a></h6>
      <div style="clear: both; color: red; font-weight: bold;">$<?= number_format($row->item_price, 2) ?>
        <?php
        if ($row->was_price > 0) { ?>
          <span style="font-weight: normal; color: #999; text-decoration: line-through"><?= $currency_symbol.$row->was_price ?></span>
          <?php
        }
        ?>
      </div>
    </div>
    <?php
  }
  ?>
</div>
<?= $pagination ?>
