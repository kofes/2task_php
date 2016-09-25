<?php
//Подключаем ключевые параметры
  // inlclude '\.\./mysql_login\.php';
  $mysql_database = "2task";
  $mysql_user = "root";
  $mysql_passwd = "12345";
  $mysql_host = "localhost";
  $global_gang_id = 1;
  //
  $db = new mysqli($mysql_host, $mysql_user, $mysql_passwd, $mysql_database);

//Проверка php запроса: status=exist|new&nickname=&passwd=[if (new) &email[&name|surname|patronymic|phone|]]
  if (!isset($_GET["status"]))                exit("STATUS_NULL");
  if (!isset($_GET["nickname"]))              exit("NICKNAME_NULL");
  if (!isset($_GET["passwd"]))                exit("PASSWD_NULL");
//Экранирование запросов
  $status = quotemeta($_GET['status']);
  if ($status != 'new' && $status != 'exist') exit("STATUS_BAD");
  $nickname = quotemeta($_GET['nickname']);
  $passwd =   quotemeta(password_hash($_GET['passwd'], PASSWORD_BCRYPT));
  if ($status == 'new' && !isset($_GET["email"]))    exit("EMAIL_NULL");
  $email = quotemeta($_GET['email']);

//Необязательные части запроса
  if (isset($_GET["name"]))       $name = quotemeta($_GET['name']);
  if (isset($_GET["surname"]))    $surname = quotemeta($_GET['surname']);
  if (isset($_GET["patronymic"])) $patronymic = quotemeta($_GET['patronymic']);
  if (isset($_GET["phone"]))      $phone = quotemeta($_GET['phone']);

//Проверяем на совпадения с nickname
  $res = $db->query("SELECT nickname FROM user WHERE nickname='$nickname'")->fetch_assoc();
  if ($status == 'new' && $res != null ||
      $status == 'exist' && $res == null) exit("NICKNAME_BAD");

//Проверяем на совпадения с email
  $res = $db->query("SELECT nickname FROM user WHERE email='$email'")->fetch_assoc();
  if ($status == 'new' && $res != null) exit("EMAIL_BAD");

//Если создаем новый профиль, то...
  if ($status == 'new') {
//... делаем insert > user
    $date = date("Y-m-d");
    $db->query("INSERT INTO user (nickname, passwd, name, surname, patronymic, email, phone, date_start)
                       VALUES ('$nickname', '$passwd', '$name', '$surname', '$patronymic', '$email', '$phone', $date)");
    $user_id = $db->query("SELECT id FROM user WHERE nickname='$nickname'")->fetch_assoc()['id'];
//... связываем с Global группой
    $db->query("INSERT INTO user_gang (user_id, gang_id)
                VALUES ('$user_id', '$global_gang_id')");
    $db->query("INSERT INTO gang (title, host)
    VALUES ('myself', '$user_id')");
//.. создаем личную группу пользователя для заданий из Global
    $myself_gang_id = $db->query("SELECT id FROM gang WHERE host='$user_id'")->fetch_assoc()['id'];
    $db->query("INSERT INTO user_gang (user_id, gang_id)
                VALUES ('$user_id', '$myself_gang_id')");
  }
  $user_id = $db->query("SELECT id FROM user WHERE nickname='$nickname'")->fetch_assoc()['id'];
  $res = $db->query("SELECT nickname, name, surname, patronymic, date_start, email, phone FROM user WHERE id = '$user_id'")->fetch_assoc();
  echo json_encode($res);
?>
