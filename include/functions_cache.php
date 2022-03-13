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


if(!defined('IN_TRACKER') && !defined('IN_ANNOUNCE'))
  die('Hacking attempt!');

class MySQLCache{
   //Путь к директории кэш-файлов
   var $CachePath="cache/";     //Необходимо ввести полный путь

   //Дата формирования данных
   var $DataDate=0;
   //Численный код ошибки выполнения последней операции с MySQL
   var $errno=0;
   //Строка ошибки последней операции с MySQL

   //Номер следующей выдаваемой строки
   var $NextRowNo=0;

   //Массив результатов запроса
   var $ResultData=array(
      'fields'=>array(),
      'data'=>array(),
   );


   function MySQLCache($query, $valid=60,$filed=false){
      if ($this->CachePath==''){
         $this->CachePath=dirname(__FILE__);
      }
      $query=trim($query);
    //  if (!eregi('^SELECT', $query)){
      if (!preg_match("/SELECT/i", $query)){
         return mysql_query($query);
      }
      
      if (!empty($filed))
      $filename=$this->CachePath.'/'.$filed;
      else
      $filename=$this->CachePath.'/'.md5($query).'.txt';
      unset($filed);
      
      /* Попытка чтения кэш-файла */
      if ((@$file=fopen($filename, 'r')) && filemtime($filename)>(time()-$valid)){
         flock($file, LOCK_SH);
         $serial=file_get_contents($filename);
         $this->ResultData=unserialize($serial);
         $this->DataDate=filemtime($filename);
         $this->FromCache=true;
         fclose($file);
         return true;
      }
      if ($file){
         fclose($file);
      }
      /* Выполнение запроса */
      $time_start=microtime(true);
      $SQLResult=mysql_query($query) or sqlerr(__FILE__, __LINE__);
      $time_end=microtime(true);
      $this->DataDate=time();
      $time_exec=$time_end-$time_start;
     
      
      /* Получение названия полей */
      $nf=mysql_num_fields($SQLResult);
      for ($i=0; $i<$nf; $i++){
         $this->ResultData['fields'][$i]=mysql_fetch_field($SQLResult, $i);
      }
      /* Получение данных */
      $nr=mysql_num_rows($SQLResult);
      for ($i=0; $i<$nr; $i++){
         $this->ResultData['data'][$i]=mysql_fetch_row($SQLResult);
      }
      /* Запись кэша */
      $file=fopen($filename, 'w');
      flock($file, LOCK_EX);
      fwrite($file, serialize($this->ResultData));
      fclose($file);
      return true;
   }

   /*** Количество полей в запросе ***/
   function num_fields(){
      return sizeof($this->ResultData['fields']);
   }

   /*** Название указанной колонки результата запроса ***/
   function field_name($num){
      if (isset($this->ResultData['fields'][$num])){
         return $this->ResultData['fields'][$num]->name;
      }else{
         return false;
      }
   }

   /*** Информация о колонке из результата запроса в виде объекта ***/
   function fetch_field($num){
      if (isset($this->ResultData['fields'][$num])){
         return $this->ResultData['fields'][$num];
      }else{
         return false;
      }
   }

   /*** Длина указанного поля ***/
   function field_len($num){
      if (isset($this->ResultData['fields'][$num])){
         return $this->ResultData['fields'][$num]->max_length;
      }else{
         return false;
      }
   }

   /*** Тип указанного поля результата запроса ***/
   function field_type($num){
      if (isset($this->ResultData['fields'][$num])){
         return $this->ResultData['fields'][$num]->type;
      }else{
         return false;
      }
   }

   /*** Флаги указанного поля результата запроса ***/
   function field_flags($num){
      if (!isset($this->ResultData['fields'][$num])){
         return false;
      }
      $result=array();
      if ($this->ResultData['fields'][$num]->not_null){
         $result[]='not_null';
      }
      if ($this->ResultData['fields'][$num]->primary_key){
         $result[]='primary_key';
      }
      if ($this->ResultData['fields'][$num]->unique_key){
         $result[]='unique_key';
      }
      if ($this->ResultData['fields'][$num]->multiple_key){
         $result[]='multiple_key';
      }
      if ($this->ResultData['fields'][$num]->blob){
         $result[]='blob';
      }
      if ($this->ResultData['fields'][$num]->unsigned){
         $result[]='unsigned';
      }
      if ($this->ResultData['fields'][$num]->zerofill){
         $result[]='zerofill';
      }
      if ($this->ResultData['fields'][$num]->binary){
         $result[]='binary';
      }
      if ($this->ResultData['fields'][$num]->enum){
         $result[]='enum';
      }
      if ($this->ResultData['fields'][$num]->auto_increment){
         $result[]='auto_increment';
      }
      if ($this->ResultData['fields'][$num]->timestamp){
         $result[]='timestamp';
      }
      return implode(' ', $result);
   }

   /* Количество рядов результата запроса */
   function num_rows(){
      return sizeof($this->ResultData['data']);
   }

   /* Обрабатывает ряд результата запроса и возвращает неассоциативный массив */
   function fetch_row(){
      if (($this->NextRowNo+1)>$this->num_rows()){
         return false;
      }
      $this->NextRowNo++;
      return $this->ResultData['data'][$this->NextRowNo-1];
   }

   /* Обрабатывает ряд результата запроса и возвращает ассоциативный массив */
   function fetch_assoc(){
      if (($this->NextRowNo+1)>$this->num_rows()){
         return false;
      }
      for ($i=0; $i<$this->num_fields(); $i++){
         $result[$this->ResultData['fields'][$i]->name]=
            $this->ResultData['data'][$this->NextRowNo][$i];
      }
      $this->NextRowNo++;
      return $result;
   }
}





/*

$cache=new MySQLCache($res1, 600);
$arr1=$cache->fetch_row();

*/

?>
