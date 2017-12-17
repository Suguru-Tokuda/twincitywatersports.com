<?php
class Store_basket extends MX_Controller {

  function __construct() {
    parent::__construct();
    $this->load->library('form_validation');
    $this->form_validation->set_ci($this);
  }

  function remove() {

    $update_id = $this->uri->segment(3);
    $allowed = $this->_make_sure_remove_allowed($update_id);

    if (!$allowed) {
      redirect('cart');
    }
    $this->_delete($update_id);
    $refer_url = $_SERVER['HTTP_REFERER'];
    redirect('cart');
  }

  function _make_sure_remove_allowed($update_id) {
    $query = $this->get_where($update_id);
    foreach ($query->result() as $row) {
      $session_id = $row->session_id;
      $shopper_id = $row->shopper_id;
    }
    $customer_session_id = $this->session->session_id;
    $this->load->module('site_security');
    $customer_shopper_id = $this->site_security->_get_user_id();


    if (($session_id == $customer_session_id) OR ($shopper_id == $customer_shopper_id)) {
      return true;
    } else {
      return false;
    }

  }

  function add_to_basket() {
    $submit = $this->input->post('submit', true);

    if ($submit == "submit") {
      // process the form
      $item_id = $this->input->post('item_id', true);

      $this->load->library('form_validation');
      // need to check if there are any colors for the item. if so, it should set 'required'
      if ($this->_has_values("colors", $item_id)) {
        $this->form_validation->set_rules('item_color', 'Item Color', 'numeric|required');
      }
      // need to check if there are any sizes for the item. if so, it should set 'required'
      if ($this->_has_values("sizes", $item_id)) {
        $this->form_validation->set_rules('item_size', 'Item Size', 'numeric|required');
      }

      $this->form_validation->set_rules('item_qty', 'Item Quantity', 'required|numeric');
      $this->form_validation->set_rules('item_id', 'Item ID', 'required|numeric');
      if ($this->form_validation->run() == true) {
        // cool"
        $data = $this->_fetch_the_data();
        $this->_insert($data);
        redirect('cart');
      } else {
        // uncool
        $refer_url = $_SERVER['HTTP_REFERER'];
        $error_msg = validation_errors("<p style='color: red;'>", "</p>");
        $this->session->set_flashdata('item', $error_msg);
        redirect($refer_url);
      }
    }
  }

  function _fetch_the_data() {
    // gathers together all of the dadta, so that we can do a table insert

    $this->load->module('site_security');
    $this->load->module('store_items');

    $item_size_id = $this->input->post('item_size', true);
    $item_color_id = $this->input->post('item_color', true);

    $item_id = $this->input->post('item_id', true);

    $item_qty = $this->input->post('item_qty', true);
    $item_data = $this->store_items->fetch_data_from_db($item_id);
    $item_price = $item_data['item_price'];
    $shopper_id = $this->site_security->_get_user_id();

    if (!is_numeric($shopper_id)) {
      $shopper_id = 0;
    }
    // echo "in store_basket";
    // echo $this->session->session_id; die();

    $data['session_id'] = $this->session->session_id;
    $data['item_title'] = $item_data['item_title'];
    $data['price'] = $item_price;
    $data['tax'] = '0';
    $data['item_id'] = $item_id;
    $data['item_size'] = $this->_get_value('size', $item_size_id);
    $data['item_qty'] = $this->input->post('item_qty', true);
    $data['item_color'] = $this->_get_value('color', $item_color_id);
    $data['date_added'] = time();
    $data['shopper_id'] = $shopper_id;
    $data['ip_address'] = $this->input->ip_address();

    return $data;
  }

  function _get_value($value_type, $update_id) {
    //NOTE: value_type can be 'color' or 'size'
    if (is_numeric($update_id)) {
      if ($value_type == 'size') {
        $this->load->module('store_item_sizes');
        $query = $this->store_item_sizes->get_where($update_id);
        foreach ($query->result() as $row) {
          $item_size = $row->size;
        }
        if (!isset($item_size)) {
          $item_size = '';
        }
        $value = $item_size;
      } else if ($value_type == 'color') {
        $this->load->module('store_item_colors');
        $query = $this->store_item_colors->get_where($update_id);
        foreach ($query->result() as $row) {
          $item_color = $row->color;
        }
        if (!isset($item_color)) {
          $item_color = '';
        }
        $value = $item_color;
      }
    } else {
      $value = '';
    }
    return $value;
  }

  function _has_values($column, $item_id) {
    $mysql_query = "SELECT * FROM store_item_$column WHERE item_id = $item_id";
    $query = $this->_custom_query($mysql_query);
    $num_rows = $query->num_rows();

    if ($num_rows > 0) {
      return true;
    } else if ($num_rows == 0) {
      return false;
    }
  }

  function test() {
    $session_id = $this->session->session_id;
    echo $session_id;
    echo "<hr>";
    $this->load->module('site_security');
    $shopper_id = $this->site_security->_get_user_id();
    echo "You are shopper ID: $shopper_id";
  }

  function get($order_by)
  {
    $this->load->model('mdl_store_basket');
    $query = $this->mdl_store_basket->get($order_by);
    return $query;
  }

  function get_with_limit($limit, $offset, $order_by)
  {
    if ((!is_numeric($limit)) || (!is_numeric($offset))) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_store_basket');
    $query = $this->mdl_store_basket->get_with_limit($limit, $offset, $order_by);
    return $query;
  }

  function get_where($id)
  {
    if (!is_numeric($id)) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_store_basket');
    $query = $this->mdl_store_basket->get_where($id);
    return $query;
  }

  function get_where_custom($col, $value)
  {
    $this->load->model('mdl_store_basket');
    $query = $this->mdl_store_basket->get_where_custom($col, $value);
    return $query;
  }

  function _insert($data)
  {
    $this->load->model('mdl_store_basket');
    $this->mdl_store_basket->_insert($data);
  }

  function _update($id, $data)
  {
    if (!is_numeric($id)) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_store_basket');
    $this->mdl_store_basket->_update($id, $data);
  }

  function _delete($id)
  {
    if (!is_numeric($id)) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_store_basket');
    $this->mdl_store_basket->_delete($id);
  }

  function count_where($column, $value)
  {
    $this->load->model('mdl_store_basket');
    $count = $this->mdl_store_basket->count_where($column, $value);
    return $count;
  }

  function get_max()
  {
    $this->load->model('mdl_store_basket');
    $max_id = $this->mdl_store_basket->get_max();
    return $max_id;
  }

  function _custom_query($mysql_query)
  {
    $this->load->model('mdl_store_basket');
    $query = $this->mdl_store_basket->_custom_query($mysql_query);
    return $query;
  }

}
