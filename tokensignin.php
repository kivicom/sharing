<?php

if (isset($_GET['action']) == 'logout') {
    $_SESSION['social_network'] = '';
    header("location:index.php");
    exit();
}

$userInfo = array(
    'social_id' => '',
    'name' => '',
    'given_name' => '',
    'family_name' => '',
    'social_page' => '',
    'sex' => '',
    'image' => '',
    'email' => '',
    'birthday' => '',
    'provider' => ''
);

if (!empty($_SESSION['social_network'])) {
    $userInfo = $_SESSION['social_network'];
}

require_once 'socialauth/api/Api.php';
require_once 'socialauth/api/google.php';
require_once'class/Database.php';

$google = new Google;
if (isset($_GET['provider']) == 'google') {
    $google->auth();
    $info = $google->getTokenInfo();


    if (isset($info['name']) || isset($info['id'])) {
        $userInfo = array(
            'social_id' => isset($info['id']) ? $info['id'] : '',
            'name' => isset($info['name']) ? $info['name'] : '',
            'given_name' => isset($info['given_name']) ? $info['given_name'] : '',
            'family_name' => isset($info['family_name']) ? $info['family_name'] : '',
            'social_page' => isset($info['link']) ? $info['link'] : $userInfo['link'],
            'image' => isset($info['picture']) ? $info['picture'] : $userInfo['picture'],
            'email' => isset($info['email']) ? $info['email'] : '',
            'sex' => isset($info['gender']) ? $info['gender'] : '',
            'birthday' => isset($info['birthday']) ? $info['birthday'] : '',
            'provider' => 'Google'
        );
    }

    $db = new Database();
    $result = $db->query("SELECT *  FROM `users` WHERE `provider` = '". $userInfo['provider'] ."' AND `social_id` = '". $userInfo['social_id'] . "' LIMIT 1");
    $record = mysqli_fetch_array($result);

    if (!$record) {
        $values = array(
        $userInfo['provider'],
        $userInfo['social_id'],
        $userInfo['name'],
        $userInfo['email'],
        $userInfo['social_page'],
        $userInfo['sex'],
        date('Y-m-d', strtotime($userInfo['birthday'])),
        $userInfo['image']
        );

        $query = "INSERT INTO `users` (`provider`, `social_id`, `name`, `email`, `social_page`, `sex`, `birthday`, `avatar`) VALUES ('";
        $query .= implode("', '", $values) . "')";

        $result = $db->query($query);
    }
    header("location:index.php");
}

$_SESSION['social_network'] = $userInfo;