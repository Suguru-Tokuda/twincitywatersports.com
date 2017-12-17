<h1><?= $cat_title ?></h1>
<?= $pagination ?>
<?= $showing_statement ?>
<div class="row">
  <?php
  $this->load->module('store_categories');

  foreach ($query->result() as $row) {
    $item_url = $row->item_url;
    $index_pic_name = $this->store_categories->_get_picture_name_by_item_url($item_url);
    $small_pic_path = base_url()."small_pics/".$index_pic_name;
    $item_page = base_url()."$item_segments./$row->cat_url/$row->item_url";
    ?>
    <div class="col-md-2 img-thumbnail" style="margin: 5px; height: 300px;" >
      <?php
      if ($index_pic_name != "") {
        ?>
        <a href="<?= $item_page ?>" ><img src="<?= $small_pic_path ?>" title="<?= $row->item_title ?>" class="img-responsive" ></a>
        <?php
      } else {
        ?>
        <p>No pictures available</a>
          <?php
      }
       ?>

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
