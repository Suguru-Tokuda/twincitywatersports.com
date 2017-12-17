<?php
class Store_accounts extends MX_Controller {

  function __construct() {
    parent::__construct();
    $this->load->library('form_validation');
    $this->form_validation->set_ci($this);
  }

  function _generate_token($update_id) {
    $data = $this->fetch_data_from_db($update_id);
    $date_made = $data['date_made'];
    $last_login = $data['last_login'];
    $password = $data['password'];

    $password_length = strlen($password);
    $start_point = $password_length - 6;
    $last_six_chars = substr($password, $start_point, 6);

    if (($password_length > 5) AND ($last_login > 0)) {
      $token = $last_six_chars.$date_made.$last_login;
    } else {
      $token = '';
    }
    return $token;
  }

  function _get_customer_id_from_token($token) {
    $last_six_chars = substr($token, 0, 6); // last six from stored (hashed) password
    $date_made = substr($token, 6, 10);
    $last_login = substr($token, 16, 10);

    $sql = "SELECT * FROM store_accounts WHERE date_made = ? AND password LIKE ? AND last_login = ?";
    $query = $this->db->query($sql, array($date_made, '%'.$last_six_chars, $last_login));

    foreach ($query->result() as $row) {
      $customer_id = $row->id;
    }
    if (!isset($customer_id)) {
      $customer_id = 0;
    }
    return $customer_id;
  }

  function manage() {
    $this->load->module('site_security');
    $this->site_security->_make_sure_is_admin();

    // gettinf flash data
    $data['flash'] = $this->session->flashdata('account');

    // getting data from DB
    // this means order by lastName
    $data['query'] = $this->get('lastName');

    // create a view file. Putting a php (html) into the admin template.
    $data['view_module'] = "store_accounts";
    // store_Accounts.php
    $data['view_file'] = "manage"; // manage.php
    $this->load->module('templates');
    $this->templates->admin($data);
  }

  function _get_customer_name($update_id, $optional_customer_data=NULL) {
    if (!isset($optional_customer_data)) {
      $data = $this->fetch_data_from_db($update_id);
    } else {
      $data['firstName'] = $optional_customer_data['firstName'];
      $data['lastName'] = $optional_customer_data['lastName'];
      $data['company'] = $optional_customer_data['company'];
    }
    $data = $this->fetch_data_from_db($update_id);
    if ($data == "") {
      $customer_name = "Unknown";
    } else {
      $firstName = trim(ucfirst($data['firstName']));
      $lastName = trim(ucfirst($data['lastName']));
      $company = trim(ucfirst($data['company']));

      $company_length = strlen($company);
      if ($company_length >2) {
        $customer_name = $company;
      } else {
        $customer_name = $firstName." ".$lastName;
      }
    }
    return $customer_name;
  }

  function update_password() {
    // security
    $this->load->module('site_security');
    $this->site_security->_make_sure_is_admin();

    $update_id = $this->uri->segment(3);
    $submit = $this->input->post('submit', true);

    if (!is_numeric($update_id)) {
      redirect('store_accounts/manage');
    } elseif ($submit == "Cancel") {
      redirect('store_accounts/create/'.$update_id);
    } elseif ($submit == "Submit") {
      // process the form
      $this->load->library('form_validation');
      $this->form_validation->set_rules('password', 'Password', 'required|min_length[7]|max_length[35]');
      $this->form_validation->set_rules('confirmPassword', "Confirm Password", 'required|matches[password]');

      if ($this->form_validation->run() == true) {
        // get the variables and assign into $data variable
        $data['password'] = $this->input->post('password', true);
        $this->load->module('site_security');
        $data['password'] = $this->site_security->_hash_string($password);

        //update the account details
        $this->_update($update_id, $data);
        // These two lines show the alert for the successful account details change.
        $flash_msg = "The account password was successfully updated.";
        $value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
        $this->session->set_flashdata('account', $value);
        // add the update data into the URL
        redirect('store_accounts/create/'.$update_id);
      }
    }

    $data['headline'] = "Update Account Password";
    // pass update id into the page
    $data['update_id'] = $update_id;
    $data['flash'] = $this->session->flashdata('account');

    // create a view file. Putting a php (html) into the admin template.
    // store_Accounts.php
    // $data['view_module'] = "store_Accounts";
    $data['view_file'] = "update_password"; // manage.php
    $this->load->module('templates');
    $this->templates->admin($data);
  }

  function create() {
    // security
    $this->load->module('site_security');
    $this->site_security->_make_sure_is_admin();

    $update_id = $this->uri->segment(3);
    $submit = $this->input->post('submit', true);

    if ($submit == "Cancel") {
      redirect('store_accounts/manage');
    } else if ($submit == "Submit") {
      // process the form
      $this->load->library('form_validation');
      $this->form_validation->set_rules('userName', 'Username', 'required');
      $this->form_validation->set_rules('firstName', 'First Name', 'required');
      $this->form_validation->set_rules('lastName', 'Last Name', 'required');
      $this->form_validation->set_rules('address1', 'Address 1', 'required');
      $this->form_validation->set_rules('city', 'City', 'required');
      $this->form_validation->set_rules('state', 'State', 'required');
      $this->form_validation->set_rules('zip', 'Zip', 'required');
      $this->form_validation->set_rules('phone', 'Phone Number', 'required|numeric');
      $this->form_validation->set_rules('email', 'Email', 'required');

      if ($this->form_validation->run() == true) {
        // get the variables and assign into $data variable
        $data = $this->fetch_data_from_post();

        if (is_numeric($update_id)) {
          //update the account details
          $this->_update($update_id, $data);
          // These two lines show the alert for the successful account details change.
          $flash_msg = "The account details were successfully updated.";
          $value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
          $this->session->set_flashdata('account', $value);
          // add the update data into the URL
          redirect('store_accounts/create/'.$update_id);
        } else {
          // insert a new account into DB
          $data['date_made'] = time();
          $this->_insert($data);
          $update_id = $this->get_max(); //get the ID of the new account

          $this->load->library('session');

          $flash_msg = "The account was successfully added.";
          $value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
          $this->session->set_flashdata('account', $value);
          // add the update data into the URL
          redirect('store_accounts/create/'.$update_id);
        }
      }
    }

    if ((is_numeric($update_id)) && ($submit != "Submit")) {
      $data = $this->fetch_data_from_db($update_id);
    } else {
      $data = $this->fetch_data_from_post();
    }

    if (!is_numeric($update_id)) {
      $data['headline'] = "Add New Account";
    } else {
      $data['headline'] = "Update Account Details";
    }
    // pass update id into the page
    $data['update_id'] = $update_id;
    $data['flash'] = $this->session->flashdata('account');

    // create a view file. Putting a php (html) into the admin template.

    // store_Accounts.php
    // $data['view_module'] = "store_Accounts";
    $data['view_file'] = "create"; // manage.php
    $this->load->module('templates');
    $this->templates->admin($data);
  }

  // get data from POST method
  function fetch_data_from_post() {
    $data['userName'] = $this->input->post('userName', true);
    $data['firstName'] = $this->input->post('firstName', true);
    $data['lastName'] = $this->input->post('lastName', true);
    $data['company'] = $this->input->post('company', true);
    $data['address1'] = $this->input->post('address1', true);
    $data['address2'] = $this->input->post('address2', true);
    $data['city'] = $this->input->post('city', true);
    $data['state'] = $this->input->post('state', true);
    $data['zip'] = $this->input->post('zip', true);
    $data['phone'] = $this->input->post('phone', true);
    $data['email'] = $this->input->post('email', true);
    return $data;
  }

  // get data from database
  function fetch_data_from_db($update_id) {

    if (!is_numeric($update_id)) {
      redirect('site_security/not_allowed');
    }

    $query = $this->get_where($update_id);
    foreach($query->result() as $row) {
      $data['userName'] = $row->userName;
      $data['firstName'] = $row->firstName;
      $data['lastName'] = $row->lastName;
      $data['company'] = $row->company;
      $data['address1'] = $row->address1;
      $data['address2'] = $row->address2;
      $data['city'] = $row->city;
      $data['state'] = $row->state;
      $data['zip'] = $row->zip;
      $data['phone'] = $row->phone;
      $data['email'] = $row->email;
      $data['date_made'] = $row->date_made;
      $data['password'] = $row->password;
      $data['last_login'] = $row->last_login;
    }
    if (!isset($data)) {
      $data = "";
    }
    return $data;
  }

  function get($order_by)
  {
    $this->load->model('mdl_store_accounts');
    $query = $this->mdl_store_accounts->get($order_by);
    return $query;
  }

  function get_with_limit($limit, $offset, $order_by)
  {
    if ((!is_numeric($limit)) || (!is_numeric($offset))) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_store_accounts');
    $query = $this->mdl_store_accounts->get_with_limit($limit, $offset, $order_by);
    return $query;
  }

  function get_where($id)
  {
    if (!is_numeric($id)) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_store_accounts');
    $query = $this->mdl_store_accounts->get_where($id);
    return $query;
  }

  function get_where_custom($col, $value)
  {
    $this->load->model('mdl_store_accounts');
    $query = $this->mdl_store_accounts->get_where_custom($col, $value);
    return $query;
  }

  function get_with_double_condition($col1, $value1, $col2, $value2)
  {
    $this->load->model('mdl_store_accounts');
    $query = $this->mdl_store_accounts->get_with_double_condition($col1, $value1, $col2, $value2);
    return $query;
  }

  function _insert($data)
  {
    $this->load->model('mdl_store_accounts');
    $this->mdl_store_accounts->_insert($data);
  }

  function _update($id, $data)
  {
    if (!is_numeric($id)) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_store_accounts');
    $this->mdl_store_accounts->_update($id, $data);
  }

  function _delete($id)
  {
    if (!is_numeric($id)) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_store_accounts');
    $this->mdl_store_accounts->_delete($id);
  }

  function count_where($column, $value)
  {
    $this->load->model('mdl_store_accounts');
    $count = $this->mdl_store_accounts->count_where($column, $value);
    return $count;
  }

  function get_max()
  {
    $this->load->model('mdl_store_accounts');
    $max_id = $this->mdl_store_accounts->get_max();
    return $max_id;
  }

  function _custom_query($mysql_query)
  {
    $this->load->model('mdl_store_accounts');
    $query = $this->mdl_store_accounts->_custom_query($mysql_query);
    return $query;
  }

}
