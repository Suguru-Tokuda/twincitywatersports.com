<?php
class Listed_items extends MX_Controller {

  function __construct() {
    parent::__construct();
    $this->load->library('form_validation');
    $this->load->library('upload');
    $this->load->library('image_lib');
    $this->form_validation->set_ci($this);
  }

  function _draw_listed_items() {
      $user_id = $this->site_security->_get_user_id();

      $mysql_query = "
      SELECT si.item_title, si.id, si.item_url, si.item_price, si.status FROM
      store_items si JOIN store_accounts sa ON si.user_id = sa.id
      WHERE user_id = $user_id
      ";

      $data['item_segments'] = $this->site_settings->_get_item_segments();
      $data['currency_symbol'] = $this->site_settings->_get_currency_symbol();
      $data['query'] = $this->_custom_query($mysql_query);
      $data['flash'] = $this->session->flashdata('item');
      $this->load->view('manage', $data);
    }

    function sort() {
      $this->load->module('site_security');
      $this->load->module('small_pics');
      $this->site_security->_make_sure_logged_in();

      $number = $this->input->post('number', true);
      for ($i = 1; $i <= $number; $i++) {
        $small_pic_id = $_POST['order'.$i];
        $data['priority'] = $i;
        $this->small_pics->_update($small_pic_id, $data);
      }
    }

    function sort_test() {
      $item_id = 30;
      $mysql_query = "SELECT * FROM small_pics WHERE item_id = $item_id";
      $query = $this->_custom_query($mysql_query);
      $data['query'] = $query;
      $data['num_rows'] = $query->num_rows();
      $data['sort_this'] = true;
      $data['view_file'] = "sort_test";
      $this->load->module('templates');
      $this->templates->public_bootstrap($data);
    }

    function manage() {
      //security
      $this->load->module('site_security');
      $this->load->module('site_settings');
      $this->load->module('custom_pagination');
      $this->site_security->_make_sure_logged_in();

      $user_id = $this->site_security->_get_user_id();

      $mysql_query = "
      SELECT si.item_title, si.id, si.item_url, si.item_price, si.status FROM
      store_items si JOIN store_accounts sa ON si.user_id = sa.id
      WHERE user_id = $user_id
      ";

      $query = $this->_custom_query($mysql_query);
      $total_items = $query->num_rows();
      // $total_items = false;

      $pagination_data['template'] = "public_bootstrap";
      $pagination_data['target_base_url'] = $this->get_target_pagination_base_url();
      $pagination_data['total_rows'] = $total_items;
      $pagination_data['offset_segment'] = 4;
      $pagination_data['limit'] = $this->get_limit();
      $data['pagination'] = $this->custom_pagination->_generate_pagination($pagination_data);

      $data['item_segments'] = $this->site_settings->_get_item_segments();
      $data['currency_symbol'] = $this->site_settings->_get_currency_symbol();
      $data['query'] = $this->_custom_query($mysql_query);
      $data['flash'] = $this->session->flashdata('item');
      $data['view_file'] = "manage";
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
      SELECT si.item_title, si.id, si.item_url, si.item_price, si.status FROM
      store_items si JOIN store_accounts sa ON si.user_id = sa.id
      WHERE user_id = $user_id
      ";
      if ($use_limit == true) {
        $limit = $this->get_limit();
        $offset = $this->_get_offset();
        $mysql_query.= " LIMIT ".$offset.", ".$limit;
      }
      return $mysql_query;
    }

    function upload_image($item_url) {
      // security
      $this->load->module('site_security');
      $this->site_security->_make_sure_logged_in();

      // only those people with an update_id for an item can get in.
      $this->load->module('store_items');
      $item_id = $this->store_items->_get_item_id_from_item_url($item_url);
      if (!is_numeric($item_id)) {
        redirect('site_security/not_allowed');
      }
      // security
      $this->load->module('site_security');
      $this->site_security->_make_sure_logged_in();

      $mysql_query = "SELECT * FROM small_pics WHERE item_id = $item_id ORDER BY priority ASC";
      $query = $this->_custom_query($mysql_query);
      $data['query'] = $query;
      $data['num_rows'] = $query->num_rows(); // number of pictures that an item has
      $data['headline'] = "Manage Image";
      $data['item_id'] = $item_id;
      $date['flash'] = $this->session->flashdata('item');
      $data['view_file'] = "upload_image";
      $data['sort_this'] = true;
      $this->load->module('templates');
      $this->templates->public_bootstrap($data);
    }

    function do_upload($item_url) {
      // security
      $this->load->module('site_security');
      $this->load->module('store_items');
      $this->site_security->_make_sure_logged_in();

      $item_id = $this->store_items->_get_item_id_from_item_url($item_url);
      // code from store items

      // getting submit from the post
      $submit = $this->input->post('submit', true);
      if ($submit == "cancel") {
        redirect('listed_items/create_item/'.$item_url);
      } else if ($submit == "upload") {
        $config['upload_path'] = './big_pics/';
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size'] = 300;
        $config['max_width'] = 3036;
        $config['max_height'] = 1902;
        $file_name = $this->site_security->generate_random_string(16);
        $config['file_name'] = $file_name;
        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('userfile')) {
          $mysql_query = "SELECT * FROM small_pics WHERE item_id = $item_id";
          $query = $this->_custom_query($mysql_query);
          $data['query'] = $query;
          $data['num_rows'] = $query->num_rows();
          $data['error'] = array('error' => $this->upload->display_errors("<p style='color: red;'>", "</p>"));
          $data['headline'] = "Upload Error";
          $data['item_id'] = $item_id;
          $date['flash'] = $this->session->flashdata('item');
          $data['view_file'] = "upload_image";
          $this->load->module('templates');
          $this->templates->public_bootstrap($data);
        } else {

          // upload was successful
          $data = array('upload_data' => $this->upload->data());
          $upload_data = $data['upload_data'];

          $file_ext = $upload_data['file_ext'];
          $file_name = $file_name.$file_ext;
          // $file_name = $upload_data['file_name'];
          $this->_generate_thumbnail($file_name);

          //update the database
          $priority = $this->_get_priority($item_id);
          $mysql_query = "INSERT INTO small_pics (item_id, picture_name, priority) VALUES ($item_id, '$file_name', $priority)";
          $this->_custom_query($mysql_query);

          $small_pic_id = $this->_get_small_pic_id($item_id, $priority);
          $mysql_query = "INSERT INTO big_pics (small_pic_id, picture_name) VALUES ($small_pic_id, '$file_name')";
          $this->_custom_query($mysql_query);

          $data['headline'] = "Upload Success";
          $data['item_id'] = $item_id;
          $flash_msg = "The picture was successfully uploaded.";
          $value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
          $this->session->set_flashdata('item', $value);

          // $date['flash'] = $this->session->flashdata('item');
          redirect(base_url()."/listed_items/upload_image/".$item_url);
          // $data['view_file'] = "upload_success";
          // $this->load->module('templates');
          // $this->templates->public_bootstrap($data);
        }
      }
    }

    function delete_image() {
      $this->load->module('site_security');
      $this->load->module('store_items');
      $this->load->module('small_pics');
      $this->load->module('big_pics');

      $item_url = $this->uri->segment(3);
      $small_pic_id = $this->uri->segment(4);
      $this->site_security->_make_sure_logged_in();
      $item_id = $this->store_items->_get_item_id_from_item_url($item_url);

      $picture_name = $this->small_pics->_get_picture_name_by_small_pic_id($small_pic_id);
      $big_pic_path = './big_pics/'.$picture_name;
      $small_pic_path = './small_pics/'.$picture_name;
      // attemp to delete item small pics
      if (file_exists($big_pic_path)) {
        unlink($big_pic_path);
      }
      if (file_exists($small_pic_path)) {
        unlink($small_pic_path);
      }
      // delete every big pic that is linked to a small pic
      $this->big_pics->_delete_where('small_pic_id', $small_pic_id);

      // reassign priority
      $priority_for_deleted_pic = $this->small_pics->get_priority_for_item($small_pic_id, $item_id);
      // delete small and big pics
      $this->small_pics->_delete($small_pic_id);
      $query = $this->small_pics->get_where_custom('item_id', $item_id);
      foreach ($query->result() as $row) {
        if ($row->priority > $priority_for_deleted_pic) {
          $new_priority = $row->priority - 1;
          $data['priority'] = $new_priority;
          $this->small_pics->_update($row->id, $data);
        }
      }
      $flash_msg = "The image was successfully deleted.";
      $value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
      $this->session->set_flashdata('item', $value);
      redirect(base_url()."/listed_items/upload_image/".$item_url);
    }

    function _get_priority($item_id) {
      $mysql_query = "SELECT * FROM small_pics WHERE item_id = $item_id ORDER BY priority DESC LIMIT 1";
      $query = $this->_custom_query($mysql_query);
      if ($query->num_rows() == 1) {
        foreach ($query->result() as $row) {
          $priority = $row->priority + 1;
        }
      } else {
        $priority = 1;
      }
      return $priority;
    }

    function _get_small_pic_id($item_id, $priority) {
      $mysql_query = "SELECT id FROM small_pics WHERE item_id = $item_id AND priority = $priority";
      $query = $this->_custom_query($mysql_query);
      foreach($query->result() as $row) {
        $small_pic_id = $row->id;
      }
      return $small_pic_id;
    }

    function _generate_thumbnail($file_name) {

      $config['image_library'] = 'gd2';
      $config['source_image'] = './big_pics/'.$file_name;
      $config['new_image'] = './small_pics/'.$file_name;
      // $config['create_thumb'] = true;
      $config['maintain_ratio'] = true;
      $config['width'] = 200;
      $config['height'] = 200;

      $this->load->library('image_lib', $config);
      $this->image_lib->resize();
    }

    function create_item() {
      //security
      $this->load->module('site_security');
      $this->load->module('site_settings');
      $this->load->module('store_items');
      $this->site_security->_make_sure_logged_in();

      $submit = $this->input->post("submit", true);
      $user_id = $this->site_security->_get_user_id();

      $item_url = $this->uri->segment(3);
      if (isset($item_url)) {
        $item_id = $this->store_items->_get_item_id_from_item_url($item_url);
      } else {
        $item_id = "";
      }

      if ($submit == "cancel") {
        redirect('listed_items/manage');
      } else if ($submit == "submit") {
        $status = $this->input->post('status', true);
        // do validation
        $this->form_validation->set_rules('item_title', 'Item Title', 'required|max_length[240]');
        $this->form_validation->set_rules('item_price', 'Item Price', 'required|numeric');
        $this->form_validation->set_rules('categories[]', 'Categories', 'required');
        if (isset($item_id)) {
          $this->form_validation->set_rules('status', 'Status', 'required');
        }
        $this->form_validation->set_rules('item_description', 'Item Description', 'required');
        $this->form_validation->set_rules('city', 'City', 'required');
        $this->form_validation->set_rules('state', 'State', 'required');

        if ($this->form_validation->run() == true) {
          // get info from the post
          $data = $this->fetch_data_from_post();
          if (is_numeric($item_id)) {
            // update
            unset($data['categories']);

            $this->store_items->_update($item_id, $data);
            // for categories, need to delete the entry for the item id and re-insert
            $this->load->module('store_cat_assign');
            $this->store_cat_assign->_custom_delete('item_id', $item_id);
            // get categories indexes
            $categories_from_post = $this->input->post('categories[]', true);
            // get categories array
            $categories = $this->_get_categories();
            for ($i = 0; $i < count($categories_from_post); $i++) {
              $cat_assign_data['cat_id'] = $categories_from_post[$i];
              $cat_assign_data['item_id'] = $item_id;
              $this->store_cat_assign->_insert($cat_assign_data);
            }
            $item_id = $this->store_items->_get_item_id_from_item_url($item_url);
            $flash_msg = "The item details were successfully updated.";
            $value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
            $this->session->set_flashdata('item', $value);
            redirect('listed_items/create_item/'.$item_url);
          } else {
            // insert into item store_items table
            $code = $this->site_security->generate_random_string(6);
            $item_url = url_title($data['item_title']).$code;
            $data['item_url'] = $item_url;
            $data['user_id'] = $user_id;
            $data['status'] = 1;
            unset($data['categories']);
            $data['date_made'] = time();
            $this->store_items->_insert($data);
            // right after insert, get user_id from DB
            $item_id = $this->store_items->_get_item_id_from_item_url($item_url);
            // insert into store_cat_assign table
            $categories_from_post = $this->input->post('categories[]', true);
            $this->load->module('store_cat_assign');
            // $categories = $this->_get_categories();
            for ($i = 0; $i < count($categories_from_post); $i++) {
              $cat_assign_data['cat_id'] = $categories_from_post[$i];
              $cat_assign_data['item_id'] = $item_id;
              $this->store_cat_assign->_insert($cat_assign_data);
            }
            $this->load->library('session');
            $flash_msg = "The item was successfully added.";
            $value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
            $this->session->set_flashdata('item', $value);
            redirect('listed_items/manage');
          }
        }
      }
      // this is for updating
      if ((is_numeric($item_id)) && ($submit != "submit")) {
        // retrieving data from DB
        $data = $this->fetch_data_from_db($item_id);
      } else {
        $data = $this->fetch_data_from_post();
      }
      if (!is_numeric($item_id)) {
        $data['headline'] = "Add New Item";
      } else {
        $data['headline'] = "Update Item Details";
      }
      // if the user is logged in, sends to the page to create an item
      $data['categories_options'] = $this->_get_categories();
      $data['states'] = $this->site_settings->_get_states_dropdown();
      $data['item_id'] = $item_id;
      $data['flash'] = $this->session->flashdata('item');
      $data['view_file'] = "create_item";
      $this->load->module('templates');
      $this->templates->public_bootstrap($data);
    }

    function _get_categories() {
      $mysql_query = "SELECT * FROM store_categories WHERE parent_cat_id != 0 ORDER BY id";
      $query = $this->_custom_query($mysql_query);

      $count = 0;
      foreach ($query->result() as $row) {
        $categories[$count] = $row->id;
        $count++;
      }
      return $categories;
    }

    function _check_for_category($item_id, $cat_id) {
      // echo "item_id: $item_id, cat_id: $cat_id"; die();
      $mysql_query = "SELECT * FROM store_cat_assign WHERE item_id = ? AND cat_id = ?";
      $query = $this->db->query($mysql_query, array($item_id, $cat_id));
      $num_rows = $query->num_rows();
      // echo "num_rows: ".$num_rows; die();
      if ($num_rows > 0) {
        return 'checked="true"';
      } else if ($num_rows == 0) {
        return "";
      }
    }

    function deleteconf($item_url) {
      // only those people with an item_id for an item can get in.
      $this->load->module('store_items');
      $item_id = $this->store_items->_get_item_id_from_item_url($item_url);

      if (!is_numeric($item_id)) {
        redirect('site_security/not_allowed');
      }
      // security
      $this->load->module('site_security');
      $this->site_security->_make_sure_logged_in();

      $data['item_url'] = $item_url;
      $data['headline'] = "Delete Item";
      $data['item_id'] = $item_id;
      $date['flash'] = $this->session->flashdata('item');
      $data['view_file'] = "deleteconf";
      $this->load->module('templates');
      $this->templates->public_bootstrap($data);
    }

    function _process_delete($item_id) {
      // attempt to delete item colors
      $this->load->module("store_item_colors");
      $this->store_item_colors->_delete_for_item($item_id);
      // attempt to delete item sizes
      $this->load->module("store_item_sizes");
      $this->store_item_sizes->_delete_for_item($item_id);
      // attempt to delete item big & small pics

      $data = $this->fetch_data_from_db($item_id);

      $this->load->module('small_pics');
      $this->load->module('big_pics');
      $small_pic_ids = $this->small_pics->get_small_pic_ids_by_item_id($item_id);

      foreach ($small_pic_ids as $key => $value) {
        $picture_name = $this->small_pics->_get_picture_name_by_small_pic_id($value);
        $big_pic_path = './big_pics/'.$picture_name;
        $small_pic_path = './small_pics/'.$picture_name;
        // attemp to delete item small pics
        if (file_exists($big_pic_path)) {
          unlink($big_pic_path);
        }
        if (file_exists($small_pic_path)) {
          unlink($small_pic_path);
        }
        // delete every big pic that is linked to a small pic
        $this->big_pics->_delete_where('small_pic_id', $value);
      }

      // then delete all the small pics for the item ID
      $this->small_pics->_delete_where('item_id', $item_id);
      $this->load->module('store_items');
      // delete the item record from store_items
      $this->store_items->_delete($item_id);
    }

    function delete($item_url) {
      // only those people with an item_id for an item can get in.
      $this->load->module('store_items');
      $item_id = $this->store_items->_get_item_id_from_item_url($item_url);

      if (!is_numeric($item_id)) {
        redirect('site_security/not_allowed');
      }
      // security
      $this->load->module('site_security');
      $this->site_security->_make_sure_logged_in();

      $submit = $this->input->post('submit', true);

      if ($submit == "cancel") {
        redirect('listed_items/create_item/'.$item_url);
      } else if ($submit == "delete") {
        // manipulate the DB
        $this->_process_delete($item_id);
        // preparing the flash message after deletion
        $flash_msg = "The item was successfully deleted.";
        $value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
        $this->session->set_flashdata('item', $value);
        redirect('listed_items/manage');
      }
    }

    function get_item_id_from_item_url($item_url) {
      $this->load->module('store_items');

      $mysql_query = "
      SELECT item_id FROM store_items WHERE item_url = '$item_url'
      ";
      $query = $this->store_items->_custom_query($mysql_query);
      foreach($query->result() as $row) {
        $item_id = $row->item_id;
      }
      if ($item_id == '') {
        $item_id = '';
      }
      return $item_id;
    }

    function fetch_data_from_post() {
      $data['item_title'] = $this->input->post('item_title', true);
      $data['item_price'] = $this->input->post('item_price', true);
      $data['item_description'] = $this->input->post('item_description', true);
      $data['categories'] = $this->input->post('categories[]', true);
      $data['city'] = $this->input->post('city', true);
      $data['status'] = $this->input->post('status', true);
      $this->load->module('site_settings');
      $states = $this->site_settings->_get_states_dropdown();
      $state_index = $this->input->post('state', true);
      $data['state'] = $states[$state_index];
      return $data;
    }

    function fetch_categories_from_post() {
      $categories = $this->input->post('categories[]', true);
      for($i = 0; $i < count($categories); $i++){
        $data['categories'];
      }
      return $data;
    }

    function fetch_data_from_db($item_id) {
      //security
      $this->load->module('site_security');
      $this->load->module('store_items');
      $this->site_security->_make_sure_logged_in();

      if (!is_numeric($item_id)) {
        redirect(base_url());
      }

      $mysql_query = "
      SELECT * FROM
      store_items si
      WHERE si.id = $item_id
      ";

      $query = $this->store_items->_custom_query($mysql_query);

      foreach ($query->result() as $row) {
        $data['item_title'] = $row->item_title;
        $data['item_url'] = $row->item_url;
        $data['item_price'] = $row->item_price;
        $data['item_description'] = $row->item_description;
        $data['status'] = $row->status;
        $data['city'] = $row->city;
        $data['state'] = $row->state;
        $data['date_made'] = $row->date_made;
      }
      return $data;
    }

    function get($order_by) {
      $this->load->model('mdl_listed_items');
      $query = $this->mdl_listed_items->get($order_by);
      return $query;
    }

    function get_with_limit($limit, $offset, $order_by) {
      if ((!is_numeric($limit)) || (!is_numeric($offset))) {
        die('Non-numeric variable!');
      }

      $this->load->model('mdl_listed_items');
      $query = $this->mdl_listed_items->get_with_limit($limit, $offset, $order_by);
      return $query;
    }

    function get_where($id) {
      if (!is_numeric($id)) {
        die('Non-numeric variable!');
      }

      $this->load->model('mdl_listed_items');
      $query = $this->mdl_listed_items->get_where($id);
      return $query;
    }

    function get_where_custom($col, $value) {
      $this->load->model('mdl_listed_items');
      $query = $this->mdl_listed_items->get_where_custom($col, $value);
      return $query;
    }

    function _insert($data) {
      $this->load->model('mdl_listed_items');
      $this->mdl_listed_items->_insert($data);
    }

    function _update($id, $data) {
      if (!is_numeric($id)) {
        die('Non-numeric variable!');
      }

      $this->load->model('mdl_listed_items');
      $this->mdl_listed_items->_update($id, $data);
    }

    function _delete($id) {
      if (!is_numeric($id)) {
        die('Non-numeric variable!');
      }

      $this->load->model('mdl_listed_items');
      $this->mdl_listed_items->_delete($id);
    }

    function count_where($column, $value) {
      $this->load->model('mdl_listed_items');
      $count = $this->mdl_listed_items->count_where($column, $value);
      return $count;
    }

    function get_max() {
      $this->load->model('mdl_listed_items');
      $max_id = $this->mdl_listed_items->get_max();
      return $max_id;
    }

    function _custom_query($mysql_query) {
      $this->load->model('mdl_listed_items');
      $query = $this->mdl_listed_items->_custom_query($mysql_query);
      return $query;
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

  }
