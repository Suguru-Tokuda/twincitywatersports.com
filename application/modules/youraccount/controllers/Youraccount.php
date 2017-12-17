<?php
class Youraccount extends MX_Controller {

  function __construct() {
    parent::__construct();
    $this->load->library('form_validation');
    $this->form_validation->set_ci($this);
  }

  function test() {
    $name = "Suguru";
    $this->load->module('site_security');
    $hashed_name = $this->site_security->_hash_string($name);
    echo "Name is $name<br>";
    echo $hashed_name;
    echo "<hr>";

    $hashed_name_length = strlen($hashed_name);
    $start_point = $hashed_name_length - 6;
    $last_six_chars = substr($hashed_name, $start_point, 6);
    echo $last_six_chars;
  }

  function logout() {
    unset($_SESSION['user_id']);
    $this->load->module('site_cookies');
    $this->site_cookies->_destroy_cookie();
    redirect(base_url());
  }

  function welcome() {
    $this->load->module('site_security');
    $this->site_security->_make_sure_logged_in();
    $data['flash'] = $this->session->flashdata('item');
    $data['view_file'] = "welcome";
    $this->load->module('templates');
    $this->templates->public_bootstrap($data);
  }

  function test1() {
    $your_name = "David";
    $this->session->set_userdata('your_name', $your_name);
    echo "The session variable was set.";
    echo "<hr>";
    echo anchor('youraccount/test1', 'Set the session variable')."<br>";
    echo anchor('youraccount/test2', 'Get (display) the session variable')."<br>";
    echo anchor('youraccount/test3', 'Unset (destroy) the session variable')."<br>";
  }

  function test2() {
    $your_name = $this->session->userdata('your_name');
    if ($your_name != "") {
      echo "<h1>Hello $your_name</h1>";
    } else {
      echo "No session variable has been set for your_name";
    }
  }

  function test3() {
    unset($_SESSION['your_name']);
    echo "The session variable was unset.";

    echo "<hr>";
    echo anchor('youraccount/test1', 'Set the session variable')."<br>";
    echo anchor('youraccount/test2', 'Get (display) the session variable')."<br>";
    echo anchor('youraccount/test3', 'Unset (destroy) the session variable')."<br>";
  }

  // login function
  function login() {
    $data['userName'] = $this->input->post('userName', true);
    $this->load->module('templates');
    $this->templates->login($data);
  }

  function submit_login() {
    $submit = $this->input->post('submit', true);

    if ($submit == "submit") {
      // process the form
      $this->form_validation->set_rules('userName', 'Username', 'required|min_length[5]|max_length[60]|callback_userName_check');
      $this->form_validation->set_rules('password', 'Password', 'required|min_length[7]|max_length[35]');

      if ($this->form_validation->run() == true) {

        // figure out the user_id
        $col1 = 'userName';
        $value1 = $this->input->post('userName', true);
        $col2 = 'email';
        $value2 = $this->input->post('userName', true);;
        $query = $this->store_accounts->get_with_double_condition($col1, $value1, $col2, $value2);
        foreach ($query->result() as $row) {
          $user_id = $row->id;
        }

        $remember = $this->input->post('remember', true);
        if ($remember == "remember") {
          $login_type = "longterm";
        } else {
          $login_type = "shortterm";
        }

        $data['last_login'] = time();
        $this->store_accounts->_update($user_id, $data);

        // send them to the private page
        $this->_in_you_go($user_id, $login_type);
      } else {
        redirect('youraccount/login');
      }
    }
  }

  function _in_you_go($user_id, $login_type) {
    // NOTE: the login_type can be longterm or shortterm
    if ($login_type == "longterm") {
      // set a cookie
      $this->load->module('site_cookies');
      $this->site_cookies->_set_cookie($user_id);
    } else {
      // set a session variable
      $this->session->set_userdata('user_id', $user_id);
    }
    // send the user to the private page
    redirect('youraccount/welcome');
  }

  function submit() {
    $submit = $this->input->post('submit', true);

    if ($submit == "submit") {
      // process the form
      $this->form_validation->set_rules('userName', 'Username', 'required|min_length[5]|max_length[60]|callback_userName_existence_check');
      $this->form_validation->set_rules('email', 'Email', 'required|valid_email|max_length[120]');
      $this->form_validation->set_rules('password', 'Password', 'required|min_length[7]|max_length[35]');
      $this->form_validation->set_rules('confirmPassword', 'Confirm Password', 'required|matches[password]');

      if ($this->form_validation->run() == true) {
        // insert a new account into DB
        $this->_process_create_account();
        $data['view_file'] = "account_create_success";
        $this->load->module('templates');
        $this->templates->public_bootstrap($data);
      } else {
        $this->start();
      }
    }
  }

  function _process_create_account() {
    $this->load->module('store_accounts');
    $data = $this->fetch_data_from_post();
    unset($data['confirmPassword']);

    $password = $data['password'];
    $this->load->module('site_security');
    $data['password'] = $this->site_security->_hash_string($password);
    $data['date_made'] = time();

    $this->store_accounts->_insert($data);
  }

  function start() {
    $data = $this->fetch_data_from_post();
    $data['flash'] = $this->session->flashdata('item');
    $data['view_file'] = "start";
    $this->load->module('templates');
    $this->templates->public_bootstrap($data);
  }

  function fetch_data_from_post() {
    $data['userName'] = $this->input->post('userName', true);
    $data['email'] = $this->input->post('email', true);
    $data['password'] = $this->input->post('password', true);
    $data['confirmPassword'] = $this->input->post('confirmPassword', true);
    return $data;
  }

  function userName_existence_check($str) {
  $this->load->module('store_accounts');

  $error_msg = "$str already exists";

  $query = $this->store_accounts->get_where_custom('userName', $str);
  $num_rows = $query->num_rows();
  if ($num_rows == 0) {
    return true;
  } else if ($num_rows == 1) {
    $this->form_validation->set_message('userName_existence_check', $error_msg);
    return false;
  }

}

  // a method to check if the userName exists.
  function userName_check($str) {

    $this->load->module('store_accounts');
    $this->load->module('site_security');

    $error_msg = "You did not enter a correct username and/or password.";

    $col1 = 'userName';
    $value1 = $str;
    $col2 = 'email';
    $value2 = $str;
    $query = $this->store_accounts->get_with_double_condition($col1, $value1, $col2, $value2);
    $num_rows = $query->num_rows();
    if ($num_rows < 1) {
      $this->form_validation->set_message('userName_check', $error_msg);
      return false;
    }

    foreach ($query->result() as $row) {
      $password_on_table = $row->password;
    }

    $password = $this->input->post('password', true);
    $result = $this->site_security->_verify_hash($password, $password_on_table);

    if ($result == true) {
      return true;
    } else {
      $this->form_validation->set_message('userName_check', $error_msg);
      return false;
    }
  }

}
