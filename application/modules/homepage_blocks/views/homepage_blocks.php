<?php
function get_theme($count) {
  switch ($count) {
    case '1':
    $theme = 'danger';
    break;
    case '2';
    $theme = 'warning';
    break;
    case '3';
    $theme = 'success';
    break;
    default;
    $theme = 'primary';
    break;
  }
  return $theme;
}

?>

<style>
.panel-danger {
  border-color: #d9534f;
}
.panel-danger > .panel-heading {
  color: #ffffff;
  background-color: #d9534f;
  border-color: #d9534f;
}
.panel-warning {
  border-color: #f0ad4e;
}
.panel-warning > .panel-heading {
  color: #ffffff;
  background-color: #f0ad4e;
  border-color: #f0ad4e;
}
.panel-success {
  border-color: #5cd85c;
}
.panel-success > .panel-heading {
  color: #ffffff;
  background-color: #5cd85c;
  border-color: #5cd85c;
}
</style>

<?php
$count = 0;
$this->load->module('homepage_offers');
$this->load->module('site_settings');
$items_segments = $this->site_settings->_get_item_segments();
$currency_symbol = $this->site_settings->_get_currency_symbol();
foreach($query->result() as $row) {
  $count++;
  $block_id = $row->id;
  $num_items_on_block = $this->homepage_offers->custom_count($block_id);
  if ($num_items_on_block > 0) {
    if ($count > 4) {
      $count = 1;
    }

    $theme = get_theme($count);

    ?>
    <div class="bs-example" data-example-id=contextural-panels>
      <div class="panel panel-<?= $theme ?>">
        <div class="panel-heading">
          <h3 class="panel-title"><?= $row->block_title ?></h3>
        </div>
        <div class="panel-body">
          <div class="xcontainer">
            <div class="row">
              <?php
              $block_data['block_id'] = $block_id;
              $block_data['theme'] = $theme;
              $block_data['item_segments'] = $items_segments;
              $block_data['currency_symbol'] = $currency_symbol;
              $this->homepage_offers->_draw_offers($block_data);
              ?>
            </div>
          </div>


        </div>
      </div>
    </div>

    <?php
  }
}
?>
