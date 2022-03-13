<?

/**  Внимание, самая популярная сборка движка теперь доступна в бесплатном скачивании (версия gold за 2010 год)!!!!
* Это старая версия проекта Tesla - торрент сайта. Демо новой версии можно посмотреть по ссылке demo.muz-tracker.net
* Если желаете использовать движок, то оставьте пожалуйста копирайта на своих местах, иначе стабильной работы не гарантируется.
* Если желаете поискать и установить новые моды, советуем воспользоваться форумами: bit-tracker.info, bit-torrent.kiev.ua.
* Если желаете поблагодарить владельца 7Max7 за эту сборку, киньте пару монеток на R190680055855 или Z187703352284
* Если желаете обновить Gold версию до Platinum - придется заплатить около 70$ (с последующими бесплатными обновлениями)
* 
* Данная копия движка взята с сайта demo muz-tracker net и tesla-tracker.net! ))
* Спасибо за внимание к движку Tesla.
**/


if(!defined('IN_TRACKER'))
  die('Hacking attempt!');


//bot_newuser($CURUSER["username"]); 
    function bot_newuser($username)
    {
                            //НАПОМИНАЮ СДЕСЬ ДАННЫЕ ДОЛЖНЫ СОВПАДАТЬ С ТЕМИ ЧТО ВЫ РЕГИСТРИРОВАЛИ
        $botid = 92;             //Ид пользователя бота которого вы создали
     //   $botclass = 2;            //Класс вашего пользователя бота
        //$botname = "ViKa";    //Имя вашего бота
        
        

$number = 21; // число
$first = 1; // по умолчанию первая картинка - 1
$random = mt_rand($first, $number);


if ($random==1)(
$text="Хи хи тра ли вали ми [b]".$username."[/b]"
);
if ($random==2)(
$text="Я, Админ сайта, прошу взыскать с пользователя [b]".$username."[/b] пару бонусов, за пиар его логина."
);
if ($random==3)(
$text="Хочется сладкого, может полусладкого от [b]".$username."[/b]."
);
if ($random==4)(
$text="Отдам в хорошие руки котенка, по имени [b]".$username."[/b]. Возможен бартер."
);
if ($random==5)(
$text="И пусть говорят, что [b]".$username."[/b] лишь пользователь, но я знаю, он у нас ЕЩЕ тот пользователь!"
);
if ($random==6)(
$text="Ой - забанила, разбаньте если не сложно мою ошибку - [b]".$username."[/b] как можно быстреее!"
);
if ($random==7)(
$text="Удалюсь за деньги, а деньги тоже работа a.k.a. [b]".$username."[/b]."
);
if ($random==8)(
$text="И не твоей улице будет праздник [b]".$username."[/b] ;-)"
);
if ($random==9)(
$text="Привет админы, а ну повысьте до модератора его [b]".$username."[/b] :-P"
);
if ($random==10)(
$text="Здравствуй странник [b]".$username."[/b], на долго ли?!"
);
if ($random==11)(
$text="От попытки я ушла, от админа ушла, теперь и от [b]".$username."[/b] уйду..."
);
if ($random==12)(
$text="Короче, тут один к базе приконектился, я как обычно Неа, Не хочу и так далее, короче его новый ник уже [b]".$username."[/b]."
);
if ($random==13)(
$text="Кто сказал, что я болтлива??????? -> [b]".$username."[/b]."
);
if ($random==14)(
$text="Пропингуй меня [b]".$username."[/b] - мои адрес localhost."
);
if ($random==15)(
$text="Случайно сгенерированная фраза порождает новую фразу, на столь безсмысленную первой, А у нас новый пользователь [b]".$username."[/b] :D"
);
if ($random==16)(
$text="А я ViKa, к сожалению я одна такая, нежели [b]".$username."[/b] ;-)"
);
if ($random==17)(
$text="Пусть говорят малахов плюс, А я напишу: [b]".$username."[/b] плюс один."
);
if ($random==18)(
$text="Меня заставили написать это предложение, не виновата я, он сам ко мне пришел - [b]".$username."[/b]!"
);

if ($random==19)(
$text="Новый пользователь [b]".$username."[/b] бывают чудеса<------:-D"
);


if ($random==20)(
$text="Сюрприииииз!! А у нас девочка [b]".$username."[/b], не проверяла конечно :P"
);
if ($random==21)(
$text="От судьбы не уйдешь, Поздравляю тебя [b]".$username."[/b] виновник торжества."
);
    	sql_query("INSERT INTO shoutbox (id, userid, date, text) VALUES ('id='," .  sqlesc($botid) . ", ".time().", " . sqlesc(format_comment($text)) . ")") or sqlerr(__FILE__, __LINE__); 
    };



    function bot_newrelease($id,$name,$releasername,$class)
    {
    	global $DEFAULTBASEURL;

        $botid = 92;             //Ид пользователя бота которого вы создали
       // $botclass = 2;            //Класс вашего пользователя бота
       // $botname = "ViKa";    //Имя вашего бота
        
        $text = "Новый торрент [url=$DEFAULTBASEURL/details.php?id=".$id."]".$name."[/url] был залит пользователем [b][color=#".get_user_rgbcolor($class, $name)."]".$releasername."[/color][/b]";  
        
		if (!empty($name))
		sql_query("INSERT INTO shoutbox (id, userid, date, text) VALUES ('id='," .  sqlesc($botid) . ", ".time().", " . sqlesc(format_comment($text)) . ")") or sqlerr(__FILE__, __LINE__); 
		
		   }; 
		   
		   
		   /*
		   
		    function bot_birthday($id,$username,$presentclass,$subject)
    {
    	global $DEFAULTBASEURL;
    
                            //НАПОМИНАЮ СДЕСЬ ДАННЫЕ ДОЛЖНЫ СОВПАДАТЬ С ТЕМИ ЧТО ВЫ РЕГИСТРИРОВАЛИ
        $botid = 92;             //Ид пользователя бота которого вы создали
        $botclass = 2;            //Класс вашего пользователя бота
        $botname = "ViKa";    //Имя вашего бота
        
        $text = "$subject был залит пользователем [b][color=#".get_user_rgbcolor($presentclass, $username)."]".$releasername."[/color][/b]";  
        
      	sql_query("INSERT INTO shoutbox (id, userid, date, text) VALUES ('id='," .  sqlesc($botid) . ", ".time().", " . sqlesc($text) . ")") or sqlerr(__FILE__, __LINE__); 
	
		   }; 

		   
		   */
		   
		   


?>