<?
require_once("include/bittorrent.php");
dbconn();

//loggedinorreturn();
//parked();

header("Content-Type: text/html; charset=" . $tracker_lang['language_charset']);
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);

$category = (int) $_POST["cat"];

if (empty($category))
die;


$res = sql_query("SELECT name FROM categories WHERE id=".sqlesc($category)) or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_array($res);

//$cat_num = number_format(get_row_count("torrents", "WHERE category=".sqlesc($category)));

echo "<br><span id=\"ss".$category."\" style=\"display: block;\"><fieldset id='tags' style='border: 2px solid gray;min-width:95%;display: block;'><legend> Теги для категории \"<b>".$row["name"]."</b>\" <a href=\"#\" style=\"font-weight:normal\" onClick=\"javascript:this.style.display='none';document.getElementById('ss".$category."').innerHTML='';\">[свернуть]</a></legend><table class=\"bottom\"><tr>";

        $tags = taggenrelist($category);
        if (empty($tags))
        echo "<span style=\"font-size:12px\"> Нет тегов в выбранной категории</span>";
        else {
        $j = 0;
        foreach ($tags as $row)
            {
            $tagsperrow = 7;
            echo ($j && $j % $tagsperrow == 0) ? "</tr><tr>" : "";
    	    echo "<td class=\"bottom\" style=\"padding-bottom: 2px;padding-left: 7px\"><b style='color: gray'>&#187 </b><a style=\"font-weight: normal;\" href=\"browse.php?tag=".$row["name"]."&incldead=1\">".htmlspecialchars($row["name"])."</a> </td>";
            ++$j;
            }
        }
echo "</tr></table></fieldset></span><br>";

?>