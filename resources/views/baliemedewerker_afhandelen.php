<?php
if(!empty($_SESSION['login'])){
    $klantId = $_SESSION['login'][0];
    $klantNaam = $_SESSION['login'][1];
    $klantRolId = $_SESSION['login'][2];
    function isEigenaar($klantRolId){
        if($klantRolId === 3){
            return true;
        }else{
            return false;
        }
    }
    if(isEigenaar($klantRolId)){
        if(!empty($_GET)){
            $code = $_GET['code'];
            $action = $_GET['action'];

            if($action == 'afgehandeld') {
              $afhandeling = 1;

              $stmt = DB::conn()->prepare("UPDATE `Order` SET afhandeling=1, openbedrag=0, besteld=1 WHERE id=?;");
              $stmt->bind_param("i", $code);
              $stmt->execute();
              $stmt->close();

              $stmt = DB::conn()->prepare("SELECT exemplaarid FROM `Orderregel` WHERE orderid=?");
              $stmt->bind_param('i', $code);
              $stmt->execute();
              $stmt->bind_result($e);
              while($stmt->fetch()){
                $exms[] = $e;
              }
              $stmt->close();

              foreach($exms as $ex){
                $stmt = DB::conn()->prepare("UPDATE `Exemplaar` SET statusid=1 WHERE id=?;");
                $stmt->bind_param("i", $ex);
                $stmt->execute();
                $stmt->close();

                $stmt = DB::conn()->prepare("SELECT filmid FROM `Exemplaar` WHERE id=?");
                $stmt->bind_param('i', $ex);
                $stmt->execute();
                $stmt->bind_result($fId);
                $stmt->fetch();
                $stmt->close();

                $stmt = DB::conn()->prepare("SELECT id FROM `Reservering` WHERE filmid=?");
                $stmt->bind_param('i', $fId);
                $stmt->execute();
                $stmt->bind_result($resId);
                $stmt->fetch();
                $stmt->close();

                if(!empty($resId)){
                  $stmt = DB::conn()->prepare("SELECT filmid, persoonid FROM `Reservering` WHERE id=?");
                  $stmt->bind_param('i', $resId);
                  $stmt->execute();
                  $stmt->bind_result($filmid, $persoonid);
                  $stmt->fetch();
                  $stmt->close();

                  $besteld = 0;
                  $afhandeling = 0;
                  $huidigeWeek = date('d-m-Y');
                  $volgendeWeek = date('d-m-Y', strtotime("+7 days"));
                  $openBedrag = 0;

                  $cart_stmt = DB::conn()->prepare("select count(o.id) from `Order` o where o.klantid =? and ifnull(besteld, false) = false;");
                  $cart_stmt->bind_param("i", $persoonid);
                  $cart_stmt->execute();
                  $cart_stmt->bind_result($countorder);
                  $cart_stmt->fetch();
                  $cart_stmt->close();

                  if($countorder == 0){
                      $order_id = rand(1, 2100);
                      $cart_stmt = DB::conn()->prepare("INSERT INTO `Order` (id, klantid, afhandeling, openbedrag, orderdatum, besteld) VALUES (?, ?, ?, ?, ?, ?)");
                      $cart_stmt->bind_param("iiiisi", $order_id, $persoonid, $afhandeling, $openBedrag, $huidigeWeek, $besteld);
                      $cart_stmt->execute();
                      $cart_stmt->close();
                  }

                  $orderid_stmt = DB::conn()->prepare("select id FROM `Order` WHERE klantid =? AND besteld = 0");
                  $orderid_stmt->bind_param("i", $persoonid);
                  $orderid_stmt->execute();
                  $orderid_stmt->bind_result($order_id);
                  $orderid_stmt->fetch();
                  $orderid_stmt->close();

                  $exm_stmt = DB::conn()->prepare("UPDATE `Exemplaar` SET statusid=2, reservering=1 WHERE id=?");
                  $exm_stmt->bind_param("i", $ex);
                  $exm_stmt->execute();
                  $exm_stmt->close();

                  $or_stmt = DB::conn()->prepare("INSERT INTO `Orderregel` (exemplaarid, orderid) VALUES (?, ?)");
                  $or_stmt->bind_param("ii", $ex, $order_id);
                  $or_stmt->execute();
                  $or_stmt->close();
                  echo $order_id;
                  $stmt = DB::conn()->prepare("DELETE FROM `Reservering` WHERE id=?");
                  $stmt->bind_param('i', $resId);
                  $stmt->execute();
                  $stmt->close();

                  echo "<br>test";
                }
              }


              // $stmt = DB::conn()->prepare("SELECT exemplaarid FROM `Orderregel` WHERE orderid=?");
              // $stmt->bind_param('i', $code);
              // $stmt->execute();
              // $stmt->bind_result($exmid);
              // $stmt->fetch();
              // $stmt->close();
              //
              // $stmt = DB::conn()->prepare("SELECT klantid FROM `Reservering` WHERE filmid=?");
              // $stmt->bind_param('i', $);
              // $stmt->execute();
              // $stmt->bind_result($klant);
              // $stmt->fetch();
              // $stmt->close();
              //
              // $besteld = 0;
              // $afhandeling = 0;
              // $huidigeWeek = date('d-m-Y');
              // $volgendeWeek = date('d-m-Y', strtotime("+7 days"));
              //
              // $cart_stmt = DB::conn()->prepare("select count(o.id) from `Order` o where o.klantid =? and ifnull(besteld, false) = false;");
              // $cart_stmt->bind_param("i", $klantId);
              // $cart_stmt->execute();
              // $cart_stmt->bind_result($countorder);
              // $cart_stmt->fetch();
              // $cart_stmt->close();
              // $openBedrag = 0;
              //
              // if($countorder == 0){
              //     $order_id = rand(1, 2100);
              //     $cart_stmt = DB::conn()->prepare("INSERT INTO `Order` (id, klantid, afhandeling, openbedrag, orderdatum, besteld) VALUES (?, ?, ?, ?, ?, ?)");
              //     $cart_stmt->bind_param("iiiisi", $order_id, $klant, $afhandeling, $openBedrag, $huidigeWeek, $besteld);
              //     $cart_stmt->execute();
              //     $cart_stmt->close();
              // }
              // $orderid_stmt = DB::conn()->prepare("select id FROM `Order` WHERE klantid =? AND besteld = 0");
              // $orderid_stmt->bind_param("i", $klantId);
              // $orderid_stmt->execute();
              // $orderid_stmt->bind_result($order_id);
              // $orderid_stmt->fetch();
              // $orderid_stmt->close();
              //
              // $stmt = DB::conn()->prepare("INSERT INTO `Orderregel`(exemplaarid, orderid), VALUES(?,?)");
              // $stmt->bind_result('ii', $exmid, $order_id);
              // $stmt->execute();
              // $stmt->close();

              }
        }
        ?>

        <div class="panel panel-default">
            <div class="panel-body">
                <div class="btn-group admin">
                    <a href="/baliemedewerker/afhandelen" class="btn btn-primary actief admin_menu">Afhandelen</a>
                    <a href="/baliemedewerker/bezorgdata" class="btn btn-primary admin_menu">BEZORGDATA</a>
                    <a href="/baliemedewerker/extraopties" class="btn btn-primary admin_menu">EXTRA OPTIES</a>
                </div>
                <h1>Afhandelen</h1>

                <?php
                $stmt = DB::conn()->prepare("SELECT id FROM `Order` WHERE afhandeling=0 AND besteld=1");
                $stmt->execute();
                $stmt->bind_result($id);
                $order_id = array();
                while($stmt->fetch()){
                    $order_id[] = $id;
                }
                $stmt->close();

        if(!empty($id)){
          ?>
          <div>
          <table class="table">
              <thead>
              <tr>
                  <th>Id</th>
                  <th>Naam</th>
                  <th>Woonplaats</th>
                  <th>Datum wegbrengen</th>
                  <th>Tijd</th>
                  <th>Datum ophalen</th>
                  <th>Tijd</th>
                  <th>Titels</th>
                  <th>Afhandeling</th>
              </tr>
              </thead>
              <tbody>
          </div>
          <?php
            foreach($order_id as $i){

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
                <td><?php echo $afleverdatum ?></td>
                <td><?php echo $aflevertijd ?></td>
                <td><?php echo $ophaaldatum ?></td>
                <td><?php echo $ophaaltijd ?></td>
                <td></td>
                <td>
                  <form method="post" action="?action=afgehandeld&code=<?php echo $i ?>">
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-check unblock"></i>
                        </button>
                    </form>
                  </td>
                </tr>
                <?php
          }
          ?>
          </table>
          <?php
        }else{
          // header("Refresh:0; url=/login");
          echo "<div class='warning'><b>ER ZIJN GEEN OPEN BESTELLINGEN</b></div>";
        }
      }
    }
?>
            </div>
