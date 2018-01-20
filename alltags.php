<? 
require_once("include/bittorrent.php");
//gzip();
dbconn(false);

/**
 * @author 7Max7
 * @copyright 2010
 */

stdheadchat("Используемые метки (теги)");
?>

<script language="javascript" type="text/javascript" src="js/jquery.js"></script>


<style>
#categories {
  width: 220px;
}

#categories td {
  color: #707070;
  font-family: Arial, sans-serif;
  font-size: 12px;
  padding-top: 3px;
  padding-bottom: 3px;
}

#categories td.title {
  border-right: 5px solid #f6f6f4;
  overflow: hidden;
  white-space: nowrap;
}

#categories td.today {
  background-color: #ffe4a5;
  padding-left: 5px;
  padding-right: 5px;
  width: 25px;
  text-align: right;
}

#categories td.count {
  padding-left: 5px;
  width: 40px;
  text-align: right;
}

#categories .active td.title {
  background-color: #e5e5e5;
}

#tag_cloud {
  overflow: hidden;
  width: 100%;
}

#tag_cloud a.level1 {
  color: #000;
  font-size: 20px;
  text-decoration: none;
}

#tag_cloud a.level2 {
  color: #444;
  font-size: 22px;
  text-decoration: none;
}

#tag_cloud a.level3 {
  color: #777;
  font-size: 18px;
  text-decoration: none;
}

#tag_cloud a.level4 {
  color: #444;
  font-size: 17px;
  text-decoration: none;
}

#tag_cloud a.level5 {
  color: #777;
  font-size: 19px;
  text-decoration: none;
}

#tag_cloud a.level6 {
  color: #000;
  font-size: 18px;
  text-decoration: none;
}

#tag_cloud a.level7 {
  color: #777;
  font-size: 16px;
  text-decoration: none;
}

#tag_cloud a.level8 {
  color: #000;
  font-size: 15px;
  text-decoration: none;
}

#tag_cloud a.level9 {
  color: #777;
  font-size: 20px;
  text-decoration: none;
}

#tag_cloud a.level0 {
  color: #777;
  font-size: 18px;
  text-decoration: none;
}


#tag_cloud a.highlight {
  color: #ff7e00;
}

#tag_cloud a:hover, #tag_cloud a:active {
  text-decoration: underline;
}

</style>

<script type="text/javascript">
        <!--
        var tag_cloud = {
          highlightTimeout : 0,
          clearTimeout : 0,
          
          highlightDelay : 100,
          clearDelay : 900,
          
          level : 0,
          
          clear : function()
          {
            $('#tag_cloud .level' + tag_cloud.level).removeClass('highlight');
            tag_cloud.level = 0;
          },
          
          over : function(level)
          {
            clearTimeout(tag_cloud.clearTimeout);            
            if(level == tag_cloud.level)
              return;
              
            clearTimeout(tag_cloud.highlightTimeout);  
            
            this.clear();
            tag_cloud.level = level;
            tag_cloud.highlightTimeout = setTimeout(function(){$('#tag_cloud .level' + level).addClass('highlight');}, tag_cloud.highlightDelay);            
          },
          
          out : function()
          {
            tag_cloud.clearTimeout = setTimeout(function(){tag_cloud.clear();}, tag_cloud.clearDelay);
          }
        };
        // -->
</script>

<?

echo "<div id=\"tag_cloud\">";


$naname = array();

$wo = array(".",",","","+",":",";","/","(",")","|");

$count_res = sql_query("SELECT * FROM tags GROUP BY name") or sqlerr(__FILE__, __LINE__);
while ($row = mysql_fetch_array($count_res)){

if (str_replace($wo, '', $row["name"]) <> $row["name"])
$naname[] = $row["id"];

echo '<a onmouseout="tag_cloud.out();" onmouseover="tag_cloud.over('.$row["category"].');" class="level'.rand(0,9).'" href="browse.php?tag='.urlencode($row["name"]).'" title="'.$row["howmuch"].'">'.htmlspecialchars_uni($row["name"]).'</a>';
 
echo ' ';
}



echo "</div>";



if (count($naname)){

//print_r($naname);
echo "<hr> Удалено неактивных и битых: ".count($naname)." шт.";
// очистка тегов если пустое значение или 0 или 1
sql_query("DELETE FROM tags WHERE name=''") or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM tags WHERE howmuch<='1'") or sqlerr(__FILE__,__LINE__);
//

sql_query("DELETE FROM tags WHERE id IN (".implode(",", $naname).")") or sqlerr(__FILE__, __LINE__);
}



stdfootchat();
?>