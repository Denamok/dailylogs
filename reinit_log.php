<?Php
require "config.php"; 

header('Content-type: application/json');
if(!isset($_POST["logid"])){
 $response_array["status"] = "error"; 
 $response_array["msg"] = "Une erreur est survenue : pas de logid";
 echo json_encode($response_array);
 exit;
}

$logid=$_POST["logid"];

$sql=$dbo->prepare("UPDATE logs SET cpt = 1, total = 1 WHERE logid = $logid");

if($sql->execute()){
    $response_array["status"] = "success"; 
    $response_array["msg"] = "$logid updated";
} else{
   $response_array["status"] = "error"; 
   $response_array["msg"] = $sql->errorInfo()[2];
}

echo json_encode($response_array);

?>
