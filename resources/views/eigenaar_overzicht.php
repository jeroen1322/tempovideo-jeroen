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
          <a href="/eigenaar/overzicht" class="btn btn-primary actief admin_menu">OVERZICHT</a>
          <a href="/eigenaar/film_toevoegen" class="btn btn-primary admin_menu">FILM TOEVOEGEN</a>
          <a href="/eigenaar/film_verwijderen" class="btn btn-primary admin_menu">FILM VERWIJDEREN</a>
          <a href="/eigenaar/film_aanpassen" class="btn btn-primary admin_menu">FILM INFO BEHEREN</a>
          <a href="/eigenaar/klant_blokkeren" class="btn btn-primary admin_menu">KLANT BLOKKEREN</a>
        </div>
        <h1>EIGENAAR OVERZICHT</h1>
        <div class="left">
          <?php
          if(!empty($_GET)){
            if($edit == true && $code == $id && $action == 'edit'){
              ?>
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
              header("Refresh:0; url=/eigenaar/overzicht");
            }
          }else{
          ?>
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
              <input type="submit" class="btn btn-success bestel" value="PAS INFORMATIE AAN">
            </form>
          </div>
          <?php
        }
            ?>
            </div>
            <div class="klant_right">
            <?php
              $vandaag = date('d-m-Y');
              $vierWekenGeleden = date('d-m-Y', strtotime("-4 weeks"));

              $stmt = DB::conn()->prepare("SELECT id FROM `Order`");
              $stmt->execute();
              $stmt->bind_result($o);
              $stmt->fetch();
              $stmt->close();
              if(!empty($o)){
                $stmt = DB::conn()->prepare("SELECT id FROM `Order`");
                $stmt->execute();
                $stmt->bind_result($id);
                while($stmt->fetch()){
                  $ids[] = $id;
                }
                $stmt->close();
                $films = array();
                foreach($ids as $i){
                  $stmt = DB::conn()->prepare("SELECT orderdatum FROM `Order` WHERE id=?");
                  $stmt->bind_param('i', $i);
                  $stmt->execute();
                  $stmt->bind_result($orderdatum);
                  $stmt->fetch();
                  $stmt->close();

                  $nieuweVierWekenGeleden = strtotime($vierWekenGeleden);
                  $nieuweOrderDatum = strtotime($orderdatum);
                  $nieuweVandaag = strtotime($vandaag);
                  if($nieuweVierWekenGeleden <= $nieuweOrderDatum && $nieuweOrderDatum <= $nieuweVandaag){

                    $stmt = DB::conn()->prepare("SELECT id FROM `Order` WHERE id=? AND besteld=1");
                    $stmt->bind_param('i', $i);
                    $stmt->execute();
                    $stmt->bind_result($O_id);
                    $stmt->fetch();
                    $stmt->close();
                    $exms = array();
                    $stmt = DB::conn()->prepare("SELECT exemplaarid FROM `Orderregel` WHERE orderid=?");
                    $stmt->bind_param('i', $O_id);
                    $stmt->execute();
                    $stmt->bind_result($exm_id);
                    while($stmt->fetch()){
                      $exms[] = $exm_id;
                    }
                    $stmt->close();

                    foreach($exms as $e){
                      $stmt = DB::conn()->prepare("SELECT filmid FROM `Exemplaar` WHERE id=?");
                      $stmt->bind_param('i', $e);
                      $stmt->execute();
                      $stmt->bind_result($f_id);
                      while($stmt->fetch()){
                        $films[] = $f_id;
                      }
                      $stmt->close();
                    }
                  }
                }
              }

              if(!empty($films)){
                ?>
                <h4>MEEST GEHUURDE FILMS AFGELOPEN 4 WEKEN</h4>
                <table class="table">
                  <thead>
                    <tr>
                      <th>Foto</th>
                      <th>Titel</th>
                      <th>Aantal keer verhuurd afgelopen 4 weken</th>
                    </tr>
                  </thead>
                  <tbody>
                <?php
                $aantalVerhuurdPerFilm = array_count_values($films);

                arsort($aantalVerhuurdPerFilm);

                foreach($aantalVerhuurdPerFilm as $a => $v){
                  $stmt = DB::conn()->prepare("SELECT titel, img FROM `Film` WHERE id=?");
                  $stmt->bind_param('i', $a);
                  $stmt->execute();
                  $stmt->bind_result($titel, $img);
                  $stmt->fetch();
                  $stmt->close();
                  $img = '/cover/'.$img;
                  $url = '/film/'.$a;
                  $titel = str_replace('_', ' ', $titel);
                  $titel = strtoupper($titel);
                  ?>
                  <tr>
                    <td><a href="<?php echo $url ?>"><img src="<?php echo $img ?>" class="winkelmand_img"></a></td>
                    <td><?php echo $titel ?></td>
                    <td><?php echo $v ?></td>
                  </tr>
                  <?php
                }
              }else{
                ?>
                <div class="warning"><b>ER ZIJN DE AFGELOPEN 4 WEKEN GEEN FILMS GEHUURD</b></div>
                <?php
              }
            ?>
        </div>
      </div>
    </div>
  <?php
  DB::conn()->close();
  }else{
    echo "404";
  }
}else{
  header("Refresh:0; url=/login");
}
