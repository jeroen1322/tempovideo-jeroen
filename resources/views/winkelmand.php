<div class="panel panel-default">
  <div class="panel-body">
    <h1>WINKELMAND</h1>

<?php
if(!empty($_SESSION['login'])){
  $klant = $_SESSION['login']['0'];
  $klantRolId = $_SESSION['login'][2];
  //Haal id op van Order op
  $stmt = DB::conn()->prepare("SELECT id FROM `Order` WHERE klantid=? AND besteld=0");
  $stmt->bind_param("i", $klant);
  $stmt->execute();

  $stmt->bind_result($order_id);

  $orderIdResult = array();

  while($stmt->fetch()){
    $orderIdResult[] = $order_id;
  }

  $stmt->close();

  if(!empty($orderIdResult)){
    ?>
    <table class="table">
      <thead>
        <tr>
          <th>Foto</th>
          <th class="table_titel">Titel</th>
          <th class="table_omschr">Omschrijving</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
    <?php
    $bedr_stmt = DB::conn()->prepare("SELECT bedrag FROM `Order` WHERE klantid=?");
    $bedr_stmt->bind_param("i", $klant);
    $bedr_stmt->execute();
    $bedr_stmt->bind_result($bedrag);
    $bedr_stmt->fetch();
    $bedr_stmt->close();
    //Haal exemplaarid van Orderregel dat bij de Order hoort op
    $or_stmt = DB::conn()->prepare("SELECT exemplaarid FROM `Orderregel` WHERE orderid=?");
    $or_stmt->bind_param("i", $order_id);
    $or_stmt->execute();
    $or_stmt->bind_result($exem_id);
    $exm_id = array();
    while($or_stmt->fetch()){
        $exm_id[] = $exem_id;
    }
    $or_stmt->close();

    foreach($exm_id as $i){

      $exm_stmt = DB::conn()->prepare("SELECT filmid FROM `Exemplaar` WHERE id=?");
      $exm_stmt->bind_param("i", $i);
      $exm_stmt->execute();

      $exm_stmt->bind_result($exm_film_id);
      $exm_stmt->fetch();
      $exm_stmt->close();

      //Haal alles van de film op dat overeen komt met de filmid van het exemplaar
      $exm_film_stmt = DB::conn()->prepare("SELECT id, titel, acteur, omschr, genre, img FROM `Film` WHERE id=?");
      $exm_film_stmt->bind_param("i", $exm_film_id);
      $exm_film_stmt->execute();

      $exm_film_stmt->bind_result($film_id, $titel, $acteur, $omschr, $genre, $img);
      $exm_film_stmt->fetch();
      $exm_film_stmt->close();


      if(!empty($film_id)){
        $cover = "/cover/" . $img;
        $URL = "/film/" . $film_id;
        $titel = strtoupper($titel);
        $titel = str_replace('_', ' ', $titel);
        // $bedrag = $bedrag / 100;
        ?>
          <tr>
            <td><a href="<?php echo $URL ?>"><img src="<?php echo $cover ?>" class="winkelmand_img"></a></td>
            <td class="table_titel"><?php echo $titel ?></td>
            <td class="table_omschr"><?php echo $omschr ?><td>
            <td>
              <form method="post" action="?action=delete&code=<?php echo $i ?>">
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                </button>
              </form>
            </td>
          </tr>
        <?php
      }
    }

    if(!empty($_GET['action'])){

      $code = $_GET['code'];

      $stmt = DB::conn()->prepare("SELECT id FROM `Exemplaar` WHERE id=? AND statusid=2");
      $stmt->bind_param("i", $code);
      $stmt->execute();
      $stmt->bind_result($exm_order_id);
      $stmt->fetch();
      $stmt->close();

      $stmt = DB::conn()->prepare("UPDATE `Exemplaar` SET statusid=1 WHERE id=?");
      $stmt->bind_param("i", $code);
      $stmt->execute();
      $stmt->close();

      $stmt = DB::conn()->prepare("SELECT orderid FROM `Orderregel` WHERE exemplaarid=?");
      $stmt->bind_param("i", $code);
      $stmt->execute();
      $stmt->bind_result($OR_order_id);
      $stmt->fetch();
      $stmt->close();

      $stmt = DB::conn()->prepare("select count(exemplaarid) from Orderregel where orderid =?;");
      $stmt->bind_param("i", $order_id );
      $stmt->execute();
      $stmt->bind_result($count);
      $stmt->fetch();
      $stmt->close();

      if($count == 1) {
          $stmt = DB::conn()->prepare("DELETE FROM `Order` WHERE id=?");
          $stmt->bind_param("i", $OR_order_id);
          $stmt->execute();
          $stmt->close();
      }

      $stmt = DB::conn()->prepare("DELETE FROM `Orderregel` WHERE exemplaarid=?");
      $stmt->bind_param("i", $code);
      $stmt->execute();
      $stmt->close();

      header("Refresh:0; url=/winkelmand");
    }

    DB::conn()->close();
    ?>
    </tbody>
    </table>
    <a href="/film/aanbod">
      <button class="btn btn-success bestel verder_winkelen">VERDER WINKELEN</button>
    </a>

    <?php
    if($klantRolId != 5){
      ?>
      <div class="winkelmand_onder">
        <a href="/winkelmand/afrekenen">
          <button class="btn btn-success bestel">AFREKENEN</button>
        </a>
      </div>
      <?php
    }
    ?>
  </div>
  </div>
    <?php
  }else{
    echo "<div class='warning'>UW WINKELMAND IS LEEG</div>";
  }
}else{
  echo "U MOET <a href='/login'>INGELOGD</a> ZIJN";
}

?>
