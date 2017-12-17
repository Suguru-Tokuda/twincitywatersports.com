<?php
class Big_pics extends MX_Controller {

  function __construct() {
    parent::__construct();
  }

  function _delete_where($col, $value) {
    $mysql_query = "DELETE FROM big_pics WHERE $col = $value";
    $this->_custom_query($mysql_query);
  }

  function get($order_by)
  {
    $this->load->model('mdl_big_pics');
    $query = $this->mdl_big_pics->get($order_by);
    return $query;
  }

  function get_with_limit($limit, $offset, $order_by)
  {
    if ((!is_numeric($limit)) || (!is_numeric($offset))) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_big_pics');
    $query = $this->mdl_big_pics->get_with_limit($limit, $offset, $order_by);
    return $query;
  }

  function get_where($id)
  {
    if (!is_numeric($id)) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_big_pics');
    $query = $this->mdl_big_pics->get_where($id);
    return $query;
  }

  function get_where_custom($col, $value)
  {
    $this->load->model('mdl_big_pics');
    $query = $this->mdl_big_pics->get_where_custom($col, $value);
    return $query;
  }

  function _insert($data)
  {
    $this->load->model('mdl_big_pics');
    $this->mdl_big_pics->_insert($data);
  }

  function _update($id, $data)
  {
    if (!is_numeric($id)) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_big_pics');
    $this->mdl_big_pics->_update($id, $data);
  }

  function _delete($id)
  {
    if (!is_numeric($id)) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_big_pics');
    $this->mdl_big_pics->_delete($id);
  }

  function count_where($column, $value)
  {
    $this->load->model('mdl_big_pics');
    $count = $this->mdl_big_pics->count_where($column, $value);
    return $count;
  }

  function get_max()
  {
    $this->load->model('mdl_big_pics');
    $max_id = $this->mdl_big_pics->get_max();
    return $max_id;
  }

  function _custom_query($mysql_query)
  {
    $this->load->model('mdl_big_pics');
    $query = $this->mdl_big_pics->_custom_query($mysql_query);
    return $query;
  }

}
