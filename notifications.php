<?php require_once('connections/padelprivee.php');
      require_once('functions/functions.php'); ?>

<?php
  if(isset($_POST['Title'])){
    $title=$_POST['Title'];
  }else{
    $title='0';
  } 

  if(isset($_POST['Message'])){
    $message=$_POST['Message'];
  }else{
    $message='0';
  } 

  if(isset($_POST['Target'])){
    $target=$_POST['Target'];
  }else{
    $target='0';
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

  if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "Notification") && ($title != '0') && ($message != '0')) {
    $date = date("Y-n-d H:i:s");
    echo $date;
    $insertSQL = sprintf("INSERT INTO `notifications` (`title`, `message`, `icon`, `date`, `target`) VALUES (%s, %s, %s, %s, %s)",
                         GetSQLValueString($title, "text"),
                         GetSQLValueString($message, "text"),
                         GetSQLValueString("appicon", "text"),
                         GetSQLValueString($date, "date"),
                         GetSQLValueString($target, "int"));

    $Result1 = mysql_query($insertSQL, $padelprivee) or die(mysql_error());
    $id = mysql_insert_id();



      /*** SETUP ***************************************************/

      $key = "zVMnqO4UHXcyCQuv8KZ4noTOzhRTgLfR"; //GET FROM APP MANAGEMENT (ACS)
      $username = "optimus";
      $password = "optimus";
      $channel = "alert";
      $tmp_fname = 'cookie.txt';
      $json = '{
      "alert":"'. $message .'",
      "title":"'. $title .'",
      "vibrate":true,
      "sound":"default",
      "badge":"'. $id .'",
      "icon":"appicon",
    	"target":"'. $target .'",
      "date":"'.$date.'"
      }';
   
      /*** PUSH NOTIFICATION ***********************************/
   
      $post_array = array('login' => $username, 'password' => $password);
   
      /*** INIT CURL *******************************************/
      $curlObj = curl_init();
      $c_opt = array(CURLOPT_URL => 'https://api.cloud.appcelerator.com/v1/users/login.json?key='.$key,
                          CURLOPT_COOKIEJAR => $tmp_fname,
                          CURLOPT_COOKIEFILE => $tmp_fname,
                          CURLOPT_RETURNTRANSFER => true,
                          CURLOPT_POST => 1,
                          CURLOPT_POSTFIELDS => "login=".$username."&password=".$password,
                          CURLOPT_FOLLOWLOCATION => 1,
                          CURLOPT_TIMEOUT => 60);
   
      /*** LOGIN **********************************************/
      @curl_setopt_array($curlObj, $c_opt);
      $session = curl_exec($curlObj);

      /*** SEND PUSH ******************************************/
      $c_opt[CURLOPT_URL] = "https://api.cloud.appcelerator.com/v1/push_notification/notify.json?key=".$key;
      $c_opt[CURLOPT_POSTFIELDS] = "channel=".$channel."&payload=".$json."&to_ids=everyone";
   
      @curl_setopt_array($curlObj, $c_opt);
      $session = curl_exec($curlObj);

      /*** THE END ********************************************/
      curl_close($curlObj);

  }

  $query_groupsRS = "SELECT `_id`, `group` FROM groups";
  $groupsRS = mysql_query($query_groupsRS, $padelprivee) or die(mysql_error());
  $row_groupsRS = mysql_fetch_assoc($groupsRS);
  $totalRows_groupsRS = mysql_num_rows($groupsRS);

?>
<?php
  $title = "Notificaciones push";
  $userName = "";
  if (isset($_SESSION['MM_Username'])) $userName = $_SESSION['MM_Username'];
  include("includes/header.php");
  $classes =array("box bg_6 transicion shadow",
             "box bg_6 transicion shadow",
             "box bg_6 transicion shadow",
             "box bg_6 static",
             "box bg_6 transicion shadow",
             "box bg_6 transicion shadow",
             "box bg_6 static",
             "box bg_6 static",
             "box bg_6 static",
             "box bg_6 transicion shadow");
  $hrefs = array( "groups.php",
             "articles.php",
             "sizes.php",
             "",
             "customers.php",
             "orders.php",
             "",
             "",
             "",
             "notifications.php");
  $texts = array( "Promociones",
             "Articulos",
             "Tallas",
             "",
             "Clientes",
             "Pedidos",
             "",
             "",
             "",
             "Notificaciones Push");
  include("includes/navigation.php");
?>
              <section class="indice">
                  <div class="contactar bg_form shadow">
                      <p class="titular">Notificaciones</p>
                      <form action="<?php echo $loginFormAction; ?>" class="myform" method="post" name="Login"> 
                        <fieldset>
                            <input type="hidden" name="language" value="es"/>
                            <input type="text" maxlength="40" name="Title" id="name" placeholder="Titulo" required="required"/>
                            <input type="text" name="Message" id="password" placeholder="Mensaje" required="required"/>
                            <select name="Target">
                              <option value="0">Promocion</option>
                            <?php
                              do {  
                            ?>
                              <option value="<?php echo $row_groupsRS['_id']?>">
                              <?php echo $row_groupsRS['group']?></option>
                              <?php
                            } while ($row_groupsRS = mysql_fetch_assoc($groupsRS));
                              $rows = mysql_num_rows($groupsRS);
                              if($rows > 0) {
                                  mysql_data_seek($groupsRS, 0);
                                $row_groupsRS = mysql_fetch_assoc($groupsRS);
                              }
                            ?>
                            </select>
                        </fieldset>
                        <input type="hidden" name="MM_insert" value="Notification" />
                        <input type='submit' value='Enviar' class='transicion' name="Enviar" />
                    </form> 
<!--
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
-->
                  </div>
              </section>
          </div>
        </div>
    </section>
<?php include("includes/footer.php"); ?>