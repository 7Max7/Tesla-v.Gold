<?
require "include/bittorrent.php";
dbconn();
loggedinorreturn();

function insert_tag($name, $description, $syntax, $example, $remarks)
{
	$result = format_comment($example);
	print("<p align=center><b>$name</b></p>\n");
	print("<table class=main width=100% border=1 cellspacing=0 cellpadding=5>\n");
	print("<tr valign=top><td class=a width=25%>Описание:</td><td class=a >$description\n");
	print("<tr valign=top><td>Синтаксис:</td><td><tt>$syntax</tt>\n");
	print("<tr valign=top><td >Пример:</td><td><tt>$example</tt>\n");
	print("<tr valign=top><td>Результат:</td><td>$result\n");
	if ($remarks != "")
		print("<tr><td class=a>Примечание:</td><td class=a>$remarks\n");
	print("</table>\n");
}

stdhead("Теги");

begin_frame("Все Теги сайта");
$test = $_POST["test"];
?>
<p><center><b><?=$SITENAME?></b> поддерживает большое количество <b>BB тегов</b> которые вы можете использовать для украшения ваших раздач и постов.</center></p>
<?
if (get_user_class() >= UC_POWER_USER){
	?>
<center><form method=post action=?>
<textarea name=test cols=60 rows=3><? print($test ? htmlspecialchars($test) : "")?></textarea><br>
<input type=submit value="Проверить этот код!" style='height: 23px; margin-left: 5px'>
</form></center>
<?}?>
<?

if ((get_user_class() >= UC_POWER_USER) && ($test != ""))
  print("<p><hr>" . format_comment($test) . "<hr></p>\n");

if (get_user_class() <= UC_MODERATOR and $test != ""){

$user = $CURUSER["username"];
$user_ip1=getenv("REMOTE_ADDR"); 
$user_data="$user (".$user_ip1.")"; 



$sf =ROOT_PATH."cache/tags.txt"; 
$fpsf=fopen($sf,"a+"); 
$date = date("d.m.y"); 
$time= date("H:i:s"); 
//fputs($fpsf,"-------конец слежения $date: $time-----\n\n"); 
fputs($fpsf,"$date:$time $user_data:\n ".$test."\n---------------\n"); 

fclose($fpsf);




}


insert_tag(
	"Жирный",
	"Делает текст жирным.",
	"[b]Текст[/b]",
	"[b]Этот текст жирный.[/b]",
	""
);

insert_tag(
	"Курсив",
	"Делает текст наклонным.",
	"[i]Текст[/i]",
	"[i]Этот текст наклонный.[/i]",
	""
);

insert_tag(
	"Подчеркивание",
	"Делает текст подчеркнутым.",
	"[u]Текст[/u]",
	"[u]Этот текст подчеркнутый.[/u]",
	""
);

insert_tag(
	"Зачеркивание",
	"Делает текст зачеркнутым.",
	"[s]Текст[/s]",
	"[s]Этот текст зачеркнутый.[/s]",
	""
);

insert_tag(
	"Цвет (№ 1)",
	"Меняет цвет текста.",
	"[color=<i>Color</i>]<i>Текст</i>[/color]",
	"[color=red]Этот текст красный.[/color]",
	"Цвета должны быть на англ языке, к примеру blue red green pink black white"
);

insert_tag(
	"Цвет (№ 2)",
	"Меняет цвет текста.",
	"[color=#<i>RGB</i>]<i>Текст</i>[/color]",
	"[color=#ff0000]Этот текст красный.[/color]",
	"RGB цвет должен быть 6-ти значным номером шестнадцатиричной системы счисления."
);

insert_tag(
	"Размер",
	"Указывает размер текста.",
	"[size=<i>n</i>]<i>Текст</i>[/size]",
	"[size=18]Это 18 размер.[/size]",
	"Число должно быть в интервале от 1 (маленький) до 100 (большой). Размер по умолчанию - 8."
);

insert_tag(
	"Шрифт",
	"Указывает шрифт для текста.",
	"[font=<i>Font</i>]<i>Текст</i>[/font]",
	"[font=Impact]Проверка[/font]",
	""
);

insert_tag(
	"Ссылки (№ 1)",
	"Вставка ссылки.",
	"[url]<i>Ссылка</i>[/url]",
	"[url]http://www.example.com/[/url]",
	""
);

insert_tag(
	"Ссылки (№ 2)",
	"Вставка ссылки с названием.",
	"[url=<i>URL</i>]<i>Текст отображения</i>[/url]",
	"[url=http://www.example.com/]Пример[/url]",
	""
);

insert_tag(
	"Картинка (№ 1)",
	"Вставка картинки.",
	"[img]<i>Ссылка]</i>[/img]",
	"[img]http://www.rambler.ru/i/logos/friends.gif[/img]",
	"Ссылка должна заканчиваться на .gif, .jpg или .png </b>"
);

insert_tag(
	"Картинка (с ссылкой на сайт)",
	"Вставка картинки.",
	"[url=<i>Ссылка</i>][img]<i>ссылка на фото</i>[/img][/url]",
	"[url=http://www.rambler.ru/][img]http://www.rambler.ru/i/logos/friends.gif[/img][/url]",
	"Ссылка должна заканчиваться на .gif, .jpg или .png </b>"
);



insert_tag(
	"Спойлер (скрытый текст)",
	"Вставка Спойлеров.",
	"[spoiler]<i>Ваш Текст</i>[/spoiler]",
	"[spoiler=Проверка]Трекер еще живой, к чему это?![/spoiler]",
	"До 5 спойлеров можете использовать, что внутрене, что по оДиночке. </b>"
);

insert_tag(
	"Легкая версия спойлера",
	"Вставка Light Спойлеров.",
	"[light=Тайтл]Ваш Текст[/light]",
	"[light=Тайтл]Ваш Текст[/light]",
	"Отличия: light версия не понимает bb коды внутри Вашего Текста (и используется одноразово)</b>"
);



insert_tag(
	"Цитата (№ 1)",
	"Вставка цитаты.",
	"[quote]<i>Цитированный текст</i>[/quote]",
	"[quote]Этот трекер Может будешь жить.[/quote]",
	""
);

insert_tag(
	"Цитата (№ 2)",
	"Вставка цитаты.",
	"[quote=<i>Author</i>]<i>Цитированный текст</i>[/quote]",
	"[quote=7Max7]Этот трекер Может будешь жить.[/quote]",
	""
);

insert_tag(
	"Список",
	"Вставка списка.",
	"[li]<i>Текст</i>",
	"[li] Предложение 1\n[li] Предложение 2",
	""
);

insert_tag(
	"Список",
	"Вставка разделительной линии.",
	"[hr]",
	"[hr] Текст [hr]",
	""
);


insert_tag(
	"от Себя (me)",
	"Вставка от себя.",
	"[me]<i>Текст</i>",
	"[me] пишу от Себя (me)",
	""
);


insert_tag(
	"Линия разрыва",
	"Текст",
	"[pi]это у меня линия",
	"[pi]это у меня линия",
	"Похожий тег BR (использует перенос на другую строчку)"
);



insert_tag(
	"Чистый bb код (Неотформатированный)",
	"Текст",
	"[bb]это у чистый <b>bb код</b>[/bb]",
	"[bb]это у чистый [b]bb код[/b][/bb]",
	"Похожий тег BR (использует перенос на другую строчку)"
);

insert_tag(
	"В центре",
	"Текст или картинка в центре.",
	"[center]<i>Текст</i>[/center]",
	"[center] Предложение [/center]",
	""
);

insert_tag(
	"Слева",
	"Текст или картинка слева.",
	"[left]<i>Текст</i>[/left]",
	"[left] Слева текст[/left]",
	""
);

insert_tag(
	"Справа",
	"Текст или картинка слева.",
	"[right]<i>Текст</i>[/right]",
	"[right] Справа текст [/right]",
	""
);

insert_tag(
	"Преформатный текст",
	"Текст",
	"[pre]Ваш текст со знаками препинания, переносами (enter) на другую строку, пробеллов и тому подобное.
[/pre]",
	"[pre]Ваш текст со знаками препинания, переносами (enter) 
	 на другую строку,    пробеллов и тому подобное
	 ;-)
[/pre]",
	"Текст без обработки тегов,  пишем как в word'e."
);

insert_tag(
	"Подсветка синтаксиса",
	"Текст",
	"[highlight]Ваш текст[/highlight]",
	"[highlight]Ваш текст[/highlight]",
	"Для яркого выделения конкретного слова"
);

insert_tag(
	"Яркое выделение текста",
	"Текст",
	"[mcom=#FFD42A:#002AFF]это ваш цветной <b>тект</b> на вашем цветном <b>фоне</b>[/mcom]",
	"[mcom=#FFD42A:#002AFF]это ваш цветной текст на вашем цветном фоне[/mcom]",
	"Любые цвета при любимом фоне"
);

insert_tag(
	"Скрытие текста при наведение показ",
	"Текст",
	"[hideback]Скрытие текста при наведение показ[/hideback]",
	"[hideback]Скрытие текста при наведение показ[/hideback]",
	"Скрываем текст без наведения иначе показываем"
);

insert_tag(
	"Рамка вокруг текста",
	"Текст или Картинка ",
	"[legend]выделение рамкой текст[/legend]",
	"[legend]выделение рамкой текст[/legend]",
	"Выделятся текст вокруг рамкой."
);

insert_tag(
	"Рамка вокруг текста с цитатой",
	"Текст или Картинка ",
	"[legend=Привет всем]выделение рамкой текст с цитатой[/legend]",
	"[legend=Привет всем]выделение рамкой текст с цитатой[/legend]",
	"Выделятся текст вокруг рамкой."
);

insert_tag(
	"Бегающая строка",
	"Текст или Картинка ",
	"[marquee]бегающая строка[/marquee]",
	"[marquee]бегающая строка[/marquee]",
	"Все бежит наЛево"
);


/*
insert_tag(
	"Аудио",
	"Проигрыватель аудио файлов.",
	"[audio]Ссылка на песню[/audio]",
	"[audio]http://allsiemens.com/mp3/files/1.mp3[/audio]",
	"Обязательно ссылка должна заканчиваться на .mp3"
);
*/

insert_tag(
	"Флеш анимация",
	"Проигрыватель флеш файлов.",
	"[flash]Ссылка на песню[/flash]",
	"[flash]http://www.flashpark.ru/files/cupgame.swf[/flash]",
	"Обязательно ссылка должна заканчиваться на .swf Возможно указывать размеры, в первом открытом теге: [flash=300:50]тут ссылка[/flash]"
);

insert_tag(
	"Видео с youtube",
	"Проигрыватель файлов с сайта youtube.",
	"[video=ссылка на файл youtube]",
	"[video=http://www.youtube.com/watch?v=4HmeA_vHjzY]",
	"Обязательно в ссылке должно быть watch?v= и id файла"
);





end_frame();

stdfoot();
?>