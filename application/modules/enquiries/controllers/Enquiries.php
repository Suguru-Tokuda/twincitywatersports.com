<?php
class Enquiries extends MX_Controller {

  function __construct() {
    parent::__construct();
    $this->load->library('form_validation');
    $this->form_validation->set_ci($this);
  }

  function fix() {
    $this->load->module('site_security');
    $query = $this->get('id');
    foreach ($query->result() as $row) {
      $data['code'] = $this->site_security->generate_random_string(6);
      $this->_update($row->id, $data);
    }
    echo "finished";
  }

  function test() {
    $firstName = "Suguru";
    $lastName = "Tokuda";
    $this->say_my_name($firstName);
  }

  function _attempt_get_data_from_code($customer_id, $code) {
    // make sure customer is allowed to view/respond and fetch data
    $query = $this->get_where_custom('code', $code);
    $num_rows = $query->num_rows();

    foreach($query->result() as $row) {
      $data['subject'] = $row->subject;
      $data['message'] = $row->message;
      $data['sent_to'] = $row->sent_to;
      $data['date_created'] = $row->date_created;
      $data['opened'] = $row->opened;
      $data['sent_by'] = $row->sent_by;
      $data['urgent'] = $row->urgent;
    }
    // make sure code is good and customer is allowed to view/respond
    if (($num_rows < 1) OR ($customer_id != $data['sent_to'])) {
      redirect('site_security/not_allowed');
    }
    return $data;
  }

  function _draw_customer_inbox($customer_id) {
    // $this->output->enable_profiler(true);
    $folder_type = "inbox";
    $data['customer_id'] = $customer_id;
    $data['query'] = $this->_fetch_customer_enquiries($folder_type, $customer_id);
    $data['folder_type'] = ucfirst($folder_type);
    $data['flash'] = $this->session->flashdata('item');
    $this->load->view('customer_inbox', $data);
  }

  function say_my_name($firstName, $lastName=NULL) {
    if (!isset($lastName)) {
      echo "There's no last name";
    } else {
      echo "Hello $firstName $lastName";
    }
  }

  function create() {
    $this->load->module('site_security');
    $is_admin = $this->session->userdata('is_admin');
    $this->site_security->_make_sure_is_admin();

    $update_id = $this->uri->segment(3);
    $submit = $this->input->post('submit', true);
    $this->load->module('timedate');

    if ($submit == "cancel") {
      redirect('enquiries/inbox');
    } else if ($submit == "submit") {
      // process the form
      $this->load->library('form_validation');
      $this->form_validation->set_rules('sent_to', 'Recipient', 'required');
      $this->form_validation->set_rules('subject', 'Subject', 'required|max_length[250]');
      $this->form_validation->set_rules('message', 'Message', 'required'); // callback is for checking if the item already exists

      if ($this->form_validation->run() == true) {
        // get the variables and assign into $data variable
        $data = $this->fetch_data_from_post();
        // convert the datepicker into a unix timestamp
        $data['date_created'] = time();
        $data['sent_by'] = 0; // admin
        $data['opened'] = 0;
        $data['code'] = $this->site_security->generate_random_string(6);

        // insert a new item into DB
        $this->_insert($data);
        $flash_msg = "The message was successfully sent.";
        $value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
        $this->session->set_flashdata('item', $value);
        redirect('enquiries/inbox');
      }
    }

    if ((is_numeric($update_id)) && ($submit != "Submit")) {
      $data = $this->fetch_data_from_db($update_id);
      $data['message'] = "<br><br>
      ----------------------------------------------------<br>
      The original message is shown blow...<br><br>".$data['message'];
    } else {
      $data = $this->fetch_data_from_post();
    }


    if (!is_numeric($update_id)) {
      $data['headline'] = "Compose New Message";
    } else {
      $data['headline'] = "Reply to Message";
    }

    $data['options'] = $this->_fetch_customers_as_options();
    // pass update id into the enquiries
    $data['update_id'] = $update_id;
    $data['flash'] = $this->session->flashdata('item');
    $data['view_file'] = "create";
    $this->load->module('templates');
    $this->templates->admin($data);
  }

  function _fetch_customers_as_options() {
    // for the dropdown menu
    $options[''] = "Select Customer...";
    $this->load->module('store_accounts');
    $query = $this->store_accounts->get('lastName');
    foreach ($query->result() as $row) {
      $customer_name = $row->firstName." ".$row->lastName;
      $company_length = strlen($row->company);
      if ($company_length > 2) {
        $customer_name.= " from ".$row->company;
      }
      $customer_name = trim($customer_name);
      if ($customer_name != "") {
        $options[$row->id] = $customer_name;
      }
    }
    if (!isset($options)) {
      $options = "";
    }
    return $options;
  }

  function view() {
    $this->load->module('site_security');
    $this->site_security->_make_sure_is_admin();

    $update_id = $this->uri->segment(3);
    $this->_set_to_opened($update_id);

    $options[''] = 'Select...';
    $options['0'] = '0 Star';
    $options['1'] = '1 Star';
    $options['2'] = '2 Stars';
    $options['3'] = '3 Stars';
    $options['4'] = '4 Stars';
    $options['5'] = '5 Stars';

    $data['options'] = $options;
    $data['update_id'] = $update_id;
    $data['headline'] = "Enquiry ID - ".$update_id;
    $data['query'] = $this->get_where($update_id);
    $data['flash'] = $this->session->flashdata('item');
    $data['view_file'] = "view";
    $this->load->module('templates');
    $this->templates->admin($data);
  }

  function submit_ranking() {
    $this->load->module('site_security');
    $this->site_security->_make_sure_is_admin();
    $data['ranking'] = $this->input->post('ranking', true);
    $update_id = $this->uri->segment(3);
    $this->_update($update_id, $data);

    $flash_msg = "The enquiry ranking was successfully updated.";
    $value = '<div class="alert alert-success" role="alert">'.$flash_msg.'</div>';
    $this->session->set_flashdata('item', $value);

    redirect('enquiries/view/'.$update_id);
  }

  function _set_to_opened($update_id) {
    $data['opened'] = 1;
    $this->_update($update_id, $data);
  }

  function inbox() {
    // $this->output->enable_profiler(true);
    $this->load->module('site_security');
    $this->site_security->_make_sure_is_admin();

    $folder_type = "inbox";
    $data['query'] = $this->_fetch_enquiries($folder_type);
    $data['folder_type'] = ucfirst($folder_type);

    // gettinf flash data
    $data['flash'] = $this->session->flashdata('item');
    // store_Accounts.php
    $data['view_file'] = "view_enquiries";
    $this->load->module('templates');
    $this->templates->admin($data);
  }

  function _fetch_enquiries($folder_type) {
    // $mysql_query = "SELECT * FROM enquiries WHERE sent_to = 0 ORDER BY date_created DESC";
    $mysql_query = "
    SELECT e.*, sa.firstName, sa.lastName, sa.company
    FROM enquiries e LEFT JOIN store_accounts sa ON e.sent_by = sa.id
    WHERE e.sent_to = 0
    ORDER BY e.date_created DESC
    ";
    $query = $this->_custom_query($mysql_query);
    return $query;
  }

  function _fetch_customer_enquiries($folder_type, $customer_id) {
    $mysql_query = "
    SELECT e.*, sa.firstName, sa.lastName, sa.company
    FROM enquiries e JOIN store_accounts sa ON e.sent_to = sa.id
    WHERE e.sent_to = $customer_id
    ORDER BY e.date_created DESC
    ";
    $query = $this->_custom_query($mysql_query);
    return $query;
  }

  // get data from POST method
  function fetch_data_from_post() {
    $data['subject'] = $this->input->post('subject', true);
    $data['message'] = $this->input->post('message', true);
    $data['sent_to'] = $this->input->post('sent_to', true);
    return $data;
  }

  // get data from database
  function fetch_data_from_db($update_id) {

    if (!is_numeric($update_id)) {
      redirect('site_security/not_allowed');
    }

    $query = $this->get_where($update_id);
    foreach($query->result() as $row) {
      $data['subject'] = $row->subject;
      $data['message'] = $row->message;
      $data['sent_to'] = $row->sent_to;
      $data['date_created'] = $row->date_created;
      $data['opened'] = $row->opened;
      $data['sent_by'] = $row->sent_by;
      $data['ranking'] = $row->ranking;
    }
    if (!isset($data)) {
      $data = "";
    }
    return $data;
  }

  function get($order_by)
  {
    $this->load->model('mdl_enquiries');
    $query = $this->mdl_enquiries->get($order_by);
    return $query;
  }

  function get_with_limit($limit, $offset, $order_by)
  {
    if ((!is_numeric($limit)) || (!is_numeric($offset))) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_enquiries');
    $query = $this->mdl_enquiries->get_with_limit($limit, $offset, $order_by);
    return $query;
  }

  function get_where($id)
  {
    if (!is_numeric($id)) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_enquiries');
    $query = $this->mdl_enquiries->get_where($id);
    return $query;
  }

  function get_where_custom($col, $value)
  {
    $this->load->model('mdl_enquiries');
    $query = $this->mdl_enquiries->get_where_custom($col, $value);
    return $query;
  }

  function get_with_double_condition($col1, $value1, $col2, $value2)
  {
    $this->load->model('mdl_enquiries');
    $query = $this->mdl_enquiries->get_with_double_condition($col1, $value1, $col2, $value2);
    return $query;
  }

  function _insert($data)
  {
    $this->load->model('mdl_enquiries');
    $this->mdl_enquiries->_insert($data);
  }

  function _update($id, $data)
  {
    if (!is_numeric($id)) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_enquiries');
    $this->mdl_enquiries->_update($id, $data);
  }

  function _delete($id)
  {
    if (!is_numeric($id)) {
      die('Non-numeric variable!');
    }

    $this->load->model('mdl_enquiries');
    $this->mdl_enquiries->_delete($id);
  }

  function count_where($column, $value)
  {
    $this->load->model('mdl_enquiries');
    $count = $this->mdl_enquiries->count_where($column, $value);
    return $count;
  }

  function get_max()
  {
    $this->load->model('mdl_enquiries');
    $max_id = $this->mdl_enquiries->get_max();
    return $max_id;
  }

  function _custom_query($mysql_query)
  {
    $this->load->model('mdl_enquiries');
    $query = $this->mdl_enquiries->_custom_query($mysql_query);
    return $query;
  }

}
