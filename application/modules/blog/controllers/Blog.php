<?php
class Blog extends MX_Controller {

  function __construct() {
    parent::__construct();
    $this->load->library('form_validation');
    $this->form_validation->set_ci($this);
  }

  function test() {
    $this->load->module('timedate');
    $nowtime = time();
    $datepicker_time = $this->timedate->get_date($nowtime, 'datepicker_us');
    echo $datepicker_time;
    echo "<hr>";
    // convert back into unix timestamp
    $timestamp = $this->timedate->make_timestamp_from_datepicker_us($datepicker_time);
    echo $timestamp;

    echo "<hr>";
    $nice_date = $this->timedate->get_date($timestamp, 'cool');
    echo $nice_date;
  }

  function _draw_feed_hp() {
    $this->load->helper('text');
    $mysql_query = "SELECT * FROM blog ORDER BY date_published DESC LIMIT 0, 3";
    $data['query'] = $this->_custom_query($mysql_query);
    $this->load->view('feed_hp', $data);
  }

  function manage() {
    $this->load->module('site_security');
    $this->site_security->_make_sure_is_admin();

    // gettinf flash data
    $data['flash'] = $this->session->flashdata('item');

    // getting data from DB
    // this means order by blog_title
    $data['query'] = $this->get('date_published desc');

    // create a view file. Putting a php (html) into the admin template.
    $data['view_module'] = "blog";
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
    $this->load->module('timedate');

    if ($submit == "Cancel") {
      redirect('blog/manage');
    } else if ($submit == "Submit") {
      // process the form
      $this->load->library('form_validation');
      $this->form_validation->set_rules('date_published', 'Date Published', 'required');
      $this->form_validation->set_rules('blog_title', 'Blog Title', 'required|max_length[250]'); // callback is for checking if the item already exists
      $this->form_validation->set_rules('blog_content', 'Blog Content', 'required');

      if ($this->form_validation->run() == true) {
        // get the variables and assign into $data variable
        $data = $this->fetch_data_from_post();
        // create a URL for an item but they need to be UNIQUE
        $data['blog_url'] = url_title($data['blog_title']);
        // convert the datepicker into a unix timestamp
        $data['date_published'] = $this->timedate->make_timestamp_from_datepicker_us($data['date_published']);

        if (is_numeric($update_id)) {
          //update the item details
          $this->_update($update_id, $data);
          // These two lines show the alert for the successful item details change.
          $flash_msg = "The blog details were successfully updated.";
          $value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
          $this->session->set_flashdata('item', $value);
          // add the update data into the URL
          redirect('blog/create/'.$update_id);
        } else {
          // insert a new item into DB
          $this->_insert($data);
          $update_id = $this->get_max(); //get the ID of the new item

          $this->load->library('session');

          $flash_msg = "The blog entry was successfully added.";
          $value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
          $this->session->set_flashdata('item', $value);
          // add the update data into the URL
          redirect('blog/create/'.$update_id);
        }
      }
    }

    if ((is_numeric($update_id)) && ($submit != "Submit")) {
      $data = $this->fetch_data_from_db($update_id);
    } else {
      $data = $this->fetch_data_from_post();
      $data['picture'] = "";
    }

    if (!is_numeric($update_id)) {
      $data['headline'] = "Create New Blog";
    } else {
      $data['headline'] = "Update Blog Details";
    }

    if ($data['date_published'] > 0) {
      // it must be a unix timestamp, so convert to datepicker format
      $data['date_published'] = $this->timedate->get_date($data['date_published'], 'datepicker_us');
    }

    // pass update id into the blog
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
    }

    // security
    $this->load->library('session');
    $this->load->module('site_security');
    $this->site_security->_make_sure_is_admin();

    $data['headline'] = "Delete Blog";
    $data['update_id'] = $update_id;
    $date['flash'] = $this->session->flashdata('item');
    $data['view_file'] = "deleteconf";
    $this->load->module('templates');
    $this->templates->admin($data);
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

    $data['headline'] = "Upload Image";
    $data['update_id'] = $update_id;
    $date['flash'] = $this->session->flashdata('item');
    $data['view_file'] = "upload_image";
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

    if ($submit == "Cancel") {
      redirect('blog/create/'.$update_id);
    } else if ($submit == "Upload") {
      $config['upload_path'] = './blog_pics/';
      $config['allowed_types'] = 'gif|jpg|png';
      // $config['max_size'] = 100;
      $config['max_size'] = 200;
      // $config['max_width'] = 1024;
      $config['max_width'] = 2024;
      // $config['max_height'] = 768;
      $config['max_height'] = 1268;
      $config['file_name'] = $this->site_security->generate_random_string(16);

      $this->load->library('upload', $config);

      if (!$this->upload->do_upload('userfile')) {
        $data['error'] = array('error' => $this->upload->display_errors("<p style='color: red;'>", "</p>"));
        $data['headline'] = "Upload Error";
        $data['update_id'] = $update_id;
        $date['flash'] = $this->session->flashdata('item');
        $data['view_file'] = "upload_image";
        $this->load->module('templates');
        $this->templates->admin($data);
      } else {
        // upload was successful
        $data = array('upload_data' => $this->upload->data());
        $upload_data = $data['upload_data'];
        // the clode below checks the picture's info
        // foreach ($upload_data as $key => $value) {
        //   echo "key of $key has value of $value<br>";
        // }
        // die();

        //raw_name ... file_ext
        $raw_name = $upload_data['raw_name'];
        $file_ext = $upload_data['file_ext'];
        // generate a thumbnail name
        $thumbnail_name = $raw_name."_thumb".$file_ext;

        $file_name = $upload_data['file_name'];
        $this->_genrate_thumbnail($file_name, $thumbnail_name);

        //update the database
        // insde [] is the column name
        $update_data['picture'] = $file_name;
        $this->_update($update_id, $update_data);

        $data['headline'] = "Upload Success";
        $data['update_id'] = $update_id;
        $date['flash'] = $this->session->flashdata('item');
        $data['view_file'] = "upload_success";
        $this->load->module('templates');
        $this->templates->admin($data);
      }
    }
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
      redirect('blog/create/'.$update_id);
    } else if ($submit == "Delete") {
      // manipulate the DB
      $this->_process_delete($update_id);
      // preparing the flash message after deletion
      $flash_msg = "The blog was successfully deleted.";
      $value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
      $this->session->set_flashdata('item', $value);

      redirect('blog/manage');
    }
  }

  function _process_delete($update_id) {
    // delete the blog record from blog
    $this->_delete($update_id);
  }

  function delete_image($update_id) {

    // only those people with an update_id for an item can get in.
    if (!is_numeric($update_id)) {
      redirect('site_security/not_allowed');
    }

    // security
    $this->load->library('session');
    $this->load->module('site_security');
    $this->site_security->_make_sure_is_admin();

    $data = $this->fetch_data_from_db($update_id);
    $picture = $data['picture'];
    // echo $blog_pic; die();

    $blog_pic_path = './blog_pics/'.$picture;
    $thumbnail_pic_path = './blog_pics/'.str_replace('.', '_thumb.', $picture);
    // echo $blog_pic_path."<br>";
    // echo $thumbnail_pic_path; die();

    // checks if the file exists in the directory and if so, attemt to remove the images
    if (file_exists($blog_pic_path)) {
      unlink($blog_pic_path);
    }

    if (file_exists($thumbnail_pic_path)) {
      unlink($thumbnail_pic_path);
    }

    // update the database
    unset($data);
    $data['picture'] = "";
    $this->_update($update_id, $data);

    $flash_msg = "The item image was successfuly deleted.";
    $value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
    $this->session->set_flashdata('item', $value);

    redirect('blog/create/'.$update_id);

  }

  // get data from POST method
  function fetch_data_from_post() {
    $data['blog_title'] = $this->input->post('blog_title', true);
    $data['blog_keywords'] = $this->input->post('blog_keywords', true);
    $data['blog_description'] = $this->input->post('blog_description', true);
    $data['blog_content'] = $this->input->post('blog_content', true);
    $data['date_published'] = $this->input->post('date_published', true);
    $data['author'] = $this->input->post('author', true);
    $daa['picture'] = $this->input->post('picture', true);
    return $data;
  }

  // get data from database
  function fetch_data_from_db($update_id) {

    if (!is_numeric($update_id)) {
      redirect('site_security/not_allowed');
    }

    $query = $this->get_where($update_id);
    foreach($query->result() as $row) {
      $data['blog_title'] = $row->blog_title;
      $data['blog_url'] = $row->blog_url;
      $data['blog_keywords'] = $row->blog_keywords;
      $data['blog_content'] = $row->blog_content;
      $data['blog_description'] = $row->blog_description;
      $data['date_published'] = $row->date_published;
      $data['author'] = $row->author;
      $data['picture'] = $row->picture;
    }
    if (!isset($data)) {
      $data = "";
    }
    return $data;
  }

  function _genrate_thumbnail($file_name, $thumbnail_name) {
    $config['image_library'] = 'gd2';
    $config['source_image'] = './blog_pics/'.$file_name;
    $config['new_image'] = './blog_pics/'.$thumbnail_name;
    // $config['craete_thumb'] = true;
    $config['maintain_ratio'] = true;
    $config['width'] = 200;
    $config['height'] = 200;

    $this->load->library('image_lib', $config);

    $this->image_lib->resize();
  }


  function get($order_by)
  {
    $this->load->model('mdl_blog');
    $query = $this->mdl_blog->get($order_by);
    return $query;
  }

  function get_with_limit($limit, $offset, $order_by)
  {
    if ((!is_numeric($limit)) || (!is_numeric($offset))) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_blog');
    $query = $this->mdl_blog->get_with_limit($limit, $offset, $order_by);
    return $query;
  }

  function get_where($id)
  {
    if (!is_numeric($id)) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_blog');
    $query = $this->mdl_blog->get_where($id);
    return $query;
  }

  function get_where_custom($col, $value)
  {
    $this->load->model('mdl_blog');
    $query = $this->mdl_blog->get_where_custom($col, $value);
    return $query;
  }

  function _insert($data)
  {
    $this->load->model('mdl_blog');
    $this->mdl_blog->_insert($data);
  }

  function _update($id, $data)
  {
    if (!is_numeric($id)) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_blog');
    $this->mdl_blog->_update($id, $data);
  }

  function _delete($id)
  {
    if (!is_numeric($id)) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_blog');
    $this->mdl_blog->_delete($id);
  }

  function count_where($column, $value)
  {
    $this->load->model('mdl_blog');
    $count = $this->mdl_blog->count_where($column, $value);
    return $count;
  }

  function get_max()
  {
    $this->load->model('mdl_blog');
    $max_id = $this->mdl_blog->get_max();
    return $max_id;
  }

  function _custom_query($mysql_query)
  {
    $this->load->model('mdl_blog');
    $query = $this->mdl_blog->_custom_query($mysql_query);
    return $query;
  }

}
