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
    ?>
    <div class="panel panel-default">
      <div class="panel-body">
        <div class="btn-group admin">
          <a href="/eigenaar/overzicht" class="btn btn-primary admin_menu">OVERZICHT</a>
          <a href="/eigenaar/film_toevoegen" class="btn btn-primary actief admin_menu">FILM TOEVOEGEN</a>
          <a href="/eigenaar/film_verwijderen" class="btn btn-primary admin_menu">FILM VERWIJDEREN</a>
          <a href="/eigenaar/film_aanpassen" class="btn btn-primary admin_menu">FILM INFO BEHEREN</a>
          <a href="/eigenaar/klant_blokkeren" class="btn btn-primary admin_menu">KLANT BLOKKEREN</a>
          <a href="/eigenaar/klacht_afhandelen" class="btn btn-primary admin_menu">KLACHT AFHANDELEN</a>
        </div>
        <h1>FILM TOEVOEGEN</h1>
        <form method="post" enctype="multipart/form-data">
          <input type="text" name="titel" placeholder="Titel" class="form-control" autocomplete="off" required>

          <input type="text" name="acteur1" placeholder="Acteur" class="form-control" autocomplete="off" required>
          <input type="text" name="acteur2" placeholder="Acteur" class="form-control" autocomplete="off">
          <input type="text" name="acteur3" placeholder="Acteur" class="form-control" autocomplete="off">
          <input type="text" name="acteur4" placeholder="Acteur" class="form-control" autocomplete="off">
          <input type="text" name="acteur5" placeholder="Acteur" class="form-control" autocomplete="off">

          <input type="text" name="oms" placeholder="Omschrijving" class="form-control" autocomplete="off" required>


          <select class="form-control" name="genretest[]" multiple="true">
            <?php
            $stmt = DB::conn()->prepare("SELECT genreid FROM `Genre`");
            $stmt->execute();
            $stmt->bind_result($genreid);
            while($stmt->fetch()){
              $genres[] = $genreid;
            }
            $stmt->close();
            foreach($genres as $g){
              $stmt = DB::conn()->prepare("SELECT omschr FROM Genre WHERE genreid=?");
              $stmt->bind_param('i', $g);
              $stmt->execute();
              $stmt->bind_result($genreOmschr);
              $stmt->fetch();
              $stmt->close();
              ?>
              <option value="<?php echo $g ?>" name="genre[]"><?php echo $genreOmschr ?></option>
              <?php
            }
            ?>
          </select>

          <input type="file" name="img" placeholder="FOTO" class="form-control" accept="image/*" required>

          <input type="submit" class="btn btn-succes form-knop" name="submit" value="VOEG TOE">
        </form>
      </div>
      </div>
    </div>
    <?php
  }else{
    echo "404";
  }
}else{
  header("Refresh:0; url=/login");
}


  if(!empty($_POST)){
  $titel = $_POST['titel'];

  $acteur1 = $_POST['acteur1'];
  $acteur2 = $_POST['acteur2'];
  $acteur3 = $_POST['acteur3'];
  $acteur4 = $_POST['acteur4'];
  $acteur5 = $_POST['acteur5'];

  $genres = $_POST['genretest'];

  $oms = $_POST['oms'];
  $genre = $_POST['genre'];
  $img = $_FILES['img'];
  $uploadName = $titel;
  $uploadName = str_replace(' ', '_', $uploadName);
  $uploadName = strtolower($uploadName);

  $target_dir = FOTO."/";
  $target_file = basename($_FILES["img"]["name"]);
  $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
  $isOk = false;
  switch($imageFileType){
    case 'jpg':
      $isOk = true;
      break;
    case 'JPG':
      $isOk = true;
      break;
    case 'jpeg':
      $isOk = true;
      break;
    case 'JPEG':
      $isOk = true;
      break;
    case 'png':
      $isOk = true;
      break;
    case 'PNG':
      $isOk = true;
      break;
  }
  if($isOk){
    $rand = rand(1, 9999);
    $name = $uploadName . "_" . $rand . "." . $imageFileType;
    $target_place = $target_dir . $name;

    if(move_uploaded_file($_FILES['img']['tmp_name'], $target_place)){

    }else{
      echo "Er was een fout tijdens het uploaden van de foto.";
    }
    $stmt = DB::conn()->prepare("select id from Film");
    $stmt->execute();
    $stmt->bind_result($films);
    $testarray = array();
    while ($stmt->fetch()){
        $testarray[]= $films;
    }
    $stmt->close();

    if($films == null){
        $filmid = 1;
    }
    else {
        $stmt = DB::conn()->prepare("select MAX(id) from Film");
        $stmt->execute();
        $stmt->bind_result($filmidlast);
        $stmt->fetch();
        $stmt->close();
        $filmid = $filmidlast + 1;
    }
    print_r($genre);
    //Gegevens invoeren in Film tabel
    $stmt = DB::conn()->prepare("INSERT INTO Film (id, titel, acteur1, acteur2, acteur3, acteur4, acteur5, omschr,  img) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssssss", $filmid, $uploadName, $acteur1, $acteur2, $acteur3, $acteur4, $acteur5, $oms, $name);
    $stmt->execute();
    $stmt->close();

    $stmt = DB::conn()->prepare("INSERT INTO TussenGenre(filmid, genreid) VALUES (?, ?)");
    $stmt->bind_param('ii', $filmid, $genre);
    $stmt->execute();
    $stmt->close();

    foreach($genres as $g){
      $stmt = DB::conn()->prepare("insert into TussenGenre (filmid, genreid) values (?, ?)");
      $stmt->bind_param("ii", $filmid, $g);
      $stmt->execute();
      $stmt->close();
    }

    //EXEMPLAAR
    $ex_stmt = DB::conn()->prepare("SELECT id FROM Film WHERE titel=? and id=?");
    $ex_stmt->bind_param("si", $uploadName, $filmid);
    $ex_stmt->execute();
    $ex_stmt->bind_result($id);
    $ex_stmt->fetch();
    $ex_stmt->close();

    for($i = 1; $i < 12; $i++){
      $statusid = 1;
      $aantalVerhuur = 0;
      $reservering = 0;
      $add_ex_stmt = DB::conn()->prepare("INSERT INTO Exemplaar (filmid, statusid, aantalVerhuur, reservering) VALUES (?, ?, ?, ?)");
      $add_ex_stmt->bind_param("iiii", $id, $statusid, $aantalVerhuur, $reservering);
      $add_ex_stmt->execute();
      $add_ex_stmt->close();
    }
    header("Refresh:0; url=/film/$filmid");
  }else{
    echo "<div class='alert'><b>U HEEFT GEEN GELDIG FOTO BESTAND GEUPLOAD</b></div>";
  }
  DB::conn()->close();
  }
