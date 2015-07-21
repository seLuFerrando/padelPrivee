<?php require_once('connections/padelprivee.php');
      require_once('functions/functions.php'); ?>

<?php
  // *** Validate request to login to this site.
  if (!isset($_SESSION)) {
    session_start();
  }

  // ** Logout the current user. **
  $logoutAction = logOutUser();

  $loginFormAction = $_SERVER['PHP_SELF'];
  if (isset($_GET['accesscheck'])) {
    $_SESSION['PrevUrl'] = $_GET['accesscheck'];
  }

  if (isset($_POST['Usuario'])) {
    $loginUsername=$_POST['Usuario'];
    $password=$_POST['Password'];
    $MM_fldUserAuthorization = "level";
    $MM_redirectLoginSuccess = "principal.php";
    $MM_redirectLoginFailed = "index.php";
    $MM_redirecttoReferrer = true;
    $LoginRS_query=sprintf("SELECT `user`, `pass`, `level` FROM `users` WHERE `user`=%s AND `pass`=%s",
        GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text")); 
    $LoginRS = mysql_query($LoginRS_query, $padelprivee) or die(mysql_error());
    $loginFoundUser = mysql_num_rows($LoginRS);

    if ($loginFoundUser) {
      $loginStrGroup  = mysql_result($LoginRS,0,'level');
      //declare two session variables and assign them
      $_SESSION['MM_Username'] = $loginUsername;
      $_SESSION['MM_UserGroup'] = $loginStrGroup;	      

      if (isset($_SESSION['PrevUrl']) && true) {
        $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
      }
      header("Location: " . $MM_redirectLoginSuccess );
    }
    else {
      header("Location: ". $MM_redirectLoginFailed );
    }
    mysql_free_result($LoginRS);
    mysql_close($padelprivee);
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
             "box bg_6 transicion shadow",
             "box bg_6 static",
             "box bg_6 transicion shadow");
  $hrefs = array("groups.php",
             "articles.php",
             "sizes.php",
             "",
             "notifications.php",
             "",
             "customers.php",
             "orders.php",
             "",
             "index.php");
  $texts = array("Promociones",
             "Articulos",
             "Tallas",
             "",
             "Notificaciones",
             "",
             "Clientes",
             "Pedidos",
             "",
             "Login");
  include("includes/navigation.php");
?>
              <section class="indice">
                  <div class="contactar bg_form shadow">
                      <p class="titular">Registrate</p>

                      <form action="<?php echo $loginFormAction; ?>" class="myform" method="post" name="Login"> 
                        <fieldset>
                            <input type="hidden" name="language" value="es"/>
                            <input type="text" name="Usuario" id="name" placeholder="Nombre" required="required"/>
                            <input type="password" name="Password" id="password" placeholder="Contraseña" />
                        </fieldset>
                        <input type='submit' value='Login' class='transicion' name="Login" onclick="Log_In" />
                    </form> 

                  </div>
              </section>
          </div>
        </div>
    </section>
<?php include("includes/footer.php"); ?>
