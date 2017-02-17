<?php
if(!empty($_SESSION['login'])){
  $klantId = $_SESSION['login'][0];
  $klantNaam = $_SESSION['login'][1];
  $klantRolId = $_SESSION['login'][2];
  function isKlant($klantRolId){
    if($klantRolId === 1){
      return true;
    }else{
      return false;
    }
  }
  function isMedewerker($klantRolId){
    if($klantRolId === 4 || $klantRolId == 3 || $klantRolId == 2){
      return true;
    }else{
      return false;
    }
  }
  if(isKlant($klantRolId) || isMedewerker($klantRolId)){
    $stmt = DB::conn()->prepare("SELECT id, naam, adres, postcode, woonplaats, telefoonnummer, email FROM `Persoon` WHERE id=?");
    $stmt->bind_param('i', $klantId);
    $stmt->execute();
    $stmt->bind_result($id, $naam, $adres, $postcode, $woonplaats, $telefoonnummer, $email);
    $stmt->fetch();
    $stmt->close();

    //Haal id op van Order op
    $stmt = DB::conn()->prepare("SELECT id FROM `Order` WHERE klantid=? AND besteld=0");
    $stmt->bind_param("i", $klantId);
    $stmt->execute();

    $stmt->bind_result($order_id);

    $orderIdResult = array();

    while($stmt->fetch()){
      $orderIdResult[] = $order_id;
    }

    $stmt->close();

    $stmt = DB::conn()->prepare("SELECT id FROM `Order` WHERE klantid=? AND besteld=0");
    $stmt->bind_param("i", $klantId);
    $stmt->execute();
    $stmt->bind_result($order_id);
    $stmt->fetch();
    $stmt->close();
    $stmt = DB::conn()->prepare("select count(exemplaarid) from Orderregel where orderid =?;");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();


    $bedrag = $count * 7.50;

    $stmt = DB::conn()->prepare("SELECT id FROM `Order` WHERE klantid=? AND besteld=1");
    $stmt->bind_param("i", $klantId);
    $stmt->execute();
    $stmt->bind_result($afgerond);
    $alAfgerond = array();
    while($stmt->fetch()){
      $alAfgerond[] = $afgerond;
    }
    $stmt->close();
    ?>
    <div class="panel panel-default">
      <div class="panel-body">
    <?php
    if(!empty($orderIdResult)){
      ?>
          <h1>AFREKENEN</h1>
          <?php
          $huidigeTijd = date('H:i');

          if(!empty($_GET['action'])){
            if($_GET['action'] == 'ok'){

              $action = $_GET['action'];
              $ophaalTijd = $_POST['ophaalTijd'];
              $sub = $_POST['subTotaal'];
              $subTotaal = number_format($sub, 2);
              $bezorgKosten = $_POST['bezorgKosten'];
              $tot = $_POST['totaal'];
              $totaal = number_format($tot, 2);

              foreach($orderIdResult as $e){
                $stmt = DB::conn()->prepare("UPDATE `Order` SET ophaaltijd=?, besteld=1, reminder=0 WHERE id=?");
                $stmt->bind_param("si", $ophaalTijd, $e);
                $stmt->execute();
                $stmt->close();

                $stmt = DB::conn()->prepare("SELECT exemplaarid FROM `Orderregel` WHERE orderid=?");
                $stmt->bind_param("i", $e);
                $stmt->execute();
                $stmt->bind_result($exm_id);
                $stmt->fetch();
                $stmt->close();

                $stmt = DB::conn()->prepare("SELECT aantalVerhuur FROM `Exemplaar` WHERE id=?");
                $stmt->bind_param("i", $exm_id);
                $stmt->execute();
                $stmt->bind_result($aantalVerhuur);
                $stmt->fetch();
                $stmt->close();

                $nieuwAantalVerhuur = $aantalVerhuur + 1;
                $stmt = DB::conn()->prepare("UPDATE `Exemplaar` SET aantalVerhuur=? WHERE id=?");
                $stmt->bind_param('ii', $nieuwAantalVerhuur, $exm_id);
                $stmt->execute();
                $stmt->execute();
              }

              ?>
              <h4>Afleverdatum:
                <?php
                $nlAflever = date('d F Y', strtotime($_POST['afleverDatum']));
                echo strtolower(nlDate($nlAflever));
                ?></h4>
              <h4>Aflevertijd: <?php echo $_POST['aflvrTijd'] ?></h4>
              <hr></hr>
              <h4>Huurperiode :
                <?php
                $days = $_POST['huurPeriode'];
                if($days == 1){
                  echo $days . " dag";
                }else{
                  echo $days . " dagen";
                }
                $aantalFilms = $count;

                echo "<br>Aantal Films: ". $aantalFilms;
                echo "<br><br>Subtotaal: €".$subTotaal;
                if($bezorgKosten != "GRATIS"){
                  echo "<br>Bezorgkosten: €". $bezorgKosten;
                }else{
                  echo "<br>Bezorgkosten: ". $bezorgKosten;
                }
                echo "<br><b>Totaal: €".$totaal."</b>"
                ?>
              </h4>
              <hr></hr>
              <h4>Ophaaldatum:
                <?php
                $nlOphaal = date('d F Y', strtotime($_POST['ophaalDatum']));
                echo strtolower(nlDate($nlOphaal));
                ?></h4>
              <h4>Ophaaltijd: <?php echo $_POST['ophaalTijd'] ?></h4>
              <hr></hr>
              <h2><b>U HEEFT €<?php echo $totaal ?> BETAALD</b></h2>
              <a href="/"><button class="btn btn-success bestel">TERUG NAAR HOME</button></a>
              <?php
            }elseif($_GET['action'] == 'afleverDatum'){
              $stmt = DB::conn()->prepare("SELECT ophaaldatum, ophaaltijd FROM `Order` WHERE besteld=1 AND klantid=?");
              $stmt->bind_param('i', $klantId);
              $stmt->execute();
              $stmt->bind_result($OHdata, $OHtijd);
              $data = array();
              while($stmt->fetch()){
                $data[] = $OHdata;
              }
              $stmt->close();
              $nu = date("d-m-Y");

              $vandaag = strtotime("today");
              $date    = strtotime($OHdata);

              $diff = $date - $vandaag;
              $days = floor($diff / (60*60*24) );

              //$difference > 1 = toekomstige datum
              //$difference > 0 = morgen
              if(!empty($date)){

                if($days > 0){
                  ?>
                  <div class="vraag">
                    <h4><i>Op <?php
                    $nlAflever = date('d F Y', strtotime($OHdata));
                    echo strtolower(nlDate($nlAflever));
                    ?>  om <?php echo $OHtijd ?> wordt er bij u een bestelling opgehaald. Wilt u deze bestelling dan laten bezorgen?</i></h4>
                    <form method="post" action="?action=ophaalDatum&afleverDatum=<?php echo $OHdata ?>&afleverTijd=<?php echo $OHtijd?>">
                      <button class="btn btn-primary bestel">JA</button>
                    </form>

                    <button class="btn btn-primary bestel nee">NEE</button>
                  </div>
                  <?php
                }
                ?>
                <form method="post" class="afleverDatum" action="?action=afleverTijd">
                <?php
              }else{
                ?>
                <form method="post" action="?action=afleverTijd">
                <?php
              }
              ?>
                <h2>AFLEVERDATUM</h2>
                <select class="form-control" name="afleverDatum">
                  <?php
                  $ophaalDatum = date('d-m-Y');
                  $ophaalDatum = date('d-m-Y', strtotime($ophaalDatum."+1 day"));
                  for($x=0; $x <= 14; $x++){
                    $date = date('d-m-Y', strtotime($ophaalDatum.'+'.$x. 'days'));
                    $nlDatum = date('d F Y', strtotime($ophaalDatum.'+'.$x. 'days'));
                    $nlDatum = strtolower(nlDate($nlDatum));
                    ?>
                    <option value="<?php echo $date ?>"><?php echo $nlDatum ?></option>
                    <?php
                  }
                  ?>
                </select>
                <input type="submit" class="btn btn-success bestel" value="SELECTEER AFLEVERTIJD">
              </form>
              <?php
            }elseif($_GET['action'] == 'afleverTijd'){
              $afleverDatum = $_POST['afleverDatum'];
              $stmt = DB::conn()->prepare("SELECT `aflevertijd` FROM `Order` WHERE afleverdatum=?");
              $stmt->bind_param('s', $afleverDatum);
              $stmt->execute();
              $bezetteAfleverTijd = array();
              $stmt->bind_result($f);
              while($stmt->fetch()){
                $bezetteAfleverTijd[] = $f;
              }
              $stmt->close();
              $afleverDate = $_POST['afleverDatum'];
              foreach($orderIdResult as $e){
                $stmt = DB::conn()->prepare("UPDATE `Order` SET afleverdatum=? WHERE id=?");
                $stmt->bind_param("si", $afleverDate, $e);
                $stmt->execute();
                $stmt->close();
              }
              ?>
              <h4>Afleverdatum: <?php
              $nlAflever = date('d F Y', strtotime($_POST['afleverDatum']));
              // echo $_POST['afleverDatum']
              echo strtolower(nlDate($nlAflever));
              ?></h4>
              <h2>AFLEVERTIJD</h2>
              <form method="post" action="?action=ophaalDatum">
                <select name="afleverTijd" class="form-control">
                  <?php
                  for($x=0; $x <= 230; $x=$x+10){
                    $afleverTime = strtotime('18:00');
                    $afleverTime = Date('H:i', strtotime("+".$x. " minutes", $afleverTime));
                    if(!in_array($afleverTime, $bezetteAfleverTijd)){
                      ?>
                      <option value="<?php echo $afleverTime ?>"><?php echo $afleverTime ?></option>
                      <?php
                    }
                  }
                  ?>
                </select>
                <input type="submit" class="btn btn-success bestel" value="SELECTEER OPHAALDATUM">
                <input type="hidden" value="<?php echo $_POST['afleverDatum']; ?>" name="afleverDatum">
              </form>
              <?php
            }elseif($_GET['action'] == 'ophaalDatum'){
              if(!empty($_POST)){
                $afleverTijd = $_POST['afleverTijd'];
              }
              if(!empty($_GET['afleverDatum'])){
                $afleverDatum = $_GET['afleverDatum'];
                $afleverTijd = $_GET['afleverTijd'];
                foreach($orderIdResult as $e){
                  $stmt = DB::conn()->prepare("UPDATE `Order` SET afleverdatum=?, aflevertijd=? WHERE id=?");
                  $stmt->bind_param("ssi", $afleverDatum, $afleverTijd, $e);
                  $stmt->execute();
                  $stmt->close();
                }
              }else{
                $afleverDatum = $_POST['afleverDatum'];
                $afleverTijd = $_POST['afleverTijd'];
              }

              foreach($orderIdResult as $e){
                $stmt = DB::conn()->prepare("UPDATE `Order` SET aflevertijd=? WHERE id=?");
                $stmt->bind_param("si", $afleverTijd, $e);
                $stmt->execute();
                $stmt->close();
              }

              ?>
              <h4>Afleverdatum: <?php
                $nlAflever = date('d F Y', strtotime($afleverDatum));
                echo strtolower(nlDate($nlAflever));
              ?></h4>
              <h4>Aflevertijd: <?php echo $afleverTijd ?></h4>
              <h2>OPHAALDATUM</h2>
              <form method="post" action="?action=ophaalTijd">
                <select class="form-control" name="ophaalDatum">
                  <?php
                  if(!empty($_GET['afleverDatum'])){
                    $ophaalDatum = $_GET['afleverDatum'];
                    $ophaalDatum = date('d-m-Y', strtotime($ophaalDatum."+1 day"));
                  }else{
                    $ophaalDatum = date('d-m-Y', strtotime($afleverDatum."+1 day"));
                  }
                  for($x=0; $x < 14; $x++){
                    $date = date('d-m-Y', strtotime($ophaalDatum.'+'.$x. 'days'));
                    $nlDatum = date('d F Y', strtotime($ophaalDatum.'+'.$x. 'days'));
                    $nlDatum = strtolower(nlDate($nlDatum));
                    ?>
                    <option value="<?php echo $date ?>"><?php echo $nlDatum ?></option>
                    <?php
                  }
                  ?>
                </select>
                <input type="submit" class="btn btn-success bestel" value="SELECTEER OPHAALTIJD">
                <input type="hidden" value="<?php echo $afleverTijd?>" name="afleverTijd">
                <input type="hidden" value="<?php echo $afleverDatum?>" name="afleverDatum">
              </form>
              <?php
            }elseif($_GET['action'] == 'ophaalTijd'){
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

              $exemplaren = array();

              foreach($orderIdResult as $e){
                $stmt = DB::conn()->prepare("UPDATE `Order` SET ophaaldatum=? WHERE id=?");
                $stmt->bind_param("si", $ophaalDatum, $e);
                $stmt->execute();
                $stmt->close();

                $stmt = DB::conn()->prepare("SELECT exemplaarid FROM `Orderregel` WHERE orderid=?");
                $stmt->bind_param('i', $e);
                $stmt->execute();
                $stmt->bind_result($exId);
                $stmt->fetch();
                $stmt->close();

                $stmt = DB::conn()->prepare("SELECT id FROM `Exemplaar` WHERE id=? AND reservering=1");
                $stmt->bind_param('i', $exId);
                $stmt->execute();
                $stmt->bind_result($id_exemplaar);
                while($stmt->fetch()){
                  $exemplaren[] = $id_exemplaar;
                }
                $stmt->close();
              }
              // print_r($exemplaren);
              $exs = count($exemplaren);

              $korting = 7.5 * $exs;

              //Bereken het aantal dagen tussen de aflever en ophaal datum
              $dateBegin = $_POST['afleverDatum'];
              $afleverDatumCalc = strtotime($dateBegin);
              $dateEinde = $_POST['ophaalDatum'];
              $ophaalDatumCalc = strtotime($dateEinde);
              $diff = $ophaalDatumCalc - $afleverDatumCalc;
              $days = floor($diff / (60*60*24) ); //Seconden naar dagen omrekenen

              ?>
              <form method="post" action="?action=ok">
                <h4>Afleverdatum: <?php
                  $nlAflever = date('d F Y', strtotime($_POST['afleverDatum']));
                  echo strtolower(nlDate($nlAflever));
                ?></h4>
              <!-- <h4>Afleverdatum: <?php echo $_POST['afleverDatum'] ?></h4> -->
              <h4>Aflevertijd: <?php echo $_POST['afleverTijd'] ?></h4><hr></hr>
              <h4>Huurperiode :
                <?php
                if($days == 1){
                  echo $days . " dag";
                }else{
                  echo $days . " dagen";
                }

                $aantalFilms = $count;

                if($days <=7){
                  if($bedrag >= 50){
                    $bezorg = "GRATIS";
                  }else{
                    $bezorg = 2;
                  }
                  $bedr = (7.5*$aantalFilms)-$korting;
                  $bedrag = number_format($bedr, 2);

                  echo "<br>Aantal Films: ". $aantalFilms;
                  $a = $bedrag;
                  echo "<br><br>Subtotaal: €".$bedrag;
                  if($bezorg == "GRATIS"){
                    echo "<br>Bezorgkosten: GRATIS";
                  }else{

                    echo "<br>Bezorgkosten: €2";
                  }
                  $tot = $bezorg + $bedrag;
                  $totaal = number_format($tot, 2);
                  echo "<br><b>Totaal: €" . $totaal."</b>";

                }elseif($days > 7){

                  $aantalDagen = $days-7;

                  if($aantalDagen == 7){
                    $extra = 6;
                  }else {
                    $extra = $aantalDagen * count($orderIdResult);
                  }

                  $bedrag = ((7.5+$extra)*$aantalFilms)-$korting;


                  if($bedrag >= 50){
                    $bezorg = "GRATIS";
                  }else{
                    $bezorg = 2;
                  }

                  echo "<br>Aantal Films: ". $aantalFilms;

                  echo "<br><br>Subtotaal: €".$bedrag;
                  $a = $bedrag;

                  if($bezorg == "GRATIS"){
                    echo "<br>Bezorgkosten: GRATIS";
                  }else{
                    echo "<br>Bezorgkosten: €2";
                  }
                  $totaal = $bezorg + $bedrag;
                  echo "<br><br><b>Totaal: €" . $totaal."</b>";
                }


                foreach($orderIdResult as $i){
                  // $stmt = DB::conn()->prepare("SELECT bedrag FROM `Order` WHERE id=?");
                  // $stmt->bind_param('i', $i);
                  // $stmt->execute();
                  // $stmt->bind_result($minBedrag);
                  // $stmt->fetch();
                  // $stmt->close();
                  //
                  // if(!empty($minBedrag)){
                  //   $totaal = $totaal - $minBedrag;
                  //   echo "TEST";
                  // }

                  $stmt = DB::conn()->prepare("UPDATE `Order` SET bedrag=? WHERE id=?");
                  $stmt->bind_param('di', $totaal, $i);
                  $stmt->execute();
                  $stmt->close();
                }

                ?>
              </h4>
              <hr></hr>
              <h4>Ophaaldatum:
                <?php
                $nlAflever = date('d F Y', strtotime($_POST['ophaalDatum']));
                echo strtolower(nlDate($nlAflever));
                ?></h4>
              <h2>OPHAALTIJD</h2>
                <select name="ophaalTijd" class="form-control">
                  <?php
                  for($x=0; $x <= 230; $x=$x+10){
                    $ophaalTime = strtotime('18:00');
                    $ophaalTime = Date('H:i', strtotime("+".$x. " minutes", $ophaalTime));
                    if(!in_array($ophaalTime, $bezetteOphaalTijd)){
                      ?>
                      <option value="<?php echo $ophaalTime ?>"><?php echo $ophaalTime ?></option>
                      <?php
                    }
                  }

                  ?>
                </select>
                <input type="hidden" value="<?php echo $aantalFilms ?>" name="aantalFilms">
                <input type="hidden" value="<?php echo $a ?>" name="subTotaal">
                <input type="hidden" value="<?php echo $bezorg ?>" name="bezorgKosten">
                <input type="hidden" value="<?php echo $totaal ?>" name="totaal">
                <input type="hidden" value="<?php echo $_POST['ophaalDatum']; ?>" name="ophaalDatum">
                <input type="hidden" value="<?php echo $_POST['afleverDatum']; ?>" name="afleverDatum">
                <input type="hidden" value="<?php echo $_POST['afleverTijd']; ?>" name="aflvrTijd">
                <input type="hidden" value="<?php echo $days ?>" name="huurPeriode">
                <input type="submit" class="btn btn-success bestel" value="AFRONDEN">
              </form>
              <?php
            }
          }else{

          if(!empty($orderIdResult)){
            ?>
            <br>
            <h4>CONTROLEER UW GEGEVENS</h4>
            <form  method="post" action="?action=afleverDatum">
            <div class="links">
              <ul class="list-group">
                <li class="list-group-item personalInfo"><b>Naam: </b><?php echo $naam?></li>
                <li class="list-group-item personalInfo"><b>Email: </b><?php echo $email ?></li>
                <li class="list-group-item personalInfo"><b>Telefoonnummer: </b><?php echo $telefoonnummer ?></li>
                <li class="list-group-item personalInfo"><b>Adres: </b><?php echo $adres ?></li>
                <li class="list-group-item personalInfo"><b>Postcode: </b><?php echo $postcode ?></li>
                <li class="list-group-item personalInfo"><b>Woonplaats: </b><?php echo $woonplaats ?></li>

                <input type="submit" class="btn btn-success bestel" value="DE GEGEVENS KLOPPEN">
              </form><br><br><br>
                            </ul>
            </div>
            <?php
            if($klantRolId == 4){
              ?>
              <a href="/eigenaar/overzicht?action=edit&code=1">
              <button class="btn btn-success bestel klopt_niet">DE GEGEVENS KLOPPEN <b>NIET</b></button>
              </a>
              <?php
            }elseif($klantRolId == 1){
              ?>
              <a href="/klant/overzicht?action=edit&code=<?php echo $klantId ?>">
              <button class="btn btn-success bestel klopt_niet">DE GEGEVENS KLOPPEN <b>NIET</b></button>
              </a>
              <?php
            }
          }
        }
      }else{
        echo "<div class='warning'><b>U HEEFT GEEN FILMS IN UW WINKELMAND OM TE BESTELLEN</b></div>";
      }
    }elseif($klantRolId == 5){
      echo "<div class='blocked'><b>UW ACCOUNT IS GEBLOKKEERD</b></div>";
    }
  ?>
</div>
</div>
<?php
  DB::conn()->close();
}else{
  header("Refresh:0; url=/login");
}
