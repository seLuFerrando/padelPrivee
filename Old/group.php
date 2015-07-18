<?php require_once('connections/padelprivee.php');
  header('Content-Type: text/html; charset=utf-8');
  $folder = "images/";
  $image="";
  if(isset($_GET['ID'])){
    $id=$_GET['ID'];
  }else{
    $id=0;
  } 
  if($id!=0){
    $queryGroup = "SELECT * FROM `groups` WHERE `_id` = $id";
    $rsGroup = mysql_query($queryGroup, $padelprivee) or die(mysql_error());
    $row_rsGroup = mysql_fetch_assoc($rsGroup);
    $image = $row_rsGroup['image'];

    $queryArticles = "SELECT articles._id, articles.article, articles.description, articles.image, articles.price 
      FROM `articles` INNER JOIN `articles_group` 
      ON `articles`.`_id` = `articles_group`.`article` 
      WHERE `articles_group`.`group` = $id";
    $rsArticles = mysql_query($queryArticles, $padelprivee) or die(mysql_error());
    $row_rsArticles = mysql_fetch_assoc($rsArticles);

  }

if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "99";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && false) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "error.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0) 
  $MM_referrer .= "?" . $QUERY_STRING;
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}

if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "Group") && ($id == 0)) {
  $insertSQL = sprintf("INSERT INTO `groups` (`group`, `status`, `image`, `dateStart`, `dateEnd`, `visibility`) VALUES (%s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['Group'], "text"),
                       GetSQLValueString($_POST['Status'], "int"),
                       GetSQLValueString($folder . $_FILES['Image']['name'], "text"),
                       GetSQLValueString($_POST['DateStart'], "date"),
                       GetSQLValueString($_POST['DateEnd'], "date"),
                       GetSQLValueString(isset($_POST['Visibility']) ? "true" : "", "defined","1","0"));

  $Result1 = mysql_query($insertSQL, $padelprivee) or die(mysql_error());
  $id = mysql_insert_id();
  $file = $folder . basename( $_FILES['Image']['name']);
  if(is_uploaded_file($_FILES['Image']['tmp_name'])){
    move_uploaded_file($_FILES['Image']['tmp_name'], $file);
  }
  $insertGoTo = "group.php?ID=" . $id;
  /*if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  } */
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "Group") && ($id != 0)) {
  if((isset($_FILES['Image'])) && ($_FILES['Image']['tmp_name'] != "")){
    $image = $folder . $_FILES['Image']['name'];
    $file = $folder . basename( $_FILES['Image']['name']);
    if(is_uploaded_file($_FILES['Image']['tmp_name'])){
      move_uploaded_file($_FILES['Image']['tmp_name'], $file);
    }
  }
  $updateSQL = sprintf("UPDATE `groups` SET `group`=%s, `status`=%s, `image`=%s, `dateStart`=%s,
   `dateEnd`=%s, `visibility`=%s WHERE `_id`=%s",
                       GetSQLValueString($_POST['Group'], "text"),
                       GetSQLValueString($_POST['Status'], "int"),
                       GetSQLValueString($image, "text"),
                       GetSQLValueString($_POST['DateStart'], "date"),
                       GetSQLValueString($_POST['DateEnd'], "date"),
                       GetSQLValueString(isset($_POST['Visibility']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString($id, "int"));

  $Result1 = mysql_query($updateSQL, $padelprivee) or die(mysql_error());
  $insertGoTo = "group.php?ID=" . $id;
  /*if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  } */
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_delete"])) && ($_POST["MM_delete"] == "Delete")) {
  $insertSQL = sprintf("DELETE FROM `articles_group` WHERE ((`group`=%s) && (`article`=%s))",
                       GetSQLValueString($id, "int"),
                       GetSQLValueString($_POST['Article'], "int"));

  $Result1 = mysql_query($insertSQL, $padelprivee) or die(mysql_error());
  $insertGoTo = "group.php?ID=" . $id;
  /*if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  } */
  header(sprintf("Location: %s", $insertGoTo));
}


$query_statusRS = "SELECT * FROM `status`";
$statusRS = mysql_query($query_statusRS, $padelprivee) or die(mysql_error());
$row_statusRS = mysql_fetch_assoc($statusRS);
$totalRows_statusRS = mysql_num_rows($statusRS);
?><!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html"; charset="utf-8"; author="seLu Ferrando" />
<title>Promocion</title>
<link rel="stylesheet" type="text/css" href="css/style.css">
<script type="text/javascript" src="js/zepto.min.js" ></script>
</head>

<body>
<form action="<?php echo $editFormAction; ?>" method="POST" enctype="multipart/form-data" name="Group">
<p>
<label>Promocion:</label>
<input name="Group" type="text" value="<?php if($id!=0)echo $row_rsGroup['group']; ?>"/>
</p>
<p>
<label>Estado:</label>
<select name="Status">
<?php
do {  
?>
  <option value="<?php echo $row_statusRS['_id']?>" 
  <?php if($id!=0) if($row_statusRS['_id']==$row_rsGroup['status']) echo "selected='selected'";?>>
  <?php echo $row_statusRS['status']?></option>
  <?php
} while ($row_statusRS = mysql_fetch_assoc($statusRS));
  $rows = mysql_num_rows($statusRS);
  if($rows > 0) {
      mysql_data_seek($statusRS, 0);
	  $row_statusRS = mysql_fetch_assoc($statusRS);
  }
?>
</select>
</p>
<p>
<label>Imagen:</label>
<input name="Image" type="file"/>
<?php if($id!=0) echo "<img src='$image'";?>
</p>
<p>
<label>Fecha inicio:</label>
<input name="DateStart" type="text" value="<?php if($id!=0)echo $row_rsGroup['dateStart']; ?>"/>
</p>
<p>
<label>Fecha fin:</label>
<input name="DateEnd" type="text" value="<?php if($id!=0)echo $row_rsGroup['dateEnd']; ?>"/>
</p>
<p>
<label>Visible:</label>
<input name="Visibility" type="checkbox" value="" <?php if($id!=0){ if($row_rsGroup['visibility']==1) echo "checked";}else echo "checked";?> />
</p>
<input name="Actualizar" type="submit" />
<input type="hidden" name="MM_insert" value="Group" />
</form>
<div <?php if($id==0) echo "style='hidden";?>>
<p>
  <!-- tabla articles -->
  <table width="90%" border="1">
    <tr>
      <th scope="col">Imagen</th>
      <th scope="col">Nombre</th>
      <th scope="col">Descripcion</th>
      <th scope="col">Precio</th>
      <th scope="col"><div align="center"><a href="articles.php?ID=<?php echo $id; ?>"><img src="images/icons/ic_input_add.png" width="48" height="48" alt="añadir" longdesc="Añadir" /></a></div></th>
    </tr>
    <?php do { ?>
      <tr>
        <td><img src="<?php echo $row_rsArticles['image']; ?>" width="90px" heigth="90px"></td>
        <td><?php echo $row_rsArticles['article']; ?></td>
        <td><?php echo $row_rsArticles['description']; ?></td>
        <td><?php echo $row_rsArticles['price']; ?></td>        
        <td><div align="center"><a href="article.php?ID=<?php echo $row_rsArticles['_id']; ?>">
          <img src="images/icons/ic_menu_edit.png" alt="editar" width="34" height="34" longdesc="Editar" /></a>&nbsp; 
          <img src="images/icons/ic_delete.png" alt="eliminar" width="34" height="34" onclick="popDelete(<?php echo $row_rsArticles['_id']; ?>)" /></div></td>
      </tr>
      <?php } while ($row_rsArticles = mysql_fetch_assoc($rsArticles)); ?>
  </table>
</p>
</div>
<div id="popUpDiv">
  <form action="<?php echo $editFormAction; ?>" method="POST" enctype="multipart/form-data" name="Articles">  
    <h3>Desea eliminar el articulo del grupo?</h3>
    <input id="Article" name="Article" type="hidden" value="0"/>
    <input id="submitBt" name="Actualizar" type="submit" value="Eliminar" />
    <button id="cancelarBt" onclick="popHide()">Cancelar</button> 
    <input id="deleteValue" type="hidden" name="MM_delete" value="Delete" />
</form>
</div>
<script type="text/javascript">
    function popDelete(a){
      $("#Article").val(a);
      $("#popUpDiv").show();
    }
    function popHide(){
      $("#deleteValue").val('nothing');
      $("#popUpDiv").hide();
    }
</script>
</body>
</html>
<?php
if($id!=0){
  mysql_free_result($rsGroup);  
  mysql_free_result($rsArticles);
}
mysql_free_result($statusRS);
mysql_close($padelprivee);
?>
