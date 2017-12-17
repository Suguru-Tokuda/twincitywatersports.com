<?php
class Site_settings extends MX_Controller {

  function __construct() {
    parent::__construct();
  }

  function _get_map_code() {
    $code = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3033.2137206972266!2d-88.9813836841813!3d40.514766979353666!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x880b70fd8daf6ebd%3A0xd371cc44d1c0b28f!2s407+E+Cypress+St%2C+Normal%2C+IL+61761!5e0!3m2!1sen!2sus!4v1502924850789" width="600" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>';
    return $code;
  }

  function _get_our_company_name() {
    $company = 'Twin City Cable Park Inc.';
    return $company;
  }

  function _get_our_address() {
    $address = '407 Cypress St<br>';
    $address.= 'Normal, IL 61761';
    return $address;
  }

  function _get_our_phone() {
    $phone = '(309) ***-****';
    return $phone;
  }

  function _get_paypal_email() {
    $email = "sfstboy@gmail.com";
    return $email;
  }

  function _get_email_for_admin_seller() {
    $email = "auction@twincitywatersports.com";
    return $email;
  }

  function _get_support_team_name() {
    $name = "Customer Support";
    return $name;
  }

  function _get_welcome_msg($customer_id) {
    $this->load->module('store_accounts');
    $customer_name = $this->store_accounts->_get_customer_name($customer_id);

    $msg = "Hello ".$customer_name.",<br><br>";
    $msg.= "Thank you for creating an account with Twin Cities Calbe Park";
    $msg.= "about any of our items, attraction, and service then please do get in touch. We are here ";
    $msg.= "seven days a week and would happy to help you.<br><br>";
    $msg.= "Regards,<br><br>";
    $msg.= "Twin Cities Cable Park Support Team";
    return $msg;
  }

  function _get_cookie_name() {
    $cookie_name = 'ehighwqges';
    return $cookie_name;
  }

  function _get_currency_symbol() {
    $symbol = "&dollar;";
    return $symbol;
  }

  function _get_currency_code() {
    $code = "USD"; // GBP, JPY
    return $code;
  }

  function _get_item_segments() {
    // return the segments for the store_item page (product page)
    $segments = "pre_owned/"; // this part changes depending on what kind of products that the shopping page tries to sell
    return $segments;
  }

  function _get_items_segments() {
    // return the segments for the category pages
    $segments = "pre_owned_items/";
    return $segments;
  }

  function _get_page_not_found_msg() {
    $msg = "<h1>It's a webpage Jim but not as we know it!</h1>";
    $msg.= "<p>Please check your vibe and try again.</p>";
    return $msg;
  }

  function _get_states_dropdown() {
    $states[''] = "Select state";
    $states['0'] = "AL";
    $states['1'] = "AK";
    $states['2'] = "AZ";
    $states['3'] = "AR";
    $states['4'] = "CA";
    $states['5'] = "CO";
    $states['6'] = "CT";
    $states['7'] = "DE";
    $states['8'] = "FL";
    $states['9'] = "GA";
    $states['10'] = "HI";
    $states['11'] = "ID";
    $states['12'] = "IL";
    $states['13'] = "IN";
    $states['14'] = "IA";
    $states['15'] = "KS";
    $states['16'] = "KY";
    $states['17'] = "LA";
    $states['18'] = "ME";
    $states['19'] = "MD";
    $states['20'] = "MA";
    $states['21'] = "MI";
    $states['22'] = "MN";
    $states['23'] = "MS";
    $states['24'] = "MO";
    $states['25'] = "MT";
    $states['26'] = "NE";
    $states['27'] = "NV";
    $states['28'] = "NH";
    $states['29'] = "NJ";
    $states['30'] = "NM";
    $states['31'] = "NY";
    $states['32'] = "NC";
    $states['33'] = "ND";
    $states['34'] = "OH";
    $states['35'] = "OK";
    $states['36'] = "OR";
    $states['37'] = "PA";
    $states['38'] = "RI";
    $states['39'] = "SC";
    $states['40'] = "SD";
    $states['41'] = "TN";
    $states['42'] = "TX";
    $states['43'] = "UT";
    $states['44'] = "VT";
    $states['45'] = "VA";
    $states['46'] = "WA";
    $states['47'] = "WV";
    $states['48'] = "WI";
    $states['49'] = "WY";
    $states['50'] = "GU";
    $states['51'] = "PR";
    return $states;
  }


}
