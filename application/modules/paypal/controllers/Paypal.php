<?php
class Paypal extends MX_Controller {

  function __construct() {
    parent::__construct();
  }

  function ipn_listener() {
    // the URL that accepts things that Paypal has posted
    $data['date_created'] = time();
    // makes an array of data retruend from paypal
    foreach($_POST as $key => $value) {
      $posted_information[$key] = $value;
    }

    $data['posted_information'] = $posted_information;
    $this->_insert($data);
  }

  function _is_on_test_mode() {
    return true; // set this to false if we are live!
  }

  function thankyou() {
    $data['view_file'] = 'thankyou';
    $this->load->module('templates');
    $this->templates->public_bootstrap($data);
  }

  function cancel() {
    $data['view_file'] = 'cancel';
    $this->load->module('templates');
    $this->templates->public_bootstrap($data);
  }

  function _draw_checkout_btn($query) {
    $this->load->module('site_settings');
    $this->load->module('site_security');
    $this->load->module('shipping');

    foreach ($query->result() as $row) {
      $session_id = $row->session_id;
    }
    $on_test_mode = $this->_is_on_test_mode();
    if ($on_test_mode == true) {
      $data['form_location'] = "https://www.sandbox.paypal.com/cgi_bin/webscr";
    } else {
      $data['form_location'] = "https://www.paypal.com/cgi_bin/webscr";
    }
    // the data is sent to paypal
    $data['return'] = base_url().'paypal/thankyou';
    $data['cancel_return'] = base_rul().'paypal/cancel';
    $data['shipping'] = $this->shipping->_get_shipping();
    $data['custom'] = $this->site_security->_encrypt_string($session_id);
    $data['paypal_email'] = $this->site_settings->_get_paypal_email();
    $data['currency_code'] = $this->site_settings->_get_currency_code();
    $data['query'] = $query;
    $this->load->view('checkout_btn', $data);
  }

  function get($order_by)
  {
    $this->load->model('mdl_paypal');
    $query = $this->mdl_paypal->get($order_by);
    return $query;
  }

  function get_with_limit($limit, $offset, $order_by)
  {
    if ((!is_numeric($limit)) || (!is_numeric($offset))) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_paypal');
    $query = $this->mdl_paypal->get_with_limit($limit, $offset, $order_by);
    return $query;
  }

  function get_where($id)
  {
    if (!is_numeric($id)) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_paypal');
    $query = $this->mdl_paypal->get_where($id);
    return $query;
  }

  function get_where_custom($col, $value)
  {
    $this->load->model('mdl_paypal');
    $query = $this->mdl_paypal->get_where_custom($col, $value);
    return $query;
  }

  function _insert($data)
  {
    $this->load->model('mdl_paypal');
    $this->mdl_paypal->_insert($data);
  }

  function _update($id, $data)
  {
    if (!is_numeric($id)) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_paypal');
    $this->mdl_paypal->_update($id, $data);
  }

  function _delete($id)
  {
    if (!is_numeric($id)) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_paypal');
    $this->mdl_paypal->_delete($id);
  }

  function count_where($column, $value)
  {
    $this->load->model('mdl_paypal');
    $count = $this->mdl_paypal->count_where($column, $value);
    return $count;
  }

  function get_max()
  {
    $this->load->model('mdl_paypal');
    $max_id = $this->mdl_paypal->get_max();
    return $max_id;
  }

  function _custom_query($mysql_query)
  {
    $this->load->model('mdl_paypal');
    $query = $this->mdl_paypal->_custom_query($mysql_query);
    return $query;
  }

}
