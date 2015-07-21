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
    $queryOrder = "SELECT * FROM `orders` WHERE `_id` = $id";
    $rsOrder = mysql_query($queryOrder, $padelprivee) or die(mysql_error());
    $row_rsOrder = mysql_fetch_assoc($rsOrder);

    $queryLines = "SELECT `lines_order`.*, `articles`.`article`, `sizes`.`size` FROM `lines_order` 
    INNER JOIN `articles` ON `lines_order`.`article`=`articles`.`_id`
    INNER JOIN `sizes` ON `lines_order`.`size`=`sizes`.`_id`
    WHERE `lines_order`.`order` = $id";
    $rsLines = mysql_query($queryLines, $padelprivee) or die(mysql_error());
    $row_rsLines = mysql_fetch_assoc($rsLines);
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

  $ready=0;
  $cashed=0;
  $dispatchet=0;
  if(!empty($_POST['Ready'])) $ready = $_POST['Ready'];
  if(!empty($_POST['Cashed'])) $cashed = $_POST['Cashed'];
  if(!empty($_POST['Dispatchet'])) $dispatchet = $_POST['Dispatchet'];

  if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "Order") && ($id == 0)) {
    $insertSQL = sprintf("INSERT INTO `orders` (`order`, `date`, `customer`, `total`, `paymentType`, `ready`, `cashed`, `dispatchet`, `observations`) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
                         GetSQLValueString($_POST['Order'], "text"),
                         GetSQLValueString($_POST['Date'], "date"),
                         GetSQLValueString($_POST['Customer'], "int"),
                         GetSQLValueString($_POST['Total'], "double"),
                         GetSQLValueString($_POST['PaymentType'], "int"),
                         GetSQLValueString($ready, "int"),
                         GetSQLValueString($cashed, "int"),
                         GetSQLValueString($dispatchet, "int"),
                         GetSQLValueString($_POST['Observations'], "text"));

    $Result1 = mysql_query($insertSQL, $padelprivee) or die(mysql_error());
    $id = mysql_insert_id();
    $insertGoTo = "order.php?ID=" . $id;
    /*if (isset($_SERVER['QUERY_STRING'])) {
      $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
      $insertGoTo .= $_SERVER['QUERY_STRING'];
    } */
    header(sprintf("Location: %s", $insertGoTo));
  }

  if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "Order") && ($id != 0)) {

    $updateSQL = sprintf("UPDATE `orders` SET `order`=%s, `date`=%s, `customer`=%s, `total`=%s, `paymentType`=%s, `ready`=%s, 
     `cashed`=%s, `dispatchet`=%s, `observations`=%s WHERE `_id`=%s",
                         GetSQLValueString($_POST['Order'], "text"),
                         GetSQLValueString($_POST['Date'], "date"),
                         GetSQLValueString($_POST['Customer'], "int"),
                         GetSQLValueString($_POST['Total'], "double"),
                         GetSQLValueString($_POST['PaymentType'], "int"),
                         GetSQLValueString($ready, "int"),
                         GetSQLValueString($cashed, "int"),
                         GetSQLValueString($dispatchet, "int"),
                         GetSQLValueString($_POST['Observations'], "text"),
                         GetSQLValueString($id, "int"));

    $Result1 = mysql_query($updateSQL, $padelprivee) or die(mysql_error());
    $insertGoTo = "order.php?ID=" . $id;
    /*if (isset($_SERVER['QUERY_STRING'])) {
      $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
      $insertGoTo .= $_SERVER['QUERY_STRING'];
    } */
    header(sprintf("Location: %s", $insertGoTo));
  }


  // OJITO AQUI Eliminar las lineas de pedido y actualizar pedido
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
  $query_customersRS = "SELECT * FROM `customers`";
  $customersRS = mysql_query($query_customersRS, $padelprivee) or die(mysql_error());
  $row_customersRS = mysql_fetch_assoc($customersRS);
  $totalRows_customersRS = mysql_num_rows($customersRS);

  $query_paymentsRS = "SELECT * FROM `payments`";
  $paymentsRS = mysql_query($query_paymentsRS, $padelprivee) or die(mysql_error());
  $row_paymentsRS = mysql_fetch_assoc($paymentsRS);
  $totalRows_paymentsRS = mysql_num_rows($paymentsRS);

  $query_sizesRS = "SELECT * FROM `sizes`";
  $sizesRS = mysql_query($query_sizesRS, $padelprivee) or die(mysql_error());
  $row_sizesRS = mysql_fetch_assoc($sizesRS);
  $totalRows_sizesRS = mysql_num_rows($sizesRS);

  $query_articlesRS = "SELECT * FROM `articles`";
  $articlesRS = mysql_query($query_articlesRS, $padelprivee) or die(mysql_error());
  $row_articlesRS = mysql_fetch_assoc($articlesRS);
  $totalRows_articlesRS = mysql_num_rows($articlesRS);
?>
<?php
  $title = "Pedido";
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
             "Pedido");
  include("includes/navigation.php");
?>
              <section class="indice">
                  <div class="contactar bg_form shadow">
                      <p class="titular"><?php if($id!=0){echo "Pedido ".$row_rsOrder['_id'];}else{echo "Nuevo Pedido";} ?></p>
                          <form action="<?php echo $editFormAction; ?>" method="POST" enctype="multipart/form-data" name="Order" class="myform" >
                              <fieldset>
                                <label>Pedido:</label>
                                <input name="Order" type="text" value="<?php if($id!=0)echo $row_rsOrder['order']; ?>"/>
                                <label>Fecha:</label>
                                <input name="Date" type="text" value="<?php if($id!=0)echo $row_rsOrder['date']; ?>"/>
                                <label>Cliente:</label>
                                <select name="Customer">
                                  <?php
                                  do {  
                                  ?>
                                    <option value="<?php echo $row_customersRS['_id']?>" 
                                    <?php if($id!=0) if($row_customersRS['_id']==$row_rsOrder['customer']) echo "selected='selected'";?>>
                                    <?php echo $row_customersRS['secondname'].",".$row_customersRS['name']?></option>
                                    <?php
                                  } while ($row_customersRS = mysql_fetch_assoc($customersRS));
                                    $rows = mysql_num_rows($customersRS);
                                    if($rows > 0) {
                                        mysql_data_seek($customersRS, 0);
                                      $row_customersRS = mysql_fetch_assoc($customersRS);
                                    }
                                  ?>
                                </select>
                                <label>Total:</label>
                                <input name="Total" type="text" value="<?php if($id!=0){echo $row_rsOrder['total'];}else{echo 0;} ?>"/>
                                <label>Tipo de Pago:</label>
                                <select name="PaymentType">
                                  <?php
                                  do {  
                                  ?>
                                    <option value="<?php echo $row_paymentsRS['_id']?>" 
                                    <?php if($id!=0) if($row_paymentsRS['_id']==$row_rsOrder['paymentType']) echo "selected='selected'";?>>
                                    <?php echo $row_paymentsRS['payment'];?></option>
                                    <?php
                                  } while ($row_paymentsRS = mysql_fetch_assoc($paymentsRS));
                                    $rows = mysql_num_rows($paymentsRS);
                                    if($rows > 0) {
                                        mysql_data_seek($paymentsRS, 0);
                                      $row_paymentsRS = mysql_fetch_assoc($paymentsRS);
                                    }
                                  ?>
                                </select>
                                <label>Preparado:</label>
                                <input name="Ready" type="checkbox" value="1" <?php if($id!=0){ if($row_rsOrder['ready']==1) echo "checked";}?> />
                                <label>Pagado:</label>
                                <input name="Cashed" type="checkbox" value="1" <?php if($id!=0){ if($row_rsOrder['cashed']==1) echo "checked";}?> />
                                <label>Enviado:</label>
                                <input name="Dispatchet" type="checkbox" value="1" <?php if($id!=0){ if($row_rsOrder['dispatchet']==1) echo "checked";}?> />
                                <label>Observaciones:</label>
                                <input name="Observations" type="text" value="<?php if($id!=0)echo $row_rsOrder['observations']; ?>"/>
                              </fieldset>
                              <input name="Actualizar" type="submit" value="Actualizar" />
                              <input type="hidden" name="MM_insert" value="Order" />
                            </form>
                        <p></p>
                  </div>
              </section>
              <section <?php if($id==0) echo "style='hidden";?> class="indice">
                  <div class="contactar bg_form shadow">
                      <p class="titular">Articulos del pedido</p>
                      <!-- tabla Lineas -->
                      <table width="90%" border="1">
                        <tr>
                          <th scope="col">Articulo</th>
                          <th scope="col">Talla</th>
                          <th scope="col">Cantidad</th>
                          <th scope="col">Precio</th>
                          <th scope="col">Total</th>
                          <th scope="col"><div align="center"><a href="order.php"><img src="images/icons/ic_input_add.png" width="48" height="48" alt="añadir" longdesc="Añadir" /></a></div></th>
                        </tr>
                        <?php do { ?>
                          <tr>
                            <td><?php echo $row_rsLines['article']; ?></td>
                            <td><?php echo $row_rsLines['size']; ?></td>
                            <td><?php echo $row_rsLines['quantity']; ?></td>
                            <td><?php echo $row_rsLines['price'];?></td>
                            <td><?php echo $row_rsLines['totalLine'];?></td>
                            <td><div align="center">
                              <img src="images/icons/ic_menu_edit.png" alt="editar" width="34" height="34" onClick="popInsert(<?php echo $row_rsLines['_id']; ?>)" />&nbsp; 
                              <img src="images/icons/ic_delete.png" alt="eliminar" width="34" height="34" onClick="popDelete(<?php echo $row_rsLines['_id']; ?>)" /></div></td>
                          </tr>
                          <?php } while ($row_rsLines = mysql_fetch_assoc($rsLines)); ?>
                      </table>
                      <p></p>
                  </div>
              </section>
              <div id="popUpDiv">
                <form action="<?php echo $lineFormAction; ?>" method="POST" enctype="multipart/form-data" name="Sizes">  
                  <p>
                  <select id="popupSelectA" name="Article">
                    <?php
                    do {  
                    ?>
                      <option value="<?php echo $row_articlesRS['_id']?>"><?php echo $row_articlesRS['article']?></option>
                      <?php
                    } while ($row_articlesRS = mysql_fetch_assoc($articlesRS));
                      $rows = mysql_num_rows($articlesRS);
                      if($rows > 0) {
                          mysql_data_seek($articlesRS, 0);
                        $row_articlesRS = mysql_fetch_assoc($articlesRS);
                      }
                    ?>
                  </select>
                  </p>

                  <p>
                  <select id="popupSelectS" name="Size">
                    <?php
                    do {  
                    ?>
                      <option value="<?php echo $row_sizesRS['_id']?>"><?php echo $row_sizesRS['size']?></option>
                      <?php
                    } while ($row_sizesRS = mysql_fetch_assoc($sizesRS));
                      $rows = mysql_num_rows($sizesRS);
                      if($rows > 0) {
                          mysql_data_seek($sizesRS, 0);
                        $row_sizesRS = mysql_fetch_assoc($sizesRS);
                      }
                    ?>
                  </select>
                  </p>
                  <p>
                  <label>Unidades:</label>
                  <input id="Q" name="Q" type="text" value="1"/>
                  <input id="oldQ" name="oldQ" type="hidden" value="1"/>
                  <input id="oldS" name="oldS" type="hidden" value="1"/>
                  </p>
                  <p>
                  <label>Precio:</label>
                  <input id="P" name="P" type="text" value="1"/>
                  <input id="oldP" name="oldP" type="hidden" value="1"/>
                  </p>
                  <p>
                  <label>Total:</label>
                  <input id="T" name="T" type="text" value="1"/>
                  <input id="oldT" name="oldT" type="hidden" value="1"/>
                  </p>
                  <input id="submitBt" name="Actualizar" type="submit" value="Insertar" />
                  <button id="cancelarBt" onclick="popHide()">Cancelar</button> 
                  <input id="insertValue" type="hidden" name="MM_insert" value="InsertLine" />
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
          </div>
        </div>
    </section>
<?php include("includes/footer.php"); ?>
<?php
  if($id!=0){
    mysql_free_result($rsLines);  
    mysql_free_result($rsOrder);
  }
  mysql_free_result($customersRS);
  mysql_free_result($paymentsRS);
  mysql_free_result($sizesRS);
  mysql_free_result($articlesRS);
  mysql_close($padelprivee);
?>
