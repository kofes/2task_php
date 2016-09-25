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

//Проверка php запроса: nickname=&passwd=&group_title=&action=edit|leave|destroy
  if (!isset($_GET["nickname"]))    exit("NICKNAME_NULL");
  if (!isset($_GET["passwd"]))      exit("PASSWD_NULL");
  if (!isset($_GET["group_title"])) exit("TITLE_NULL");
  if (!isset($_GET["action"]))      exit("ACTION_NULL");

//Забиваем и экранируем данные
  $nickname =    quotemeta($_GET['nickname']);
  $passwd =      $_GET['passwd'];
  $group_title = quotemeta($_GET['group_title']);
  $action =      quotemeta($_GET['action']);

  if ($action != 'edit' && $action != 'leave' && $action != 'destroy') exit("ACTION_BAD");
  //Пользователь не может выйти из глобальной группы и личной
  if ($group_title == 'Global' || $group_title == 'myself') exit("GROUP_BAD");
//user > user_id
  $res = $db->query("SELECT id, passwd FROM user WHERE nickname='$nickname'")->fetch_assoc();
  if ($res['id'] == null) exit("NICKNAME_BAD");
  if (!password_verify($passwd, $res['passwd'])) exit("PASSWD_BAD");
  //Берем group_id, в котором состоит пользователь
  $group_id = $db->query("SELECT gang.id
                          FROM gang
                          INNER JOIN user_gang
                            ON gang.id = user_gang.gang_id AND gang.title = '$group_title'
                          WHERE user_gang.user_id = '$user_id'")->fetch_assoc()['id'];
  if ($group_id == null) exit("GROUP_BAD");
  //Извлекаем id хоста
  $host = $db->query("SELECT host FROM gang WHERE gang.id = '$group_id'")->fetch_assoc()['host'];
  if ($host != $user_id && $action != 'leave') exit('ACTION_DENIED');
  //Если host!=user, то leave
  if ($host != $user_id)
    $db->query("DELETE FROM user_gang WHERE user_id='$user_id' AND gang_id='$group_id'");
  if ($action == 'edit') {
    // Нужно описать интерфейс Edit
  } else {
    //Если host решил покинуть или удалить группу, то нужно удалить
    //пользователей группы, группу, таски группы.
    $db->query("DELETE FROM user_gang WHERE gang_id='$group_id'");
    $db->query("DELETE FROM gang WHERE id = '$group_id'");
    foreach ($db->query("SELECT task_id FROM gang_task WHERE gang_id = '$group_id'") as $task_id) {
      $id = $task_id['task_id'];
      $db->query("DELETE FROM task WHERE id = '$task_id'");
      $db->query("DELETE FROM gang_task WHERE task_id = '$id'");
    }
  }
?>
