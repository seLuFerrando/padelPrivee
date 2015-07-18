<?php require_once('connections/padelprivee.php');
      require_once('functions/functions.php'); ?>
<?php
  header('Content-Type: text/html; charset=utf-8');
  if(isset($_GET['ID'])){
    $id=$_GET['ID'];
  }else{
    $id=0;
  } 
  if($id!=0){
    $querySizes = "SELECT * FROM `sizes` WHERE `_id` = $id";
    $rsSizes = mysql_query($querySizes, $padelprivee) or die(mysql_error());
    $row_rsSizes = mysql_fetch_assoc($rsSizes);
  }

  if (!isset($_SESSION)) {
    session_start();
  }
  $MM_authorizedUsers = "99";
  $MM_donotCheckaccess = "false";

  // *** Restrict Access To Page: Grant or deny access to this page
  $MM_restrictGoTo = "index.php";
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
  // ** Logout the current user. **
  $logoutAction = logOutUser();

  $editFormAction = $_SERVER['PHP_SELF'];
  if (isset($_SERVER['QUERY_STRING'])) {
    $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
  }

  if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "Customer") && ($id == 0)) {
    $insertSQL = sprintf("INSERT INTO `sizes` (`size`) VALUES (%s)",
                         GetSQLValueString($_POST['Size'], "text"));

    $Result1 = mysql_query($insertSQL, $padelprivee) or die(mysql_error());
    //$id = mysql_insert_id();
    $insertGoTo = "sizes.php";
    /*if (isset($_SERVER['QUERY_STRING'])) {
      $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
      $insertGoTo .= $_SERVER['QUERY_STRING'];
    } */
    header(sprintf("Location: %s", $insertGoTo));
  }

  if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "Customer") && ($id != 0)) {

    $updateSQL = sprintf("UPDATE `sizes` SET `size`=%s WHERE `_id`=%s",
                         GetSQLValueString($_POST['Size'], "text"),
                         GetSQLValueString($id, "int"));

    $Result1 = mysql_query($updateSQL, $padelprivee) or die(mysql_error());
    $insertGoTo = "sizes.php";
    /*if (isset($_SERVER['QUERY_STRING'])) {
      $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
      $insertGoTo .= $_SERVER['QUERY_STRING'];
    } */
    header(sprintf("Location: %s", $insertGoTo));
  }
?>
<?php
  $title = "Talla";
  $userName = "";
  if (isset($_SESSION['MM_Username'])) $userName = $_SESSION['MM_Username'];
  include("includes/header.php");
  $classes =array("box bg_6 transicion shadow",
             "box bg_6 transicion shadow",
             "box bg_6 static",
             "box bg_6 transicion shadow",
             "box bg_6 static",
             "box bg_6 transicion shadow",
             "box bg_6 transicion shadow",
             "box bg_6 static",
             "box bg_6 static",             
             "box bg_6 transicion shadow");
  $hrefs = array( "groups.php",
             "articles.php",
             "",
             "notifications.php",
             "",
             "clients.php",
             "orders.php",
             "",
             "",
             "sizes.php");
  $texts = array( "Promociones",
             "Articulos",
             "",
             "Notificaciones",
             "",
             "Clientes",
             "Pedidos",
             "",
             "",
             "Tallas");
  include("includes/navigation.php");
?>
              <section class="indice">
                  <div class="contactar bg_form shadow">
                      <p class="titular"><?php if($id!=0){echo "Talla ".$row_rsSizes['_id'];}else{echo "Nueva Talla";} ?></p>
                      <form action="<?php echo $editFormAction; ?>" method="POST" enctype="multipart/form-data" name="Size" class="myform">
                      <fieldset>
                      <p><input name="Size" type="text" value="<?php if($id!=0)echo $row_rsSizes['size']; ?>"/></p>
                      </fieldset>
                      <input name="Actualizar" type="submit" value="Actualizar" />
                      <input type="hidden" name="MM_insert" value="Customer" />
                      </form>
                        <p></p>
                  </div>
              </section>
          </div>
        </div>
    </section>
<?php include("includes/footer.php"); ?>
<?php
  if($id!=0){
    mysql_free_result($rsSizes);  
  }

  mysql_close($padelprivee);
?>
