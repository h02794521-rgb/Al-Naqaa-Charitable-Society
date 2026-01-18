<?php
$cn=mysqli_connect("localhost","root","") or die("Could not Connect My Sql");
$conn1=mysqli_connect("localhost","root","") or die("Could not Connect My Sql");

mysqli_select_db($cn,"naqaa_db") or die("Could connect to Database");
$mysqli = new mysqli('localhost', 'root', '', 'naqaa_db');
$conn= new mysqli('localhost', 'root', '', 'naqaa_db');

?>