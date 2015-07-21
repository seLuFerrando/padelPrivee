    <section class="clearfix">
        <div class="container">
          <div id="menu_rwd" class="col25">
            <nav class="nav">
            <ul>
              <?php for ($i=0;$i<count($classes);$i++){ ?>
                <li><a class="<?php echo $classes[$i]; ?>" href="<?php echo $hrefs[$i]; ?>" >
                  <span class="center"><?php echo $texts[$i]; ?></span></a></li>
              <?php } ?>
            </ul>
            </nav>
          </div>
          <div class="col75">
              <nav class="nav">
                  <ul>
                      <li><a href="<?php echo $hrefs[9]; ?>" class="<?php echo $classes[9]; ?>" title="<?php echo $texts[0]; ?>" >
                        <span class="center"><?php echo $texts[9]; ?></span></a></li> 
                  </ul>
              </nav>
