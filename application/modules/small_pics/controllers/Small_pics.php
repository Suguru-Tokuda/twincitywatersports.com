<?php
class Small_pics extends MX_Controller {

  function __construct() {
    parent::__construct();
  }

  function _get_picture_name_by_small_pic_id($id) {
    $mysql_query = "SELECT picture_name FROM small_pics WHERE id = $id";
    $query = $this->_custom_query($mysql_query);
    foreach($query->result() as $row) {
      $picture_name = $row->picture_name;
    }
    return $picture_name;
  }

  function _delete_where($col, $value) {
    $mysql_query = "DELETE FROM small_pics WHERE $col = $value";
    $this->_custom_query($mysql_query);
  }

  function get_small_pic_ids_by_item_id($item_id) {
    $mysql_query = "SELECT id FROM small_pics WHERE item_id = $item_id";
    $query = $this->_custom_query($mysql_query);
    $count = 0;
    foreach($query->result() as $row) {
      $small_pic_ids[$count] = $row->id;
      $count++;
    }
    return $small_pic_ids;
  }

  function get_index_small_pic_name_id_by_item_id($item_id) {
    $mysql_query = "SELECT sp.picture_name FROM small_pics sp WHERE item_id = ? AND sp.priority = 1";
    $query = $this->db->query($mysql_query, array($item_id));
    foreach($query->result() as $row) {
      $picture_name = $row->picture_name;
    }
    if (!isset($picture_name)) {
      $picture_name = "";
    }
    return $picture_name;
  }

  function get($order_by)
  {
    $this->load->model('mdl_small_pics');
    $query = $this->mdl_small_pics->get($order_by);
    return $query;
  }

  function get_with_limit($limit, $offset, $order_by)
  {
    if ((!is_numeric($limit)) || (!is_numeric($offset))) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_small_pics');
    $query = $this->mdl_small_pics->get_with_limit($limit, $offset, $order_by);
    return $query;
  }

  function get_where($id)
  {
    if (!is_numeric($id)) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_small_pics');
    $query = $this->mdl_small_pics->get_where($id);
    return $query;
  }

  function get_where_custom($col, $value)
  {
    $this->load->model('mdl_small_pics');
    $query = $this->mdl_small_pics->get_where_custom($col, $value);
    return $query;
  }

  function get_priority_for_item($id, $item_id) {
    $this->load->model('mdl_small_pics');
    $query = $this->mdl_small_pics->get_priority_for_item($id, $item_id);
    foreach($query->result() as $row) {
      $priority = $row->priority;
    }
    return $priority;
  }

  function _insert($data)
  {
    $this->load->model('mdl_small_pics');
    $this->mdl_small_pics->_insert($data);
  }

  function _update($id, $data)
  {
    if (!is_numeric($id)) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_small_pics');
    $this->mdl_small_pics->_update($id, $data);
  }

  function _delete($id)
  {
    if (!is_numeric($id)) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_small_pics');
    $this->mdl_small_pics->_delete($id);
  }

  function count_where($column, $value)
  {
    $this->load->model('mdl_small_pics');
    $count = $this->mdl_small_pics->count_where($column, $value);
    return $count;
  }

  function get_max()
  {
    $this->load->model('mdl_small_pics');
    $max_id = $this->mdl_small_pics->get_max();
    return $max_id;
  }

  function _custom_query($mysql_query)
  {
    $this->load->model('mdl_small_pics');
    $query = $this->mdl_small_pics->_custom_query($mysql_query);
    return $query;
  }



}
