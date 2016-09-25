<?php
//Подключаем ключевые параметры
  // inlclude 'maxwell\.hopto\.org\:25575\/mysql_login\.php';
  $mysql_database = "2task";
  $mysql_user = "root";
  $mysql_passwd = "12345";
  $mysql_host = "localhost";
  $global_gang_id = 1;
//Константы
  $havent_group_flag = -1


  //
  $db = new mysqli($mysql_host, $mysql_user, $mysql_passwd, $mysql_database);

//Проверка php запроса: nickname
  if (!isset($_GET["nickname"]))              exit("NICKNAME_NULL");
//Проверка php запроса: passwd
  if (!isset($_GET["passwd"]))              exit("PASSWD_NULL");
//Проверка php запроса: group_title
  if (!isset($_GET["group_title"]))              exit("GROUP_TITLE_NULL");


//Экранирование запроса
  $nickname = quotemeta($_GET['nickname']);
  $passwd = quotemeta($_GET['passwd']);
  $group_list = quotemeta($_GET['group_list']);

  $output[] = $db->query("
    SELECT list_users.*
    FROM list_users
    WHERE
  ")->fetch_assoc();
  echo (json_encode($output));
?>
