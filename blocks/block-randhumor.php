<?php
if (!defined('BLOCK_FILE')) {
 Header("Location: ../index.php");
 die();
}
$blocktitle = "Дежурный анекдот";
$content.="<font color=\"#000000\">";
$content.="<script src=\"http://humor.look.ru/joke.phtml?type=anecdote&theme=0&q=1\"></script>
<script>
for(i in text) { document.write(text[i]); }
</script>
</font>
";
?> 