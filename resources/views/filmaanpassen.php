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

    if(!empty($_GET['action'])){
      $code = $_GET['code'];
      $edit = true;
    }else{
      $edit = false;
    }

    ?>
<div class="panel panel-default">
  <div class="panel-body">
    <div class="btn-group admin">
      <a href="/eigenaar/overzicht" class="btn btn-primary admin_menu">OVERZICHT</a>
      <a href="/eigenaar/film_toevoegen" class="btn btn-primary admin_menu">FILM TOEVOEGEN</a>
      <a href="/eigenaar/film_verwijderen" class="btn btn-primary admin_menu">FILM VERWIJDEREN</a>
      <a href="/eigenaar/film_aanpassen" class="btn btn-primary actief admin_menu">FILM INFO BEHEREN</a>
      <a href="/eigenaar/klant_blokkeren" class="btn btn-primary admin_menu">KLANT BLOKKEREN</a>
      <a href="/eigenaar/klacht_afhandelen" class="btn btn-primary admin_menu">KLACHT AFHANDELEN</a>
    </div>
    <h1>FILM INFORMATIE BEHEREN</h1>
    <?php
    $stmt = DB::conn()->prepare("SELECT id FROM `Film`");
    $stmt->execute();
    $stmt->bind_result($id);
    $film_id = array();
    while($stmt->fetch()){
      $film_id[] = $id;
    }
    $stmt->close();

    if(!empty($id)){
      ?>
      <table class="table">
        <thead>
          <tr>
            <th>Foto</th>
            <th>Titel</th>
            <th>Omschrijving</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
      <?php

      foreach($film_id as $i){
        $stmt = DB::conn()->prepare("SELECT id, titel, omschr, img FROM `Film` WHERE id=?");
        $stmt->bind_param("i", $i);
        $stmt->execute();
        $stmt->bind_result($id, $titel, $omschr, $img);
        $stmt->fetch();
        $stmt->close();
        $cover = "/cover/" . $img;
        $URL = "/film/" . $id;


        if($edit == true && $code == $id){
          ?>
          <tr>
            <td><a href="<?php echo $URL ?>"><img src="<?php echo $cover ?>" class="winkelmand_img"></a></td>
            <td>
              <form method="post" action="?action=save&code=<?php echo $id ?>">
                <input type="text" class="form-control" autocomplete="off" value="<?php echo $titel ?>" name="titel">
            </td>
            <td><input type="text" class="form-control" autocomplete="off" value="<?php echo $omschr ?>" name="omschr"></td>
            <td>
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-floppy-o" aria-hidden="true"></i>
                </button>
              </form>
            </td>
          </tr>
          <?php
            $nieuweTitel = $_POST['titel'];
            $nieuweOmschr = $_POST['omschr'];
            if(!empty($_POST)){
              //Gegevens invoeren in Film tabel
              $stmt = DB::conn()->prepare("UPDATE `Film` SET `titel`=? WHERE id=?");
              $stmt->bind_param("ss", $nieuweTitel, $code);
              $stmt->execute();
              $stmt->close();
              //Gegevens invoeren in Film tabel
              $stmt = DB::conn()->prepare("UPDATE `Film` SET `omschr`=? WHERE id=?");
              $stmt->bind_param("ss", $nieuweOmschr, $code);
              $stmt->execute();
              $stmt->close();

              header("Refresh:0; url=/eigenaar/film_aanpassen");
            }
        }else{
        ?>
        <tr>
          <td><a href="<?php echo $URL ?>"><img src="<?php echo $cover ?>" class="winkelmand_img"></a></td>
          <td><?php echo $titel ?></td>
          <td><?php echo $omschr ?></td>
          <td>
            <form method="post" action="?action=edit&code=<?php echo $id ?>">
              <button type="submit" class="btn btn-success">
                  <i class="fa fa-pencil" aria-hidden="true"></i>
              </button>
            </form>
          </td>
        </tr>
        <?php
      }
    }

      DB::conn()->close();
    }else{
      echo "<div class='warning'><b>ER ZIJN GEEN FILMS IN DE DATABASE</b></div>";
    }
  }else{
    echo "NOPE HIER MAG JE NIET KOMEN!";
  }
}else{
  header("Refresh:0; url=/login");
}
