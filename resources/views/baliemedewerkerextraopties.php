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
                  <a href="/baliemedewerker/afhandelen" class="btn btn-primary admin_menu klant_menu">Afhandelen</a>
                  <a href="/baliemedewerker/extraopties" class="btn btn-primary admin_menu klant_menu actief">EXTRA OPTIES</a>
              </div>
                <h1>EXTRA OPTIES</h1>
        <?php
        $stmt = DB::conn()->prepare("SELECT id FROM `Order` WHERE afhandeling=0 AND besteld=1");
        $stmt->execute();
        $stmt->bind_result($id);
        $order_id = array();
        while($stmt->fetch()){
            $order_id[] = $id;
        }
        $stmt->close();
        if(!empty($_GET)){
            $code = $_GET['code'];
            $action = $_GET['action'];
            $exm = $_GET['id'];
            if($action == 'extraBedrag') {
                $afhandeling = 1;

                $stmt = DB::conn()->prepare("UPDATE `Order` SET openbedrag = 5 WHERE id=?;");
                $stmt->bind_param("i", $code);
                $stmt->execute();
                $stmt->close();
                }

            }

        }
        if(!empty($id)) {
            ?>
            <div>
              <table class="table">
                <thead>
                <tr>
                    <th>Id</th>
                    <th>Naam</th>
                    <th>Woonplaats</th>
                    <th>Datum ophalen</th>
                    <th>Tijd</th>
                    <th>Extra bedrag toevoegen</th>
                </tr>
                </thead>
                <tbody>
            </div>
            <?php
            foreach ($order_id as $i) {
                $stmt = DB::conn()->prepare("SELECT exemplaarid FROM `Orderregel` WHERE orderid=?");
                $stmt->bind_param('i', $i);
                $stmt->execute();
                $stmt->bind_result($exmid);
                $stmt->fetch();
                $stmt->close();

                $stmt = DB::conn()->prepare("SELECT o.id, p.naam, p.adres, p.woonplaats, o.aflevertijd, o.ophaaltijd, o.afleverdatum, o.ophaaldatum FROM Persoon p, `Order` o where afhandeling = 0 and besteld  = 1 and o.id=?;");
                $stmt->bind_param("i", $i);
                $stmt->execute();
                $stmt->bind_result($id, $naam, $adres, $woonplaats, $aflevertijd, $ophaaltijd, $afleverdatum, $ophaaldatum);
                $stmt->fetch();
                $stmt->close();
                ?>
                <tr>
                    <td><?php echo $id ?></td>
                    <td><?php echo $naam ?></td>
                    <td><?php echo $woonplaats ?></td>
                    <td><?php echo $ophaaldatum ?></td>
                    <td><?php echo $ophaaltijd ?></td>
                    <td>
                        <form method="post"
                              action="?action=extraBedrag&code=<?php echo $i ?>&id=<?php echo $id ?>">
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-credit-card"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php
            }
        }
        else{
          echo "<div class='warning'><b>ER ZIJN GEEN OPEN BESTELLINGEN</b></div>";
        }
        ?>
        </table>
      </div>
    </div>
  </div>
    <?php
}else{
    header("Refresh:0; url=/login");
}
?>
