<?php
//Подключаем ключевые параметры
  // include '../mysql_login.php';
  $mysql_database = "2task";
  $mysql_user = "root";
  $mysql_passwd = "12345";
  $mysql_host = "localhost";
  $global_gang_id = 1;
  //
  $db = new mysqli($mysql_host, $mysql_user, $mysql_passwd, $mysql_database);

//Проверка php запроса: nickname=&passwd=&status=user|group[if (group) &title]]
  if (!isset($_GET["nickname"]))              exit("NICKNAME_NULL");
  if (!isset($_GET["passwd"]))                exit("PASSWD_NULL");
  if (!isset($_GET["status"]))                exit("STATUS_NULL");
//Забиваем и экранируем данные
  $nickname = quotemeta($_GET['nickname']);
  // $passwd =   quotemeta($_GET['passwd']);
  $passwd =   $_GET['passwd'];
  $status =   quotemeta($_GET['status']);
  if ($status != 'group' && $status != 'user') exit("STATUS_BAD");
//user > user_id
  $res = $db->query("SELECT id, passwd FROM user WHERE nickname='$nickname'")->fetch_assoc();
  if ($res['id'] == null) exit("NICKNAME_BAD");
  if (!password_verify($passwd, $res['passwd'])) exit("PASSWD_BAD");
//Если запрос из профиля, то выбираем данные с название группы и названием задания (Gang|Task)
  if ($status == 'user')
    foreach ($db->query("SELECT gang_id FROM user_gang WHERE user_id='$res['id']'") as $row) {
      $id = $row['gang_id'];
      $elem = $db->query("SELECT gang.title AS Gang, task.title AS Task, task.coins AS Coins, task.xp AS XP
        FROM gang
        INNER JOIN gang_task
          ON gang.id = gang_task.gang_id AND gang.id = '$id'
        INNER JOIN task
          ON gang_task.task_id = task.id
        GROUP BY gang.title
        LIMIT 1")->fetch_assoc();
      if ($elem != null)
        $output[] = $elem;
    }
//Если запрос из группы, то отдаем ей данные с заданиями данной группы (id|title|description|xp|coins|status)
  else {
    if (!isset($_GET["title"])) exit("TITLE_NULL");
    $title = quotemeta($_GET['title']);
    $gang_id = $db->query("SELECT gang.id
      FROM gang
      INNER JOIN user_gang
        ON gang.id = user_gang.gang_id AND gang.title = '$title'
      RIGHT JOIN user
        ON user_gang.user_id = $res['id']")->fetch_assoc()['id'];
    // echo $gang_id;
    $elem = $db->query("SELECT task.id,task.title,task.description, task.xp, task.coins
      FROM task
      INNER JOIN gang_task
        ON task.id = gang_task.task_id
      LEFT JOIN gang
        ON gang_task.gang_id = '$gang_id'
      GROUP BY task.id")->fetch_assoc();
    if ($elem != null)
      $output[] = $elem;
  }
  exit(json_encode($output));
?>
