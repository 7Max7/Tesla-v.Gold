<?
require "include/bittorrent.php";
dbconn();
loggedinorreturn();

if (get_user_class() < UC_MODERATOR)
   {
attacks_log('makepoll'); stderr($tracker_lang['error'], $tracker_lang['access_denied']);
die();
}

$action = htmlentities($_GET["action"]);
$pollid = (int)$_GET["pollid"];



if ($action == "edit")
{
	stdhead("Редактирование опроса");
	
	if (!is_valid_id($pollid))
		stderr($tracker_lang['error'],$tracker_lang['invalid_id']);
	$res = sql_query("SELECT * ,
	
	 (SELECT username FROM users WHERE id=polls.createby) AS createuser,
 (SELECT username FROM users WHERE id=polls.editby) AS edituser


	FROM polls WHERE forum='0' AND id = $pollid")
			or sqlerr(__FILE__, __LINE__);
	if (mysql_num_rows($res) == 0)
		stderr($tracker_lang['error'],"Нет опроса с таким ID.");
	$poll = mysql_fetch_array($res);
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$pollid = (int)$_POST["pollid"];
	$action = $_POST["action"];
	
	if ($action=='edit' && !is_valid_id($pollid))
		stderr($tracker_lang['error'],$tracker_lang['invalid_id']);
  $question = htmlspecialchars($_POST["question"]);
  $option0 = htmlspecialchars($_POST["option0"]);
  $option1 = htmlspecialchars($_POST["option1"]);
  $option2 = htmlspecialchars($_POST["option2"]);
  $option3 = htmlspecialchars($_POST["option3"]);
  $option4 = htmlspecialchars($_POST["option4"]);
  $option5 = htmlspecialchars($_POST["option5"]);
  $option6 = htmlspecialchars($_POST["option6"]);
  $option7 = htmlspecialchars($_POST["option7"]);
  $option8 = htmlspecialchars($_POST["option8"]);
  $option9 = htmlspecialchars($_POST["option9"]);
  $option10 = htmlspecialchars($_POST["option10"]);
  $option11 = htmlspecialchars($_POST["option11"]);
  $option12 = htmlspecialchars($_POST["option12"]);
  $option13 = htmlspecialchars($_POST["option13"]);
  $option14 = htmlspecialchars($_POST["option14"]);
  $option15 = htmlspecialchars($_POST["option15"]);
  $option16 = htmlspecialchars($_POST["option16"]);
  $option17 = htmlspecialchars($_POST["option17"]);
  $option18 = htmlspecialchars($_POST["option18"]);
  $option19 = htmlspecialchars($_POST["option19"]);
  
  $sort = (htmlentities($_POST["sort"])=="yes" ? "yes":"no");
  $comme = (htmlentities($_POST["comme"])=="yes" ? "yes":"no");
  
  $returnto = htmlentities($_POST["returnto"]);

  if (!$question || !$option0 || !$option1)
    stderr($tracker_lang['error'], "Заполните все поля формы!");

  if ($pollid){
		sql_query("UPDATE polls SET " .		
		"editby = " . sqlesc($CURUSER[id]) . ", " .
		"edittime = " . sqlesc(get_date_time()) . ", " .
		"question = " . sqlesc($question) . ", " .
		"option0 = " . sqlesc($option0) . ", " .
		"option1 = " . sqlesc($option1) . ", " .
		"option2 = " . sqlesc($option2) . ", " .
		"option3 = " . sqlesc($option3) . ", " .
		"option4 = " . sqlesc($option4) . ", " .
		"option5 = " . sqlesc($option5) . ", " .
		"option6 = " . sqlesc($option6) . ", " .
		"option7 = " . sqlesc($option7) . ", " .
		"option8 = " . sqlesc($option8) . ", " .
		"option9 = " . sqlesc($option9) . ", " .
		"option10 = " . sqlesc($option10) . ", " .
		"option11 = " . sqlesc($option11) . ", " .
		"option12 = " . sqlesc($option12) . ", " .
		"option13 = " . sqlesc($option13) . ", " .
		"option14 = " . sqlesc($option14) . ", " .
		"option15 = " . sqlesc($option15) . ", " .
		"option16 = " . sqlesc($option16) . ", " .
		"option17 = " . sqlesc($option17) . ", " .
		"option18 = " . sqlesc($option18) . ", " .
		"option19 = " . sqlesc($option19) . ", " .
		"sort = " . sqlesc($sort) . ", " .
		"comment = " . sqlesc($comme) . " " .
    "WHERE forum='0' AND id = $pollid") or sqlerr(__FILE__, __LINE__);
    
    	/// пишем в лог
    	$user_color = get_user_rgbcolor($CURUSER["class"], $CURUSER[username]);
		write_log("$CURUSER[username] отредактировал опрос $question ($pollid)\n", "$user_color","other");
		}
		
  else
  	sql_query("INSERT INTO polls VALUES(0" .
  	", " . sqlesc("") .
  	", " . sqlesc("") .
  	", " . sqlesc("$CURUSER[id]") .
  	", '" . get_date_time() . "'" .
	", " . sqlesc($question) .
    ", " . sqlesc($option0) .
    ", " . sqlesc($option1) .
    ", " . sqlesc($option2) .
    ", " . sqlesc($option3) .
    ", " . sqlesc($option4) .
    ", " . sqlesc($option5) .
    ", " . sqlesc($option6) .
    ", " . sqlesc($option7) .
    ", " . sqlesc($option8) .
    ", " . sqlesc($option9) .
 	", " . sqlesc($option10) .
	", " . sqlesc($option11) .
	", " . sqlesc($option12) .
	", " . sqlesc($option13) .
	", " . sqlesc($option14) .
	", " . sqlesc($option15) .
	", " . sqlesc($option16) .
	", " . sqlesc($option17) .
	", " . sqlesc($option18) .
	", " . sqlesc($option19) . 
	", " . sqlesc($sort) . 
    ", " . sqlesc($comme) .
      ", " . sqlesc(0) .
  	")") or sqlerr(__FILE__, __LINE__);

  if ($returnto == "main")
		header("Location: $DEFAULTBASEURL");
  elseif ($pollid)
		header("Location: $DEFAULTBASEURL/makepoll.php?action=edit&pollid=$pollid&returnto=main");
	else
		header("Location: $DEFAULTBASEURL");
	die;
}



if ($poll[edituser])
{
$b="<p><b>[</b>Отредактировал $poll[edituser] в $poll[edittime]<b>]</b></p>";
}
else
$b="";
if ($poll[createuser])
{
$a="<b>[</b>Создал $poll[createuser] в $poll[added]<b>]</b></p>";
}
else
$a="";

stdhead("Создание опроса");


if ($pollid) {
//	print("<h1>Редактировать опрос</h1>  $a$b<br>");
print("$a$b");
}
else
{
	
	// Warn if current poll is less than 3 days old
	$res = sql_query("SELECT question,added FROM polls WHERE forum='0' ORDER BY added DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);
	$arr = mysql_fetch_assoc($res);
	if ($arr)
	{
	  $hours = floor((gmtime() - sql_timestamp_to_unix_timestamp($arr["added"])) / 3600);
	  $days = floor($hours / 24);
	  if ($days < 3)
	  {
	    $hours -= $days * 24;
	    if ($days)
	      $t = "$days" . ($days > 1 ? "дней" : "день");
	    else
	      $t = "$hours " . ($hours > 1 ? "часов" : "час");
	    print("<p><font color=red><b>Внимание: На данным момент сущестует опрос - <i>" . format_comment($arr["question"]) . "</i>, которому не больше $t.</b></font></p>");
	  }
	}
//	print("<h1>Создать опрос</h1>");
	
}

?>

<table border=1 cellspacing=0 cellpadding=5>
Можете использовать <b>bb коды</b> в вариантах
<form method=post action=makepoll.php>
<tr><td class=rowhead>Вопрос <font color=red>*</font></td><td align=left><input name=question size=80 maxlength=255 value="<?=$poll['question']?>"></td></tr>
<tr><td class=rowhead>Вариант 1 <font color=red>*</font></td><td align=left><input name=option0 size=80 maxlength=255 value="<?=$poll['option0']?>"><br /></td></tr>
<tr><td class=rowhead>Вариант 2 <font color=red>*</font></td><td align=left><input name=option1 size=80 maxlength=255 value="<?=$poll['option1']?>"><br /></td></tr>
<tr><td class=rowhead>Вариант 3</td><td align=left><input name=option2 size=80 maxlength=255 value="<?=$poll['option2']?>"><br /></td></tr>
<tr><td class=rowhead>Вариант 4</td><td align=left><input name=option3 size=80 maxlength=255 value="<?=$poll['option3']?>"><br /></td></tr>
<tr><td class=rowhead>Вариант 5</td><td align=left><input name=option4 size=80 maxlength=255 value="<?=$poll['option4']?>"><br /></td></tr>
<tr><td class=rowhead>Вариант 6</td><td align=left><input name=option5 size=80 maxlength=255 value="<?=$poll['option5']?>"><br /></td></tr>
<tr><td class=rowhead>Вариант 7</td><td align=left><input name=option6 size=80 maxlength=255 value="<?=$poll['option6']?>"><br /></td></tr>
<tr><td class=rowhead>Вариант 8</td><td align=left><input name=option7 size=80 maxlength=255 value="<?=$poll['option7']?>"><br /></td></tr>
<tr><td class=rowhead>Вариант 9</td><td align=left><input name=option8 size=80 maxlength=255 value="<?=$poll['option8']?>"><br /></td></tr>
<tr><td class=rowhead>Вариант 10</td><td align=left><input name=option9 size=80 maxlength=255 value="<?=$poll['option9']?>"><br /></td></tr>
<tr><td class=rowhead>Вариант 11</td><td align=left><input name=option10 size=80 maxlength=255 value="<?=$poll['option10']?>"><br /></td></tr>
<tr><td class=rowhead>Вариант 12</td><td align=left><input name=option11 size=80 maxlength=255 value="<?=$poll['option11']?>"><br /></td></tr>
<tr><td class=rowhead>Вариант 13</td><td align=left><input name=option12 size=80 maxlength=255 value="<?=$poll['option12']?>"><br /></td></tr>
<tr><td class=rowhead>Вариант 14</td><td align=left><input name=option13 size=80 maxlength=255 value="<?=$poll['option13']?>"><br /></td></tr>
<tr><td class=rowhead>Вариант 15</td><td align=left><input name=option14 size=80 maxlength=255 value="<?=$poll['option14']?>"><br /></td></tr>
<tr><td class=rowhead>Вариант 16</td><td align=left><input name=option15 size=80 maxlength=255 value="<?=$poll['option15']?>"><br /></td></tr>
<tr><td class=rowhead>Вариант 17</td><td align=left><input name=option16 size=80 maxlength=255 value="<?=$poll['option16']?>"><br /></td></tr>
<tr><td class=rowhead>Вариант 18</td><td align=left><input name=option17 size=80 maxlength=255 value="<?=$poll['option17']?>"><br /></td></tr>
<tr><td class=rowhead>Вариант 19</td><td align=left><input name=option18 size=80 maxlength=255 value="<?=$poll['option18']?>"><br /></td></tr>
<tr><td class=rowhead>Вариант 20</td><td align=left><input name=option19 size=80 maxlength=255 value="<?=$poll['option19']?>"><br /></td></tr>
<tr><td class=rowhead>Сортировать</td><td>
<input type=radio name=sort value=yes <?=$poll["sort"] != "no" ? " checked" : "" ?>>Да
<input type=radio name=sort value=no <?=$poll["sort"] == "no" ? " checked" : "" ?>> Нет
</td></tr>
<tr><td class=rowhead>Комментарии</td><td>
<input type=radio name=comme value=yes <?=$poll["comment"] != "no" ? " checked" : "" ?>>Да
<input type=radio name=comme value=no <?=$poll["comment"] == "no" ? " checked" : "" ?>> Нет
</td></tr>
<tr><td colspan=2 align=center><input type=submit value=<?=$pollid?"'Редактировать'":"'Создать'"?> style='height: 20pt'></td></tr>
</table>
<p>Условия <font color=red>*</font> обязательны</p>
<input type=hidden name=pollid value=<?=$poll["id"]?>>
<input type=hidden name=action value=<?=$pollid?'edit':'create'?>
<input type=hidden name=returnto value=<?=htmlentities($_POST["returnto"])?>>
</form>

<? stdfoot(); ?>