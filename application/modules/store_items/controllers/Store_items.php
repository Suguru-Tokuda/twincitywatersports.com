<?php
class Store_items extends MX_Controller
{

  function __construct() {
    parent::__construct();

    // These two lines are needed to display custom validation messages
    $this->load->library('form_validation');
    $this->load->module('custom_pagination');
    $this->form_validation->set_ci($this);
  }

  function search() {
    $this->load->module('site_security');
    $this->load->module('site_settings');
    $submit = $this->input->post('submit', true);

    $searchKeywords = $this->input->post('searchKeywords', true);
    $searchKeywords = trim($searchKeywords);
    $searchKeywords = explode(" ", $searchKeywords);

    if ($submit == "submit") {
      $mysqlQuery = "SELECT si.id, si.item_url, si.item_price, si.item_title, si.was_price, sp.picture_name FROM store_items si LEFT JOIN small_pics sp ON si.id = sp.item_id WHERE item_title LIKE '%$searchKeywords[0]%' OR item_description LIKE '%$searchKeywords[0]%'";

      if (sizeOf($searchKeywords) > 1) {
        for ($i = 1; $i < sizeOf($searchKeywords); $i++) {
          $mysqlQuery.= " OR item_title LIKE '%$searchKeywords[$i]%' OR item_description LIKE '%$searchKeywords[$i]%'";
        }
      }

      $storeItemsQuery = $this->_custom_query($mysqlQuery);

      $total_items = $storeItemsQuery->num_rows();

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

      $keywords = "";
      foreach($searchKeywords as $value) {
        $keywords.= $value." ";
      }

      $data['keywords'] = "<b>Keywords</b>: ".$keywords;
      $data['currency_symbol'] = $this->site_settings->_get_currency_symbol();
      $data['view_module'] = "store_categories";
      $data['view_file'] = "search_view";
      $data['query'] = $storeItemsQuery;
      $this->load->module('templates');
      $this->templates->public_bootstrap($data);
    }
  }

  // method for pagination
  function get_target_pagination_base_url() {
    $first_bit = $this->uri->segment(1);
    $second_bit = $this->uri->segment(2);
    $third_bit = $this->uri->segment(3);
    $target_base_url = base_url().$first_bit."/".$second_bit."/".$third_bit;
    return $target_base_url;
  }

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

  function sort() {
    $this->load->module('site_security');
    $this->load->module('small_pics');
    $this->site_security->_make_sure_is_admin();

    $number = $this->input->post('number', true);
    for ($i = 1; $i <= $number; $i++) {
      $small_pic_id = $_POST['order'.$i];
      $data['priority'] = $i;
      $this->small_pics->_update($small_pic_id, $data);
    }
  }

  function _get_item_id_from_item_url($item_url) {
    $query = $this->get_where_custom('item_url', $item_url);
    foreach($query->result() as $row) {
      $update_id = $row->id;
    }
    if (!isset($update_id)) {
      $update_id = 0;
    }
    return $update_id;
  }

  function _get_cat_url_by_item_url($item_url) {
    $mysql_query = "
    SELECT sc.cat_url FROM
    store_items si JOIN store_cat_assign sca ON si.id = sca.item_id
    JOIN store_categories sc ON sca.cat_id = sc.id
    WHERE si.item_url = '$item_url'
    ";
    $query = $this->_custom_query($mysql_query);
    foreach($query->result() as $row) {
      $cat_url = $row->cat_url;
    }
    return $cat_url;
  }

  function _get_small_pic_by_item_url($item_url) {
    $mysql_query = "
    SELECT sp.picture_name AS picture_name FROM
    store_items si JOIN small_pics sp ON si.id = sp.item_id
    WHERE si.item_url = '$item_url'
    ORDER BY sp.priority DESC
    LIMIT 1
    ";
    $query = $this->_custom_query($mysql_query);
    foreach($query->result() as $row) {
      $picture_name = $row->picture_name;
    }
    if (!isset($picture_name)) {
      $picture_name = "";
    }
    return $picture_name;
  }

  function get_item_title_by_id($update_id) {
    $query = $this->get_where_custom('id', $update_id);
    foreach($query->result() as $row) {
      $item_title = $row->item_title;
    }
    if (!isset($item_title)) {
      $item_title = "";
    }
    return $item_title;
  }

  function _get_all_items_for_dropdown() {
    // note: this gets used on store_cat_assign
    $mysql_query = "SELECT * FROM store_items ORDER BY item_title";
    $query = $this->_custom_query($mysql_query);
    foreach($query->result() as $row) {
      $items[$row->id] = $row->item_title;
    }
    if (!isset($items)) {
      $items = "";
    }
    return $items;
  }

  function view($update_id) {
    $this->load->module('timedate');
    // only those people with an update_id for an item can get in.
    if (!is_numeric($update_id)) {
      redirect('site_security/not_allowed');
    }
    // fetch the item details
    $data = $this->fetch_data_from_db($update_id);
    $data['date_made'] = $this->timedate->get_date($data['date_made'], 'datepicker_us');
    $data['update_id'] = $update_id;
    $data['pics_query'] = $this->_get_pics_by_update_id($update_id);

    // foreach ($data['pics_query']->result() as $row) {
    //   echo "picture Name: $row->picture_name";
    // }

    // build the breadcrumbs data array
    $breadcrumbs_data['template'] = "public_bootstrap";
    $breadcrumbs_data['current_page_title'] = $data['item_title'];
    $breadcrumbs_data['breadcrumbs_array'] = $this->_generate_breadcrumbs_array($update_id);
    $data['breadcrumbs_data'] = $breadcrumbs_data;

    $data['flash'] = $this->session->flashdata('item');
    $this->load->module('site_settings');
    $currency_symbol = $this->site_settings->_get_currency_symbol();
    $data['item_price_desc'] = $currency_symbol.number_format($data['item_price'], 2);
    // this module helps to make a friendly URL
    $data['view_module'] = "store_items";
    $data['view_file'] = "view";
    $this->load->module('templates');
    $this->templates->public_bootstrap($data);
  }

  function _get_pics_by_update_id($update_id) {
    $mysql_query = "
    SELECT @counter := @counter + 1 as row_number, picture_name
    FROM small_pics WHERE item_id = ?
    ";

    $query = $this->db->query($mysql_query, array($update_id));
    return $query;
  }

  function _generate_breadcrumbs_array($update_id) {
    $homepage_url = base_url();
    $breadcrumbs_array[$homepage_url] = 'Home';

    // figure out what the sub_cat_id is for this item
    $sub_cat_id = $this->_get_sub_cat_id($update_id);
    // now that we have the sub_cat_id, get the title and the URL
    $this->load->module('store_categories');
    $sub_cat_title = $this->store_categories->_get_cat_title($sub_cat_id);
    // get the sub cat URL
    $sub_cat_url = $this->store_categories->_get_full_cat_url($sub_cat_id);
    $breadcrumbs_array[$sub_cat_url] = $sub_cat_title;
    return $breadcrumbs_array;
  }

  function _get_sub_cat_id($update_id) {

    if (!isset($_SERVER['HTTP_REFERER'])) {
      $refer_url = '';
    } else {
      $refer_url = $_SERVER['HTTP_REFERER'];
    }
    //http://localhost/practice/pre_owned_items/Big_boats
    $this->load->module('site_settings');
    $this->load->module('store_cat_assign');
    $this->load->module('store_categories');

    $items_segments = $this->site_settings->_get_items_segments();
    $ditch_this = base_url().$items_segments;
    $cat_url = str_replace($ditch_this, '', $refer_url);
    $sub_cat_id = $this->store_categories->_get_cat_id_from_cat_url($cat_url);
    if ($sub_cat_id > 0) {
      return $sub_cat_id;
    } else {
      $sub_cat_id = $this->_get_best_sub_cat_id($update_id);
    }
    return $sub_cat_id;
  }

  function _get_best_sub_cat_id($update_id) {
    $this->load->module('store_cat_assign');
    // figure out which associated sub cat has the most items
    $query = $this->store_cat_assign->get_where_custom('item_id', $update_id);
    foreach($query->result() as $row) {
      $potential_sub_cats[] = $row->cat_id;
    }
    // how many sub cats does this item appear in?
    $num_sub_cats_for_item = count($potential_sub_cats);

    if ($num_sub_cats_for_item == 1) {
      // the item only appears in one sub category, so use this
      $sub_cat_id = $potential_sub_cats['0'];
      return $sub_cat_id;
    } else {
      // we have more than one sub cat START

      foreach ($potential_sub_cats as $key => $value) {
        $sub_cat_id = $value;
        $num_items_in_sub_cat = $this->store_cat_assign->count_where('cat_id', $sub_cat_id);
        $num_items_count[$sub_cat_id] = $num_items_in_sub_cat;
      }
      // which array key is paired with the highest value?
      $sub_cat_id = $this->get_best_array_key($num_items_count);
      return $sub_cat_id;
      // we have more than one sub cat END
    }
  }

  function _get_sub_cat_url_by_item_id($item_id) {
    $mysql_query = "SELECT sc.cat_url FROM store_categories sc
                    JOIN store_cat_assign sca ON sc.id = sca.cat_id
                    JOIN store_items si ON sca.item_id = si.id
                    WHERE si.id = ?";
    $query = $this->db->query($mysql_query, array($item_id));
    $sub_cat_url = "";
    foreach($query->result() as $row) {
      $sub_cat_url = $row->cat_url;
    }
    return $sub_cat_url;
  }

  function get_best_array_key($target_array) {
    foreach ($target_array as $key => $value) {
      if (!isset($key_with_highest_value)) {
        $key_with_highest_value = $key;
      } else if ($value > $target_array[$key_with_highest_value]) {
        $key_with_highest_value = $key;
      }
    }
    return $key_with_highest_value;
  }

  // '_' means private
  function _genrate_thumbnail($file_name) {
    $config['image_library'] = 'gd2';
    $config['source_image'] = './big_pics/'.$file_name;
    $config['new_image'] = './small_pics/'.$file_name;
    // $config['craete_thumb'] = true;
    $config['maintain_ratio'] = true;
    $config['width'] = 200;
    $config['height'] = 200;

    $this->load->library('image_lib', $config);
    $this->image_lib->resize();
  }

  // This function displays the upload_image page
  function upload_image($update_id) {

    // only those people with an update_id for an item can get in.
    if (!is_numeric($update_id)) {
      redirect('site_security/not_allowed');
    }
    // security
    $this->load->library('session');
    $this->load->module('site_security');
    $this->site_security->_make_sure_is_admin();

    $mysql_query = "SELECT * FROM small_pics WHERE item_id = $update_id ORDER BY priority ASC";
    $query = $this->_custom_query($mysql_query);
    $data['query'] = $query;
    $data['num_rows'] = $query->num_rows(); // number of pictures that an item has
    $data['headline'] = "Manage Images";
    $data['update_id'] = $update_id;
    $date['flash'] = $this->session->flashdata('item');
    $data['view_file'] = "upload_image";
    $data['sort_this'] = true;
    $this->load->module('templates');
    $this->templates->admin($data);
  }

  function do_upload($update_id) {

    // only those people with an update_id for an item can get in.
    if (!is_numeric($update_id)) {
      redirect('site_security/not_allowed');
    }

    // security
    $this->load->library('session');
    $this->load->module('site_security');
    $this->site_security->_make_sure_is_admin();

    // getting submit from the post
    $submit = $this->input->post('submit', true);

    if ($submit == "cancel") {
      redirect('store_items/create/'.$update_id);
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
        $mysql_query = "SELECT * FROM small_pics WHERE item_id = $update_id";
        $query = $this->_custom_query($mysql_query);
        $data['query'] = $query;
        $data['num_rows'] = $query->num_rows();
        $data['error'] = array('error' => $this->upload->display_errors("<p style='color: red;'>", "</p>"));
        $data['headline'] = "Upload Error";
        $data['_id'] = $update_id;
        $date['flash'] = $this->session->flashdata('item');
        $data['view_file'] = "upload_image";
        $this->load->module('templates');
        $this->templates->admin($data);
      } else {

        // upload was successful
        $data = array('upload_data' => $this->upload->data());
        $upload_data = $data['upload_data'];

        $file_ext = $upload_data['file_ext'];
        $file_name = $file_name.$file_ext;
        // $file_name = $upload_data['file_name'];
        $this->_genrate_thumbnail($file_name);

        //update the database
        $priority = $this->_get_priority($update_id);
        $mysql_query = "INSERT INTO small_pics (item_id, picture_name, priority) VALUES ($update_id, '$file_name', $priority)";
        $this->_custom_query($mysql_query);

        $small_pic_id = $this->_get_small_pic_id($update_id, $priority);
        $mysql_query = "INSERT INTO big_pics (small_pic_id, picture_name) VALUES ($small_pic_id, '$file_name')";
        $this->_custom_query($mysql_query);

        $data['headline'] = "Upload Success";
        $data['update_id'] = $update_id;
        $flash_msg = "The picture was successfully uploaded.";
        $value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
        $this->session->set_flashdata('item', $value);

        // $date['flash'] = $this->session->flashdata('item');
        redirect(base_url()."/store_items/upload_image/".$update_id);
        // $data['view_file'] = "upload_success";
        // $this->load->module('templates');
        // $this->templates->public_bootstrap($data);


        // $config['upload_path'] = './big_pics/';
        // $config['allowed_types'] = 'gif|jpg|png';
        // // $config['max_size'] = 100;
        // $config['max_size'] = 200;
        // // $config['max_width'] = 1024;
        // $config['max_width'] = 2024;
        // // $config['max_height'] = 768;
        // $config['max_height'] = 1268;
        //
        // $this->load->library('upload', $config);
        //
        // if (!$this->upload->do_upload('userfile')) {
        //   $data['error'] = array('error' => $this->upload->display_errors("<p style='color: red;'>", "</p>"));
        //   $data['headline'] = "Upload Error";
        //   $data['update_id'] = $update_id;
        //   $date['flash'] = $this->session->flashdata('item');
        //   $data['view_file'] = "upload_image";
        //   $this->load->module('templates');
        //   $this->templates->admin($data);
        // } else {
        //   // upload was successful
        //
        //   $data = array('upload_data' => $this->upload->data());
        //
        //   $upload_data = $data['upload_data'];
        //   $file_name = $upload_data['file_name'];
        //   $this->_genrate_thumbnail($file_name);
        //
        //   //update the database
        //   // insde [] is the column name
        //   $update_data['big_pic'] = $file_name;
        //   $update_data['small_pic'] = $file_name;
        //   $this->_update($update_id, $update_data);
        //
        //   $data['headline'] = "Upload Success";
        //   $data['update_id'] = $update_id;
        //   $date['flash'] = $this->session->flashdata('item');
        //   $data['view_file'] = "upload_success";
        //   $this->load->module('templates');
        //   $this->templates->admin($data);
      }
    }
  }

  function _get_priority($update_id) {
    $mysql_query = "SELECT * FROM small_pics WHERE item_id = $update_id ORDER BY priority DESC LIMIT 1";
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

  function _get_small_pic_id($update_id, $priority) {
    $mysql_query = "SELECT id FROM small_pics WHERE item_id = $update_id AND priority = $priority";
    $query = $this->_custom_query($mysql_query);
    foreach($query->result() as $row) {
      $small_pic_id = $row->id;
    }
    return $small_pic_id;
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

    $data['headline'] = "Delete Image";
    $data['update_id'] = $update_id;
    $date['flash'] = $this->session->flashdata('item');
    $data['view_file'] = "deleteconf";
    $this->load->module('templates');
    $this->templates->admin($data);
  }

  function _process_delete($update_id) {
    // attempt to delete item colors
    $this->load->module("store_item_colors");
    $this->store_item_colors->_delete_for_item($update_id);
    // attempt to delete item sizes
    $this->load->module("store_item_sizes");
    $this->store_item_sizes->_delete_for_item($update_id);
    // attempt to delete item big & small pics
    $data = $this->fetch_data_from_db($update_id);

    $this->load->module('small_pics');
    $this->load->module('big_pics');
    $small_pic_ids = $this->small_pics->get_small_pic_ids_by_item_id($update_id);

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
    }

    // $big_pic = $data['big_pic'];
    // $small_pic = $data['small_pic'];
    //
    // $big_pic_path = './big_pics/'.$big_pic;
    // $small_pic_path = './small_pics/'.$small_pic;
    // // attemp to delete item small pics
    // if (file_exists($big_pic_path)) {
    //   unlink($big_pic_path);
    // }
    //
    // if (file_exists($small_pic_path)) {
    //   unlink($small_pic_path);
    // }

    // delete the item record from store_items
    $this->_delete($update_id);
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
      redirect('store_items/create/'.$update_id);
    } else if ($submit == "Delete") {
      // manipulate the DB
      $this->_process_delete($update_id);
      // preparing the flash message after deletion
      $flash_msg = "The item was successfully deleted.";
      $value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
      $this->session->set_flashdata('item', $value);

      redirect('store_items/manage');
    }
  }

  function delete_image($update_id) {
    $this->load->module('site_security');
    $this->load->module('small_pics');
    $this->load->module('big_pics');


    // only those people with an update_id for an item can get in.
    if (!is_numeric($update_id)) {
      redirect('site_security/not_allowed');
    }

    // security
    $this->load->library('session');
    $this->load->module('site_security');
    $this->site_security->_make_sure_is_admin();

    $small_pic_id = $this->uri->segment(4);

    $data = $this->fetch_data_from_db($update_id);

    $picture_name = $this->small_pics->_get_picture_name_by_small_pic_id($small_pic_id);

    $big_pic_path = './big_pics/'.$picture_name;
    $small_pic_path = './small_pics/'.$picture_name;

    // checks if the file exists in the directory and if so, attemt to remove the images
    if (file_exists($big_pic_path)) {
      unlink($big_pic_path);
    }

    if (file_exists($small_pic_path)) {
      unlink($small_pic_path);
    }

    // // update the database
    // unset($data);
    // $data['big_pic'] = "";
    // $data['small_pic'] = "";
    // $this->_update($update_id, $data);

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

    $flash_msg = "The item image was successfuly deleted.";
    $value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
    $this->session->set_flashdata('item', $value);

    redirect('store_items/upload_image/'.$update_id);
  }

  function manage() {
    $this->load->module('site_security');
    $this->site_security->_make_sure_is_admin();

    // gettinf flash data
    $data['flash'] = $this->session->flashdata('item');

    // getting data from DB
    // this means order by item_title
    // $data['query'] = $this->get('item_title');
    $mysql_query = "SELECT si.*, sa.userName  FROM store_items si
    LEFT JOIN store_accounts sa ON si.user_id = sa.id;
    ";
    $data['query'] = $this->_custom_query($mysql_query);

    // create a view file. Putting a php (html) into the admin template.
    $data['view_module'] = "store_items";
    // store_Items.php
    $data['view_file'] = "manage"; // manage.php
    $this->load->module('templates');
    $this->templates->admin($data);
  }

  function create() {
    $this->load->module('site_security');
    $this->load->module('site_settings');
    $this->site_security->_make_sure_is_admin();

    $update_id = $this->uri->segment(3);
    $submit = $this->input->post('submit', true);

    if ($submit == "Cancel") {
      redirect('store_items/manage');
    } else if ($submit == "Submit") {
      // process the form
      $this->load->library('form_validation');
      $this->form_validation->set_rules('item_title', 'Item Title', 'required|max_length[240]|callback_item_check'); // callback is for checking if the item already exists
      $this->form_validation->set_rules('item_price', 'Item Price', 'required|numeric');
      $this->form_validation->set_rules('was_price', 'Was Price', 'numeric');
      $this->form_validation->set_rules('categories[]', 'Categories', 'required');
      // $this->form_validation->set_rules('status', 'Status', 'required|numeric');
      $this->form_validation->set_rules('status', 'Status', 'required');
      $this->form_validation->set_rules('city', 'City', 'required');
      $this->form_validation->set_rules('state', 'State', 'required');
      $this->form_validation->set_rules('item_description', 'Item Description', 'required');

      if ($this->form_validation->run() == true) {
        // get the variables and assign into $data variable
        $data = $this->fetch_data_from_post();
        // create a URL for an item but they need to be UNIQUE
        $data['item_url'] = url_title($data['item_title']);

        if (is_numeric($update_id)) {
          //update the item details
          unset($data['categories']);
          $this->_update($update_id, $data);
          // for categories, need to delete the entry for the item id and re-insert
          $this->load->module('store_cat_assign');
          $this->store_cat_assign->_custom_delete('item_id', $update_id);
          // get categories indexes
          $categories_from_post = $this->input->post('categories[]', true);
          // get categories array
          $categories = $this->_get_categories();
          for ($i = 0; $i < count($categories_from_post); $i++) {
            $cat_assign_data['cat_id'] = $categories_from_post[$i];
            $cat_assign_data['item_id'] = $update_id;
            $this->store_cat_assign->_insert($cat_assign_data);
          }
          // These two lines show the alert for the successful item details change.
          $flash_msg = "The item details were successfully updated.";
          $value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
          $this->session->set_flashdata('item', $value);
          // add the update data into the URL
          redirect('store_items/create/'.$update_id);
        } else {
          // insert a new item into DB
          $code = $this->site_security->generate_random_string(6);
          $item_url = url_title($data['item_title']).$code;
          $data['item_url'] = $item_url;
          $data['user_id'] = 0; // 0 = admin
          $data['date_made'] = time();
          unset($data['categories']);
          $this->_insert($data);

          $update_id = $this->get_max(); //get the ID of the new item
          $update_id = $this->_get_item_id_from_item_url($item_url);
          // insert into store_cat_assign table
          $categories_from_post = $this->input->post('categories[]', true);
          $this->load->module('store_cat_assign');
          // $categories = $this->_get_categories();
          for ($i = 0; $i < count($categories_from_post); $i++) {
            $cat_assign_data['cat_id'] = $categories_from_post[$i];
            $cat_assign_data['item_id'] = $update_id;
            $this->store_cat_assign->_insert($cat_assign_data);
          }
          $this->load->library('session');
          $flash_msg = "The item was successfully added.";
          $value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
          $this->session->set_flashdata('item', $value);
          // add the update data into the URL
          redirect('store_items/create/'.$update_id);
        }
      }
    }

    if ((is_numeric($update_id)) && ($submit != "Submit")) {
      $data = $this->fetch_data_from_db($update_id);
    } else {
      $data = $this->fetch_data_from_post();
    }

    if (!is_numeric($update_id)) {
      $data['headline'] = "Add New Item";
    } else {
      $data['headline'] = "Update Item Details";
    }

    // pass update id into the page
    $data['update_id'] = $update_id;
    $data['flash'] = $this->session->flashdata('item');

    // create a view file. Putting a php (html) into the admin template.
    $data['categories_options'] = $this->_get_categories();
    $data['states'] = $this->site_settings->_get_states_dropdown();
    $data['view_file'] = "create"; // manage.php
    $this->load->module('templates');
    $this->templates->admin($data);
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

  // get data from POST method
  function fetch_data_from_post() {
    $data['item_title'] = $this->input->post('item_title', true);
    $data['item_price'] = $this->input->post('item_price', true);
    $data['was_price'] = $this->input->post('was_price', true);
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

  // get data from database
  function fetch_data_from_db($update_id) {
    //security
    $this->load->module('site_security');
    $this->load->module('store_items');

    if (!is_numeric($update_id)) {
      redirect('site_security/not_allowed');
    }

    $mysql_query = "
    SELECT * FROM
    store_items si
    WHERE id = $update_id
    ";

    $query = $this->store_items->_custom_query($mysql_query);

    foreach ($query->result() as $row) {
      $data['item_title'] = $row->item_title;
      $data['item_url'] = $row->item_url;
      $data['item_price'] = $row->item_price;
      $data['was_price'] = $row->was_price;
      $data['item_description'] = $row->item_description;
      $data['status'] = $row->status;
      $data['city'] = $row->city;
      $data['state'] = $row->state;
      $data['date_made'] = $row->date_made;
    }
    if (!isset($data)) {
      $data = "";
    }

    return $data;
    // if (!is_numeric($update_id)) {
    //   redirect('site_security/not_allowed');
    // }
    //
    // $query = $this->get_where($update_id);
    // foreach($query->result() as $row) {
    //   $data['item_title'] = $row->item_title;
    //   $data['item_url'] = $row->item_url;
    //   $data['item_price'] = $row->item_price;
    //   $data['item_description'] = $row->item_description;
    //   $data['big_pic'] = $row->big_pic;
    //   $data['small_pic'] = $row->small_pic;
    //   $data['was_price'] = $row->was_price;
    //   $data['status'] = $row->status;
    // }
    // if (!isset($data)) {
    //   $data = "";
    // }
    // return $data;
  }

  function get_user_by_use_id($item_id) {
    $mysql_query = "
    SELECT sa.id as user_id, sa.userName as userName, sa.email as email FROM store_accounts sa
    JOIN store_items si on sa.id = si.user_id
    WHERE si.id = ?
    ";
    $query = $this->db->query($mysql_query, array($item_id));

    return $query;
  }

  function _draw_contact_sellter($item_id) {
    $query = $this->get_user_by_use_id($item_id);
    foreach($query->result() as $row) {
      $data['email'] = $row->email;
      $data['user_id'] = $row->user_id;
      $data['userName'] = $row->userName;
    }
    $this->load->module('site_settings');
    if (!isset($data['email'])) {
      $data['email'] = $this->site_settings->_get_email_for_admin_seller();
    }
    if (!isset($data['userName'])) {
      $data['userName'] = "TCWS Admin";
    }
    if (!isset($data['user_id'])) {
      $data['user_id'] = 0;
    }

    $data['item_id'] = $item_id;
    $this->load->view('contact_seller', $data);
  }


  function get($order_by)
  {
    $this->load->model('mdl_store_items');
    $query = $this->mdl_store_items->get($order_by);
    return $query;
  }

  function get_with_limit($limit, $offset, $order_by)
  {
    if ((!is_numeric($limit)) || (!is_numeric($offset))) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_store_items');
    $query = $this->mdl_store_items->get_with_limit($limit, $offset, $order_by);
    return $query;
  }

  function get_where($id)
  {
    if (!is_numeric($id)) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_store_items');
    $query = $this->mdl_store_items->get_where($id);
    return $query;
  }

  function get_where_custom($col, $value)
  {
    $this->load->model('mdl_store_items');
    $query = $this->mdl_store_items->get_where_custom($col, $value);
    return $query;
  }

  function _insert($data)
  {
    $this->load->model('mdl_store_items');
    $this->mdl_store_items->_insert($data);
  }

  function _update($id, $data)
  {
    if (!is_numeric($id)) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_store_items');
    $this->mdl_store_items->_update($id, $data);
  }

  function _delete($id)
  {
    if (!is_numeric($id)) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_store_items');
    $this->mdl_store_items->_delete($id);
  }

  function count_where($column, $value)
  {
    $this->load->model('mdl_store_items');
    $count = $this->mdl_store_items->count_where($column, $value);
    return $count;
  }

  function get_max()
  {
    $this->load->model('mdl_store_items');
    $max_id = $this->mdl_store_items->get_max();
    return $max_id;
  }

  function _custom_query($mysql_query)
  {
    $this->load->model('mdl_store_items');
    $query = $this->mdl_store_items->_custom_query($mysql_query);
    return $query;
  }

  // a method to check if the item name exists.
  function item_check($str) {

    $item_url = url_title($str);
    $mysql_query = "SELECT * FROM store_items WHERE item_title = '$str' AND item_url = '$item_url'";

    $update_id = $this->uri->segment(3);
    if (is_numeric($update_id)) {
      // this is an update
      $mysql_query .= "AND id != $update_id";
    }

    $query = $this->_custom_query($mysql_query);
    $num_rows = $query->num_rows();

    if ($num_rows > 0) {
      $this->form_validation->set_message('item_check', 'The item title that you submitted is not available.');
      return false;
    } else {
      return true;
    }
  }

}
