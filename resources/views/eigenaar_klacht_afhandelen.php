<?php
if(!empty($_SESSION['login'])){
  $klantId = $_SESSION['login'][0];
  $klantNaam = $_SESSION['login'][1];
  $klantRolId = $_SESSION['login'][2];
  function isEigenaar($klantRolId){
    if($klantRolId === 4){
      return true;
    }else{
      return false;
    }
  }
  if(isEigenaar($klantRolId)){
    $stmt = DB::conn()->prepare("SELECT naam, email FROM `Persoon` WHERE id=?");
    $stmt->bind_param('i', $klantId);
    $stmt->execute();
    $stmt->bind_result($naam, $email);
    $stmt->fetch();
    $stmt->close();
    ?>
    <div class="panel panel-default">
      <div class="panel-body">
        <div class="btn-group admin">
          <a href="/eigenaar/overzicht" class="btn btn-primary admin_menu">OVERZICHT</a>
          <a href="/eigenaar/film_toevoegen" class="btn btn-primary admin_menu">FILM TOEVOEGEN</a>
          <a href="/eigenaar/film_verwijderen" class="btn btn-primary admin_menu">FILM VERWIJDEREN</a>
          <a href="/eigenaar/film_aanpassen" class="btn btn-primary admin_menu">FILM INFO BEHEREN</a>
          <a href="/eigenaar/klant_blokkeren" class="btn btn-primary admin_menu">KLANT BLOKKEREN</a>
          <a href="/eigenaar/klacht_afhandelen" class="btn btn-primary admin_menu actief">KLACHT AFHANDELEN</a>
        </div>
      <h1>KLACHT AFHANDELEN</h1>
      <?php
      if(!empty($_POST)){
        if(!empty($_GET)){
          if($_GET['action'] == 'reply'){
            $stmt = DB::conn()->prepare("UPDATE `Klacht` SET `status`=2 WHERE id=?");
            $stmt->bind_param('i', $_GET['code']);
            $stmt->execute();
            $stmt->close();
            echo "<div class='succes'><b>KLACHT BEANTWOORD</b></div>";
          }
        }
        klachtReactieMail($_POST['naam'], $_POST['email'], $_POST['reactie']);
      }

        if($_GET['location'] == 'in_verwachting'){
          ?>
          <h4><i>ALLE NOG NIET BEANTWOORDE KLACHTEN</i></h4>
          <div class="btn-group admin">
            <a href="?" class="btn btn-primary admin_menu">NIEUW</a>
            <a href="?location=in_verwachting" class="btn btn-primary admin_menu actief">NOG OPEN</a>
            <a href="?location=behandeld" class="btn btn-primary admin_menu">BEHANDELD</a>
          </div>
          <?php

          $stmt = DB::conn()->prepare("SELECT id FROM `Klacht` WHERE status=1");
          $stmt->execute();
          $stmt->bind_result($id);
          while($stmt->fetch()){
            $ids[] = $id;
          }
          $stmt->close();

          if(!empty($ids)){
            foreach($ids as $i){
              $stmt = DB::conn()->prepare("SELECT klantid, onderwerp, bericht, datum, orderid FROM `Klacht` WHERE id=?");
              $stmt->bind_param('i', $i);
              $stmt->execute();
              $stmt->bind_result($klantid, $onderwerp, $bericht, $datum, $ordernummer);
              $stmt->fetch();
              $stmt->close();

              $stmt = DB::conn()->prepare("SELECT naam, email FROM `Persoon` WHERE id=?");
              $stmt->bind_param('i', $klantid);
              $stmt->execute();
              $stmt->bind_result($naam, $email);
              $stmt->fetch();
              $stmt->close();

              ?>
              <div class="panel panel-default">
                <div class="panel-heading"><h4><?php echo $naam ?></h4></div>
                <div class="panel-body">
                    <p>Datum: <?php echo $datum ?></p>
                    <p>Email: <?php echo $email ?></p>
                    <?php
                    if(!empty($ordernummer)){
                      echo "<p>Ordernummer: ".$ordernummer."</p>";
                    }
                    ?>
                    <hr></hr>
                    <p><b>Onderwerp: </b><?php echo $onderwerp ?></p>
                    <p><b>Bericht:</b></p>
                    <p><?php echo $bericht ?></p>
                    <div class="filmDetail_right">
                        <button type="submit" class="btn btn-success bestel" data-toggle="collapse" data-target="#<?php echo $i ?>">BEANTWOORD</button>
                    </div>
                    <br><br>
                    <hr></hr>
                    <div id="<?php echo $i ?>" class="collapse order_collapse">
                      <form method="post" action="?action=reply&code=<?php echo $i?>">
                        <input type="text" class="form-control" placeholder="reactie" name="reactie">
                        <input type="hidden" value="<?php echo $naam ?>" name="naam">
                        <input type="hidden" value="<?php echo $email ?>" name="email">
                      </form>
                    </div>
                </div>
              </div>

              <?php
            }
          }else{
            echo "<div class='warning'><b>ER ZIJN NOG GEEN KLACHTEN INGEDIEND</b></div>";
          }

        }elseif($_GET['location'] == 'behandeld'){
          ?>
          <h4><i>ALLE BEHANDELDE KLACHTEN</i></h4>
          <div class="btn-group admin">
            <a href="?" class="btn btn-primary admin_menu">NIEUW</a>
            <a href="?location=in_verwachting" class="btn btn-primary admin_menu">NOG OPEN</a>
            <a href="?location=behandeld" class="btn btn-primary admin_menu actief">BEHANDELD</a>
          </div>
          <?php
          $stmt = DB::conn()->prepare("SELECT id FROM `Klacht` WHERE status=2");
          $stmt->execute();
          $stmt->bind_result($id);
          while($stmt->fetch()){
            $ids[] = $id;
          }
          $stmt->close();

          if(!empty($ids)){
            foreach($ids as $i){
              $stmt = DB::conn()->prepare("SELECT klantid, onderwerp, bericht, datum, orderid FROM `Klacht` WHERE id=?");
              $stmt->bind_param('i', $i);
              $stmt->execute();
              $stmt->bind_result($klantid, $onderwerp, $bericht, $datum, $orderid);
              $stmt->fetch();
              $stmt->close();

              $stmt = DB::conn()->prepare("SELECT naam, email FROM `Persoon` WHERE id=?");
              $stmt->bind_param('i', $klantid);
              $stmt->execute();
              $stmt->bind_result($naam, $email);
              $stmt->fetch();
              $stmt->close();

              ?>
              <div class="panel panel-default">
                <div class="panel-heading"><h4><?php echo $naam ?></h4></div>
                <div class="panel-body">
                    <p>Datum: <?php echo $datum ?></p>
                    <p>Email: <?php echo $email ?></p>
                    <?php
                    if(!empty($ordernummer)){
                      echo "<p>Ordernummer: ".$ordernummer."</p>";
                    }
                    ?>
                    <hr></hr>
                    <p><b>Onderwerp: </b><?php echo $onderwerp ?></p>
                    <p><b>Bericht:</b></p>
                    <p><?php echo $bericht ?></p>
                </div>
              </div>

              <?php
            }
          }else{
            echo "<div class='warning'><b>ER ZIJN NOG GEEN KLACHTEN BEANTWOORD</b></div>";
          }
        }else{

      $stmt = DB::conn()->prepare("SELECT id FROM `Klacht` WHERE status=1");
      $stmt->execute();
      $stmt->bind_result($id);
      while($stmt->fetch()){
        $ids[] = $id;
      }
      $stmt->close();
      ?>
      <h4><i>KLACHTEN DIE AFGELOPEN WEEK ZIJN BINNENGEKOMEN</i></h4>
      <div class="btn-group admin">
        <a href="?" class="btn btn-primary admin_menu actief">NIEUW</a>
        <a href="?location=in_verwachting" class="btn btn-primary admin_menu">NOG OPEN</a>
        <a href="?location=behandeld" class="btn btn-primary admin_menu">BEHANDELD</a>
      </div>
      <?php
      if(!empty($ids)){
          foreach($ids as $i){
            $stmt = DB::conn()->prepare("SELECT klantid, onderwerp, bericht, datum, orderid FROM `Klacht` WHERE id=?");
            $stmt->bind_param('i', $i);
            $stmt->execute();
            $stmt->bind_result($klantid, $onderwerp, $bericht, $datum, $ordernummer);
            $stmt->fetch();
            $stmt->close();

            $stmt = DB::conn()->prepare("SELECT naam, email FROM `Persoon` WHERE id=?");
            $stmt->bind_param('i', $klantid);
            $stmt->execute();
            $stmt->bind_result($naam, $email);
            $stmt->fetch();
            $stmt->close();

            $vorigeWeek = date('d-m-Y', strtotime("-1 week"));
            $vorigeWeek = strtotime($vorigeWeek);
            $vandaag = date('d-m-Y', strtotime('today'));
            $vandaag = strtotime($vandaag);
            $date = date('d-m-Y', strtotime($datum));
            $date = strtotime($date);

            if($date >= $vorigeWeek){
            ?>
            <div class="panel panel-default">
              <div class="panel-heading"><h4><?php echo $naam ?></h4></div>
              <div class="panel-body">
                  <p>Datum: <?php echo $datum ?></p>
                  <p>Email: <?php echo $email ?></p>
                  <?php
                  if(!empty($ordernummer)){
                    echo "<p>Ordernummer: ".$ordernummer."</p>";
                  }
                  ?>
                  <hr></hr>
                  <p><b>Onderwerp: </b><?php echo $onderwerp ?></p>
                  <p><b>Bericht:</b></p>
                  <p><?php echo $bericht ?></p>
                  <div class="filmDetail_right">
                      <button type="submit" class="btn btn-success bestel" data-toggle="collapse" data-target="#<?php echo $i ?>">BEANTWOORD</button>
                  </div>
                  <br><br>
                  <hr></hr>
                  <div id="<?php echo $i ?>" class="collapse order_collapse">
                    <form method="post" action="?action=reply&code=<?php echo $i?>">
                      <input type="text" class="form-control" placeholder="reactie" name="reactie">
                      <input type="hidden" value="<?php echo $naam ?>" name="naam">
                      <input type="hidden" value="<?php echo $email ?>" name="email">
                    </form>
                  </div>
              </div>
            </div>

            <?php
          }else{
            echo "<div class='warning'><b>ER ZIJN DE AFGELOPEN TWEE WEKEN NOG GEEN KLACHTEN INGEDIEND</b></div>";
          }
        }
      }else{
        echo "<div class='warning'><b>ER ZIJN NOG GEEN KLACHTEN INGEDIEND</b></div>";
      }
      ?>
    </div>
  </div>
    <?php
  }
}
}
