<?php
class Cart extends MX_Controller {

  function __construct() {
    parent::__construct();
  }

  function index() {
    $this->load->module('site_security');
    $session_id = $this->session->session_id;
    $shopper_id = $this->site_security->_get_user_id();

    $third_bit = $this->uri->segment(3);
    if ($third_bit != '') {
      // if the token isn't empty, try to get session_id from token
      $session_id = $this->_check_and_get_session_id($third_bit);
    } else {
      $session_id = $this->session->session_id;
    }

    if (!is_numeric($shopper_id)) {
      $shoppe_id = 0;
    }
    $table = "store_basket";
    $date['flash'] = $this->session->flashdata('item');
    $data['view_file'] = "cart";

    $data['query'] = $this->_fetch_cart_content($session_id, $shopper_id, $table);

    // count the number of items in the cart
    $data['num_rows'] = $data['query']->num_rows();
    $data['showing_statement'] = $this->_get_showing_statement($data['num_rows']);
    $this->load->module('templates');
    $this->templates->public_bootstrap($data);
  }

  function _create_checkout_token($session_id) {
    $this->load->module('site_security');
    $encrypted_string = $this->site_security->_encrypt_string($session_id);
    // remove dodgy characters
    $checkedout_token = str_replace('+', '-plus-', $encrypted_string);
    $checkedout_token = str_replace('/', '-fwrd-', $checkedout_token);
    $checkedout_token = str_replace('=', '-eqls-', $checkedout_token);
    return $checkedout_token;
  }

  function _get_session_id_from_token($checkout_token) {
    $this->load->module('site_security');
    // remove dodgy characters
    $session_id = str_replace('-plus-', '+', $checkout_token);
    $session_id = str_replace('-fwrd-', '/', $session_id);
    $session_id = str_replace('-eqls-', '=', $session_id);

    $session_id = $this->site_security->_decrypt_string($session_id);
    return $session_id;
  }

  function _check_and_get_session_id($checkout_token) {

    $session_id = $this->_get_session_id_from_token($checkout_token);
    if ($session_id == '') {
      redirect(base_url());
    }
    // check to see if this session ID appears on store_basket table
    $this->load->module('store_basket');
    $query = $this->store_basket->get_where_custom('session_id', $session_id);
    $num_rows = $query->num_rows();

    if ($num_rows < 1) {
      redirect(base_url());
    }
    return $session_id;
  }

  function test() {
    $string = "Hello blue sky";
    $this->load->module('site_security');
    $encrypted_string = $this->site_security->_encrypt_string($string);
    $decrypted_string = $this->site_security->_decrypt_string($encrypted_string);

    echo "string is $string<hr>";
    echo "encrypted string is $encrypted_string<hr>";
    echo "decrypted string is $decrypted_string<hr>";
  }

  function test2() {
    $string = "Hello blue sky";
    $this->load->module('site_security');

    $third_bit = $this->uri->segment(3);
    if ($third_bit != '') {
      $encrypted_string = $third_bit;
    } else {
      $encrypted_string = $this->site_security->_encrypt_string($string);
    }

    $decrypted_string = $this->site_security->_decrypt_string($encrypted_string);

    echo "string is $string<hr>";
    echo "encrypted string is $encrypted_string<hr>";
    echo "decrypted string is $decrypted_string<hr>";

    // create new encrypted string
    $new_encrypted_string = $this->site_security->_encrypt_string($string);
    echo anchor('cart/test2/'.$new_encrypted_string, 'refresh');
  }

  function submit_choice() {
    $submit = $this->input->post('submit', true);

    if ($submit == "no") {
      $checkout_token = $this->input->post('checkout_token', true);
      redirect('cart/index/'.$checkout_token);
    } else if ($submit == "yes") {
      redirect('youraccount/start');
    }
  }

  function go_to_checkout() {
    $this->load->module('site_security');
    $shopper_id = $this->site_security->_get_user_id();
    if (!is_numeric($shopper_id)) {
      $shoppe_id = 0;
    }
    $data['checkout_token'] = $this->uri->segment(3);
    $date['flash'] = $this->session->flashdata('item');
    $data['view_file'] = "go_to_checkout";
    $this->load->module('templates');
    $this->templates->public_bootstrap($data);
  }

  function _attempt_draw_checkout_btn($query) {
    $data['query'] = $query;
    $this->load->module('site_security');
    $shopper_id = $this->site_security->_get_user_id();
    $third_bit = $this->uri->segment(3);

    if (!is_numeric($shopper_id) AND ($third_bit == '')) {
      $this->_draw_checkout_btn_fake($query);
    } else {
      $this->_draw_checkout_btn_real($query);
    }
  }

  function _draw_checkout_btn_real($query) {
    $this->load->module('paypal');
    $this->paypal->_draw_checkout_btn($query);
  }

  function _draw_checkout_btn_fake($query) {
    foreach($query->result() as $row) {
      $session_id = $row->session_id;
    }

    $data['checkout_token'] = $this->_create_checkout_token($session_id);
    $this->load->view('checkout_btn_fake', $data);
  }

  function _draw_cart_content($query, $user_type) {
    // NOTE: user_type can be 'public' or 'admin'
    $this->load->module('site_settings');
    $this->load->module('shipping');
    $data['currency_symbol'] = $this->site_settings->_get_currency_symbol();

    if ($user_type == 'public') {
      $view_file = 'cart_contents_public';
    } else {
      $view_file = 'cart_contents_admin';
    }
    $data['shipping'] = $this->shipping->_get_shipping();
    $data['query'] = $query;
    $this->load->view($view_file, $data);
  }

  function _fetch_cart_content($session_id, $shopper_id, $table) {
    // fetch the contents of the shopping cart
    $this->load->module($table);
    $mysql_query = "
    SELECT sb.*, si.small_pic, si.item_url
    FROM store_basket sb LEFT JOIN store_items si ON sb.item_id = si.id
    ";
    if ($shopper_id > 0) {
      $where_condition = " WHERE sb.shopper_id = $shopper_id";
    } else {
      $where_condition = " WHERE sb.session_id = '$session_id'";
    }
    $mysql_query.= $where_condition;
    $query = $this->store_basket->_custom_query($mysql_query);
    return $query;
  }

  function _get_showing_statement($num_items) {
    if ($num_items == 1) {
      $showing_statement = "You have one item in your shopping cart.";
    } else {
      $showing_statement = "You have $num_items items in your shopping cart.";
    }
    return $showing_statement;
  }

  function _draw_add_to_cart($item_id) {

    // fetch the color & size options for this item;
    $submitted_color = $this->input->post('submitted_color', true);
    $submitted_size = $this->input->post('submitted_size', true);

    if ($submitted_color == "") {
      $color_options[''] = "Select...";
    }

    if ($submitted_size == "") {
      $size_options[''] = "Select...";
    }

    $this->load->module('store_item_colors');

    $query = $this->store_item_colors->get_where_custom('item_id', $item_id);
    $data['num_colors'] = $query->num_rows();
    foreach ($query->result() as $row) {
      $color_options[$row->id] = $row->color;
    }

    $this->load->module('store_item_sizes');
    $query = $this->store_item_sizes->get_where_custom('item_id', $item_id);
    $data['num_sizes'] = $query->num_rows();
    foreach ($query->result() as $row) {
      $size_options[$row->id] = $row->size;
    }


    $data['submitted_color'] = $submitted_color;
    $data['color_options'] = $color_options;
    $data['submitted_size'] = $submitted_size;
    $data['size_options'] = $size_options;
    $data['item_id'] = $item_id;
    $this->load->view('add_to_cart', $data);
  }

}
