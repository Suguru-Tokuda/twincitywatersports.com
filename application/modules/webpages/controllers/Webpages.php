<?php
class Webpages extends MX_Controller {

  function __construct() {
    parent::__construct();
    $this->load->library('form_validation');
    $this->form_validation->set_ci($this);
  }

  function manage() {
    $this->load->module('site_security');
    $this->site_security->_make_sure_is_admin();

    // gettinf flash data
    $data['flash'] = $this->session->flashdata('item');

    // getting data from DB
    // this means order by page_title
    $data['query'] = $this->get('page_url');

    // create a view file. Putting a php (html) into the admin template.
    $data['view_module'] = "webpages";
    // store_Items.php
    $data['view_file'] = "manage"; // manage.php
    $this->load->module('templates');
    $this->templates->admin($data);
  }

  function create() {
    $this->load->module('site_security');
    $this->site_security->_make_sure_is_admin();

    $update_id = $this->uri->segment(3);
    $submit = $this->input->post('submit', true);


    if ($submit == "Cancel") {
      redirect('webpages/manage');
    } else if ($submit == "Submit") {
      // process the form
      $this->load->library('form_validation');
      $this->form_validation->set_rules('page_title', 'Page Title', 'required|max_length[250]'); // callback is for checking if the item already exists
      $this->form_validation->set_rules('page_content', 'Page Content', 'required');

      if ($this->form_validation->run() == true) {
        // get the variables and assign into $data variable
        $data = $this->fetch_data_from_post();
        // create a URL for an item but they need to be UNIQUE
        $data['page_url'] = url_title($data['page_title']);

        if (is_numeric($update_id)) {
          //update the item details
          if ($update_id < 3) {
            unset($data['page_url']);
          }
          $this->_update($update_id, $data);
          // These two lines show the alert for the successful item details change.
          $flash_msg = "The item details were successfully updated.";
          $value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
          $this->session->set_flashdata('item', $value);
          // add the update data into the URL
          redirect('webpages/create/'.$update_id);
        } else {
          // insert a new item into DB
          $this->_insert($data);
          $update_id = $this->get_max(); //get the ID of the new item

          $this->load->library('session');

          $flash_msg = "The page was successfully added.";
          $value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
          $this->session->set_flashdata('item', $value);
          // add the update data into the URL
          redirect('webpages/create/'.$update_id);
        }
      }
    }

    if ((is_numeric($update_id)) && ($submit != "Submit")) {
      $data = $this->fetch_data_from_db($update_id);
    } else {
      $data = $this->fetch_data_from_post();
    }

    if (!is_numeric($update_id)) {
      $data['headline'] = "Create New Page";
    } else {
      $data['headline'] = "Update Page Details";
    }
    // pass update id into the page
    $data['update_id'] = $update_id;
    $data['flash'] = $this->session->flashdata('item');

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
    } else if ($update_id < 3) { // prevent them from deleting home and contactus
      redirect('site_security/not_allowed');
    }

    // security
    $this->load->library('session');
    $this->load->module('site_security');
    $this->site_security->_make_sure_is_admin();

    $data['headline'] = "Delete Page";
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
      redirect('webpages/create/'.$update_id);
    } else if ($submit == "Delete") {
      // manipulate the DB
      $this->_process_delete($update_id);
      // preparing the flash message after deletion
      $flash_msg = "The page was successfully deleted.";
      $value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
      $this->session->set_flashdata('item', $value);

      redirect('webpages/manage');
    }
  }

  function _process_delete($update_id) {
    // delete the page record from webpages
    $this->_delete($update_id);
  }

  // get data from POST method
  function fetch_data_from_post() {
    $data['page_title'] = $this->input->post('page_title', true);
    $data['page_keywords'] = $this->input->post('page_keywords', true);
    $data['page_description'] = $this->input->post('page_description', true);
    $data['page_content'] = $this->input->post('page_content', true);
    return $data;
  }

  // get data from database
  function fetch_data_from_db($update_id) {

    if (!is_numeric($update_id)) {
      redirect('site_security/not_allowed');
    }

    $query = $this->get_where($update_id);
    foreach($query->result() as $row) {
      $data['page_title'] = $row->page_title;
      $data['page_url'] = $row->page_url;
      $data['page_keywords'] = $row->page_keywords;
      $data['page_content'] = $row->page_content;
      $data['page_description'] = $row->page_description;
    }
    if (!isset($data)) {
      $data = "";
    }
    return $data;
  }



  function get($order_by)
  {
    $this->load->model('mdl_webpages');
    $query = $this->mdl_webpages->get($order_by);
    return $query;
  }

  function get_with_limit($limit, $offset, $order_by)
  {
    if ((!is_numeric($limit)) || (!is_numeric($offset))) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_webpages');
    $query = $this->mdl_webpages->get_with_limit($limit, $offset, $order_by);
    return $query;
  }

  function get_where($id)
  {
    if (!is_numeric($id)) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_webpages');
    $query = $this->mdl_webpages->get_where($id);
    return $query;
  }

  function get_where_custom($col, $value)
  {
    $this->load->model('mdl_webpages');
    $query = $this->mdl_webpages->get_where_custom($col, $value);
    return $query;
  }

  function _insert($data)
  {
    $this->load->model('mdl_webpages');
    $this->mdl_webpages->_insert($data);
  }

  function _update($id, $data)
  {
    if (!is_numeric($id)) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_webpages');
    $this->mdl_webpages->_update($id, $data);
  }

  function _delete($id)
  {
    if (!is_numeric($id)) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_webpages');
    $this->mdl_webpages->_delete($id);
  }

  function count_where($column, $value)
  {
    $this->load->model('mdl_webpages');
    $count = $this->mdl_webpages->count_where($column, $value);
    return $count;
  }

  function get_max()
  {
    $this->load->model('mdl_webpages');
    $max_id = $this->mdl_webpages->get_max();
    return $max_id;
  }

  function _custom_query($mysql_query)
  {
    $this->load->model('mdl_webpages');
    $query = $this->mdl_webpages->_custom_query($mysql_query);
    return $query;
  }

}
