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
    <?php
    if(!empty($_POST)){
      $ordernummer = $_POST['ordernummer'];
      $onderwerp = $_POST['onderwerp'];
      $bericht = $_POST['bericht'];
      $vandaag = date('d-m-Y');
      $status = 1;
      $klant = $klantId;

      $stmt = DB::conn()->prepare("INSERT INTO `Klacht`(klantid, onderwerp, bericht, orderid, datum, status) VALUES(?, ?, ?, ?, ?, ?)");
      $stmt->bind_param('issssi', $klant, $onderwerp, $bericht, $ordernummer, $vandaag, $status);
      $stmt->execute();
      $stmt->close();
      echo "<div class='succes'><b>UW KLACHT IS INGEDIEND</b></div>";
      klachtIndienMail($naam, $email);
    }
    ?>
      <h1>KLACHT INDIENEN</h1>
      <form method="post" enctype="multipart/form-data">
        <input type="text" name="ordernummer" placeholder="Ordernummer (optioneel)" class="form-control" autocomplete="off">
        <input type="text" name="onderwerp" placeholder="Onderwerp" class="form-control" autocomplete="off" required>
        <input type="text" name="bericht" placeholder="bericht" class="form-control" autocomplete="off" required>

        <input type="submit" class="btn btn-succes form-knop" name="submit" value="DIEN KLACHT IN">
      </form>
    </div>
  </div>
    <?php
  }
}
