<?php
class Store_item_sizes extends MX_Controller
{

function __construct() {
parent::__construct();
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
    $size = trim($this->input->post('size', true));

    if ($submit == "Finished") {
      redirect('store_items/create/'.$update_id);
    } else if ($submit == "Submit") {
      // attempt to insert
      if ($size != "") {
        $data['item_id'] = $update_id;
        $data['size'] = $size;
        $this->_insert($data);

        $flash_msg = "The new size option was successfully added.";
        $value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
        $this->session->set_flashdata('item', $value);
      }
    }
    redirect('store_item_sizes/update/'.$update_id);
}

function update($update_id) {
  // only those people with an update_id for an item can get in.
  if (!is_numeric($update_id)) {
    redirect('site_security/not_allowed');
  }
  // security
  $this->load->library('session');
  $this->load->module('site_security');
  $this->site_security->_make_sure_is_admin();

  // fetch existing options for this item
  $data['query'] = $this->get_where_custom('item_id', $update_id);
  $data['num_rows'] = $data['query']->num_rows();

  $data['headline'] = "Update Item Sizes";
  $data['update_id'] = $update_id;
  $data['flash'] = $this->session->flashdata('item');
  $data['view_file'] = "update";
  $this->load->module('templates');
  $this->templates->admin($data);
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

  // fetch the item_id
  $query = $this->get_where($update_id);
  foreach($query->result() as $row) {
    $item_id = $row->item_id;
  }
  // deleting the size
  $this->_delete($update_id);

  $flash_msg = "The option was successfully deleted.";
  $value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
  $this->session->set_flashdata('item', $value);

  redirect('store_item_sizes/update/'.$item_id);
}

function _delete_for_item($item_id) {
  $mysql_query = "DELETE FROM store_item_sizes WHERE item_id = $item_id";
  $query = $this->_custom_query($mysql_query);
}

function get($order_by)
{
    $this->load->model('mdl_store_item_sizes');
    $query = $this->mdl_store_item_sizes->get($order_by);
    return $query;
}

function get_with_limit($limit, $offset, $order_by)
{
    if ((!is_numeric($limit)) || (!is_numeric($offset))) {
        die('Non-numeric variable!');
    }

    $this->load->model('mdl_store_item_sizes');
    $query = $this->mdl_store_item_sizes->get_with_limit($limit, $offset, $order_by);
    return $query;
}

function get_where($id)
{
    if (!is_numeric($id)) {
        die('Non-numeric variable!');
    }

    $this->load->model('mdl_store_item_sizes');
    $query = $this->mdl_store_item_sizes->get_where($id);
    return $query;
}

function get_where_item_id($item_id) {
  $this->load->model('mdl_store_item_sizes');
  $query = $this->mdl_store_item_sizes->get_where_item_id($item_id);
  return $query;
}

function get_where_custom($col, $value)
{
    $this->load->model('mdl_store_item_sizes');
    $query = $this->mdl_store_item_sizes->get_where_custom($col, $value);
    return $query;
}

function _insert($data)
{
    $this->load->model('mdl_store_item_sizes');
    $this->mdl_store_item_sizes->_insert($data);
}

function _update($id, $data)
{
    if (!is_numeric($id)) {
        die('Non-numeric variable!');
    }

    $this->load->model('mdl_store_item_sizes');
    $this->mdl_store_item_sizes->_update($id, $data);
}

function _delete($id)
{
    if (!is_numeric($id)) {
        die('Non-numeric variable!');
    }

    $this->load->model('mdl_store_item_sizes');
    $this->mdl_store_item_sizes->_delete($id);
}

function count_where($column, $value)
{
    $this->load->model('mdl_store_item_sizes');
    $count = $this->mdl_store_item_sizes->count_where($column, $value);
    return $count;
}

function get_max()
{
    $this->load->model('mdl_store_item_sizes');
    $max_id = $this->mdl_store_item_sizes->get_max();
    return $max_id;
}

function _custom_query($mysql_query)
{
    $this->load->model('mdl_store_item_sizes');
    $query = $this->mdl_store_item_sizes->_custom_query($mysql_query);
    return $query;
}

}
