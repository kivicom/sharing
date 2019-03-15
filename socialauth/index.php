<?php
    session_start();

    /*if (isset($_GET['action']) == 'getUser') {
        echo json_encode($_SESSION['social_network']);
        die;
    }*/

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

    require_once 'api/Api.php';
    require_once 'api/google.php';

    $google = new Google;
    if (isset($_GET['provider']) == 'google') {
        $google->auth();
        $info = $google->getTokenInfo();
        header("location:index.php");
        if (isset($info['name']) || isset($info['social_id'])) {
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
        $db = mysqli_connect('localhost', 'c33253_club', '123456', 'c33253_club');
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
    }

    $_SESSION['social_network'] = $userInfo;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
    <title>Аутентификация через Google</title>
</head>
<body>

<?php if(!empty($_SESSION['social_network']['name'])) :?>
    <p><a href="index.php?action=logout">Выйти</a></p>
<?php else:?>
    <p><a href="<?php echo $google->getLink(); ?>">Аутентификация через Google</a></p>
<?php endif;?>

<?php if ($userInfo) {

        if (!is_null($_SESSION['social_network']['social_id']))
            echo "Социальный ID пользователя: " . $_SESSION['social_network']['social_id'] . '<br />';

        if (!is_null($_SESSION['social_network']['name']))
            echo "Логин пользователя: " . $_SESSION['social_network']['name'] . '<br />';

        if (!is_null($_SESSION['social_network']['given_name']))
            echo "Имя пользователя: " . $_SESSION['social_network']['given_name'] . '<br />';

        if (!is_null($_SESSION['social_network']['family_name']))
            echo "Фамилия пользователя: " . $_SESSION['social_network']['family_name'] . '<br />';

        if (!is_null($_SESSION['social_network']['sex']))
            echo "Пол пользователя: " . $_SESSION['social_network']['sex'] . '<br />';

        if (!is_null($_SESSION['social_network']['email']))
            echo "E-mail пользователя: " . $_SESSION['social_network']['email'] . '<br />';

        if (!is_null($_SESSION['social_network']['social_page']))
            echo "Ссылка на профиль пользователя: " . $_SESSION['social_network']['social_page'] . "<br />";

        if (!is_null($_SESSION['social_network']['provider']))
            echo "Provider: " . $_SESSION['social_network']['provider'] . "<br />";

        if (!is_null($_SESSION['social_network']['image']))
            echo '<img src="' . $_SESSION['social_network']['image'] . '" />'; echo "<br />";

}
?>
</body>
</html>