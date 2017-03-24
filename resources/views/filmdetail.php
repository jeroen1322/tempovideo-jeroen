<div class="row">
    <div class="col-md-10 col-md-offset-1 details">
<?php
if(!empty($_SESSION['login'])){
  $klantId = $_SESSION['login'][0];
  $klantNaam = $_SESSION['login'][1];

  $stmt = DB::conn()->prepare("SELECT rolid FROM Persoon WHERE id=?");
  $stmt->bind_param('i', $klantId);
  $stmt->execute();
  $stmt->bind_result($klantRolId);
  $stmt->fetch();
  $stmt->close();
  function isEigenaar($klantRolId){
    if($klantRolId === 4){
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

}

$film = $this->filmNaam;
//Pak de foto van de film
$stmt = DB::conn()->prepare("SELECT id, titel, acteur1, acteur2, acteur3, acteur4, acteur5, omschr, img FROM Film WHERE id=?");
$stmt->bind_param("s", $film);
$stmt->execute();

$stmt->bind_result($id, $titel, $acteur1, $acteur2, $acteur3, $acteur4, $acteur5, $omschr, $img);
while($stmt->fetch()){
  $acteurs[] = $acteur1;
  $acteurs[] = $acteur2;
  $acteurs[] = $acteur3;
  $acteurs[] = $acteur4;
  $acteurs[] = $acteur5;
}
$stmt->close();

$stmt = DB::conn()->prepare("SELECT genreid FROM TussenGenre WHERE filmid=?");
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->bind_result($genreid);
while($stmt->fetch()){
  $genresids[] = $genreid;
}
$stmt->close();

$genres = array();
foreach($genresids as $g){
  $stmt = DB::conn()->prepare("SELECT omschr FROM Genre WHERE genreid=?");
  $stmt->bind_param('i', $g);
  $stmt->execute();
  $stmt->bind_result($genre);
  while($stmt->fetch()){
    $genres[] = $genre;
  }
  $stmt->close();
}

$exm_stmt = DB::conn()->prepare("SELECT id FROM `Exemplaar` WHERE filmid=? AND statusid=1");
$exm_stmt->bind_param("i", $id);
$exm_stmt->execute();
$exm_stmt->bind_result($exemplaar_id);
$beschikbaar = array();
while($exm_stmt->fetch()){
    $beschikbaar[] = $exemplaar_id;
}
$exm_stmt->close();
$count = count($beschikbaar)-1;

$cover = "/cover/" . $img;
$titel = str_replace('_', ' ', $titel);
$titel = strtoupper($titel);
$edit = false;
if(!empty($_GET['action'])){
  if($_GET['action'] == 'add'){
    $_SESSION['cart_item'] = array();
    $_SESSION['cart_item']['id'] = $_GET['code'];
    $product_cart_id = $_SESSION['cart_item']['id'];
    // echo $product_cart_id;


    $klant = $_SESSION['login']['0'];
    $besteld = 0;
    $afhandeling = 0;
    $huidigeWeek = date('d-m-Y');
    $volgendeWeek = date('d-m-Y', strtotime("+7 days"));

    $cart_stmt = DB::conn()->prepare("select count(o.id) from `Order` o where o.klantid =? and ifnull(besteld, false) = false;");
    $cart_stmt->bind_param("i", $klantId);
    $cart_stmt->execute();
    $cart_stmt->bind_result($countorder);
    $cart_stmt->fetch();
    $cart_stmt->close();
    $openBedrag = 0;
    if($countorder == 0){
        $order_id = rand(1, 2100);
        $cart_stmt = DB::conn()->prepare("INSERT INTO `Order` (id, klantid, afhandeling, openbedrag, orderdatum, besteld) VALUES (?, ?, ?, ?, ?, ?)");
        $cart_stmt->bind_param("iiiisi", $order_id, $klant, $afhandeling, $openBedrag, $huidigeWeek, $besteld);
        $cart_stmt->execute();
        $cart_stmt->close();
    }
    $orderid_stmt = DB::conn()->prepare("select id FROM `Order` WHERE klantid =? AND besteld = 0");
    $orderid_stmt->bind_param("i", $klantId);
    $orderid_stmt->execute();
    $orderid_stmt->bind_result($order_id);
    $orderid_stmt->fetch();
    $orderid_stmt->close();

    //VOEG TOE AAN `ORDERREGEL`
    $exm_stmt = DB::conn()->prepare("SELECT id FROM `Exemplaar` WHERE filmid=? AND statusid=1");
    $exm_stmt->bind_param("i", $id);
    $exm_stmt->execute();
    $exm_stmt->bind_result($exemplaar_id);
    $exm_stmt->fetch();
    $exm_stmt->close();

    $exm_stmt = DB::conn()->prepare("UPDATE `Exemplaar` SET statusid=2 WHERE id=?");
    $exm_stmt->bind_param("i", $exemplaar_id);
    $exm_stmt->execute();
    $exm_stmt->close();

    $or_stmt = DB::conn()->prepare("INSERT INTO `Orderregel` (exemplaarid, orderid) VALUES (?, ?)");
    $or_stmt->bind_param("ii", $exemplaar_id, $order_id);
    $or_stmt->execute();
    $or_stmt->close();
    $e = str_replace(' ', '_', $titel);
    header("Refresh:0; url=/film/" . $id);

  }elseif($_GET['action'] == 'edit'){
    $code = $_GET['code'];
    $edit = true;
  }elseif($_GET['action'] == 'reserveer'){
    $prijs = -7.5;
    $code = $_GET['code'];
    $vandaag = date('d-m-Y');

    $stmt = DB::conn()->prepare("INSERT INTO `Reservering` (filmid, persoonid,datum) VALUES(?, ?,?)");
    $stmt->bind_param('iis', $code, $klantId, $vandaag);
    $stmt->execute();
    $stmt->close();

    echo "<div class='succes'><b>UW RESERVERING IS GEPLAATST | U HEEFT €7.50 BETAALD</b></div>";
  }
}



if(!empty($id)){

?>
      <title><?php echo $titel ?></title>
      <a class="btn btn-success terug_button" href="/film/aanbod">
        <li class="fa fa-arrow-left filmaanbod-terug"></li>Filmaanbod
      </a>
        <div class="filmDetails">
          <div class="panel panel-default">
            <div class="panel-body">
              <?php
              if(!empty($_SESSION['login'])){
                if(isGeblokkeerd($klantRolId)){
                  echo "<div class='blocked'><b>UW ACCOUNT IS GEBLOKKEERD</b></div>";
                }
              }
              if(!empty($_GET['action'])){
                  if($_GET['action'] == 'save'){

                    $code = $id;
                    $nieuweTitel = $_POST['titel'];
                    $nieuweTitel = str_replace(' ', '_', $nieuweTitel);
                    $nieuweOmschr = $_POST['omschr'];

                    $nieuweActeur1 = $_POST['acteur1'];
                    $nieuweActeur2 = $_POST['acteur2'];
                    $nieuweActeur3 = $_POST['acteur3'];
                    $nieuweActeur4 = $_POST['acteur4'];
                    $nieuweActeur5 = $_POST['acteur5'];

                    $nieuweGenre = $_POST['genreCheckbox'];

                    $stmt = DB::conn()->prepare("SELECT genreid FROM TussenGenre WHERE filmid=?");
                    $stmt->bind_param('i', $id);
                    $stmt->execute();
                    $stmt->bind_result($filmGen);
                    while($stmt->fetch()){
                      $filmGens[] = $filmGen;
                    }
                    $stmt->close();

                    foreach($nieuweGenre as $n){
                      $stmt = DB::conn()->prepare("INSERT INTO TussenGenre(genreid, filmid) VALUES (?, ?)");
                      $stmt->bind_param('ii', $n, $id);
                      $stmt->execute();
                      $stmt->close();
                    }
                    foreach($filmGens as $f){
                      if(!in_array($f, $nieuweGenre)){
                        $stmt = DB::conn()->prepare("DELETE FROM TussenGenre WHERE genreid=? AND filmid=?");
                        $stmt->bind_param('ii', $f, $id);
                        $stmt->execute();
                        $stmt->close();
                      }
                    }
                    // //Gegevens invoeren in Film tabel
                    $stmt = DB::conn()->prepare("UPDATE `Film` SET `titel`=?, `omschr`=?, `acteur1`=?, `acteur2`=?, `acteur3`=?, `acteur4`=?, `acteur5`=?  WHERE id=?");
                    $stmt->bind_param("ssssssss", $nieuweTitel, $nieuweOmschr, $nieuweActeur1, $nieuweActeur2, $nieuweActeur3, $nieuweActeur4, $nieuweActeur5, $code);
                    $stmt->execute();
                    $stmt->close();
                    $reloadTitel = strtolower($nieuweTitel);
                    header("Refresh:0; url=/film/$id");
                  }
              }
              ?>
              <img src="<?php echo $cover ?>" class="img-responsive cover"/>
              <?php
              if($edit == true && $code == $id){
                ?>
                <form method="post" action="?action=save&code=<?php echo $id ?>">
                <div class="edit_film">
                      <h1><b><input type="text" class="form-control" autocomplete="off" value="<?php echo $titel ?>" name="titel"></b></h1>
                    <h3>Omschrijving</h3>
                    <input type="text" class="form-control" autocomplete="off" value="<?php echo $omschr ?>" name="omschr">
                    <h3>Acteurs</h3>
                    <input type="text" class="form-control" autocomplete="off" value="<?php echo $acteur1 ?>" name="acteur1">
                    <input type="text" class="form-control" autocomplete="off" value="<?php echo $acteur2 ?>" name="acteur2">
                    <input type="text" class="form-control" autocomplete="off" value="<?php echo $acteur3 ?>" name="acteur3">
                    <input type="text" class="form-control" autocomplete="off" value="<?php echo $acteur4 ?>" name="acteur4">
                    <input type="text" class="form-control" autocomplete="off" value="<?php echo $acteur5 ?>" name="acteur5">
                    <h3>Genre</h3>
                      <?php
                      $stmt = DB::conn()->prepare("SELECT genreid FROM `Genre`");
                      $stmt->execute();
                      $stmt->bind_result($genreid);
                      while($stmt->fetch()){
                        $Genres[] = $genreid;
                      }
                      $stmt->close();

                      $stmt = DB::conn()->prepare("SELECT genreid FROM TussenGenre WHERE filmid=?");
                      $stmt->bind_param('i', $id);
                      $stmt->execute();
                      $stmt->bind_result($filmGenre);
                      while($stmt->fetch()){
                        $filmGenres[] = $filmGenre;
                      }
                      $stmt->close();

                      foreach($Genres as $g){
                        $stmt = DB::conn()->prepare("SELECT omschr FROM Genre WHERE genreid=?");
                        $stmt->bind_param('i', $g);
                        $stmt->execute();
                        $stmt->bind_result($genreOmschr);
                        $stmt->fetch();
                        $stmt->close();

                        ?>
                        <label class="col-md-3">
                          <input type='checkbox' class="form-group" name='genreCheckbox[]' value="<?php echo $g ?>" <?php if(in_array($g, $filmGenres)){ echo "checked"; }?> ><?php echo $genreOmschr ?><br>
                        </label>
                        <?php
                      }
                      ?>
                </div>
                <div class="filmDetail_right">
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-floppy-o" aria-hidden="true"></i>
                    </button>
                  </form>
                </div>
                <?php
              }else{
                ?>
                <div class="film_info">
                <?php
                if(!empty($_SESSION['login'])){
                  if(isEigenaar($klantRolId)){
                  ?>
                  <div class="filmDetail_right">
                    <form method="post" action="?action=edit&code=<?php echo $id ?>">
                      <button type="submit" class="btn btn-success">
                          <i class="fa fa-pencil" aria-hidden="true"></i>
                      </button>
                    </form>
                  </div>
                  <?php
                  }
                }
              ?>

              <h1><b><?php echo $titel ?></b></h1>
              <h3>Omschrijving</h3>
              <p><?php echo $omschr ?></p>
              <h3>Acteurs</h3>
                <?php
                $i = 0;
                foreach($acteurs as $a){
                  $i = $i + 1;
                  if(!empty($a)){
                    ?>
                    <p><?php echo $i?> | <?php echo $a ?><p>
                      <?php
                  }
                }
                ?>
              <h3>Genre</h3>
              <?php
              foreach($genres as $gen){
                echo "<p>".$gen."</p>";
              }
              ?>
              <br>
              <?php
              $dis = false;
              if($count >=4){
                ?>
                <p class='green_count'><i>NOG BESCHIKBAAR: <?php echo $count ?> </i></p>
                <?php
              }elseif($count <= 4 && $count > 1){
                ?>
                <p class='orange_count'><i>NOG BESCHIKBAAR: <?php echo $count ?></i></p>
                <?php
              }elseif($count >=1){
                ?>
                <p class='red_count'><i>NOG BESCHIKBAAR: <?php echo $count ?></i></p>
                <?php
              }elseif($count == 0){
                $dis = true;
                ?>
                <p class='red_count'><i>NOG BESCHIKBAAR: <?php echo $count ?></i></p>
                  <?php
              }
              if(!empty($_SESSION['login'])){
                if(isGeblokkeerd($klantRolId)){
                  $dis = true;
                }
              }
              ?>
              <h3><b>Prijs</b></h3>
              <p><b>€7,50</b></p>
                <?php
                if($klantRolId != 5){
                  if($dis){
                    ?>
                    <form method="post" action="?action=reserveer&code=<?php echo $id ?>">
                    <!-- <input type="submit" class="btn btn-success bestel" value="Bestel" disabled> -->

                    <input type="submit" class="btn btn-success bestel" value="Reserveer (€7.50)">

                    <?php
                  }elseif(empty($_SESSION['login'])){
                    ?>
                    <input type="submit" class="btn btn-success bestel" value="Bestel" disabled><br><br><br>
                    <h5><b>U moet <a href="/login">ingelogd</a> zijn om te kunnen bestellen</b></h5>
                    <?php
                  }else{
                    ?>
                    <form method="post" action="?action=add&code=<?php echo $id ?>">
                    <input type="submit" class="btn btn-success bestel" value="Bestel">
                  </form>
                    <?php
                  }
                }

                ?>
              </form>
              </div>
              <?php
              }
              ?>

            </div>
          </div>
      </div>
    </div>
</div>
<?php

}else{
  echo "404 - FILM NIET GEVONDEN";
}
DB::conn()->close();
