<?php
class Homepage_blocks extends MX_Controller {

  function __construct() {
    parent::__construct();
    $this->load->library('form_validation');
    $this->form_validation->set_ci($this);
  }

  function _draw_sortable_list() {
    $mysql_query = "SELECT * FROM homepage_blocks ORDER BY priority";
    $data['query'] = $this->_custom_query($mysql_query);
    $this->load->view('sortable_list', $data);
  }

  function _draw_blocks() {
    // draw the offer blocks that are on the homepage
    $data['query'] = $this->get('priority');
    $num_rows = $data['query']->num_rows();
    if ($num_rows > 0) {
      $this->load->view('homepage_blocks', $data);
    }
  }

  // function fix() {
  //   $query = $this->get('id');
  //   foreach ($query->result() as $row) {
  //     $cat_url = $row->cat_url;
  //     $replacement = str_replace('-', '_', $cat_url);
  //     $data['cat_url'] = $replacement;
  //     $this->_update($row->id, $data);
  //   }
  // }

  function get_oldest_user($target_array) {
    // $oldest = $target_array[0];
    // for ($i = 1; $i < $target_array.count; $i++) {
    //   if ($oldest < $target_array[$i]) {
    //     $oldest == $target_array[$i];
    //   }
    // }
    // return $oldest;
    foreach ($target_array as $key => $value) {
      if (!isset($key_with_highest_value)) {
        $key_with_highest_value = $key;
      } else if ($value > $target_array[$key_with_highest_value]) {
        $key_with_highest_value = $key;
      }
    }
    return $key_with_highest_value;
  }

  function view($update_id) {
    // only those people with an update_id for an item can get in.
    if (!is_numeric($update_id)) {
      redirect('site_security/not_allowed');
    }
    $this->load->module('site_settings');
    $this->load->module('custom_pagination');

    // fetch the homepage offer details
    $data = $this->fetch_data_from_db($update_id);

    // count the items that belong to this homepage offer
    $use_limit = false;
    $mysql_query = $this->_generate_mysql_query($update_id, $use_limit);
    $query = $this->_custom_query($mysql_query);
    $total_items = $query->num_rows();

    // fetch the items for this page
    $use_limit = true;
    $mysql_query = $this->_generate_mysql_query($update_id, $use_limit);

    $pagination_data['template'] = "public_bootstrap";
    $pagination_data['target_base_url'] = $this->get_target_pagination_base_url();
    $pagination_data['total_rows'] = $total_items;
    $pagination_data['offset_segment'] = 4;
    $pagination_data['limit'] = $this->get_limit();
    $data['pagination'] = $this->custom_pagination->_generate_pagination($pagination_data);

    $showing_statement_data['limit'] = $this->get_limit();
    $showing_statement_data['offset'] = $this->_get_offset();
    $showing_statement_data['total_rows'] = $total_items;
    $data['showing_statement'] = $this->custom_pagination->get_showing_statement($showing_statement_data);

    $data['item_segments'] = $this->site_settings->_get_item_segments();
    $data['currency_symbol'] = $this->site_settings->_get_currency_symbol();
    $data['query'] = $this->_custom_query($mysql_query);
    $data['update_id'] = $update_id;
    $data['flash'] = $this->session->flashdata('item');
    // this module helps to make a friendly URL
    $data['view_module'] = "homepage_blocks";
    $data['view_file'] = "view";
    $this->load->module('templates');
    $this->templates->public_bootstrap($data);
  }

  function get_target_pagination_base_url() {
    $first_bit = $this->uri->segment(1);
    $second_bit = $this->uri->segment(2);
    $third_bit = $this->uri->segment(3);
    $target_base_url = base_url().$first_bit."/".$second_bit."/".$third_bit;
    return $target_base_url;
  }

  function _generate_mysql_query($update_id, $use_limit) {
    // NOTE: use_limit can be true or false
    $mysql_query = "
    SELECT si.item_title, si.item_url, si.item_price, si.small_pic, si.was_price, sc.cat_url
    FROM store_items si JOIN store_cat_assign sca ON sca.item_id = si.id
    JOIN homepage_blocks sc ON sc.id = sca.cat_id
    WHERE sca.cat_id = $update_id
    and si.status = 1
    ";
    if ($use_limit == true) {
      $limit = $this->get_limit();
      $offset = $this->_get_offset();
      $mysql_query.= " LIMIT ".$offset.", ".$limit;
    }
    return $mysql_query;
  }

  // method for pagination
  function get_limit() {
    $limit = 20;
    return $limit;
  }

  function _get_offset() {
    $offset = $this->uri->segment(4);
    if (!is_numeric($offset)) {
      $offset = 0;
    }
    return $offset;
  }
  // method for pagination

  function manage() {
    $this->load->module('site_security');
    $this->site_security->_make_sure_is_admin();

    $data['query'] = $this->get('priority');

    $data['sort_this'] = true;
    // gettinf flash data
    $data['flash'] = $this->session->flashdata('item');
    // getting data from DB

    // create a view file. Putting a php (html) into the admin template.
    $data['view_module'] = "homepage_blocks";
    // store_Items.php
    $data['view_file'] = "manage"; // manage.php
    $this->load->module('templates');
    $this->templates->admin($data);
  }

  function sort() {
    $this->load->module('site_security');
    $this->site_security->_make_sure_is_admin();

    $number = $this->input->post('number', true);

    for ($i = 1; $i <= $number; $i++) {
      $update_id = $_POST['order'.$i];
      $data['priority'] = $i;
      $this->_update($update_id, $data); // updating the DB
      // $info = "The following was posted: ";
      // foreach ($_POST as $key => $value) {
      //   $info.= "key of $key with value of $value";
      // }
      // $data['posted_info'] = $info;
      // $update_id = 10;
      // $this->_update($update_id, $data);

    }
  }

  function create() {
    $this->load->module('site_security');
    $this->site_security->_make_sure_is_admin();

    $update_id = $this->uri->segment(3);
    $submit = $this->input->post('submit', true);

    if ($submit == "Cancel") {
      redirect('homepage_blocks/manage');
    } else if ($submit == "Submit") {
      // process the form
      $this->load->library('form_validation');
      $this->form_validation->set_rules('block_title', 'Offer Block Title', 'required'); // callback is for checking if the homepage offer already exists

      if ($this->form_validation->run() == true) {
        // get the variables and assign into $data variable
        $data = $this->fetch_data_from_post();
        if (is_numeric($update_id)) {
          //update the homepage offer details
          $this->_update($update_id, $data);
          // These two lines show the alert for the successful homepage offer details change.
          $flash_msg = "The homepage offer details were successfully updated.";
          $value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
          $this->session->set_flashdata('homepage offer', $value);
          // add the update data into the URL
          redirect('homepage_blocks/create/'.$update_id);
        } else {
          $this->_insert($data);
          // insert a new homepage offer into DB
          $update_id = $this->get_max(); //get the ID of the new homepage offer
          $this->load->library('session');
          $flash_msg = "The homepage offer was successfully added.";
          $value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
          $this->session->set_flashdata('homepage offer', $value);
          // add the update data into the URL
          redirect('homepage_blocks/create/'.$update_id);
        }
      }
    }

    if ((is_numeric($update_id)) && ($submit != "Submit")) {
      $data = $this->fetch_data_from_db($update_id);
    } else {
      $data = $this->fetch_data_from_post();
    }

    if (!is_numeric($update_id)) {
      $data['headline'] = "Create New Homepage Offer Block";
    } else {
      $block_title = $this->_get_block_title($update_id);
      $data['headline'] = "Update ".$block_title;
    }
    // echo count($data['options']); die();
    // pass update id into the page
    $data['update_id'] = $update_id;
    $data['flash'] = $this->session->flashdata('homepage offer');

    // create a view file. Putting a php (html) into the admin template.

    // store_Items.php
    // $data['view_module'] = "store_Items";
    $data['view_file'] = "create"; // manage.php
    $this->load->module('templates');
    $this->templates->admin($data);
  }

  function deleteconf($update_id) {

    // only those people with an update_id for an item can get in.
    if (!is_numeric($update_id)) {
      redirect('site_security/not_allowed');
    }

    // security
    $this->load->library('session');
    $this->load->module('site_security');
    $this->site_security->_make_sure_is_admin();

    $data['headline'] = "Delete Homepage Offer";
    $data['update_id'] = $update_id;
    $date['flash'] = $this->session->flashdata('item');
    $data['view_file'] = "deleteconf";
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

    $submit = $this->input->post('submit', true);

    if ($submit == "Cancel") {
      redirect('homepage_blocks/create/'.$update_id);
    } else if ($submit == "Delete") {
      // manipulate the DB
      $this->_process_delete($update_id);
      // preparing the flash message after deletion
      $flash_msg = "The offer block was successfully deleted.";
      $value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
      $this->session->set_flashdata('item', $value);

      redirect('homepage_blocks/manage');
    }
  }

  function _process_delete($update_id) {
    // delete any items that are associated with this offer block
    $mysql_query = "DELETE FROM homepage_offers WHERE block_id = $update_id";
    $query = $this->_custom_query($mysql_query);

    // delete the blog record from blog
    $this->_delete($update_id);
  }

  // get data from POST method
  function fetch_data_from_post() {
    $data['block_title'] = $this->input->post('block_title', true);
    return $data;
  }

  // get data from database
  function fetch_data_from_db($update_id) {

    if (!is_numeric($update_id)) {
      redirect('site_security/not_allowed');
    }

    $query = $this->get_where($update_id);
    foreach($query->result() as $row) {
      $data['block_title'] = $row->block_title;
    }
    if (!isset($data)) {
      $data = "";
    }
    return $data;
  }

  function get($order_by)
  {
    $this->load->model('mdl_homepage_blocks');
    $query = $this->mdl_homepage_blocks->get($order_by);
    return $query;
  }

  function _get_block_title($update_id) {
    $data = $this->fetch_data_from_db($update_id);
    $block_title = $data['block_title'];
    echo $block_title;
    return $block_title;
  }

  function _get_cat_title($update_id) {
    $data = $this->fetch_data_from_db($update_id);
    $cat_title = $data['cat_title'];
    return $cat_title;
  }

  function get_with_limit($limit, $offset, $order_by)
  {
    if ((!is_numeric($limit)) || (!is_numeric($offset))) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_homepage_blocks');
    $query = $this->mdl_homepage_blocks->get_with_limit($limit, $offset, $order_by);
    return $query;
  }

  function get_where($id)
  {
    if (!is_numeric($id)) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_homepage_blocks');
    $query = $this->mdl_homepage_blocks->get_where($id);
    return $query;
  }

  function get_where_custom($col, $value)
  {
    $this->load->model('mdl_homepage_blocks');
    $query = $this->mdl_homepage_blocks->get_where_custom($col, $value);
    return $query;
  }

  function _insert($data)
  {
    $this->load->model('mdl_homepage_blocks');
    $this->mdl_homepage_blocks->_insert($data);
  }

  function _update($id, $data)
  {
    if (!is_numeric($id)) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_homepage_blocks');
    $this->mdl_homepage_blocks->_update($id, $data);
  }

  function _delete($id)
  {
    if (!is_numeric($id)) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_homepage_blocks');
    $this->mdl_homepage_blocks->_delete($id);
  }

  function count_where($column, $value)
  {
    $this->load->model('mdl_homepage_blocks');
    $count = $this->mdl_homepage_blocks->count_where($column, $value);
    return $count;
  }

  function get_max()
  {
    $this->load->model('mdl_homepage_blocks');
    $max_id = $this->mdl_homepage_blocks->get_max();
    return $max_id;
  }

  function _custom_query($mysql_query)
  {
    $this->load->model('mdl_homepage_blocks');
    $query = $this->mdl_homepage_blocks->_custom_query($mysql_query);
    return $query;
  }

}
