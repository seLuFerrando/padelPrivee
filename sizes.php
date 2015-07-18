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

?>
<?php
  $query_sizesRS = "SELECT * FROM `sizes`";
  $sizesRS = mysql_query($query_sizesRS, $padelprivee) or die(mysql_error());
  $row_sizesRS = mysql_fetch_assoc($sizesRS);
  $totalRows_sizesRS = mysql_num_rows($sizesRS);
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
                      <p class="titular">Tallas</p>
                      <table width="75%" border="1">
                          <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Talla</th>
                            <th scope="col"><div align="center"><a href="size.php"><img src="images/icons/ic_input_add.png" width="48" height="48" alt="añadir" longdesc="Añadir" /></a></div></th>
                          </tr>

                      <?php do { ?>
                          <tr>
                            <td><?php echo $row_sizesRS['_id']; ?></td>
                            <td><?php echo $row_sizesRS['size']; ?></td>
                            <td><div align="center"><a href="size.php?ID=<?php echo $row_sizesRS['_id']; ?>">
                                <img src="images/icons/ic_menu_edit.png" alt="editar" width="34" height="34" longdesc="Editar"  /></a>&nbsp; 
                                <img src="images/icons/ic_delete.png" alt="eliminar" width="34" height="34" longdesc="Eliminar" class="jslink" /></div></td>
                          </tr>
                      <?php } while ($row_sizesRS = mysql_fetch_assoc($sizesRS)); ?>
                        </table>
                        <p></p>  
                  </div>
              </section>
          </div>
        </div>
    </section>
<?php include("includes/footer.php"); ?>
<?php
  mysql_free_result($sizesRS);
  mysql_close($padelprivee);
?>
