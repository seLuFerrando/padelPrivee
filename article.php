<?php require_once('connections/padelprivee.php');
      require_once('functions/functions.php');
  header('Content-Type: text/html; charset=utf-8');
  $folder = "images/";
  $image="";

  if(isset($_GET['ID'])){
    $id=$_GET['ID'];
  }else{
    $id=0;
  } 

  if($id!=0){
    $queryArticle = "SELECT * FROM `articles` WHERE `_id` = $id";
    $rsArticle = mysql_query($queryArticle, $padelprivee) or die(mysql_error());
    $row_rsArticle = mysql_fetch_assoc($rsArticle);
    $image = $row_rsArticle['image'];
    $q = $row_rsArticle['quantity'];

    $querySizes = "SELECT `sizes`.`_id`, `sizes`.`size`, `sizes_article`.`quantity` 
      FROM `sizes` INNER JOIN `sizes_article` 
      ON `sizes`.`_id` = `sizes_article`.`size` 
      WHERE `sizes_article`.`article` = $id";
    $rsSizes = mysql_query($querySizes, $padelprivee) or die (mysql_error());
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

  $sizeFormAction = $_SERVER['PHP_SELF'];
  if (isset($_SERVER['QUERY_STRING'])) {
    $sizeFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
  }
  // ** Logout the current user. **
  $logoutAction = logOutUser();

  if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "InsertSize")) {
    $insertSQL = sprintf("INSERT INTO `sizes_article` (`article`, `size`, `quantity`) VALUES (%s, %s, %s)",
                         GetSQLValueString($id, "int"),
                         GetSQLValueString($_POST['Size'], "int"),
                         GetSQLValueString($_POST['Q'], "int"));

    $Result1 = mysql_query($insertSQL, $padelprivee) or die(mysql_error());
    $updateSQL = sprintf("UPDATE `articles` SET `quantity`=%s WHERE `_id` = %s",
                         GetSQLValueString($q + $_POST['Q'], "int"),
                         GetSQLValueString($id));
    $Result2 = mysql_query($updateSQL, $padelprivee) or die(mysql_error());
    $insertGoTo = "article.php?ID=" . $id;
    /*if (isset($_SERVER['QUERY_STRING'])) {
      $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
      $insertGoTo .= $_SERVER['QUERY_STRING'];
    } */
    header(sprintf("Location: %s", $insertGoTo));
  }

  if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "UpdateSize")) {
    $insertSQL = sprintf("UPDATE `sizes_article` SET `quantity`=%s, `size`=%s WHERE ((`article`=%s) && (`size`=%s))",
                         GetSQLValueString($_POST['Q'], "int"),
                         GetSQLValueString($_POST['Size'], "int"),
                         GetSQLValueString($id, "int"),
                         GetSQLValueString($_POST['oldS'], "int"));

    $Result1 = mysql_query($insertSQL, $padelprivee) or die(mysql_error());
    $updateSQL = sprintf("UPDATE `articles` SET `quantity`=%s WHERE `_id` = %s",
                         GetSQLValueString($q + $_POST['Q'] - $_POST['oldQ'] , "int"),
                         GetSQLValueString($id));

    $Result2 = mysql_query($updateSQL, $padelprivee) or die(mysql_error());
    $insertGoTo = "article.php?ID=" . $id;
    /*if (isset($_SERVER['QUERY_STRING'])) {
      $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
      $insertGoTo .= $_SERVER['QUERY_STRING'];
    } */
    header(sprintf("Location: %s", $insertGoTo));
  }

  if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "DeleteSize")) {
    $insertSQL = sprintf("DELETE FROM `sizes_article` WHERE ((`article`=%s) && (`size`=%s))",
                         GetSQLValueString($id, "int"),
                         GetSQLValueString($_POST['Size'], "int"));

    $Result1 = mysql_query($insertSQL, $padelprivee) or die(mysql_error());
    $updateSQL = sprintf("UPDATE `articles` SET `quantity`=%s WHERE `_id` = %s",
                         GetSQLValueString($q - $_POST['Q'], "int"),
                         GetSQLValueString($id));

    $Result2 = mysql_query($updateSQL, $padelprivee) or die(mysql_error());
    $insertGoTo = "article.php?ID=" . $id;
    /*if (isset($_SERVER['QUERY_STRING'])) {
      $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
      $insertGoTo .= $_SERVER['QUERY_STRING'];
    } */
    header(sprintf("Location: %s", $insertGoTo));
  }

  $editFormAction = $_SERVER['PHP_SELF'];
  if (isset($_SERVER['QUERY_STRING'])) {
    $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
  }

  if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "Article") && ($id == 0)) {
    $insertSQL = sprintf("INSERT INTO `articles` (`article`, `description`, `price`, `image`, `quantity`) VALUES (%s, %s, %s, %s, %s)",
                         GetSQLValueString($_POST['Article'], "text"),
                         GetSQLValueString($_POST['Description'], "text"),
                         GetSQLValueString($_POST['Price'], "double"),
                         GetSQLValueString($folder . $_FILES['Image']['name'], "text"),
                         GetSQLValueString(0, "int"));

    $Result1 = mysql_query($insertSQL, $padelprivee) or die(mysql_error());
    $id = mysql_insert_id();
    $file = $folder . basename( $_FILES['Image']['name']);
    if(is_uploaded_file($_FILES['Image']['tmp_name'])){
      move_uploaded_file($_FILES['Image']['tmp_name'], $file);
    }
    $insertGoTo = "article.php?ID=" . $id;
    /*if (isset($_SERVER['QUERY_STRING'])) {
      $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
      $insertGoTo .= $_SERVER['QUERY_STRING'];
    } */
    header(sprintf("Location: %s", $insertGoTo));
  }

  if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "Article") && ($id != 0)) {
    if((isset($_FILES['Image'])) && ($_FILES['Image']['tmp_name'] != "")){
      $image = $folder . $_FILES['Image']['name'];
      $file = $folder . basename( $_FILES['Image']['name']);
      if(is_uploaded_file($_FILES['Image']['tmp_name'])){
        move_uploaded_file($_FILES['Image']['tmp_name'], $file);
      }
    }
    $updateSQL = sprintf("UPDATE `articles` SET `article`=%s, `description`=%s, `price`=%s, `image`=%s,
     `quantity`=%s WHERE `_id`=%s",
                         GetSQLValueString($_POST['Article'], "text"),
                         GetSQLValueString($_POST['Description'], "text"),
                         GetSQLValueString($_POST['Price'], "double"),
                         GetSQLValueString($image, "text"),
                         GetSQLValueString($_POST['Quantity'], "int"),
                         GetSQLValueString($id, "int"));

    $Result1 = mysql_query($updateSQL, $padelprivee) or die(mysql_error());
    $insertGoTo = "article.php?ID=" . $id;
    /*if (isset($_SERVER['QUERY_STRING'])) {
      $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
      $insertGoTo .= $_SERVER['QUERY_STRING'];
    } */
    header(sprintf("Location: %s", $insertGoTo));
  }

  $query_sizesRS = "SELECT * FROM `sizes`";
  $sizesRS = mysql_query($query_sizesRS, $padelprivee) or die(mysql_error());
  $row_sizesRS = mysql_fetch_assoc($sizesRS);
  $totalRows_sizesRS = mysql_num_rows($sizesRS);
?>
<?php
  $title = "Articulos";
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
  $hrefs = array("groups.php",
             "sizes.php",
             "",
             "notifications.php",
             "",
             "clients.php",
             "orders.php",
             "",
             "",
             "articles.php");
  $texts = array("Promociones",
             "Tallas",
             "",
             "Notificaciones",
             "",
             "Clientes",
             "Pedidos",
             "",
             "",
             "Articulos");
  include("includes/navigation.php");
?>
            <section class="indice">
                  <div class="contactar bg_form shadow">
                      <p class="titular"><?php if($id!=0){echo "Articulo ".$row_rsArticle['_id'];}else{echo "Nuevo Articulo";} ?></p>
                      <form action="<?php echo $editFormAction; ?>" method="POST" enctype="multipart/form-data" name="Article" class="myform">
                      <fieldset>
                          <label>Articulo:</label>
                          <input name="Article" type="text" value="<?php if($id!=0)echo $row_rsArticle['article']; ?>"/>
                          <label>Descripcion:</label>
                          <input name="Description" type="text" value="<?php if($id!=0)echo $row_rsArticle['description']; ?>"/>
                          <label>Precio:</label>
                          <input name="Price" type="text" value="<?php if($id!=0)echo $row_rsArticle['price']; ?>"/>
                          <label>Imagen:</label>
                          <input name="Image" type="file"/>
                          <?php if($id!=0) echo "<img src='$image'";?>
                          <p></p>
                          <label>Unidades:</label>
                          <input name="Quantity" type="text" value="<?php if($id!=0)echo $row_rsArticle['quantity']; ?>"/>
                      </fieldset>
                      <input name="Actualizar" type="submit" value="Actualizar" />
                      <input type="hidden" name="MM_insert" value="Article" />
                      </form>
                        <p></p>
                  </div>
              </section>
              <section <?php if($id==0) echo "style='hidden";?> class="indice">
                  <div class="contactar bg_form shadow">
                      <p class="titular">Articulos de la promocion</p>
                      <table width="90%" border="1">
                        <tr>
                          <th scope="col">Talla</th>
                          <th scope="col">Unidades</th>
                          <th scope="col"><div id="popInsert" align="center">
                              <img src="images/icons/ic_input_add.png" onclick="popInsert()" width="48" height="48" alt="añadir" longdesc="Añadir" class="jslink" />
                          </div></th>
                        </tr>
                        <?php do { ?>
                          <tr>
                            <td id="sizeRow"><?php echo $row_rsSizes['size']; ?></td>
                            <td id="qRow"><?php echo $row_rsSizes['quantity']; ?></td>        
                            <td><div align="center">
                              <img class="popUpdate" onclick="popUpdate(<?php echo $row_rsSizes['_id']; ?>, <?php echo $row_rsSizes['quantity']; ?>)" src="images/icons/ic_menu_edit.png" width="34" class="jslink" height="34" longdesc="Editar" />&nbsp; 
                              <img class="popDelete" onclick="popDelete(<?php echo $row_rsSizes['_id']; ?>, <?php echo $row_rsSizes['quantity']; ?>)" src="images/icons/ic_delete.png" alt="eliminar" class="jslink" width="34" height="34" longdesc="Eliminar" /></div></td>
                          </tr>
                          <?php }while($row_rsSizes = mysql_fetch_assoc($rsSizes)); ?>
                      </table>
                      <p></p>
                  </div>
              </section>
              <div id="popUpDiv">
                <form action="<?php echo $sizeFormAction; ?>" method="POST" enctype="multipart/form-data" name="Sizes">  
                  <p>
                  <select id="popupSelect" name="Size">
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
                  <input id="submitBt" name="Actualizar" type="submit" value="Insertar" />
                  <button id="cancelarBt" onclick="popHide()">Cancelar</button> 
                  <input id="insertValue" type="hidden" name="MM_insert" value="InsertSize" />
                </form>
              </div>
                <script type="text/javascript">
                  function popInsert(){
                    $("#Q").show();
                    $("#insertValue").val('InsertSize');
                    $("#submitBt").val('Insertar');
                    $("#popUpDiv").show();
                  }
                  function popUpdate(s, q){
                    $("#Q").val(q);
                    $("#oldQ").val(q);
                    $("#oldS").val(s);
                    $("#popupSelect").val(s);
                    $("#insertValue").val('UpdateSize');
                    $("#submitBt").val('Actualizar');
                    $("#popUpDiv").show();
                  }
                  function popDelete(s, q){
                    $("#Q").val(q);
                    $("#Q").hide();
                    $("#popupSelect").val(s);
                    $("#insertValue").val('DeleteSize');
                    $("#submitBt").val('Eliminar');
                    $("#popUpDiv").show();
                  }
                  function popHide(){
                    $("#insertValue").val('nothing');
                    $("#popUpDiv").hide();
                  }
                </script>
          </div>
        </div>
    </section>
<?php include("includes/footer.php"); ?>
<?php
if($id!=0){
  mysql_free_result($rsSizes);  
  mysql_free_result($rsArticle);
}
mysql_close($padelprivee);
?>

