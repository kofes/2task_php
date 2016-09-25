<?php
//Подключаем ключевые параметры
  // inlclude 'maxwell\.hopto\.org\:25575\/mysql_login\.php';
  $mysql_database = "2task";
  $mysql_user = "root";
  $mysql_passwd = "12345";
  $mysql_host = "localhost";
  $global_gang_id = 1;
  //
  $db = new mysqli($mysql_host, $mysql_user, $mysql_passwd, $mysql_database);

//Проверка php запроса: nickname
  if (!isset($_GET["nickname"]))              exit("NICKNAME_NULL");
//Экранирование запроса
  $nickname = quotemeta($_GET['nickname']);
  $output[] = $db->query("SELECT achivement.*
                          FROM achivement
                          INNER JOIN user_achivement
                            ON achivement.id = user_achivement.achivement_id
                          LEFT JOIN user
                            ON user_achivement.user_id = user.id
                          WHERE user.nickname = '$nickname'")->fetch_assoc();
  echo (json_encode($output));
?>
