<?Php

function get_categories($category, $date){ 
   require "config.php"; 
   if ($category != ''){
     $category = "AND category = " . $dbo->quote(htmlentities($category));
   } else {
     $category = "";
   }
   return $dbo->query("SELECT DISTINCT category FROM logs WHERE date = '$date' $category ORDER BY category ASC");
}

function get_new_logs_from_category($category, $status, $limit, $date){
   require "config.php"; 
   if ($limit > 0){
     $limit = "LIMIT " . $limit;
   } else {
     $limit = "";
   }
   return $dbo->query("SELECT DISTINCT logid, message, status, comment, link, score, md5, category FROM logs WHERE date = '$date' AND category = '$category' AND status = $status ORDER BY score DESC $limit");
}

function get_new_logs(){
   require "config.php"; 
   return $dbo->query("SELECT DISTINCT category, message, comment FROM logs ORDER BY score DESC");
}

function get_nb_logs($status, $date){
   require "config.php"; 
   return $dbo->query("SELECT COUNT(*) as cpt FROM logs WHERE status = $status AND date = '$date'");
}

function get_first_date($logid, $cpt){
   require "config.php"; 
   $today=$cpt - 1;
   return $dbo->query("SELECT DATE_SUB(date, INTERVAL $today DAY) AS subdate FROM logs WHERE logid = $logid");
}

function get_nb_days($logid){
   require "config.php"; 
   return $dbo->query("SELECT cpt FROM logs WHERE logid = $logid");
}

function get_nb_total_days($logid){
   require "config.php"; 
   return $dbo->query("SELECT total FROM logs WHERE logid = $logid");
}

function get_min_day(){
   require "config.php"; 
   return $dbo->query("SELECT MIN(DAY(date)) FROM logs ORDER BY date");
}

function get_max_day(){
   require "config.php"; 
   return $dbo->query("SELECT MAX(DAY(date)) FROM logs ORDER BY date");
}

function get_min_month(){
   require "config.php"; 
   return $dbo->query("SELECT MIN(MONTH(date)) FROM logs ORDER BY date");
}

function get_max_month(){
   require "config.php"; 
   return $dbo->query("SELECT MAX(MONTH(date)) FROM logs ORDER BY date");
}

function get_min_year(){
   require "config.php"; 
   return $dbo->query("SELECT MIN(YEAR(date)) FROM logs ORDER BY date");
}

function get_max_year(){
   require "config.php"; 
   return $dbo->query("SELECT MAX(YEAR(date)) FROM logs ORDER BY date");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Dailylogs</title>
    <link href="css/style.css" rel="stylesheet">
    <script src="js/jquery.min.js"></script>
    <script src="js/autosize.min.js"></script>
    <script src="js/theme.js"></script>

</head>

<body>

<?Php

// Status
if(!isset($_GET["status"])){
     $status = 0;
} else {
     $status = $_GET["status"];
}
if (($status != 0) && ($status != 1) && ($status != 2)){
    $status = 0;
}

// Limit
if(!isset($_GET["limit"])){
     $limit = 10;
} else {
     $limit = $_GET["limit"];
}

// Category
if(!isset($_GET["category"])){
     $selected_category = "";
} else {
     $selected_category = $_GET["category"];
}

// Day
if(!isset($_GET["day"])){
     $day = date('d');
} else {
     if (intval($_GET["day"]) > 0) 
        $day = $_GET["day"];
     else 
        $day = date('d');
}

// Month
if(!isset($_GET["month"])){
     $month = date('m');
} else {
     if (intval($_GET["month"]) > 0) 
        $month = $_GET["month"];
     else 
        $month = date('m');
}

// Year
if(!isset($_GET["year"])){
     $year = date('Y');
} else {
     if (intval($_GET["year"]) > 0) 
        $year = $_GET["year"];
     else 
        $year = date('Y');
}

// Date
$date = $year . "-" . $month . "-" . $day; 

// Counts
$nb_new_logs = get_nb_logs(0, $date)->fetchColumn();
$nb_validated_logs = get_nb_logs(1, $date)->fetchColumn();
$nb_unvalidated_logs = get_nb_logs(2, $date)->fetchColumn();


?>

<h1>Daily logs du <?Php echo $day . "-" . $month . "-" . $year;?><img id="loader" class="loader" src="images/loader.gif"/></h1>

<h2><a href="index.php?status=0&limit=<?Php echo $limit ?>&category=<?Php echo $selected_category ?>&day=<?Php echo $day ?>&month=<?Php echo $month ?>&year=<?Php echo $year ?>">Nouveaux messages (<?Php echo $nb_new_logs ?>)</a> 
- <a href="index.php?status=1&limit=<?Php echo $limit ?>&category=<?Php echo $selected_category ?>&day=<?Php echo $day ?>&month=<?Php echo $month ?>&year=<?Php echo $year ?>">Messages validés (<?Php echo $nb_validated_logs ?>)</a> 
- <a href="index.php?status=2&limit=<?Php echo $limit ?>&category=<?Php echo $selected_category ?>&day=<?Php echo $day ?>&month=<?Php echo $month ?>&year=<?Php echo $year ?>">Messages non validés (<?Php echo $nb_unvalidated_logs ?>)</a></h2>


<form action="index.php" method="get">
<input type="hidden" name="status" value="<?Php echo $status; ?>">
<input type="hidden" name="limit" value="<?Php echo $limit; ?>">
<input type="hidden" name="category" value="<?Php echo $selected_category; ?>">
<div class="limit-category">
   <div class="limit-category-content">Limiter les résultats à la date du 
<select name="day">
  <option value="">Jour</option>
	<?php for ($_day = get_min_day()->fetchColumn(); $_day <= get_max_day()->fetchColumn(); $_day++) { ?>
	<option <?Php if ($day == $_day) echo 'selected="selected"';?> value="<?php echo strlen($_day)==1 ? '0'.$_day : $_day; ?>"><?php echo strlen($_day)==1 ? '0'.$_day : $_day; ?></option>
	<?php } ?>
</select>
<select name="month">
	<option value="">Mois</option>
	<?php for ($_month = get_min_month()->fetchColumn(); $_month <= get_max_month()->fetchColumn(); $_month++) { ?>
	<option <?Php if ($month == $_month) echo 'selected="selected"';?> value="<?php echo strlen($_month)==1 ? '0'.$_month : $_month; ?>"><?php echo strlen($_month)==1 ? '0'.$_month : $_month; ?></option>
	<?php } ?>
</select>
<select name="year">
  <option value="">Année</option>
  <?php for ($_year = get_min_year()->fetchColumn(); $_year >= get_max_year()->fetchColumn(); $_year--) { ?>
	<option <?Php if ($year == $_year) echo 'selected="selected"';?> value="<?php echo $_year; ?>"><?php echo $_year; ?></option>
	<?php } ?>
</select>
<input type="submit" name="update_date" value="Mettre à jour"/>
</div><br/>
</div>
</form>

<form action="index.php" method="get">
<input type="hidden" name="status" value="<?Php echo $status; ?>">
<input type="hidden" name="category" value="<?Php echo $selected_category; ?>">
<input type="hidden" name="day" value="<?Php echo $day; ?>">
<input type="hidden" name="month" value="<?Php echo $month; ?>">
<input type="hidden" name="year" value="<?Php echo $year; ?>">
<div class="limit">
   <div class="limit-content">Limiter les résultats à 
   <select id="limit" onchange="this.form.submit()" name="limit">
           <option value="-1" <?Php if ($limit == -1) echo 'selected="selected"';?>>tous les</option>
           <option value="10" <?Php if ($limit == 10) echo 'selected="selected"';?>>10</option>
           <option value="20" <?Php if ($limit == 20) echo 'selected="selected"';?>>20</option>
           <option value="50" <?Php if ($limit == 50) echo 'selected="selected"';?>>50</option>
   </select> résultats par catégorie.</div><br/>
</div>
</form>

<form action="index.php" method="get">
<input type="hidden" name="status" value="<?Php echo $status; ?>">
<input type="hidden" name="limit" value="<?Php echo $limit; ?>">
<input type="hidden" name="day" value="<?Php echo $day; ?>">
<input type="hidden" name="month" value="<?Php echo $month; ?>">
<input type="hidden" name="year" value="<?Php echo $year; ?>">
<div class="limit-category">
   <div class="limit-category-content">Limiter les résultats à la catégorie  
   <select id="limit-category" onchange="this.form.submit()" name="category">
           <option value="">Toutes les catégories</option>

<?Php
// Categories
$categories = get_categories($selected_category, $date);
foreach ($categories as $category) {?>
           <option value="<?Php echo $category['category']; ?>" <?Php if (htmlentities($selected_category) == $category['category']) echo 'selected="selected"';?>><?Php echo $category['category']; ?></option>
<?Php } ?>
   </select></div>
</div>
</form>


<?Php
$categories = get_categories($selected_category, $date);
foreach ($categories as $category) {
?>

  <?Php 
  $logs = get_new_logs_from_category($category['category'], $status, $limit, $date);

  if ($logs->rowCount() > 0){?>
  <h3><?Php echo $category['category'] . " : " . $logs->rowCount() . " nouveaux messages";?></h3>
    <table class="<?Php if ($status == 0) {echo 'red';} else if ($status == 1) {echo 'green';} else if ($status == 2) {echo 'yellow';}?>" id="logs">
    <tr>
      <th>Score</th>
      <th>Message</th>
      <th>Nombre de jours consécutifs</th>
      <th>Nombre de jours total</th>
      <th>Date d'apparition</th>
      <th>Commentaire</th>
      <th>Lien</th>
      <th>Action</th>
    </tr>
    <?Php foreach ($logs as $log) {?>

       <tr>
       <td class="score"><?Php if ($log['score'] > 0) echo $log['score']; else echo 1;?></td>
       <td class="message"><?Php echo $log['message'];?></td>
       <td class="nb_days"><?Php $nb_days = get_nb_days($log['logid'])->fetchColumn(); echo $nb_days;?></td>
       <td class="nb_total_days"><?Php echo get_nb_total_days($log['logid'])->fetchColumn();?></td>
       <td class="first_date"><?Php echo get_first_date($log['logid'], $nb_days)->fetchColumn();?></td>
       <td class="comment">
           <textarea onclick="resize(<?Php echo $log['logid'] ?>)" class="comment" id="comment_<?Php echo $log['logid'] ?>" size="50" type="text" name="comment" autocomplete="off"><?Php echo htmlentities($log['comment']);?></textarea>
       </td>
       <td class="link"><input type="hidden" id="link_<?Php echo $log['logid'] ?>" value="<?Php echo $log['link'] ?>"><?Php if ($log['link'] != '') {?><a target="_blank" href="<?Php echo $log['link'] ?>"><img src="images/link.png"/></a> <?Php }?></td>
       <td class="actions">
         <select class="action" id="action_<?Php echo $log['logid'] ?>" onchange="doAction(<?Php echo $log['logid'] ?>, <?Php echo $log['status'] ?>);">
           <option value="nothing" selected="selected"></option>
           <?Php if ($log['status'] != 1){?> 
               <option value="validate">Valider</option>
           <?Php } ?> 
           <?Php if ($log['status'] != 2){?> 
               <option value="unvalidate">Invalider</option>
           <?Php } ?> 
           <option value="update">Mettre à jour</option>
           <?Php if ($log['status'] != 0){?> 
               <option value="new">Taguer nouveau</option>
           <?Php } ?> 
           <option value="link">Modifier le lien</option>
           <option value="reinit">Réinitialiser</option>
         </select>
      </td>
      </tr>
    <?Php } ?>
    </table>

  

<?Php }} ?>

</body>

</html>
