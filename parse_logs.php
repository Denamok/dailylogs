<?Php

$category = "";
$message = "";
$status = "";
$date = "";
$cpt = 0;
$total = 0;
$comment = "";
$link = "";
$score = 0;
$nb_insert=0;
$nb_update=0;
$nb_nothing=0;
$debug=True;
//$debug=False;


function init(){
  global $category, $message, $date, $status, $comment, $link, $score, $nb_nothing, $nb_insert, $nb_update, $cpt, $total;
  $category = "";
  $message = "";
  $status = "";
  $date = "";
  $cpt = 0;
  $total = 0;
  $comment = "";
  $link = "";
  $score = 0;
  $nb_insert=0;
  $nb_update=0;
  $nb_nothing=0;
}

function parse_line($line) {
    global $category, $message, $score;
    if (preg_match('/background-color:black/',$line)){
      // Category
       $after = explode(">", $line);
       $before = explode("<", $after[2]);
       $category = $before[0];
   } else if ($line == strip_tags($line)) {
      // Score
      $result = explode(' ',trim($line));
      $score = intval($result[0]);
      // Message
      if ($score > 0){
         $message = substr(strstr(trim($line)," "), 1);
      } else {
         $message = trim($line);
      }
   }
}

function prepare_log($date_to_insert){
   require "config.php"; 
   global $category, $message, $date, $status, $comment, $link, $score, $cpt, $total, $nb_nothing;

   // Date
   $status = 0; // New
   $date = $date_to_insert;
   if ($message != ""){
       //echo "category = " . $category . ", message = " . $message . ", date = " . $date . "<br/>";
       $md5 = md5($message);
       $stmt = $dbo->prepare("SELECT status, comment, link, DATEDIFF('$date', date) AS diff, cpt, total FROM logs WHERE md5 = '$md5' and category = '$category' ORDER BY date DESC LIMIT 1");
       $stmt->execute();
       if ($log = $stmt->fetch()) {
          if ($log["diff"] == 0){
            // Log already inserted today in database : doing nothing
            // print_log("NOTHING - ");
            $nb_nothing++;
          } else if ($log["diff"] == 1){
            // Log already detected yesterday
            $status = $log["status"];
            $comment = $log["comment"];
            $link = $log["link"];
            $cpt = $log["cpt"] + 1;
            $total = $log["total"] + 1;
            update_log();
          } else {
             // Log already detected in the past...
            $status = 0;
            $comment = $log["comment"];
            $link = $log["link"];
            $cpt = 1;
            $total = $log["total"] + 1;
            update_log();
          }
       } else {
//       echo "=======".$message."=========";
         $cpt = 1;
         $total = 1;
         insert_log();
       }
   }
}

function print_log($m){
   global $category, $message, $date, $status, $comment, $link, $score, $debug;
   if ($debug) echo $m . "LOG = ($category, $message, $date, $status, $comment, $link, $score) <br/>";
}

function insert_log(){
   require "config.php"; 
   global $category, $message, $date, $status, $comment, $link, $score, $nb_insert, $cpt, $total;
   print_log("INSERT - ");

   $md5 = md5($message);
   $sql=$dbo->prepare("insert into logs (category,md5, message,date,status,comment,link,score,cpt,total) values('$category','$md5'," . $dbo->quote($message) . ",'$date', '$status'," . $dbo->quote($comment) . ",'$link','$score',$cpt,$total)");

   if($sql->execute()){
     $nb_insert++;
   } else {
      echo $sql->errorInfo()[2] . "<br/>";
   }

}

function update_log(){
   require "config.php"; 
   global $category, $message, $date, $status, $comment, $link, $score, $cpt, $total, $nb_update;
   print_log("UPDATE - ");
  
   $md5 = md5($message);
   //$sql=$dbo->prepare("update logs set date = '$date', comment = " . $dbo->quote($comment) . ", status = $status, link = '$link', score = '$score' where category = '$category' and md5 = '$md5'");
   $sql=$dbo->prepare("insert into logs (category,md5, message,date,status,comment,link,score,cpt,total) values('$category','$md5'," . $dbo->quote($message) . ",'$date', '$status'," . $dbo->quote($comment) . ",'$link','$score',$cpt,$total)");
   if($sql->execute()){
     $nb_update++;
   } else {
      echo $sql->errorInfo()[2] . "<br/>";
   }

}

function parse($file, $date){
  init();
  $limit=38000;
  $n=0;
  $handle = fopen($file, "r");
  if ($handle) {
    while ((($line = fgets($handle)) !== false) && ($n < $limit)) {
        parse_line($line);
        prepare_log($date);
        $n++;
    }
    fclose($handle);
  } else {
    // error opening the file.
  } 
}

function download_last_logs(){
   require "config.php"; 
   $connection = ssh2_connect($logserver_name, 22);
   ssh2_auth_password($connection, $logserver_username, $logserver_password);
   ssh2_scp_recv($connection, $log_filename, "logs.txt");
}

function move_logs(){
  rename("logs.txt", "old/logs-" . date('Y-m-d') . ".txt");
}


// Force date
if(!isset($_GET["date"])){
     $date = date('Y-m-d');
} else {
     $date = $_GET["date"];
}

download_last_logs();
parse("logs.txt", $date);
move_logs();


// TESTS
//parse("tests/dailycronerrorsmail.txt.1", "2016-04-14");
//parse("tests/dailycronerrorsmail.txt.2", "2016-04-15");
//parse("tests/dailycronerrorsmail.txt.3", "2016-04-16");
//parse("tests/dailycronerrorsmail.txt.4", "2016-04-17");
//parse("tests/dailycronerrorsmail.txt.5", "2016-04-18");
//parse("tests/dailycronerrorsmail.txt.6", "2016-04-19");

?>
