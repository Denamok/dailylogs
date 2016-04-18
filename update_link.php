<?Php
require "config.php"; 

header('Content-type: application/json');
if(!isset($_POST["logid"])){
 $response_array["status"] = "error"; 
 $response_array["msg"] = "Une erreur est survenue : pas de logid";
 echo json_encode($response_array);
 exit;
}

if(!isset($_POST["link"])){
 $response_array["status"] = "error"; 
 $response_array["msg"] = "Une erreur est survenue : pas de link";
 echo json_encode($response_array);
 exit;
}

$logid=$_POST["logid"];
$link=$dbo->quote($_POST["link"]);

$sql=$dbo->prepare("UPDATE logs SET link = $link WHERE logid = $logid");

if($sql->execute()){
    $response_array["status"] = "success"; 
    $response_array["msg"] = "$logid updated";
} else{
   $response_array["status"] = "error"; 
   $response_array["msg"] = $sql->errorInfo()[2];
}

echo json_encode($response_array);

?>
