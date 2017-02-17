<?php
if(!empty($_SESSION['login'])){
  $klantId = $_SESSION['login'][0];
  $klantNaam = $_SESSION['login'][1];
  $klantRolId = $_SESSION['login'][2];
  function isKlant($klantRolId){
    if($klantRolId === 1 || $klantRolId === 5){
      return true;
    }else{
      return false;
    }
  }
  function isGeblokkeerd($klantRolId){
    if($klantRolId === 5){
      return true;
    }else{
      return false;
    }
  }
  if(isKlant($klantRolId)){
    $stmt = DB::conn()->prepare("SELECT id, naam, adres, postcode, woonplaats, telefoonnummer, email FROM `Persoon` WHERE id=?");
    $stmt->bind_param('i', $klantId);
    $stmt->execute();
    $stmt->bind_result($id, $naam, $adres, $postcode, $woonplaats, $telefoonnummer, $email);
    $stmt->fetch();
    $stmt->close();

    if(!empty($_GET['action'])){
      $code = $_GET['code'];
      $action = $_GET['action'];
      $edit = true;
    }else{
      $edit = false;
    }

  ?>
  <div class="panel panel-default">
    <div class="panel-body">
      <div class="btn-group admin">
        <a href="/klant/overzicht" class="btn btn-primary admin_menu klant_menu actief">OVERZICHT</a>
        <a href="/klant/klacht_indienen" class="btn btn-primary admin_menu klant_menu">KLACHT INDIENEN</a>
      </div>
      <?php
      if(isGeblokkeerd($klantRolId)){
        echo "<div class='blocked'><b>UW ACCOUNT IS GEBLOKKEERD</b></div>";
      }
      ?>
      <h1>OVERZICHT</h1>
      <?php
      if(!empty($_GET)){
        if($_GET['action'] == 'ok_verleng'){
          ?>
          <div class='succes'><b>UW ORDER IS MET SUCCES VERLENGD MET
            <?php
              echo $_POST['aantalDagen'];
              if($_POST['aantalDagen'] == 1){
                echo ' DAG';
              }else{
                echo ' DAGEN';
              }
            ?>
            </b>
            <div class="terug_order">
              <a href="/klant/overzicht"><button class="btn bestel">TERUG NAAR UW OVERZICHT</button></a>
            </div>
          </div>
          <?php
        }
      }
      ?>
      <h3><b><?php echo $naam ?></b></h3>
      <hr></hr>
      <div class="left">
        <?php
        if(!empty($_GET)){
          if($edit == true && $code == $id && $action == 'edit'){
            ?>
            <h4><b>INFORMATIE</b></h4>
            <div class="info">
              <form method="post" action=?action=save&code=<?php echo $id ?>>

              <h5>ALGEMENE INFORMATIE</h5>
              <ul class="list-group">
                <li class="list-group-item"><b>Klantnummer: </b><?php echo $id ?></li>
                <li class="list-group-item"><b>Naam: </b><input type="text" class="form-control" name="naam" value="<?php echo $naam ?>" required></li>
              </ul>

              <h5>CONTACT INFORMATIE</h5>
              <ul class="list-group">
                <li class="list-group-item"><b>Email: </b><input type="email" class="form-control" name="email" value="<?php echo $email ?>" required></li>
                <li class="list-group-item"><b>Telefoonnummer: </b><input type="text" class="form-control" name="telefoonnummer" value="<?php echo $telefoonnummer ?>" required></li>
              </ul>

              <h5>ADRES INFORMATIE</h5>
              <ul class="list-group">
                <li class="list-group-item"><b>Adres: </b><input type="text" class="form-control" name="adres" value="<?php echo $adres ?>" required></li>
                <li class="list-group-item"><b>Postcode: </b><input type="text" class="form-control" name="postcode" value="<?php echo $postcode ?>" required></li>
                <li class="list-group-item"><b>Woonplaats: </b><input type="text" class="form-control" name="woonplaats" value="<?php echo $woonplaats ?>" required></li>
              </ul>
              <form method="post" action="?action=edit&code=<?php echo $id ?>">
                <button type="submit" class="btn btn-success bestel"><li class="fa fa-floppy-o"></li> OPSLAAN</button>
              </form>
            </div>
            <?php
          }elseif($action == 'save'){
            $stmt = DB::conn()->prepare("UPDATE `Persoon` SET `naam`=?, `email`=?, `telefoonnummer`=?, `adres`=?, `postcode`=?, `woonplaats`=? WHERE id=?");
            $stmt->bind_param("ssssssi", $_POST['naam'], $_POST['email'], $_POST['telefoonnummer'], $_POST['adres'], $_POST['postcode'], $_POST['woonplaats'], $code);
            $stmt->execute();
            $stmt->close();
            header("Refresh:0; url=/klant/overzicht");
          }elseif($action == 'verleng'){
            ?>
            <h3>VERLENG HUUR VAN ORDER #<?php echo $code ?></h3>
            <?php
            $stmt = DB::conn()->prepare("SELECT ophaaldatum, ophaaltijd FROM `Order` WHERE id=?");
            $stmt->bind_param('i', $code);
            $stmt->execute();
            $stmt->bind_result($ophaalD, $ophaalT);
            $stmt->fetch();
            $stmt->close();
            ?>
            <hr></hr>
            <h4>ORIGINELE OPHAAL DATA</h4>
            <h4><b>OPHAALDATUM:</b> <?php echo $ophaalD ?></h4>
            <h4><b>OPHAALTIJD:</b> <?php echo $ophaalT ?></h4>
            <hr></hr>
            <h4>NIEUWE OPHAAL DATA</h4>
            <h4><b>OPHAALDATUM:</b></h4>
            <form method="post" action="?action=ophaalTijd">
              <select class="form-control" name="ophaalDatum">
                <?php
                $ophaalDatum = date($ophaalD);
                $ophaalDatum = date('d-m-Y', strtotime($ophaalDatum."+1 day"));
                for($x=0; $x <= 6; $x++){
                  $date = date('d-m-Y', strtotime($ophaalDatum.'+'.$x. 'days'));
                  ?>
                  <option value="<?php echo $date ?>"><?php echo $date ?></option>
                  <?php
                }
                ?>
              </select>
              <input type="submit" class="btn btn-success bestel verlengbtn" value="SELECTEER NIEUWE OPHAALTIJD">
              <input type="hidden" name="id" value="<?php echo $code ?>">
            </form>
            <?php
          }elseif($action == 'ophaalTijd'){
            $order_id = $_POST['id'];
            $ophaalDatum = $_POST['ophaalDatum'];
            ?>
            <h3>VERLENG HUUR VAN ORDER #<?php echo $order_id ?></h3>
            <hr></hr>
            <?php
            $stmt = DB::conn()->prepare("SELECT ophaaldatum, ophaaltijd FROM `Order` WHERE id=?");
            $stmt->bind_param('i', $order_id);
            $stmt->execute();
            $stmt->bind_result($ophaalD, $ophaalT);
            $stmt->fetch();
            $stmt->close();

            $stmt = DB::conn()->prepare("SELECT exemplaarid FROM `Orderregel` WHERE orderid=?");
            $stmt->bind_param('i', $order_id);
            $stmt->execute();
            $stmt->bind_result($exemplaar);
            $exmplaars = array();
            while($stmt->fetch()){
              $exemplaars[] = $exemplaar;
            }
            $stmt->close();
            $aantalFilmsInOrder = count($exemplaars);

            $origineleOphaalDatum = strtotime($ophaalD);
            $nieuweOphaalDatum = strtotime($ophaalDatum);

            //Bereken het aantal dagen tussen de aflever en ophaal datum
            $diff = $nieuweOphaalDatum - $origineleOphaalDatum;
            $days = floor($diff / (60*60*24) ); //Seconden naar dagen omrekenen

            if($days == 7){
              $extraKosten = 6 * $aantalFilmsInOrder;
            }else{
              $extraKosten = $days * $aantalFilmsInOrder;
            }
            ?>
            <h4>ORIGINELE OPHAAL DATA</h4>
            <h4><b>OPHAALDATUM:</b> <?php echo $ophaalD ?></h4>
            <h4><b>OPHAALTIJD:</b> <?php echo $ophaalT ?></h4>
            <hr></hr>
            <h4><b>HUUR VERLENGEN MET: </b>
              <?php
              echo $days;
              if($days == 1){
                echo ' dag';
              }else{
                echo ' dagen';
              }
              ?>
            </h4>
            <h4><b>Extra kosten:</b> €<?php echo $extraKosten ?></h4>
            <hr></hr>
            <h4>NIEUWE OPHAAL DATA</h4>
            <h4><b>OPHAALDATUM:</b> <?php echo $ophaalDatum ?></h4>
            <h4><b>OPHAALTIJD:</b></h4>
            <?php
            $ophaalDatum = $_POST['ophaalDatum'];
            $stmt = DB::conn()->prepare("SELECT `ophaaltijd` FROM `Order` WHERE ophaaldatum=?");
            $stmt->bind_param('s', $ophaalDatum);
            $stmt->execute();
            $bezetteOphaalTijd = array();
            $stmt->bind_result($f);
            while($stmt->fetch()){
              $bezetteOphaalTijd[] = $f;
            }
            $stmt->close();

            ?>
            <form method="post" action="?action=ok_verleng">
              <select name="ophaalTijd" class="form-control">
                <?php
                for($x=0; $x <= 120; $x=$x+10){
                  $ophaalTime = strtotime('14:00');
                  $ophaalTime = Date('H:i', strtotime("+".$x. " minutes", $ophaalTime));
                  if(!in_array($ophaalTime, $bezetteOphaalTijd)){
                    ?>
                    <option value="<?php echo $ophaalTime ?>"><?php echo $ophaalTime ?></option>
                    <?php
                  }
                }

                ?>
              </select>
              <input type="submit" class="btn btn-success bestel verlengbtn" value="VERLENG">
              <input type="hidden" name="ophaalDatum" value="<?php echo $ophaalDatum?>">
              <input type="hidden" name="id" value="<?php echo $order_id?>">
              <input type="hidden" name="aantalDagen" value="<?php echo $days ?>">
              <input type="hidden" name="extraKosten" value="<?php echo $extraKosten?>">
            </form>
            <?php
          }elseif($action == 'ok_verleng'){
            $order_id = $_POST['id'];
            $ophaalDatum = $_POST['ophaalDatum'];
            $ophaalTijd = $_POST['ophaalTijd'];
            $days = $_POST['aantalDagen'];
            $extraKosten = $_POST['extraKosten'];
            ?>

            <h3>VERLENG HUUR VAN ORDER #<?php echo $order_id ?></h3>
            <hr></hr>
            <?php
            $stmt = DB::conn()->prepare("SELECT ophaaldatum, ophaaltijd FROM `Order` WHERE id=?");
            $stmt->bind_param('i', $order_id);
            $stmt->execute();
            $stmt->bind_result($ophaalD, $ophaalT);
            $stmt->fetch();
            $stmt->close();
            ?>
            <h4>ORIGINELE OPHAAL DATA</h4>
            <h4><b>OPHAALDATUM:</b> <?php echo $ophaalD ?></h4>
            <h4><b>OPHAALTIJD:</b> <?php echo $ophaalT ?></h4>
            <hr></hr>
            <h4><b>HUUR VERLENGEN MET: </b>
              <?php
              echo $days;
              if($days == 1){
                echo ' dag';
              }else{
                echo ' dagen';
              }
              ?>
            </h4>
            <h4><b>Extra kosten:</b> €<?php echo $extraKosten ?></h4>
            <hr></hr>
            <h4><b>
            <h4>NIEUWE OPHAAL DATA</h4>
            <h4><b>OPHAALDATUM:</b> <?php echo $ophaalDatum ?></h4>
            <h4><b>OPHAALTIJD:</b> <?php echo $ophaalTijd?></h4>
            <?php
            $stmt = DB::conn()->prepare("UPDATE `Order` SET ophaaldatum=?, ophaaltijd=?, openbedrag=? WHERE id=?");
            $stmt->bind_param('ssdi', $ophaalDatum, $ophaalTijd, $extraKosten, $order_id);
            $stmt->execute();
            $stmt->close();
          }
        }else{
        ?>
        <h4><b>INFORMATIE</b></h4>
        <div class="info">
          <h5>ALGEMENE INFORMATIE</h5>
          <ul class="list-group">
            <li class="list-group-item"><b>Klantnummer: </b><?php echo $id ?></li>
            <li class="list-group-item"><b>Naam: </b><?php echo $naam?></li>
            <li class="list-group-item"><b></b></li>
          </ul>

          <h5>CONTACT INFORMATIE</h5>
          <ul class="list-group">
            <li class="list-group-item"><b>Email: </b><?php echo $email ?></li>
            <li class="list-group-item"><b>Telefoonnummer: </b><?php echo $telefoonnummer ?></li>
          </ul>

          <h5>ADRES INFORMATIE</h5>
          <ul class="list-group">
            <li class="list-group-item"><b>Adres: </b><?php echo $adres ?></li>
            <li class="list-group-item"><b>Postcode: </b><?php echo $postcode ?></li>
            <li class="list-group-item"><b>Woonplaats: </b><?php echo $woonplaats ?></li>
          </ul>
          <form method="post" action="?action=edit&code=<?php echo $id ?>">
            <?php
            if(isGeblokkeerd($klantRolId)){
              ?>
              <input type="submit" class="btn btn-success bestel" value="PAS INFORMATIE AAN" disabled>
              <?php
            }else{
              ?>
              <input type="submit" class="btn btn-success bestel" value="PAS INFORMATIE AAN">
              <?php
            }
            ?>
          </form>
        </div>
        <?php
      }

      ?>
    </div>
    <div class="klant_right">
      <h4><b>ORDERS</b></h4>
      <?php
      //Haal id op van Order op
      $stmt = DB::conn()->prepare("SELECT id FROM `Order` WHERE klantid=? AND besteld=1");
      $stmt->bind_param("i", $id);
      $stmt->execute();

      $stmt->bind_result($order_id);

      $orderIdResult = array();

      while($stmt->fetch()){
        $orderIdResult[] = $order_id;
      }

      $stmt->close();
      $u_orderIdResult = array_unique($orderIdResult);
      if(!empty($u_orderIdResult)){

        foreach($u_orderIdResult as $i){

          $stmt = DB::conn()->prepare("SELECT afleverdatum, ophaaldatum, orderdatum, openbedrag FROM `Order` WHERE id=?");
          $stmt->bind_param('i', $i);
          $stmt->execute();
          $stmt->bind_result($afleverdatum, $ophaaldatum, $orderdatum, $openBedrag);
          $stmt->fetch();
          $stmt->close();

          $stmt = DB::conn()->prepare("SELECT exemplaarid FROM Orderregel WHERE orderid=?");
          $stmt->bind_param('i', $i);
          $stmt->execute();
          $stmt->bind_result($exmid);
          $exemplaren = array();
          while($stmt->fetch()){
            $exemplaren[] =$exmid;
          }
          $stmt->close();

          $films = array();
          foreach($exemplaren as $e){
            $stmt = DB::conn()->prepare("SELECT filmid FROM Exemplaar WHERE id=?");
            $stmt->bind_param('i', $e);
            $stmt->execute();
            $stmt->bind_result($filmid);
            while($stmt->fetch()){
              $films[] = $filmid;
            }
            $stmt->close();
          }

          ?>
          <div class="order">
            <p class="order_info" data-toggle="collapse" data-target="#<?php echo $i ?>"><?php echo $orderdatum ?> | #<?php echo $i ?><i class="fa fa-arrow-down neer" aria-hidden="true"></i></p>
            <div id="<?php echo $i ?>" class="collapse order_collapse">
              <h3>FILMS</h3>
              <table class="table">
                <thead>
                  <th><b>FOTO</b></th>
                  <th><b>TITEL</b></th>
                </thead>
                <tbody>
              <?php
              foreach($films as $f){
                $stmt = DB::conn()->prepare("SELECT titel, img FROM Film WHERE id=?");
                $stmt->bind_param('i', $f);
                $stmt->execute();
                $stmt->bind_result($titel, $img);
                $stmt->fetch();
                $stmt->close();

                $cover = "/cover/" . $img;
                $URL = "/film/" . $f;
                $titel = strtoupper($titel);
                $titel = str_replace('_', ' ', $titel);

                ?>
                <tr>
                  <td><a href="<?php echo $url ?>"><img src="<?php echo $cover ?>" class="winkelmand_img"></a></td>
                  <td><?php echo $titel ?></td>
                </tr>
                <?php
              }
              ?>
              </tbody>
            </table>
            <h4><b>AFLEVERDATUM:</b> <?php echo $afleverdatum ?></h4>
            <h4><b>OPHAALDATUM:</b> <?php echo $ophaaldatum ?></h4>

            <?php
            $ophaalDatumCheck = strtotime($ophaaldatum);
            $vandaag = strtotime("today");
            if($ophaalDatumCheck > $vandaag && $openBedrag == 0){
              ?>
              <form method="post" action="?action=verleng&code=<?php echo $i ?>">
                <input type="submit" class="btn bestel" value="VERLENG HUUR">
              </form>
              <?php
            }

            ?>
            </div>
          </div>
          <?php
        }
      }else{
        echo "<div class='warning'><b>U HEEFT NOG GEEN FILMS BESTELD</b></div>";
      }
      ?>
    </div>
  </div>
</div>

  <?php
  }
DB::conn()->close();
}else{
  header("Refresh:0; url=/login");
}
