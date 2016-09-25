<?php
//Подключаем ключевые параметры
  // inlclude 'maxwell\.hopto\.org\:25575\/mysql_login\.php';
  $mysql_database = "2task";
  $mysql_user = "root";
  $mysql_passwd = "12345";
  $mysql_host = "localhost";
  $global_gang_id = 1;

  $db = new mysqli($mysql_host, $mysql_user, $mysql_passwd, $mysql_database);

//Проверка php запроса: nickname
  if (!isset($_GET["nickname"]))       exit("NICKNAME_NULL");
//Проверка php запроса: passwd
  if (!isset($_GET["passwd"]))         exit("PASSWD_NULL");
//Проверка php запроса: group_title
  if (!isset($_GET["group_title"]))    exit("GROUP_TITLE_NULL");

// //Экранирование запроса
  $nickname =    quotemeta($_GET['nickname']);
  $passwd =      $_GET['passwd'];
  $group_title = quotemeta($_GET['group_title']);
  $res = $db->query("SELECT id FROM user WHERE nickname='$nickname'")->fetch_assoc();
  if ($res['id'] == null) exit("NICKNAME_BAD");
  if (!password_verify($passwd, $res['passwd'])) exit ("PASSWD_BAD");
  $group_id = $db->query(
   "SELECT gang.id
    FROM gang
    INNER JOIN user_gang
      ON user_gang.gang_id = gang.id
    INNER JOIN user
      ON user_gang.user_id = $res['id']
    WHERE gang.title = '$group_title'
    ");
  $output = $db->query(
   "SELECT user.nickname, user.name, user.surname, user.patronymic, user.date_start, user.email, user.phone, user.level, user.xp
     FROM user
     INNER JOIN user_gang
     ON user_gang.user_id = user.id
     INNER JOIN gang
     ON user_gang.gang_id = '$group_id'
     ORDER BY user.level"
   )->fetch_assoc();
   echo (json_encode($output));
?>
