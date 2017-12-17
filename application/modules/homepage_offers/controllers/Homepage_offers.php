<?php
class Homepage_offers extends MX_Controller {

  function __construct() {
    parent::__construct();
    $this->load->library('form_validation');
    $this->form_validation->set_ci($this);
  }

  function _draw_offers($data) {
    $block_id = $data['block_id'];
    $theme = $data['theme'];
    $item_segments = $data['item_segments'];
    $query = $this->get_where_custom('block_id', $block_id);

    $mysql_query = "
    SELECT si.id as item_id, si.*, sc.*
    FROM homepage_offers ho JOIN homepage_blocks hb ON ho.block_id = hb.id
    JOIN store_items si ON ho.item_id = si.id
    JOIN store_cat_assign sca ON si.id = sca.item_id
    JOIN store_categories sc ON sca.cat_id = sc.id
    WHERE ho.block_id = $block_id
    ";

    $query = $this->_custom_query($mysql_query);

    $num_rows = $query->num_rows();
    if ($num_rows > 0) {
      $data['query'] = $query;
      $data['theme'] = $theme;
      $data['item_segments'] = $item_segments;
      $this->load->view('offers', $data);
    }
  }

  function submit($update_id) {
    // only those people with an update_id for an item can get in.
    if (!is_numeric($update_id)) {
      redirect('site_security/not_allowed');
    }
    // security
    $this->load->library('session');
    $this->load->module('site_security');
    $this->site_security->_make_sure_is_admin();

    // getting values from post
    $submit = $this->input->post('submit', true);
    $item_id = trim($this->input->post('item_id', true));

    if ($submit == "Finished") {
      redirect('homepage_blocks/create/'.$update_id);
    } else if ($submit == "Submit") {
      $this->load->library('form_validation');
      $this->form_validation->set_rules('item_id', 'Item', 'required');

      if ($this->form_validation->run() == true) {
        // attempt to insert
        if ($item_id != "") {
          $data['block_id'] = $update_id;
          $data['item_id'] = $item_id;
          $this->_insert($data);

          $flash_msg = "The new item was successfully added to the offer.";
          $value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
          $this->session->set_flashdata('item', $value);
          redirect('homepage_offers/update/'.$update_id);
        }
      } else {
        $this->update($update_id);
      }
    }
  }

  function update($update_id) {
    // only those people with an update_id for an item can get in.
    if (!is_numeric($update_id)) {
      redirect('site_security/not_allowed');
    }
    // security
    $this->load->library('session');
    $this->load->module('site_security');
    $this->load->module('store_items');
    $this->site_security->_make_sure_is_admin();

    $items = $this->store_items->_get_all_items_for_dropdown();
    $assigned_items = $this->_get_assigned_items_for_offer();

    if (!isset($assigned_items)) {
      $assigned_items[] = "No more to select";
    } else {
      // the item has been assigned to at least one category
      // array_diff is used ot subtract first array from second array
      $unassigned_items = array_diff($items, $assigned_items);
    }

    $count = count($unassigned_items);
    if ($count == 0) {
      $unassigned_items[''] = "No more to select";
    }

    // fetch existing options for this item
    $data['query'] = $this->get_where_custom('block_id', $update_id);
    $data['block_title'] = $this->get_block_title_by_id($update_id);
    $data['num_rows'] = $data['query']->num_rows();
    $data['options'] = $unassigned_items;
    $data['headline'] = "Update Homepage Offers";
    $data['update_id'] = $update_id;
    $data['flash'] = $this->session->flashdata('item');
    $data['view_file'] = "update";
    // $data['unassigned_items'] = $unassigned_items;
    $this->load->module('templates');
    $this->templates->admin($data);
  }

  function _get_assigned_items_for_offer() {
    $mysql_query = "
    SELECT si.id, si.item_title
    FROM store_items si JOIN homepage_offers ho ON si.id = ho.item_id
    ORDER BY si.item_title
    ";

    $query = $this->_custom_query($mysql_query);
    $num_rows = $query->num_rows();

    if ($num_rows > 0) {
      foreach ($query->result() as $row) {
        $assigned_items[$row->id] = $row->item_title;
      }
    } else {
      $assigned_items[0] = "";
    }

    if (!isset($assigned_items)) {
      $assigned_items = "";
    }
    return $assigned_items;
  }

  function _get_dropdown_options() {
    // build an array of all the store items
    // if (!is_numeric($update_id)) {
    //   $update_id = 0;
    // }
    $options[''] = 'Please Select...';

    $mysql_query = "SELECT * FROM store_items ORDER BY item_title";
    $query = $this->_custom_query($mysql_query);
    foreach ($query->result() as $row) {
      $options[$row->id] = $row->item_title;
    }
    return $options;
  }

  // get data from POST method
  function fetch_data_from_post() {
    $data['item_id'] = $this->input->post('item_id', true);
    return $data;
  }

  function delete($update_id) {
    // only those people with an update_id for an item can get in.
    if (!is_numeric($update_id)) {
      redirect('site_security/not_allowed');
    }
    // security
    $this->load->library('session');
    $this->load->module('site_security');
    $this->site_security->_make_sure_is_admin();

    // fetch the block_id
    $query = $this->get_where($update_id);
    foreach($query->result() as $row) {
      $block_id = $row->block_id;
    }
    // deleting the item_id
    $this->_delete($update_id);

    $flash_msg = "The option was successfully deleted.";
    $value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
    $this->session->set_flashdata('item', $value);

    redirect('homepage_offers/update/'.$block_id);
  }

  function _delete_for_item($block_id) {
    $mysql_query = "DELETE FROM homepage_offers WHERE block_id = $block_id";
    $query = $this->_custom_query($mysql_query);
  }

  function get($order_by)
  {
    $this->load->model('mdl_homepage_offers');
    $query = $this->mdl_homepage_offers->get($order_by);
    return $query;
  }

  function get_with_limit($limit, $offset, $order_by)
  {
    if ((!is_numeric($limit)) || (!is_numeric($offset))) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_homepage_offers');
    $query = $this->mdl_homepage_offers->get_with_limit($limit, $offset, $order_by);
    return $query;
  }

  function get_block_title_by_id($id) {
    $mysql_query = "SELECT * FROM homepage_blocks WHERE id = $id";
    $query = $this->_custom_query($mysql_query);
    foreach ($query->result() as $row) {
      $block_title = $row->block_title;
    }
    return $block_title;
  }

  function get_where($id)
  {
    if (!is_numeric($id)) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_homepage_offers');
    $query = $this->mdl_homepage_offers->get_where($id);
    return $query;
  }

  function get_where_custom($col, $value)
  {
    $this->load->model('mdl_homepage_offers');
    $query = $this->mdl_homepage_offers->get_where_custom($col, $value);
    return $query;
  }

  function _insert($data)
  {
    $this->load->model('mdl_homepage_offers');
    $this->mdl_homepage_offers->_insert($data);
  }

  function _update($id, $data)
  {
    if (!is_numeric($id)) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_homepage_offers');
    $this->mdl_homepage_offers->_update($id, $data);
  }

  function _delete($id)
  {
    if (!is_numeric($id)) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_homepage_offers');
    $this->mdl_homepage_offers->_delete($id);
  }

  function count_where($column, $value)
  {
    $this->load->model('mdl_homepage_offers');
    $count = $this->mdl_homepage_offers->count_where($column, $value);
    return $count;
  }

  function custom_count($block_id) {
    $mysql_query = "
    SELECT si.*, sc.*
    FROM homepage_offers ho JOIN homepage_blocks hb ON ho.block_id = hb.id
    JOIN store_items si ON ho.item_id = si.id
    JOIN store_cat_assign sca ON si.id = sca.item_id
    JOIN store_categories sc ON sca.cat_id = sc.id
    WHERE ho.block_id = $block_id
    ";
    $query = $this->_custom_query($mysql_query);
    return $query->num_rows();
  }

  function get_max()
  {
    $this->load->model('mdl_homepage_offers');
    $max_id = $this->mdl_homepage_offers->get_max();
    return $max_id;
  }

  function _custom_query($mysql_query)
  {
    $this->load->model('mdl_homepage_offers');
    $query = $this->mdl_homepage_offers->_custom_query($mysql_query);
    return $query;
  }

}
