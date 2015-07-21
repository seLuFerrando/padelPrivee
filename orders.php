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
  $query_ordersRS = "SELECT orders.*, payments.payment, customers.name, customers.secondname FROM orders 
  INNER JOIN customers ON orders.customer = customers._id 
  INNER JOIN payments ON payments._id = orders.paymentType";
  $ordersRS = mysql_query($query_ordersRS, $padelprivee) or die(mysql_error());
  $row_ordersRS = mysql_fetch_assoc($ordersRS);
  $totalRows_ordersRS = mysql_num_rows($ordersRS);
?>
<?php
  $title = "Pedidos";
  $userName = "";
  if (isset($_SESSION['MM_Username'])) $userName = $_SESSION['MM_Username'];
  include("includes/header.php");
  $classes =array("box bg_6 transicion shadow",
             "box bg_6 transicion shadow",
             "box bg_6 transicion shadow",
             "box bg_6 static",
             "box bg_6 transicion shadow",
             "box bg_6 static",
             "box bg_6 transicion shadow",
             "box bg_6 static",
             "box bg_6 static",             
             "box bg_6 transicion shadow");
  $hrefs = array( "groups.php",
             "articles.php",
             "sizes.php",
             "",
             "notifications.php",
             "",
             "customers.php",
             "",
             "",
             "orders.php");
  $texts = array( "Promociones",
             "Articulos",
             "Tallas",
             "",
             "Notificaciones",
             "",
             "Clientes",
             "",
             "",
             "Pedidos");
  include("includes/navigation.php");
?>
              <section class="indice">
                  <div class="contactar bg_form shadow">
                      <p class="titular">Pedidos</p>
                      <table width="90%" border="1">
                          <tr>
                            <th scope="col">Fecha</th>
                            <th scope="col">Pedido</th>
                            <th scope="col">Cliente</th>
                            <th scope="col">Total</th>
                            <th scope="col">TipoPago</th>
                            <th scope="col">Preparado</th>
                            <th scope="col">Pagado</th>
                            <th scope="col">Servido</th>
                            <th scope="col">Observaciones</th>
                            <th scope="col"><div align="center"><a href="order.php?ID=<?php echo $row_ordersRS['_id'] ; ?>"><img src="images/icons/ic_input_add.png" width="48" height="48" alt="añadir" longdesc="Añadir" /></a></div></th>
                          </tr>

                      <?php do { ?>
                          <tr>
                            <td><?php echo $row_ordersRS['date']; ?></td>
                            <td><?php echo $row_ordersRS['order']; ?></td>
                            <td><?php echo $row_ordersRS['secondname'].",".$row_ordersRS['name'] ; ?></td>
                            <td><?php echo $row_ordersRS['total']; ?></td>
                            <td><?php echo $row_ordersRS['payment']; ?></td>
                            <td><div align="center"><input name="Ready" type="checkbox" value="<?php if($row_rsOrders['ready']==1) echo "checked";?> "/></div></td>
                            <td><div align="center"><input name="Cashed" type="checkbox" value="<?php if($row_rsOrders['cashed']==1) echo "checked";?> "/></div></td>
                            <td><div align="center"><input name="Dispatch" type="checkbox" value="<?php if($row_rsOrders['dispatch']==1) echo "checked";?> "/></div></td>
                            <td><?php echo $row_ordersRS['observations']; ?></td>
                            <td><div align="center"><a href="order.php?ID=<?php echo $row_rsOrders['_id']; ?>">
                                <img src="images/icons/ic_menu_edit.png" alt="editar" width="34" height="34" longdesc="Editar" /></a>&nbsp; 
                                <img src="images/icons/ic_delete.png" alt="eliminar" width="34" height="34" onClick="popDelete(<?php echo $row_ordersRS['_id']; ?>)" /></div></td>
                          </tr>
                      <?php } while ($row_ordersRS = mysql_fetch_assoc($ordersRS)); ?>
                        </table>
  
                      <p></p>
                  </div>
              </section>
          </div>
        </div>
    </section>
<?php include("includes/footer.php"); ?>
<?php
  mysql_free_result($ordersRS);
  mysql_close($padelprivee);
?>
