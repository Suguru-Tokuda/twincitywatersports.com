<?php
class site_security extends MX_Controller {

  function __construct() {
    parent::__construct();
  }

  function test() {
    $length = 32;
    echo $this->generate_random_string($length);
  }

  function _check_admin_login_details($userName, $password) {
    $target_userName = "admin";
    $target_pass = "password";

    if (($userName == $target_userName) && ($password == $target_pass)) {
      return true;
    } else {
      return false;
    }
  }

  function _make_sure_logged_in() {
    // make sure the customer (member) is logged in
    $user_id = $this->_get_user_id();
    if (!is_numeric($user_id)) {
      redirect('youraccount/login');
    }
  }

  function _get_user_id() {
    // attempt to get the ID for the user

    // start by checking for a session variable
    $user_id = $this->session->userdata('user_id');

    if (!is_numeric($user_id)) {
      // check for a valid cookie
      $this->load->module('site_cookies');
      $user_id = $this->site_cookies->_attempt_get_user_id();
    }
    return $user_id;
  }

  // this method returns a randomized string
  function generate_random_string($length) {
    $characters = '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
  }

  function _encrypt_string($str) {
    $this->load->library('encryption');
    $encrypted_string = $this->encryption->encrypt($str);
    return $encrypted_string;
  }

  function _decrypt_string($str) {
    $this->load->library('encryption');
    $decrypted_string = $this->encryption->decrypt($str);
    return $decrypted_string;
  }

  function _hash_string($str) {
    $hashed_string = password_hash($str, PASSWORD_BCRYPT, array(
      'cost' => 11
    ));
    return $hashed_string;
  }

  function _verify_hash($plain_text, $hashed_string) {
    $result = password_verify($plain_text, $hashed_string);
    return $result; // TRUE or FALSE
  }

  function _make_sure_is_admin() {
    $is_admin = $this->session->userdata('is_admin');
    // $is_admin = $this->session->userdata('is_admin');
    if ($is_admin == 1) {
      return true;
    } else {
      redirect('site_security/not_allowed');
    }
  }

  function not_allowed() {
    echo "You are not allowed to be here.";
  }

}
