<?php 

$dbname = 'mysql:host=localhost;dbname=jewelrydatabase';
$dbuser = 'root';
$dbpass = '';

$conn = new PDO($dbname, $dbuser, $dbpass);

if (!$conn){
    echo 'Not Connected to database';
}
?>