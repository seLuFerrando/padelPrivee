<?php require_once('connections/padelprivee.php'); ?>
<?php
  if(isset($_GET['ID'])){
    $id=$_GET['ID'];
  }else{
    $id=0;
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
?>
<?php
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
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "InsertGroup")) {
  if (is_array($_POST['Articles'])) {
        $num_countries = count($_POST['Articles']);
        $current = 0;
        foreach ($_POST['Articles'] as $key => $value) {
            if ($current != $num_countries-1){
              $insertSQL = sprintf("INSERT INTO `articles_group` (`group`, `article`) VALUES (%s, %s)",
                                   GetSQLValueString($id, "int"),
                                   GetSQLValueString($value, "int"));

              $Result1 = mysql_query($insertSQL, $padelprivee) or die(mysql_error());
            }
        }
  }
  $insertGoTo = "group.php?ID=" . $id;
  /*if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  } */
  header(sprintf("Location: %s", $insertGoTo));
}
}

$query_articlesRS = "SELECT * FROM `articles` ";
$articlesRS = mysql_query($query_articlesRS, $padelprivee) or die(mysql_error());
$row_articlesRS = mysql_fetch_assoc($articlesRS);
$totalRows_articlesRS = mysql_num_rows($articlesRS);
?><!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html"; charset="utf-8"; author="seLu Ferrando" />
<title>Articulos</title>
<link rel="stylesheet" type="text/css" href="css/style.css">
<script type="text/javascript" src="js/zepto.min.js" ></script>
</head>

<body>
<?php if($id!=0) echo"<form action=" . $editFormAction. " method='POST' enctype='multipart/form-data' name='Articles'>"; ?>
<table width="90%" border="1">
    <tr>
      <?php if($id!=0) echo "<th scope='col'>Añadir</th>";?>
      <th scope="col">Imagen</th>
      <th scope="col">Articulo</th>
      <th scope="col">Descripcion</th>
      <th scope="col">Precio</th>
      <th scope="col">Unidades</th>
      <th scope="col"><div align="center"><a href="article.php"><img src="images/icons/ic_input_add.png" width="48" height="48" alt="añadir" longdesc="Añadir" /></a></div></th>
    </tr>

<?php do { ?>
    <tr>
      <?php if($id!=0) echo "<td><input name='Articles[]' type='checkbox' value='" . $row_articlesRS['_id'] . "'/></td>"; ?>
      <td><img src="<?php echo $row_articlesRS['image']; ?>" width="90px" heigth="90px"></td>
      <td><?php echo $row_articlesRS['article']; ?></td>
      <td><?php echo $row_articlesRS['description']; ?></td>
      <td><?php echo $row_articlesRS['price']; ?></td>
      <td><?php echo $row_articlesRS['quantity']; ?></td>
      <td><div align="center"><a href="article.php?ID=<?php echo $row_articlesRS['_id']; ?>">
          <img src="images/icons/ic_menu_edit.png" alt="editar" width="34" height="34" longdesc="Editar" /></a>&nbsp; 
          <img src="images/icons/ic_delete.png" alt="eliminar" width="34" height="34" longdesc="Eliminar" /></div></td>
    </tr>
<?php } while ($row_articlesRS = mysql_fetch_assoc($articlesRS)); ?>
  </table>
  <?php if($id!=0) echo"<input id='submitBt' name='Actualizar' type='submit' value='Insertar' />";?>
  <?php if($id!=0) echo"<input id='insertValue' type='hidden' name='MM_insert' value='InsertGroup' />";?>
  <?php if($id!=0) echo"</form>"; ?>
</body>
</html>
<?php
mysql_free_result($articlesRS);
?>
