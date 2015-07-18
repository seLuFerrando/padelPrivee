<?php require_once('connections/padelprivee.php');
      require_once('functions/functions.php'); ?>

<?php
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

  $query_groupsRS = "SELECT groups.*, status.status FROM groups, status WHERE groups.status = status._id";
  $groupsRS = mysql_query($query_groupsRS, $padelprivee) or die(mysql_error());
  $row_groupsRS = mysql_fetch_assoc($groupsRS);
  $totalRows_groupsRS = mysql_num_rows($groupsRS);
?>
<?php
  $title = "Promociones";
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
  $hrefs = array( "articles.php",
             "sizes.php",
             "",
             "notifications.php",
             "",
             "clients.php",
             "orders.php",
             "",
             "",
             "groups.php");
  $texts = array( "Articulos",
             "Tallas",
             "",
             "Notificaciones",
             "",
             "Clientes",
             "Pedidos",
             "",
             "",
             "Promociones");
  include("includes/navigation.php");
?>
              <section class="indice">
                  <div class="contactar bg_form shadow">
                      <p class="titular">Promociones</p>

                      <table width="90%" border="1">
                          <tr>
                            <th scope="col">Imagen</th>
                            <th scope="col">Promocion</th>
                            <th scope="col">Estado</th>
                            <th scope="col">Inicio</th>
                            <th scope="col">Fin</th>
                            <th scope="col">Activa</th>
                            <th scope="col"><div align="center"><a href="group.php"><img src="images/icons/ic_input_add.png" width="48" height="48" alt="añadir" longdesc="Añadir" /></a></div></th>
                          </tr>

                      <?php do { ?>
                          <tr>
                            <td><img src="<?php echo $row_groupsRS['image']; ?>" width="90px" heigth="90px"></td>
                            <td><?php echo $row_groupsRS['group']; ?></td>
                            <td><?php echo $row_groupsRS['status']; ?></td>
                            <td><?php echo $row_groupsRS['dateStart']; ?></td>
                            <td><?php echo $row_groupsRS['dateEnd']; ?></td>
                            <td><input <?php if (!(strcmp($row_groupsRS['visibility'],1))) {echo "checked=\"checked\"";} ?> name="" type="checkbox" value="" /></td>
                            <td><div align="center"><a href="group.php?ID=<?php echo $row_groupsRS['_id']; ?>">
                                <img src="images/icons/ic_menu_edit.png" alt="editar" width="34" height="34" longdesc="Editar" /></a>&nbsp; 
                                <img src="images/icons/ic_delete.png" alt="eliminar" width="34" height="34" longdesc="Eliminar" /></div></td>    
                          </tr>
                      <?php } while ($row_groupsRS = mysql_fetch_assoc($groupsRS)); ?>
                        </table>
                      <p></p>
                  </div>
              </section>
          </div>
        </div>
    </section>
<?php include("includes/footer.php"); ?>
<?php
  mysql_free_result($groupsRS);
  mysql_close($padelprivee);
?>
