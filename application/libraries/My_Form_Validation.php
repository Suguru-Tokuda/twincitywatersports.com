<?php
/** application/libraries/My_Form_validation **/
class My_Form_validation extends CI_Form_validation {
  public $CI;
  
  public function set_ci_reference( MX_Controller $ci ) {
    $this->CI = $ci;
  }
}
