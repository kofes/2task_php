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
  $status = quotemeta($_GET['status']);

  for ($i = 1; isset($_GET['title$i']); $i++) {
    # code...
    $title = quotemeta($_GET['title$i']);
    $description = quotemeta($_GET['title$i']);

    if (!isset($_GET['xp$i'])){
      $xp = quotemeta(0);
    }
    else{
      $xp = quotemeta($_GET['xp$i']);
    }

    if (!isset($_GET['coins$i'])){
      $coins = quotemeta(0);
    }
    else{
      $coins = quotemeta($_GET['coins$i']);
    }

    $output[] = $db->query(
     "SELECT task.id
      FROM task
      INNER JOIN gang_task
      ON gang_task.task_id = task.id
      INNER JOIN gang
      ON gang.id = gang_task.gang_id
      INNER JOIN user
      ON user.id = gang.host_task
      WHERE task.title = '$title' AND user.nickname = '$nickname'
      ORDER BY title"
    )->fetch_assoc();

    if (count($output) > 0){
      $db->query(
        "UPDATE task SET title = '$title', desctiption = '$desctiption', xp = $xp, coins = $coins
        WHERE task.id = $output[0][0]"
      );
    }
  }
  else {
    $db->query(
      "INSERT INTO task (title, description, xp, coins)
      VALUES('$title', '$description', $xp, $coins)"
    );
  }

  // echo (json_encode($output));
?>
