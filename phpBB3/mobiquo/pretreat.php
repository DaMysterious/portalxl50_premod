<?php
if (isset($_GET['welcome']))
{
    include('./smartbanner/app.php');
    exit;
}

if ($_POST['method_name'] == 'verify_connection'){
	require_once TT_ROOT."include/classTTConnection.php";
    $type = isset($_POST['type']) ? $_POST['type'] : 'both';
    $connection = new classTTConnection();
    $connection->use_sockets = false;
    $connection->timeout = 10;
    echo serialize($connection->verify_connection($type));
    exit;
}

if($_GET['method_name'] != 'set_api_key' && $_SERVER['REQUEST_METHOD'] == 'GET')
{
    include 'web.php';
    exit;
}