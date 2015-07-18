<?php require_once('connections/padelprivee.php');
      require_once('functions/functions.php');
  header('Content-Type: text/html; charset=utf-8');
  if(isset($_GET['ID'])){
    $id=$_GET['ID'];
  }else{
    $id=0;
  } 
  if($id!=0){
    $queryCustomer = "SELECT * FROM `customers` WHERE `_id` = $id";
    $rsCustomer = mysql_query($queryCustomer, $padelprivee) or die(mysql_error());
    $row_rsCustomer = mysql_fetch_assoc($rsCustomer);

    $queryOrders = "SELECT `orders`.*, `payments`.`payment` 
    FROM `orders` INNER JOIN `payments` ON orders.paymentType=`payments`.`_id` 
    WHERE `customer` = $id";
    $rsOrders = mysql_query($queryOrders, $padelprivee) or die(mysql_error());
    $row_rsOrders = mysql_fetch_assoc($rsOrders);
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
    if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0) $MM_referrer .= "?" . $QUERY_STRING;
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
    $insertSQL = sprintf("INSERT INTO `customers` (`name`, `secondname`, `address`, `postalCode`, `city`, `nif`, `login`, `password`, `email`, `phone`, `date`) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                         GetSQLValueString($_POST['Name'], "text"),
                         GetSQLValueString($_POST['SecondName'], "text"),
                         GetSQLValueString($_POST['Address'], "text"),
                         GetSQLValueString($_POST['PostalCode'], "text"),
                         GetSQLValueString($_POST['City'], "text"),
                         GetSQLValueString($_POST['Nif'], "text"),
                         GetSQLValueString($_POST['Login'], "text"),
                         GetSQLValueString($_POST['Password'], "text"),
                         GetSQLValueString($_POST['Email'], "text"),
                         GetSQLValueString($_POST['Phone'], "text"),
                         GetSQLValueString($_POST['Date'], "date"));

    $Result1 = mysql_query($insertSQL, $padelprivee) or die(mysql_error());
    $id = mysql_insert_id();
    $insertGoTo = "customer.php?ID=" . $id;
    /*if (isset($_SERVER['QUERY_STRING'])) {
      $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
      $insertGoTo .= $_SERVER['QUERY_STRING'];
    } */
    header(sprintf("Location: %s", $insertGoTo));
  }

  if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "Customer") && ($id != 0)) {

    $updateSQL = sprintf("UPDATE `customers` SET `name`=%s, `secondname`=%s, `address`=%s, `postalCode`=%s, `city`=%s, `nif`=%s, 
     `login`=%s, `password`=%s, `email`=%s, `phone`=%s, `date`=%s WHERE `_id`=%s",
                         GetSQLValueString($_POST['Name'], "text"),
                         GetSQLValueString($_POST['SecondName'], "text"),
                         GetSQLValueString($_POST['Address'], "text"),
                         GetSQLValueString($_POST['PostalCode'], "text"),
                         GetSQLValueString($_POST['City'], "text"),
                         GetSQLValueString($_POST['Nif'], "text"),
                         GetSQLValueString($_POST['Login'], "text"),
                         GetSQLValueString($_POST['Password'], "text"),
                         GetSQLValueString($_POST['Email'], "text"),
                         GetSQLValueString($_POST['Phone'], "text"),
                         GetSQLValueString($_POST['Date'], "date"),
                         GetSQLValueString($id, "int"));

    $Result1 = mysql_query($updateSQL, $padelprivee) or die(mysql_error());
    $insertGoTo = "customer.php?ID=" . $id;
    /*if (isset($_SERVER['QUERY_STRING'])) {
      $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
      $insertGoTo .= $_SERVER['QUERY_STRING'];
    } */
    header(sprintf("Location: %s", $insertGoTo));
  }


  // TODO delete order and order lines
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
?>
<?php
  $title = "Login";
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
             "orders.php",
             "",
             "",
             "customers.php");
  $texts = array( "Promociones",
             "Articulos",
             "Tallas",
             "",
             "Notificaciones",
             "",
             "Pedidos",
             "",
             "",
             "Clientes");
  include("includes/navigation.php");
?>
              <section class="indice">
                  <div class="contactar bg_form shadow">
                      <p class="titular"><?php if($id!=0){echo "Cliente ".$row_rsCustomer['_id'];}else{echo "Nuevo Cliente";} ?></p>
                          <form action="<?php echo $editFormAction; ?>" method="POST" enctype="multipart/form-data" name="Customer" class="myform" >
                              <fieldset>
                                  <label>Nombre:</label>
                                  <input name="Name" type="text" value="<?php if($id!=0)echo $row_rsCustomer['name']; ?>"/>
                                  <label>Apellidos:</label>
                                  <input name="SecondName" type="text" value="<?php if($id!=0)echo $row_rsCustomer['secondname']; ?>"/>
                                  <label>Direccion:</label>
                                  <input name="Address" type="text" value="<?php if($id!=0)echo $row_rsCustomer['address']; ?>"/>
                                  <label>Codigo postal:</label>
                                  <input name="PostalCode" type="text" value="<?php if($id!=0)echo $row_rsCustomer['postalCode']; ?>"/>
                                  <label>Localidad:</label>
                                  <input name="City" type="text" value="<?php if($id!=0)echo $row_rsCustomer['city']; ?>"/>
                                  <label>NIF:</label>
                                  <input name="Nif" type="text" value="<?php if($id!=0)echo $row_rsCustomer['nif']; ?>"/>
                                  <label>Login:</label>
                                  <input name="Login" type="text" value="<?php if($id!=0)echo $row_rsCustomer['login']; ?>"/>
                                  <label>Password:</label>
                                  <input name="Password" type="password" value="<?php if($id!=0)echo $row_rsCustomer['password']; ?>"/>
                                  <label>Email:</label>
                                  <input name="Email" type="text" value="<?php if($id!=0)echo $row_rsCustomer['email']; ?>"/>
                                  <label>Telefono:</label>
                                  <input name="Phone" type="text" value="<?php if($id!=0)echo $row_rsCustomer['phone']; ?>"/>
                                  <label>Fecha de Alta:</label>
                                  <input name="Date" type="text" value="<?php if($id!=0)echo $row_rsCustomer['date']; ?>"/>
                              </fieldset>
                              <input name="Actualizar" type="submit" value="Actualizar" />
                              <input type="hidden" name="MM_insert" value="Customer" />
                          </form>
                        <p></p>
                  </div>
              </section>
              <section <?php if($id==0) echo "style='hidden";?> class="indice">
                  <div class="contactar bg_form shadow">
                      <p class="titular">Pedidos del Cliente</p>
                        <!-- tabla pedidos -->
                        <table width="98%" border="1">
                          <tr>
                            <th scope="col">Pedido</th>
                            <th scope="col">Fecha</th>
                            <th scope="col">Importe</th>
                            <th scope="col">Tipo Pago</th>
                            <th scope="col">Preparado</th>
                            <th scope="col">Cobrado</th>
                            <th scope="col">Servido</th>
                            <th scope="col"><!--<div align="center"><a href="order.php"><img src="images/icons/ic_input_add.png" width="48" height="48" alt="añadir" longdesc="Añadir" /></a></div>--></th>
                          </tr>
                          <?php do { ?>
                            <tr>
                              <td><?php echo $row_rsOrders['order']; ?></td>
                              <td><?php echo $row_rsOrders['date']; ?></td>
                              <td><?php echo $row_rsOrders['total']; ?></td>
                              <td><?php echo $row_rsOrders['payment'];?></td>
                              <td><div align="center"><input name="Ready" type="checkbox" value="<?php if($row_rsOrders['ready']==1) echo "checked";?> "/></div></td>
                              <td><div align="center"><input name="Cashed" type="checkbox" value="<?php if($row_rsOrders['cashed']==1) echo "checked";?> "/></div></td>
                              <td><div align="center"><input name="Dispatch" type="checkbox" value="<?php if($row_rsOrders['dispatch']==1) echo "checked";?> "/></div></td>
                              <td><div align="center"><a href="order.php?ID=<?php echo $row_rsOrders['_id']; ?>">
                                <img src="images/icons/ic_menu_edit.png" alt="editar" width="34" height="34" longdesc="Editar" /></a>&nbsp; 
                                <img src="images/icons/ic_delete.png" alt="eliminar" width="34" height="34" class="jslink" onClick="popDelete(<?php echo $row_rsOrders['_id']; ?>)" /></div></td>
                            </tr>
                            <?php } while ($row_rsOrders = mysql_fetch_assoc($rsOrders)); ?>
                        </table>
                      <p></p>
                  </div>
              </section>
              <div id="popUpDiv">
                <form action="<?php echo $editFormAction; ?>" method="POST" enctype="multipart/form-data" name="Articles">  
                  <h3>Desea eliminar el Pedido definitivamente?</h3>
                  <input id="Article" name="Article" type="hidden" value="0"/>
                  <input id="submitBt" name="Actualizar" type="submit" value="Eliminar" />
                  <button id="cancelarBt" onClick="popHide()">Cancelar</button> 
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
          </div>
        </div>
    </section>
<?php include("includes/footer.php"); ?>
<?php
if($id!=0){
  mysql_free_result($rsCustomer);  
  mysql_free_result($rsOrders);
}

mysql_close($padelprivee);
?>
