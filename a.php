<?php
$haha = "123";
$password = password_hash($haha, PASSWORD_DEFAULT);
echo $password;