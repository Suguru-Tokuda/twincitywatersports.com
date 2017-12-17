<?php
class Contactus extends MX_Controller {

  function __construct() {
    parent::__construct();
    $this->load->library('form_validation');
    $this->form_validation->set_ci($this);
  }

  function submit() {
    $submit = $this->input->post('submit', true);
    $refer_url = $_SERVER['HTTP_REFERER'];
    $target_refer_url = base_url().'contacts';

    if ($submit == "submit") {
      // process the form
      $this->load->library('form_validation');
      $this->form_validation->set_rules('yourname', 'Your name', 'required|max_length[60]');
      $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
      $this->form_validation->set_rules('phone', 'Phone number', 'required|numeric|max_length[20]');
      $this->form_validation->set_rules('message', 'Message', 'required');

      if ($this->form_validation->run() == true) {
        // get the variables and assign into $data variable
        $posted_data = $this->fetch_data_from_post();
        $this->load->module('enquiries');
        $this->load->module('site_security');

        $data['code'] = $this->site_security->generate_random_string(6);

        $data['subject'] = 'Contact Form';
        $data['message'] = $this->build_msg($posted_data);
        $data['sent_to'] = 0;
        $data['date_created'] = time();
        $data['opened'] = 0;
        $data['sent_by'] = 0;
        $data['urgent'] = 0;

        $this->enquiries->_insert($data);
        redirect('contactus/thankyou');
      } else {
        $this->index();
      }
    } else {
      // form submission error
      $this->index();
    }
  }

  function build_msg($posted_data) {
    $yourname = ucfirst($posted_data['yourname']);
    $msg = $yourname.' submitted the following information:<br><br>';
    $msg.= 'Name: '.$yourname."<br>";
    $msg.= 'Email: '.$posted_data['email']."<br>";
    $msg.= 'Phone number: '.$posted_data['phone']."<br>";
    $msg.= 'Name: '.$posted_data['message']."<br>";
    return $msg;
  }

  function index() {
    $this->load->module('site_settings');
    $data = $this->fetch_data_from_post();
    $data['our_company'] = $this->site_settings->_get_our_company_name();
    $data['our_address'] = $this->site_settings->_get_our_address();
    $data['our_phone'] = $this->site_settings->_get_our_phone();
    $data['map_code'] = $this->site_settings->_get_map_code();
    $data['form_location'] = base_url()."contactus/submit";
    $data['flash'] = $this->session->flashdata('item');
    $data['view_file'] = "contactus";
    $this->load->module('templates');
    $this->templates->public_bootstrap($data);
  }

  function thankyou() {
    $data['view_file'] = "thankyou";
    $this->load->module('templates');
    $this->templates->public_bootstrap($data);
  }

  function fetch_data_from_post() {
    $data['yourname'] = $this->input->post('yourname', true);
    $data['email'] = $this->input->post('email', true);
    $data['phone'] = $this->input->post('phone', true);
    $data['message'] = $this->input->post('message', true);
    return $data;
  }

}
