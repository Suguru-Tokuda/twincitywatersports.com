<?php
class Store_categories extends MX_Controller {

  function __construct() {
    parent::__construct();
    $this->load->library('form_validation');
    $this->form_validation->set_ci($this);
  }

  function text() {
    $users['han'] = 42;
    $users['luke'] = 32;
    $users['yoda'] = 900;
    $users['chewie'] = 200;
    $users['zabadee'] = 12;

    echo "<h1>Who is the oldeset?</h1>";
    $oldest_user = $this->get_oldest_user($users);
    echo $oldest_user;
  }

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

  function _get_cat_id_from_cat_url($cat_url) {
    $query = $this->get_where_custom('cat_url', $cat_url);
    foreach($query->result() as $row) {
      $cat_id = $row->id;
    }
    if (!isset($cat_id)) {
      $cat_id = 0;
    }
    return $cat_id;
  }

  function _get_full_cat_url($update_id) {
    $this->load->module('site_settings');
    $items_segments = $this->site_settings->_get_items_segments();
    $data = $this->fetch_data_from_db($update_id);
    $cat_url = $data['cat_url'];
    $full_cat_url = base_url().$items_segments.$cat_url;
    return $full_cat_url;
  }

  function _get_parent_cat_url($cat_id) {
    $this->load->module('site_settings');
    $data = $this->fetch_data_from_db($cat_id);
    $parent_cat_id = $this->get_parent_cat_id_by_cat_id($cat_id);
    $parent_cat_url = $this->parent_cat_url_by_cat_id($parent_cat_id);
    return $parent_cat_url;
  }

  function view($update_id) {
    // only those people with an update_id for an item can get in.
    if (!is_numeric($update_id)) {
      redirect('site_security/not_allowed');
    }
    $this->load->module('site_settings');
    $this->load->module('custom_pagination');

    // fetch the category details
    $data = $this->fetch_data_from_db($update_id);

    // count the items that belong to this category
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
    $data['view_module'] = "store_categories";
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

  function _get_picture_name_by_item_url($item_url) {
    $mysql_query = "
    SELECT sp.picture_name FROM small_pics sp
    JOIN store_items si ON sp.item_id = si.id
    WHERE si.item_url = ?
    AND sp.priority = 1
    ";
    $query = $this->db->query($mysql_query, array($item_url));
    foreach ($query->result() as $row) {
      $picture_name = $row->picture_name;
    }
    if (!isset($picture_name)) {
      $picture_name = '';
    }
    return $picture_name;
  }

  function _generate_mysql_query($update_id, $use_limit) {
    // NOTE: use_limit can be true or false
    $mysql_query = "
    SELECT si.item_title, si.item_url, si.item_price, si.small_pic, si.was_price, sc.cat_url
    FROM store_items si
    JOIN store_cat_assign sca ON sca.item_id = si.id
    JOIN store_categories sc ON sc.id = sca.cat_id
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

    $parent_cat_id = $this->uri->segment(3);
    if (!is_numeric($parent_cat_id)) {
      $parent_cat_id = 0;
    }

    $data['sort_this'] = true;
    $data['parent_cat_id'] = $parent_cat_id;
    // gettinf flash data
    $data['flash'] = $this->session->flashdata('item');

    // getting data from DB
    // this means order by category_title
    $data['query'] = $this->get_where_custom('parent_cat_id', $parent_cat_id);

    // create a view file. Putting a php (html) into the admin template.
    $data['view_module'] = "store_categories";
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

  function _draw_top_nav() {
    $mysql_query = "SELECT * FROM store_categories WHERE parent_cat_id = 0 ORDER BY priority";
    $query = $this->_custom_query($mysql_query);
    foreach($query->result() as $row) {
      $parent_categories[$row->id] = $row->cat_title;
    }
    $this->load->module('site_settings');
    $items_segments = $this->site_settings->_get_items_segments();
    $data['target_url_start'] = base_url().$items_segments;
    $data['parent_categories'] = $parent_categories;
    $this->load->view('top_nav', $data);
  }

  function _draw_sortable_list($parent_cat_id) {
    $mysql_query = "SELECT * FROM store_categories WHERE parent_cat_id = $parent_cat_id ORDER BY priority";
    $data['query'] = $this->_custom_query($mysql_query);
    $this->load->view('sortable_list', $data);
  }

  function _get_all_sub_cats_for_dropdown() {
    // note: this gets used on store_cat_assign
    $mysql_query = "SELECT * FROM store_categories WHERE parent_cat_id != 0 ORDER BY parent_cat_id";
    $query = $this->_custom_query($mysql_query);
    foreach($query->result() as $row) {
      $parent_cat_title = $this->_get_cat_title($row->parent_cat_id);
      $sub_categories[$row->id] = $parent_cat_title." > ".$row->cat_title;
    }
    if (!isset($sub_categories)) {
      $sub_categories = "";
    }
    return $sub_categories;
  }

  function _get_parent_cat_title($update_id) {
    $data = $this->fetch_data_from_db($update_id);
    $parent_cat_id = $data['parent_cat_id'];
    $parent_cat_title = $this->_get_cat_title($parent_cat_id);
    return $parent_cat_title;
  }

  function create() {
    $this->load->module('site_security');
    $this->site_security->_make_sure_is_admin();

    $update_id = $this->uri->segment(3);
    $submit = $this->input->post('submit', true);

    if ($submit == "Cancel") {
      redirect('store_categories/manage');
    } else if ($submit == "Submit") {
      // process the form
      $this->load->library('form_validation');
      $this->form_validation->set_rules('cat_title', 'Category Title', 'required'); // callback is for checking if the category already exists

      if ($this->form_validation->run() == true) {
        // get the variables and assign into $data variable
        $data = $this->fetch_data_from_post();
        if ($data['cat_title'])
        $data['cat_url'] = url_title(str_replace("-", "_", $data['cat_title']));
        if (is_numeric($update_id)) {
          //update the category details
          $this->_update($update_id, $data);
          // These two lines show the alert for the successful category details change.
          $flash_msg = "The category details were successfully updated.";
          $value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
          $this->session->set_flashdata('category', $value);
          // add the update data into the URL
          redirect('store_categories/create/'.$update_id);
        } else {
          $this->_insert($data);
          // insert a new category into DB
          $update_id = $this->get_max(); //get the ID of the new category
          $this->load->library('session');
          $flash_msg = "The category was successfully added.";
          $value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
          $this->session->set_flashdata('category', $value);
          // add the update data into the URL
          redirect('store_categories/create/'.$update_id);
        }
      }
    }

    if ((is_numeric($update_id)) && ($submit != "Submit")) {
      $data = $this->fetch_data_from_db($update_id);
    } else {
      $data = $this->fetch_data_from_post();
    }

    if (!is_numeric($update_id)) {
      $data['headline'] = "Add New Category";
    } else {
      $data['headline'] = "Update Category Details";
    }

    $data['options'] = $this->_get_dropdown_options($update_id);
    $data['num_dropdown_options'] = count($data['options']);
    // echo count($data['options']); die();
    // pass update id into the page
    $data['update_id'] = $update_id;
    $data['flash'] = $this->session->flashdata('category');

    // create a view file. Putting a php (html) into the admin template.

    // store_Items.php
    // $data['view_module'] = "store_Items";
    $data['view_file'] = "create"; // manage.php
    $this->load->module('templates');
    $this->templates->admin($data);
  }

  function _get_cat_title($update_id) {
    $data = $this->fetch_data_from_db($update_id);
    $cat_title = $data['cat_title'];
    return $cat_title;
  }

  function _count_sub_cats($update_id) {
    // return the number of sub categories, belonging to THIS category
    $query = $this->get_where_custom('parent_cat_id', $update_id);
    $num_rows = $query->num_rows();
    return $num_rows;
  }

  function _get_dropdown_options($update_id) {
    // build an array of all the parent categories
    if (!is_numeric($update_id)) {
      $update_id = 0;
    }
    $options[''] = 'Please Select...';

    $mysql_query = "SELECT * FROM store_categories WHERE parent_cat_id = 0 AND id != $update_id";
    $query = $this->_custom_query($mysql_query);
    foreach ($query->result() as $row) {
      $options[$row->id] = $row->cat_title;
    }
    return $options;
  }

  function get_cat_id_by_item_id($item_id) {
    $mysql_query = "SELECT * FROM store_cat_assign sca WHERE sca.item_id = ?";
    $query = $this->db->query($mysql_query, array($item_id));
    $cat_id = 0;
    foreach($query->result() as $row) {
      $cat_id = $row->cat_id;
    }
    return $cat_id;
  }

  function get_parent_cat_id_by_cat_id($cat_id) {
    $mysql_query = "SELECT parent_cat_id FROM store_categories WHERE id = ?";
    $query = $this->db->query($mysql_query, array($cat_id));
    $parent_cat_id = 0;
    foreach($query->result() as $row) {
      $parent_cat_id = $row->parent_cat_id;
    }
    return $parent_cat_id;
  }

  function parent_cat_url_by_cat_id($cat_id) {
    $mysql_query = "SELECT cat_url FROM store_categories WHERE id = ?";

    $query = $this->db->query($mysql_query, array($cat_id));
    $parent_cat_url = "";
    foreach($query->result() as $row) {
      $parent_cat_url = $row->cat_url;
    }
    return $parent_cat_url;
  }

  // get data from POST method
  function fetch_data_from_post() {
    $data['cat_title'] = $this->input->post('cat_title', true);
    $data['parent_cat_id'] = $this->input->post('parent_cat_id', true);
    return $data;
  }

  // get data from database
  function fetch_data_from_db($update_id) {
    if (!is_numeric($update_id)) {
      redirect('site_security/not_allowed');
    }

    $query = $this->get_where($update_id);
    foreach($query->result() as $row) {
      $data['cat_title'] = $row->cat_title;
      $data['cat_url'] = $row->cat_url;
      $data['parent_cat_id'] = $row->parent_cat_id;
    }
    if (!isset($data)) {
      $data = "";
    }
    return $data;
  }

  function _get_cat_title_by_id($id) {
    $mysql_query = "SELECT cat_title FROM store_categories WHERE id = $id";
    $query = $this->_custom_query($mysql_query);
    foreach ($query->result() as $row) {
      $cat_title = $row->cat_title;
    }
    return $cat_title;
  }

  function get($order_by)
  {
    $this->load->model('mdl_store_categories');
    $query = $this->mdl_store_categories->get($order_by);
    return $query;
  }

  function get_with_limit($limit, $offset, $order_by)
  {
    if ((!is_numeric($limit)) || (!is_numeric($offset))) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_store_categories');
    $query = $this->mdl_store_categories->get_with_limit($limit, $offset, $order_by);
    return $query;
  }

  function get_where($id)
  {
    if (!is_numeric($id)) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_store_categories');
    $query = $this->mdl_store_categories->get_where($id);
    return $query;
  }

  function get_where_custom($col, $value)
  {
    $this->load->model('mdl_store_categories');
    $query = $this->mdl_store_categories->get_where_custom($col, $value);
    return $query;
  }

  function _insert($data)
  {
    $this->load->model('mdl_store_categories');
    $this->mdl_store_categories->_insert($data);
  }

  function _update($id, $data)
  {
    if (!is_numeric($id)) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_store_categories');
    $this->mdl_store_categories->_update($id, $data);
  }

  function _delete($id)
  {
    if (!is_numeric($id)) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_store_categories');
    $this->mdl_store_categories->_delete($id);
  }

  function count_where($column, $value)
  {
    $this->load->model('mdl_store_categories');
    $count = $this->mdl_store_categories->count_where($column, $value);
    return $count;
  }

  function get_max()
  {
    $this->load->model('mdl_store_categories');
    $max_id = $this->mdl_store_categories->get_max();
    return $max_id;
  }

  function _custom_query($mysql_query)
  {
    $this->load->model('mdl_store_categories');
    $query = $this->mdl_store_categories->_custom_query($mysql_query);
    return $query;
  }

}
