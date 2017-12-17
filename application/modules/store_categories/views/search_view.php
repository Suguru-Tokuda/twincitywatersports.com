<?= $pagination ?>
<?= $showing_statement ?><br>
<?= $keywords ?>
<div class="row">
  <?php
  $this->load->module('store_categories');
  $this->load->module('store_items');
  foreach ($query->result() as $row) {
    $item_id = $row->id;
    $item_url = $row->item_url;
    $index_pic_name = $this->store_categories->_get_picture_name_by_item_url($item_url);
    /**Operations
    1. Get parent_cat_id by item_id;
    2. Get cat_url by parent_cat_id;
    3. make an item url cat_parent_url + item's cat_url + item_url;
    */
    $best_cat_id = $this->store_items->_get_best_sub_cat_id($item_id);
    $sub_cat_url = $this->store_items->_get_sub_cat_url_by_item_id($item_id);
    $sub_cat_url = strtolower($sub_cat_url);
    $parent_cat_url = $this->store_categories->_get_parent_cat_url($best_cat_id);
    $parent_cat_url = strtolower($parent_cat_url);
    $item_url = $row->item_url;
    $item_title = $row->item_title;
    $item_price = $row->item_price;
    $was_price = $row->was_price;
    $picture_name = $row->picture_name;
    $small_pic_path = base_url()."small_pics/".$picture_name;
    // $item_page = base_url()."$item_segments./$cat_url/$item_url";
    $item_page = base_url().$parent_cat_url."/".$sub_cat_url."/".$item_url;
    ?>
    <div class="col-md-2 img-thumbnail" style="margin: 5px; height: 300px;" >
      <?php
      if ($index_pic_name != "") {
        ?>
        <a href="<?= $item_page ?>" ><img src="<?= $small_pic_path ?>" title="<?= $item_title ?>" class="img-responsive" ></a>
        <?php
      } else {
        ?>
        <p>No pictures available</a>
          <?php
      }
       ?>

      <h6><a href="<?= $item_page ?>" ><?= $item_title ?></a></h6>
      <div style="clear: both; color: red; font-weight: bold;">$<?= number_format($item_price, 2) ?>
        <?php
        if ($row->was_price > 0) { ?>
          <span style="font-weight: normal; color: #999; text-decoration: line-through"><?= $currency_symbol.$was_price ?></span>
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
