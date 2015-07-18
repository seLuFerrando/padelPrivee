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
?>
<?php
  mysql_select_db($database_padelprivee, $padelprivee);
  $query_clientsRS = "SELECT * FROM customers";
  $clientsRS = mysql_query($query_clientsRS, $padelprivee) or die(mysql_error());
  $row_clientsRS = mysql_fetch_assoc($clientsRS);
  $totalRows_clientsRS = mysql_num_rows($clientsRS);
?>
<?php
  $title = "Clientes";
  $userName = "";
  if (isset($_SESSION['MM_Username'])) $userName = $_SESSION['MM_Username'];
  include("includes/header.php");
  $classes =["box bg_6 transicion shadow",
             "box bg_6 transicion shadow",
             "box bg_6 transicion shadow",
             "box bg_6 static",
             "box bg_6 transicion shadow",
             "box bg_6 static",
             "box bg_6 transicion shadow",
             "box bg_6 static",
             "box bg_6 static",
             "box bg_6 transicion shadow"];
  $hrefs = [ "groups.php",
             "articles.php",
             "sizes.php",
             "",
             "notifications.php",
             "",
             "orders.php",
             "",
             "",
             "customers.php"];
  $texts = [ "Promociones",
             "Articulos",
             "Tallas",
             "",
             "Notificaciones",
             "",
             "Pedidos",
             "",
             "",
             "Clientes"];
  include("includes/navigation.php");
?>
            <section class="indice">
                  <div class="contactar bg_form shadow">
                      <p class="titular">Promociones</p>
                      <table width="90%" border="1">
                          <tr>
                            <th scope="col">NIF</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Apellidos</th>
                            <th scope="col">Direccion</th>
                            <th scope="col">Poblacion</th>
                            <th scope="col">Email</th>
                            <th scope="col">Telefono</th>
                            <th scope="col"><div align="center"><a href="customer.php"><img src="images/icons/ic_input_add.png" width="48" height="48" alt="añadir" longdesc="Añadir" /></a></div></th>
                          </tr>

                      <?php do { ?>
                          <tr>
                            <td><?php echo $row_clientsRS['nif']; ?></td>
                            <td><?php echo $row_clientsRS['name']; ?></td>
                            <td><?php echo $row_clientsRS['secondname']; ?></td>
                            <td><?php echo $row_clientsRS['address']; ?></td>
                            <td><?php echo $row_clientsRS['postalCode']." - ".$row_clientsRS['city']; ?></td>
                            <td><?php echo $row_clientsRS['email']; ?></td>
                            <td><?php echo $row_clientsRS['phone']; ?></td>
                            <td><div align="center"><a href="customer.php?ID=<?php echo $row_clientsRS['_id']; ?>">
                              <img src="images/icons/ic_menu_edit.png" alt="editar" width="34" height="34" longdesc="Editar" /></a>&nbsp; 
                              <img src="images/icons/ic_delete.png" alt="eliminar" width="34" height="34" longdesc="Eliminar" /></div></td>
                          </tr>
                      <?php } while ($row_clientsRS = mysql_fetch_assoc($clientsRS)); ?>
                        </table>
                      <p></p>
                  </div>
              </section>
          </div>
        </div>
    </section>
<?php include("includes/footer.php"); ?>
<?php
mysql_free_result($clientsRS);
mysql_close($padelprivee);
?>
