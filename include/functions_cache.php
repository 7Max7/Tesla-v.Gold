<?

/**  ��������, ����� ���������� ������ ������ ������ �������� � ���������� ���������� (������ gold �� 2010 ���)!!!!
* ��� ������ ������ ������� Tesla - ������� �����. ���� ����� ������ ����� ���������� �� ������ demo.muz-tracker.net
* ���� ������� ������������ ������, �� �������� ���������� ��������� �� ����� ������, ����� ���������� ������ �� �������������.
* ���� ������� �������� � ���������� ����� ����, �������� ��������������� ��������: bit-tracker.info, bit-torrent.kiev.ua.
* ���� ������� ������������� ��������� 7Max7 �� ��� ������, ������ ���� ������� �� R190680055855 ��� Z187703352284
* ���� ������� �������� Gold ������ �� Platinum - �������� ��������� ����� 70$ (� ������������ ����������� ������������)
* 
* ������ ����� ������ ����� � ����� demo muz-tracker net � tesla-tracker.net! ))
* ������� �� �������� � ������ Tesla.
**/


if(!defined('IN_TRACKER') && !defined('IN_ANNOUNCE'))
  die('Hacking attempt!');

class MySQLCache{
   //���� � ���������� ���-������
   var $CachePath="cache/";     //���������� ������ ������ ����

   //���� ������������ ������
   var $DataDate=0;
   //��������� ��� ������ ���������� ��������� �������� � MySQL
   var $errno=0;
   //������ ������ ��������� �������� � MySQL

   //����� ��������� ���������� ������
   var $NextRowNo=0;

   //������ ����������� �������
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
      
      /* ������� ������ ���-����� */
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
      /* ���������� ������� */
      $time_start=microtime(true);
      $SQLResult=mysql_query($query) or sqlerr(__FILE__, __LINE__);
      $time_end=microtime(true);
      $this->DataDate=time();
      $time_exec=$time_end-$time_start;
     
      
      /* ��������� �������� ����� */
      $nf=mysql_num_fields($SQLResult);
      for ($i=0; $i<$nf; $i++){
         $this->ResultData['fields'][$i]=mysql_fetch_field($SQLResult, $i);
      }
      /* ��������� ������ */
      $nr=mysql_num_rows($SQLResult);
      for ($i=0; $i<$nr; $i++){
         $this->ResultData['data'][$i]=mysql_fetch_row($SQLResult);
      }
      /* ������ ���� */
      $file=fopen($filename, 'w');
      flock($file, LOCK_EX);
      fwrite($file, serialize($this->ResultData));
      fclose($file);
      return true;
   }

   /*** ���������� ����� � ������� ***/
   function num_fields(){
      return sizeof($this->ResultData['fields']);
   }

   /*** �������� ��������� ������� ���������� ������� ***/
   function field_name($num){
      if (isset($this->ResultData['fields'][$num])){
         return $this->ResultData['fields'][$num]->name;
      }else{
         return false;
      }
   }

   /*** ���������� � ������� �� ���������� ������� � ���� ������� ***/
   function fetch_field($num){
      if (isset($this->ResultData['fields'][$num])){
         return $this->ResultData['fields'][$num];
      }else{
         return false;
      }
   }

   /*** ����� ���������� ���� ***/
   function field_len($num){
      if (isset($this->ResultData['fields'][$num])){
         return $this->ResultData['fields'][$num]->max_length;
      }else{
         return false;
      }
   }

   /*** ��� ���������� ���� ���������� ������� ***/
   function field_type($num){
      if (isset($this->ResultData['fields'][$num])){
         return $this->ResultData['fields'][$num]->type;
      }else{
         return false;
      }
   }

   /*** ����� ���������� ���� ���������� ������� ***/
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

   /* ���������� ����� ���������� ������� */
   function num_rows(){
      return sizeof($this->ResultData['data']);
   }

   /* ������������ ��� ���������� ������� � ���������� ��������������� ������ */
   function fetch_row(){
      if (($this->NextRowNo+1)>$this->num_rows()){
         return false;
      }
      $this->NextRowNo++;
      return $this->ResultData['data'][$this->NextRowNo-1];
   }

   /* ������������ ��� ���������� ������� � ���������� ������������� ������ */
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
