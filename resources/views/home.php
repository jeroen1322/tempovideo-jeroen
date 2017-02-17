<?php
$stmt = DB::conn()->PREPARE("SELECT id FROM Film ORDER BY id DESC LIMIT 6");
$stmt->execute();
$stmt->bind_result($id);
$film_id = array();
while($stmt->fetch()){
  $film_id[] = $id;
}
$stmt->close();

$stmt = DB::conn()->prepare("SELECT id FROM Exemplaar");
$stmt->execute();
$stmt->bind_result($exms);
while($stmt->fetch()){
  $exemplaren[] = $exms;
}
$stmt->close();

foreach($exemplaren as $e){
  $stmt = DB::conn()->prepare("SELECT aantalVerhuur, filmid FROM Exemplaar WHERE id=?");
  $stmt->bind_param('i', $e);
  $stmt->execute();
  $stmt->bind_result($aantalVerhuur, $filmid);
  $stmt->fetch();
  $stmt->close();

  if($aantalVerhuur >= 30){
    $stmt = DB::conn()->prepare("SELECT id FROM Exemplaar WHERe filmid=? AND statusid=1 AND aantalVerhuur<30");
    $stmt->bind_param('i', $filmid);
    $stmt->execute();
    $stmt->bind_result($beschikbaarExemplaar);
    $stmt->fetch();
    $stmt->close();

    $stmt = DB::conn()->prepare("UPDATE `Orderregel` SET exemplaarid=? WHERE exemplaarid=?");
    $stmt->bind_param('ii', $beschikbaarExemplaar, $e);
    $stmt->execute();
    $stmt->close();

    $stmt = DB::conn()->prepare("DELETE FROM `Exemplaar` WHERE id=?");
    $stmt->bind_param("i", $e);
    $stmt->execute();
    $stmt->close();

  }

}

$vandaag = strtotime("today");

$stmt = DB::conn()->PREPARE("SELECT id FROM `Order` WHERE besteld=1 AND reminder=0");
$stmt->execute();
$stmt->bind_result($orderid);
$orderids = array();
while($stmt->fetch()){
  $orderids[] = $orderid;
}
$stmt->close();

foreach($orderids as $d){
  $stmt = DB::conn()->PREPARE("SELECT id, klantid, afleverdatum, ophaaldatum, ophaaltijd FROM `Order` WHERE id=?");
  $stmt->bind_param('i', $d);
  $stmt->execute();
  $stmt->bind_result($order, $klant, $afleverdatum, $ophaaldatum, $ophaaltijd);
  $stmt->fetch();
  $stmt->close();

  $ophaalDatumTime = strtotime($ophaaldatum);
  $diff = $ophaalDatumTime - $vandaag;
  $days = floor($diff / (60*60*24) ); //Seconden naar dagen omrekenen

  if($days <= 1){
    $stmt = DB::conn()->PREPARE("SELECT exemplaarid FROM `Orderregel` WHERE orderid=?");
    $stmt->bind_param('i', $d);
    $stmt->execute();
    $stmt->bind_result($exmid);
    $stmt->fetch();
    $stmt->close();

    $stmt = DB::conn()->PREPARE("SELECT filmid FROM `Exemplaar` WHERE id=?");
    $stmt->bind_param('i', $exmid);
    $stmt->execute();
    $stmt->bind_result($filmid);
    $stmt->fetch();
    $stmt->close();

    $stmt = DB::conn()->PREPARE("SELECT titel FROM `Film` WHERE id=?");
    $stmt->bind_param('i', $filmid);
    $stmt->execute();
    $stmt->bind_result($filmtitel);
    $stmt->fetch();
    $stmt->close();

    $stmt = DB::conn()->PREPARE("SELECT naam, email FROM `Persoon` WHERE id=?");
    $stmt->bind_param('i', $klant);
    $stmt->execute();
    $stmt->bind_result($naam, $email);
    $stmt->fetch();
    $stmt->close();

    // if(ophaalMail($order, $filmtitel, $naam, $email, $afleverdatum)){
    //   $stmt = DB::conn()->prepare("UPDATE `Order` SET reminder=1 WHERE id=?");
    //   $stmt->bind_param("i", $d);
    //   $stmt->execute();
    //   $stmt->close();
    // }
  }
}

$stmt = DB::conn()->prepare("SELECT id FROM `Order`");
$stmt->execute();
$stmt->bind_result($id);
while($stmt->fetch()){
  $ids[] = $id;
}
$stmt->close();

if(!empty($ids)){
  foreach($ids as $i){
    $stmt = DB::conn()->prepare("SELECT Afhandeling FROM `Order` WHERE id=? AND besteld=1 AND afhandeling=0");
    $stmt->bind_param('i', $i);
    $stmt->execute();
    $stmt->bind_result($afhandeling);
    $stmt->fetch();
    $stmt->close();

    if($afhandeling == 0){
      $stmt = DB::conn()->prepare("SELECT klantid, afleverdatum, ophaaldatum FROM `Order` WHERE id=?");
      $stmt->bind_param('i', $i);
      $stmt->execute();
      $stmt->bind_result($klantid, $afleverdatum, $ophaaldatum);
      $stmt->fetch();
      $stmt->close();

      $drieWekenGeleden = date('d-m-Y', strtotime("-3 week"));
      $today = date('d-m-Y');
      $drie = strtotime($drieWekenGeleden);
      $ophaal = strtotime($ophaaldatum);
      if(!empty($ophaal)){
        if($ophaal < $drie){
          $stmt = DB::conn()->prepare("SELECT naam, email, rolid FROM `Persoon` WHERE id=?");
          $stmt->bind_param('i', $klantid);
          $stmt->execute();
          $stmt->bind_result($naam, $email, $rolid);
          $stmt->fetch();
          $stmt->close();

          if($rolid != 5){
            $stmt = DB::conn()->prepare("UPDATE `Persoon` SET rolid=5 WHERE id=?;");
            $stmt->bind_param("i", $klantid);
            $stmt->execute();
            $stmt->close();

            blockMail($naam, $email);
          }
        }
      }
    }
  }
}


?>
<div class="panel panel-default">
  <div class="panel-body">
    <h1 class="netToegevoegd">RECENT TOEGEVOEGDE FILMS</h1>
      <div class="nieuw_film_slider">
        <?php
        if(!empty($film_id)){
          foreach($film_id as $i){
            $stmt = DB::conn()->PREPARE("SELECT id,titel, img FROM Film WHERE id=?");
            $stmt->bind_param('i', $i);
            $stmt->execute();
            $stmt->bind_result($id, $titel, $img);
            $stmt->fetch();
            $stmt->close();
            $cover = "/cover/".$img;
            $url = $id;
            $titel = str_replace('_', ' ', $titel);
            $titel = strtoupper($titel);
            ?>
            <div class="filmThumbnail filmAanbodFilm col-md-3 ">
                    <a href="/film/<?php echo $url ?>">
                        <div class="thumb">
                            <img src=<?php echo"$cover" ?> class="thumb_img nieuweThumb nieuweFilm"/></a>
                            <h2 class="textfilmaanbod"><?php echo "$titel"?></h2>
                        </div>
                    </a>
                </div>
            <?php
          }
        }
        ?>
      </div>
  </div>
</div>
<?php
DB::conn()->close();
