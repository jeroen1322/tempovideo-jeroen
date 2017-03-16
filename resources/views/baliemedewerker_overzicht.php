<?php
if(!empty($_SESSION['login'])){
    $klantId = $_SESSION['login'][0];
    $klantNaam = $_SESSION['login'][1];
    $klantRolId = $_SESSION['login'][2];
    function isBalieMedewerker($klantRolId){
        if($klantRolId === 3){
            return true;
        }else{
            return false;
        }
    }
    if(isBalieMedewerker($klantRolId)){
    ?>
        <div class="panel panel-default">
            <div class="panel-body">
              <div class="btn-group admin">
                  <a href="/baliemedewerker/afhandelen" class="btn btn-primary actief admin_menu">Afhandelen</a>
                  <a href="/baliemedewerker/extraopties" class="btn btn-primary admin_menu">EXTRA OPTIES</a>
              </div>
                <h1> </h1>
            </div>
        </div>
    <?php
    }
}else{
  header("Refresh:0; url=/login");
}
?>
