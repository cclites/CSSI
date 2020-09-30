<?php

return array(
  "driver" => "smtp",
  "host" => "smtp.mailtrap.io",
  "port" => 2525,
  "from" => array(
      "address" => "Notifications@cssi.com",
      "name" => "Example"
  ),
  "username" => "3a6a69eb2c5d38",
  "password" => "bb2163f1fae9b5",
  "sendmail" => "/usr/sbin/sendmail -bs",
  "pretend" => false
);

?>