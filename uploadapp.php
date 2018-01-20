<? 
require_once("include/bittorrent.php"); 

dbconn(false); 
loggedinorreturn(); 

global $auto_duploader;

if (is_valid_id($auto_duploader) && !empty($auto_duploader)){

if(($CURUSER["class"] == UC_USER || $CURUSER["class"] == UC_POWER_USER) && $CURUSER['override_class'] == 255 && $CURUSER["promo_time"]<get_date_time(gmtime() - 86400*$auto_duploader)){

$usercomment = gmdate("Y-m-d") . " - Хочет заливать, автомат повышение.\n".$CURUSER["usercomment"]; 
sql_query("UPDATE users SET class='3', promo_time=" . sqlesc(get_date_time()).", usercomment=".sqlesc($usercomment)." WHERE id=" . $CURUSER["id"]) or sqlerr(__FILE__,__LINE__);

echo "Вас автоматически повысили до прав Аплоадер (Заливающий).<script>setTimeout('document.location.href=\"index.php\"', 10000);</script>";
die;
}

}
//if ($CURUSER['override_class'] <> 255)
//stderr("Ошибка", "Нельзя протестировать под вашими правами эту функцию.");


stdheadchat("Заявка в Аплоудеры"); 


?>
<style> 
.popup { cursor: help; text-decoration: none } 
</style>
<?

if ($_POST["form"] == "") 
{
        if($CURUSER["class"] < UC_MODERATOR) 
        $CURUSER["uploadpos"] == 'no'?0:2; 
        else 
        $form=10; 
}
else 
$form=htmlspecialchars($_POST["form"]); 

if($form == 0)
{
        $res=mysql_query("SELECT * FROM uploadapp WHERE userid=".$CURUSER["id"]) or sqlerr(__FILE__, __LINE__); 
        if(mysql_num_rows($res)) 
        {
                $row=mysql_fetch_array($res); 
                $form=4; 
        }
}

$debug=0; 
$upreq = 5; 
$upreqn = $upreq * 1073741824; 


begin_main_frame(); 
if($debug) 
{
        begin_frame("Debug Box"); 
        print("<table>"); 
        print("<form action=\"uploadapp.php\" method=\"post\" enctype=\"multipart/form-data\" name=\"debug\" id=\"uploadapp\">"); 
        tr("User Class","&nbsp;&nbsp;".get_user_class_name($CURUSER["class"]),1); 
        tr("Variables", 
        "form = " . $form. "<br>". 
        "user = " . htmlspecialchars($_POST["user"]). "<br>". 
        "groupacct = " . htmlspecialchars($_POST["groupacct"]). "<br>". 
        "grouname = " . unesc(htmlspecialchars($_POST["groupname"])). "<br>". 
        "groupdes = " . unesc(htmlspecialchars($_POST["groupdes"])). "<br>". 
        "joined = " . unesc(htmlspecialchars($_POST["joined"])). "<br>". 
        "ratio = " . unesc(htmlspecialchars($_POST["ratio"])). "<br>". 
        "upk =". unesc(htmlspecialchars($_POST["upk"])). "<br>". 
        "rbseed = ".unesc(htmlspecialchars($_POST["rbseed"])). 
        "<br>rbrelease = ".unesc(htmlspecialchars($_POST["rbrelease"])). 
        "<br>rbstime = ".unesc(htmlspecialchars($_POST["rbstime"]))) . "<br>". 
        "plans =". unesc(htmlspecialchars($_POST["plans"])). "<br>". 
        "comment =". unesc(htmlspecialchars($_POST["comment"]). "<br>"."",1); 
        tr("View forms","<input type=\"radio\" name=\"form\" value=\"0\" ". ($form==0?"checked":""). ">Upload App". 
        "<input name=\"form\" type=\"radio\" value=\"3\" ".($form==3?"checked":"").">Moderator+ Page". 
        "<input type=\"submit\" name=\"SubmitD\" value=\"Change Forms\">",1); 
        print("</table> </form>"); 
        end_frame(); 
} 
if($form>=10 && $CURUSER["class"] < UC_MODERATOR){
        begin_frame("Неправильный запрос"); 
        end_frame(); 
}
elseif($form == 0)
{
	    $CURUSER[uploaded]=$CURUSER[uploaded]+1;
	    $CURUSER[downloaded]=$CURUSER[downloaded]+1;
	
        if (($CURUSER[added] > gmdate("Y-m-d H:i:s", time() - 7*24*60*60)) || ($CURUSER[uploaded]/$CURUSER[downloaded] < 1))
        {
                begin_frame("Ошибка"); 
                print("Ваш рейтинг слишком низок или дата регистрации была произведена недавно.<br>Аплодером может быть человек, который:<br> 1) зарегистрировался более 7 дней назад<br>
				 2) имеет рейтинг не ниже одного (1).<br>Повысьте пожалуйста рейтинг свой или подождите некоторое время. <br>Спасибо за внимание."); 
                end_frame(); 
        }
        else         
        {
        begin_frame("Кандидаты в Алоудеры"); 

        ?> 
        
        <form action="uploadapp.php" method="post" enctype="multipart/form-data" name="uploadapp" id="uploadapp"> 
        <table border="1" cellspacing="0" cellpadding="10"> 
        <table border="0"> 
        <? 
        if ($CURUSER["downloaded"] > 0)
        $ratio = $CURUSER['uploaded'] / $CURUSER['downloaded']; 
        else if ($CURUSER["uploaded"] > 0) 
        $ratio = 1; 
        else
        $ratio = 0; 
        tr("Пользователь","&nbsp;&nbsp;<input name=\"user\" type=\"hidden\" value=\"". $CURUSER['id']."\">".$CURUSER['username'],1); 
  /*     tr("Is this a<br>Group Acct?","<input type=\"radio\" name=\"groupacct\" value=\"1\">Yes". 
 "<input name=\groupacct\" type=\"radio\" value=\"0\" checked>No",1); 
       tr("","<h2><center>For Group Applications only</center></h2>",1); 
       tr("Group Name","<input name=\"groupname\" type=\"text\" id=\"groupname\" size=\"50\" maxlength=\"50\">",1); 
       tr("Group ID<br>(3 char designator)","<input name=\"groupdes\" type=\"text\" id=\"groupdes\" size=\"7\" maxlength=\"3\">",1); 
       tr("","<h2><center>For All Applicants</center></h2>",1);  */ 
        tr("Дата регистрации","&nbsp;&nbsp;<input name=\"joined\" type=\"hidden\" value=\"".$CURUSER['added']."\">".$CURUSER['added'],1); 
        tr("Мое ратио больше или около 1.0","&nbsp;&nbsp;<input name=\"ratio\" type=\"hidden\" value=\"".($ratio>=1?"ok":"not ok")."\">".($ratio>=1?"Да":"Нет"),1); 
        $upreqm=$CURUSER['uploaded']>=$upreqn; 
        tr("Я загрузил около или более ". $upreq ." Гб","&nbsp;&nbsp;<input name=\"upk\" type=\"hidden\" value=\"".($upreqm?"yes":"no")."\">".($upreqm?"Да":"Нет"),1); 
        tr("Что я хочу раздавать","<textarea name=\"plans\" cols=\"100\" rows=\"4\" wrap=\"VIRTUAL\"></textarea><br>(напишите в этой форме типы материала, например фильмы, музыка, софт итд)",1); 
        tr("Почему вы должны повысить меня до Аплоудера","<textarea name=\"comment\" cols=\"100\" rows=\"6\" wrap=\"VIRTUAL\"></textarea><br>(напишите в этой форме причину, почему вы хотите быть аплодером и свою канальную скорость на аплоад)",1); 


		?>
        </table> 
        <p>Я знаю как сидировать и создавать торренты<br> 
        <input type="radio" name="rbseed" value="1"> 
        Да<br> 
        <input name="rbseed" type="radio" value="0" checked>Нет</p> 
        <p>Я понимаю, что мне надо будет постоянно выкладывать на треккер свои торренты<br><input type="radio" name="rbrelease" value="1"> 
        Да<br> 
        <input name="rbrelease" type="radio" value="0" checked> 
        Нет</p> 
        <p>Я понимаю что мне надо будет сидировать свои торренты на максимальной скорости пока не появятся еще 2 сидера<br> 
        <input type="radio" name="rbstime" value="1"> 
        Да<br> 
        <input name="rbstime" type="radio" value="0" checked> 
        Нет</p> 
        <br> 
        <input name="form" type="hidden" value="1"> 
        <input type="submit" name="Submit" value="Послать запрос"> 
        </table> 
        </form> 
        <? 

        } 

} 
else if ($form==1) 
{
        begin_frame("Запрос на повышение"); 
        $qry="INSERT INTO uploadapp (userid,applied,grpacct,grpname,grpdes,content,comment,seeding,othergrps,seedtime) ". 
        "VALUES (". htmlspecialchars($_POST["user"]).", ". 
        implode(",",array_map("sqlesc",array(
        get_date_time(), 
        htmlspecialchars($_POST["groupacct"]), 
        htmlspecialchars($_POST["groupname"]), 
        htmlspecialchars($_POST["groupdes"]), 
        htmlspecialchars($_POST["plans"]), 
        htmlspecialchars($_POST["comment"]), 
        htmlspecialchars($_POST["rbseed"]), 
       htmlspecialchars($_POST["rbrelease"]),
        htmlspecialchars($_POST["rbstime"])))).")";
        $ret=mysql_query($qry); 
        if (!$ret) {
                if (mysql_errno() == 1062) 
                print("Запрос уже был послан.<br>"); 
                else 
                print("mysql puked: ".mysql_error()); 
        }
        else
        {
                $subject = sqlesc("Новый запрос на должность Аплоадера"); 
                $now = sqlesc(get_date_time()); 
                $msg = sqlesc("Подан запрос на должность [b]Аплоадера[/b].\n[url=$DEFAULTBASEURL/uploadapp.php]Перейти в панель управления запросами[/url]."); 

                sql_query("INSERT INTO messages (sender, receiver, added, msg, subject, poster) SELECT 0, id, $now, $msg, $subject, 0 FROM users WHERE class = 4") or sqlerr(__FILE__,__LINE__); 

                print("<h2>Ваш запрос успешно послан. Ждите ответа от администрации.</h2>"); 
            } 
        end_frame(); 
} 
else if($form==2) 
{ 
        begin_frame("Запрос на повышение"); 
        print("<h2>Вы уже посылали запрос.</h2>"); 
        end_frame(); 
} 
else if($form==4) 
{ 
        begin_frame("Ваш запрос на повышение"); 
        $votesyes=$votesno=0; 
        if($row["votes"]!=""){
                $votes=explode(" ",$row["votes"]); 
                for($i=0;$i<count($votes);$i++) 
                {
                        $votei=explode(":",$votes[$i]); 
                        $votei[1]?$votesyes++:$votesno++; 
                }
        }
        print("Голосование: Да = ".($votesyes?$votesyes:0)." &nbsp;&nbsp; Нет = ".($votesno?$votesno:0)); 
        print("<br>Опросы ".($row["active"]=="0"?"Закрыты":"Открыты")."."); 
        end_frame(); 
} 
else if($form>=10) 
{ 
        begin_frame("Голосование за Аплоудеров"); 
        if($form==11) 
        {
                $res=mysql_query("SELECT * FROM uploadapp WHERE id=".((int)$_POST["pollid"])) or sqlerr(__FILE__, __LINE__); 
                $row=mysql_fetch_array($res); 
                $votesyes=$votesno=$voted=0; 
                if($row["votes"]!="") 
                {
                        $votes=explode(" ",$row["votes"]); 
                        for($i=0;$i<count($votes);$i++) 
                        {
                                $votei=explode(":",$votes[$i]); 
                                if($CURUSER["id"]==$votei[0]) 
                                $voted++; 
                                $votei[1]?$votesyes++:$votesno++; 
                        } 
                } 
                if($_POST["ballet"] && $voted==0) 
                { 
                        $votes=($row["votes"]!=""?$row["votes"]." ":"").implode(":",array($CURUSER["id"],$_POST["ballet"]=="Да"?1:0)); 
                        mysql_query("UPDATE uploadapp SET votes='".$votes."' WHERE id=".((int)$_POST["pollid"])); 
                        print("Голос для ".$_POST["pollid"]." получен (".$_POST["ballet"].")<br>"); 
                }
                else if($_POST["closepoll"])
                {
                        print("Запрос на закрытие опроса ".$_POST["pollid"]." получен<br>"); 
                        if(count($votes)<1) 
                        { 
                                print("Отказано в доступе, нужно не менее 1 голос"); 
                        } 
                        else 
                        { 
                                mysql_query("UPDATE uploadapp SET active='0' WHERE id=".((int)$_POST["pollid"])); 
                                $tvotes=$votesyes+$votesno; 
                                $votea=$votesyes>$votesno; 
                                $modcomment = gmdate("Y-m-d") . " - Запрос на повышение: ".($votea?"Разрешено":"Отказано")." (Да = ".$votesyes." Нет = ".$votesno." (". 
                                number_format((($votea?$votesyes:$votesno)/$tvotes)*100,3)."%)"; 
                                print($modcomment."<br>"); 
                                if($votea) 
                                { 
                                        $mq="UPDATE users SET class='".UC_UPLOADER."',modcomment=CONCAT(modcomment,".sqlesc($modcomment."\n").") WHERE class<=3 and id=".$row["userid"]; 
                                        mysql_query($mq);
                                        print("Обновляем записи о пользователе...<br>"); 
                                        $dt = sqlesc(get_date_time()); 
                                        $msg = sqlesc("Поздравляем, вас повысили до Аплоудера!\n"); 
                                      
                                        mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster, location) VALUES(0, ".$row["userid"].", $dt, $msg, 0, 1)") or sqlerr(__FILE__, __LINE__); 
                                } 
                                else 
                                { 
                                        $mq="UPDATE users SET modcomment=CONCAT(modcomment,".sqlesc($modcomment."\n").") WHERE class<=3 and id=".$row["userid"]; 
                                        mysql_query($mq); 
                                        $dt = sqlesc(get_date_time()); 
                                        $msg = sqlesc("Извините, Но вам отказали статус Аплодера.\n"); 
                                        mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster, location) VALUES(0, ".$row["userid"].", $dt, $msg, 0, 1)") or sqlerr(__FILE__, __LINE__); 
                                } 
                        } 
                 } 
                 else if($_POST["removepoll"]){
                         mysql_query("DELETE FROM uploadapp where id=".((int)$_POST["pollid"])); 
                         print("Опрос ".$_POST["pollid"]." удален из базы"); 
                 }
                 else if($_POST["addcomment"]) 
                 {
                         print ("Комментарий: ".sqlesc($_POST["newcomments"])."<br>"); 
                         if(($_POST["newcomments"])) 
                         {
                                 $un=sqlesc($CURUSER["username"].": ".$_POST["newcomments"]."\n"); 
                                 $mq="UPDATE uploadapp SET modcomments=CONCAT(modcomments,".$un.") WHERE id=".sqlesc(htmlentities($_POST["pollid"])); 
                                 mysql_query($mq); 
                                 print("Добавить комментарий к опросу ".$_POST["pollid"]); 
                         }
                 }

       } 
       print("<h1>Текущие запросы</h1>"); 
       $res=mysql_query("SELECT * FROM uploadapp ORDER BY applied DESC") or sqlerr(__FILE__, __LINE__); 
       if(!mysql_num_rows($res)) 
       print("<h3>Нет"); 
       else 
       {
               ?> 
               <table width=100%> 
               <tr><td class=a>#</td> 
               <td class=a>Пользователь</td> 
               <td class=a><span title=" Дата запроса " class="popup">Дата&nbsp;запроса</span></td> 
               <!--<td><span title=" Group Affiliated Application " class="popup">Group</span></td> --> 
               <td class=a><span title=" Желаемый контент на раздачу" class="popup">Контент</span></td> 
               <td class=a><span title=" Комментарии от пользователя для адмнистрации" class="popup">Комментарии</span></td> 
               <td class=a><span title=" Подходит ли ратио пользователя для вступления в команду Аплоудеров?" class="popup">Ратио</span></td> 
               <td class=a><span title=" Подходит ли общий текущий аплоад пользователя?" class="popup"><?=$upreq?>Гб+</span></td> 
               <td class=a><span title=" Знает ли пользователь как создавать и сидировать торренты?" class="popup">Знание</span></td> 
               <!--<td><span title=" Does User acknowledge other groups right to only upload their titles?" class="popup">Groups</span></td>--> 
               <td class=a><span title=" Пользователь понимает, что ему надо будет сидировать свои торренты на максимальной скорости пока не появятся еще 2 сидера?" class="popup">Сидирование</span></td> 
               <td class=a>Опрос</td> 
               </tr> 
               <? 
               while($row=mysql_fetch_array($res)) 
               {
                       $resu=mysql_query("SELECT * FROM users where id = ".$row["userid"])  or sqlerr(__FILE__, __LINE__);
                       $rowu=mysql_fetch_array($resu); 
                       $voted=$tvotes=$votesyes=$votesno=0; 
                       if($row["votes"]<>"") 
                       {
                               $votes=explode(" ",$row["votes"]); 
                               for($i=0;$i<count($votes);$i++) {
                                       $votei=explode(":",$votes[$i]); 
                                       if($CURUSER["id"]==$votei[0]) $voted++; 
                                       $votei[1]?$votesyes++:$votesno++; 
                                       $tvotes++; 
                               }
                       }
                       if ($rowu["downloaded"] > 0) 
                       $ratio = $rowu['uploaded'] / $rowu['downloaded']; 
                       else if ($rowu["uploaded"] > 0) 
                       $ratio = 1; 
                       else 
                       $ratio = 0; 
                       
                       if (empty($rowu["username"]))
                       sql_query("DELETE FROM uploadapp WHERE userid=".$row["userid"]."")or sqlerr(__FILE__, __LINE__);
                       
                       ?> 
                       <tr> 
                       <form action="uploadapp.php" method="post" enctype="multipart/form-data" name="poll<?=$row["id"]?>" id="uploadapp"> 
                       <input name="form" type="hidden" value="11"> 
                       <input name="pollid" type="hidden" value="<?=$row["id"]?>"> 
                       <td class=b><?=$row["id"]?></td> 
                       <td align=center>
                       
					   <a href=userdetails.php?id=<?=$row["userid"]?>><?=get_user_class_color($rowu["class"], $rowu["username"])?></a>
					   
					   
					   </td> 
                       <td><?=$row["applied"]?></td> 
                       <!--<td <?=($row["grpacct"]?"bgcolor=\"#FFFF00\">(".unesc($row["grpdes"]).")&nbsp;".unesc($row["grpname"]):">N/A")?></td>--> 
                   <td><?=htmlspecialchars($row["content"])?></td>
                       <td><?=htmlspecialchars($row["comment"])?></td> 
                       <td bgcolor="<?=($ratio>=1?"#00FF00":"#FF0000")?>"></td> 
                       <td bgcolor="<?=($rowu["uploaded"]>=$upreqn?"#00FF00":"#FF0000")?>"></td> 
                       <td bgcolor="<?=($row["seeding"]?"#00FF00":"#FF0000")?>"></td> 
                       <!--<td bgcolor="<?=($row["othergrps"]?"#00FF00":"#FF0000")?>"></td>--> 
                       <td bgcolor="<?=($row["seedtime"]?"#00FF00":"#FF0000")?>"></td> 
                       <td  class=a rowspan="2"><?=($voted||!$row["active"]?$votesyes." Да<br>".$votesno." Нет":"Голосов: ".$tvotes."<br>". 
                       "<input name=\"ballet\" type=\"submit\" value=\"Да\">". 
                       "<input name=\"ballet\" type=\"submit\" value=\"Нет\">"). 
                       (($CURUSER["class"]>=UC_MODERATOR&&$row["active"])?"<br><input name=\"closepoll\" type=\"submit\" value=\"Закрыть опрос\">": 
                       "<br>".($row["active"]?"<br><font color=#00FF00>Опрос открыт</font>":"<br><font color=#FF0000>Опрос закрыт</font>")). 
                       ($CURUSER["class"]>=UC_ADMINISTRATOR?"<br><input name=\"removepoll\" type=\"submit\" value=\"Удалить опрос\">":"") 
                       ?></td></tr>  </form><tr> 
                       <form action="uploadapp.php" method="post" enctype="multipart/form-data" name="Опрос <?=$row["id"]?>" id="uploadapp"> 
                       <input name="form" type="hidden" value="11"> 
                       <input name="pollid" type="hidden" value="<?=$row["id"]?>"> 
                       <td  class=a>&nbsp</td> 
                       <td>Комментарии</td> 
                       <td colspan="2"><textarea name="modcomments" rows="7" cols="35"><?=$row["modcomments"]?></textarea></td> 
                       <td colspan="5"><textarea name="newcomments" rows="6" cols="35"></textarea><br><input type="submit" name="addcomment" value="Добавить комментарий"></td> 
                       </form> 
                       </tr> 
                       <? 
               } 
       print("</table>"); 
       }


       end_frame(); 
}
end_main_frame(); 



stdfootchat(); 
?> 