<?php
session_start();

require('include/route.php');
require('include/controller.php');
require('include/render.php');
require('include/model.php');
     
$route = $_GET['route'] ?? "";

new Route($route);

?>
