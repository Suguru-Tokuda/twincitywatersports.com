<?php
// function _attempt_make_active($link_text) {
//   if ((current_url() == base_url().'youraccount/welcome') AND ($link_text == "Your Messages")) {
//     echo ' class="active"';
//   } else if ((current_url() == base_url().'youraccount/youroders') AND ($link_text == "Your Orders")) {
//     echo ' class="active"';
//   } else if ((current_url() == base_url().'listed_items/manage') AND ($link_text == "Your Items")) {
//     echo ' class="active"';
//   } else if ((current_url() == base_url().'youraccount/yourprofile') AND ($link_text == "Your Profile")) {
//     echo ' class="active"';
//   } else if ((current_url() == base_url().'youraccount/logout') AND ($link_text == "Log out")) {
//     echo ' class="active"';
//   }
// }
function _attempt_make_active($link_text) {
  if ((current_url() == base_url().'youraccount/welcome') AND ($link_text == "Your Messages")) {
       echo ' class="active"';
  } else if ((current_url() == base_url().'listed_items/manage') AND ($link_text == "Your Items")) {
    echo ' class="active"';
  } else if ((current_url() == base_url().'youraccount/yourprofile') AND ($link_text == "Your Profile")) {
    echo ' class="active"';
  } else if ((current_url() == base_url().'youraccount/logout') AND ($link_text == "Log out")) {
    echo ' class="active"';
  }
}
?>
<ul class="nav nav-tabs" style="margin-top: 24px;">
  <li role="presentation" <?= _attempt_make_active('Your Messages') ?>>
    <a href="<?= base_url() ?>youraccount/welcome">Your Message</a>
  </li>
  <!-- <li role="presentation" <?= _attempt_make_active('Your Orders') ?>>
    <a>Your Orders</a>
  </li> -->
  <li role="presentation" <?= _attempt_make_active('Your Items') ?>>
    <a href="<?= base_url() ?>listed_items/manage">Your Items</a>
  </li>
  <li role="presentation" <?= _attempt_make_active('Your Profile') ?>>
    <a>Update Your Profile</a>
  </li>
  <li role="presentation" <?= _attempt_make_active('Log out') ?>>
    <a href="<?= base_url() ?>youraccount/logout">Log out</a>
  </li>
</ul>
