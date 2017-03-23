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
    $afrekenen = new Afrekenen;

    //Haal eerst alle klant gegevens op
    $klantInfo = $afrekenen->getKlantInfo($klantId);
    $id = $klantInfo['id'];
    $naam = $klantInfo['naam'];
    $adres = $klantInfo['adres'];
    $postcode = $klantInfo['postcode'];
    $woonplaats = $klantInfo['woonplaats'];
    $telefoonnummer = $klantInfo['telefoonnummer'];
    $email = $klantInfo['email'];

    //Haal het id van alle nog openstaande orders van de klant op
    $returns = $afrekenen->getKlantOrders($klantId);
    $order_id = $returns['order_id'];
    $orderIdResult = $returns['orderIdResult'];

    //Bereken het aantal openstaande order van de klant
    $count = $afrekenen->countOrders($order_id);

    $bedrag = $count * 7.50;
    ?>
    <div class="panel panel-default">
      <div class="panel-body">
    <?php
    if(!empty($order_id)){
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
            }elseif($_GET['action'] == 'korting'){
              $krt = new Korting;
              //TODO: Change to !empty
              if(empty($krt->checkVoorKortingCode($klantId))){
                ?>
                <div class="kortingCode">
                  <h4>Wilt u een kortingscode invoeren?</h4>
                  <form method="post" action="?action=afleverDatum">
                    <input type="text" placeholder="KORTINGS CODE" class="form-control" name="kortingsCode" required>
                    <button class="btn btn-primary bestel">JA</button>
                  </form>
                  <a href="/winkelmand/afrekenen?action=afleverDatum"><button class="btn btn-primary bestel nee_korting">NEE</button></a>
                </div>
                <?php
              }
            }elseif($_GET['action'] == 'afleverDatum'){
              if(!empty($_POST['kortingsCode'])){
                ?>
                <input type="hidden" name="korting" value="<?php $_POST['kortingsCode']?>">
                <?php
              }
              //Haal de ophaal data (datum en tijd) op van de order
              //Dit wordt gedaan om te kijken of er
              $data = $afrekenen->getOphaalData($klantId);
              if(!empty($data)){
                $OHdata = $data['OHdata'];
                $OHtijd = $data['OHtijd'];
              }

              $nu = date("d-m-Y");
              $vandaag = strtotime("today");
              $date    = strtotime($OHdata);
              $datum = date('d-m-Y', $date);
              $diff = $date - $vandaag;
              $days = floor($diff / (60*60*24) );

              //$diff > 1 = toekomstige datum
              //$difference > 0 = morgen
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
              <div class='afleverDatum'>
              <h2>AFLEVERDATUM</h2>
              <form method="post" action="?action=afleverTijd">
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
              </div>
              <?php
            }elseif($_GET['action'] == 'afleverTijd'){
              //Pak het afleverdatum dat meegegeven is uit het formulier afleverDatum
              $afleverDatum = $_POST['afleverDatum'];
              //Maak een array met alle af bezette aflevertijden op de datum van de afleverDatum
              $bezetteAfleverTijd = $afrekenen->controlleerBezetteAfleverTijden($afleverDatum);

              foreach($orderIdResult as $e){
                $afrekenen->updateAfleverdatum($afleverDatum, $e);
              }
              ?>
              <h4>Afleverdatum: <?php
              $nlAflever = date('d F Y', strtotime($_POST['afleverDatum']));
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
              //Als er in het begin op het dialoogvenster 'Ja' is geselecteerd,
              //Springt de code direct hier naartoe en geeft de afleverTijd en afleverDatum mee via GET
              if(!empty($_POST)){
                $afleverTijd = $_POST['afleverTijd'];
              }
              if(!empty($_GET['afleverDatum'])){
                $afleverDatum = $_GET['afleverDatum'];
                $afleverTijd = $_GET['afleverTijd'];
                foreach($orderIdResult as $e){
                  $afrekenen->updateAfleverDatumTijd($afleverDatum, $afleverTijd, $e);
                }
              }else{
                $afleverDatum = $_POST['afleverDatum'];
                $afleverTijd = $_POST['afleverTijd'];
              }

              foreach($orderIdResult as $e){
                $afrekenen->updateAfleverTijd($afleverTijd, $e);
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
              //Krijg de ophaalDatum van het vorige formulier via POST
              $ophaalDatum = $_POST['ophaalDatum'];
              //Maak een array met alle alle tijden die al bezet zijn op de datum van de geselecteerde ophaalDatum
              $bezetteOphaalTijd = $afrekenen->controlleerBezetteOphaalTijden($ophaalDatum);

              foreach($orderIdResult as $e){
                $afrekenen->updateOphaalDatum($ophaalDatum, $e);

                $exId = $afrekenen->getExemplaarId($e);

                $exemplaren = $afrekenen->getGereserveerdeExemplaren($exId);
              }

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
                  $afrekenen->updateOrderTotaal($totaal, $i);
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
