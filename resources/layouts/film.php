<?php
include(__DIR__ . '/../db.php');
include(MAIL . '/mailLib.php');

session_start();

if(!empty($_SESSION['login'])){
  $klantId = $_SESSION['login'][0];
  // $klantNaam = $_SESSION['login'][1];
  $klantRolId = $_SESSION['login'][2];

   $stmt = DB::conn()->prepare("SELECT naam FROM `Persoon` WHERE id=?");
   $stmt->bind_param('i', $klantId);
   $stmt->execute();
   $stmt->bind_result($klantNaam);
   $stmt->fetch();
   $stmt->close();
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Material Design fonts -->
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Roboto:300,400,500,700">
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="/font-awesome-4.7.0/css/font-awesome.min.css">


    <!-- Bootstrap -->
    <link href="../bootstrap/css/bootstrap.min.css " rel="stylesheet">

    <!-- Bootstrap Material Design -->
    <link rel="stylesheet" type="text/css" href="../css/slick.css">
    <link rel="stylesheet" type="text/css" href="../css/slick-theme.css">
    <link rel="stylesheet" type="text/css" href="../bootstrap-material/css/bootstrap-material-design.css">
    <link rel="stylesheet" type="text/css" href="../bootstrap-material/css/ripples.css">
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <!-- <link rel="stylesheet" type="text/css" href="dist/css/ripples.min.css"> -->

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="navbar navbar-default">
      <div class="container-fluid container">
        <div class="navbar-header">
          <?php
            if(!empty($_SESSION['login'])){
              $stmt = DB::conn()->prepare("SELECT id FROM `Order` WHERE klantid=? AND besteld=0");
              $stmt->bind_param("i", $klantId);
              $stmt->execute();
              $stmt->bind_result($order_id);
              $stmt->fetch();
              $stmt->close();
              $stmt = DB::conn()->prepare("select count(exemplaarid) from Orderregel where orderid =?;");
              $stmt->bind_param("i", $order_id);
              $stmt->execute();
              $stmt->bind_result($count);
              $stmt->fetch();
              $stmt->close();

              if($count > 0){
                ?>
                <ul class="nav navbar-nav mobile_cart_right">
                  <li><a href="/winkelmand" class="menu_winkelmand"><i class="fa fa-shopping-cart" aria-hidden="true"></i>(<?php echo $count; ?>)</a></li>
                </ul>
                <?php
              }
            }
            ?>
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="/">TEMPOVIDEO</a>
        </div>
        <div class="navbar-collapse collapse navbar-responsive-collapse">
          <ul class="nav navbar-nav">
            <li><a href="/film/aanbod">FILMAANBOD</a></li>
            <li><a href="/contact">CONTACT</a></li>
          </ul>
            <?php
            if(!empty($_SESSION['login'])){
              ?>
              <ul class="nav navbar-nav menu_right">
                <?php
                $stmt = DB::conn()->prepare("SELECT id FROM `Order` WHERE klantid=? AND besteld=0");
                $stmt->bind_param("i", $klantId);
                $stmt->execute();
                $stmt->bind_result($order_id);
                $stmt->fetch();
                $stmt->close();
                $stmt = DB::conn()->prepare("select count(exemplaarid) from Orderregel where orderid =?;");
                $stmt->bind_param("i", $order_id);
                $stmt->execute();
                $stmt->bind_result($count);
                $stmt->fetch();
                $stmt->close();
                if(!empty($order_id)){
                  ?>
                  <li><a href="/winkelmand" class="menu_winkelmand"><i class="fa fa-shopping-cart" aria-hidden="true"></i>(<?php echo $count; ?>)</a></li>
                  <?php

                }
                ?>
                <li class="dropdown">
                   <a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo $klantNaam ?>
                   <span class="caret"></span></a>
                   <ul class="dropdown-menu">
                     <?php
                     if($_SESSION['login'][2] === 4){
                       ?>
                         <li><a href="/eigenaar/overzicht" class="naam">OVERZICHT</a></li>
                       <?php
                     }elseif($_SESSION['login'][2] === 1 || $_SESSION['login'][2] === 5){
                       ?>
                       <li><a href="/klant/overzicht" class="naam">OVERZICHT</a></li>
                       <?php
                     }
                     elseif($_SESSION['login'][2] === 3){
                         ?>
                         <li><a href="/baliemedewerker/afhandelen" class="naam">OVERZICHT</a></li>
                         <?php
                     }
                     ?>
                     <li><a href="/uitloggen">UITLOGGEN</a></li>
                   </ul>
                 </li>
              </ul>
              <?php
            }else{
              ?>
              <ul class="nav navbar-nav menu_right">
                <li><a href="/login">LOGIN</a></li>
                <li><a href="/registreer">REGISTREER</a></li>
              </ul>
              <?php
            }
            ?>
        </div>
      </div>
    </div>
    <div class="container">
      <div class="content">
            <?= $this->yieldView(); ?>
      </div>
    </div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://code.jquery.com/jquery-2.2.4.js" integrity="sha256-iT6Q9iMJYuQiMWNd9lDyBUStIq/8PuOW33aOqmvFpqI=" crossorigin="anonymous"></script>
     <!-- Include all compiled plugins (below), or include individual files as needed -->

    <script type="text/javascript" src="//code.jquery.com/jquery-1.11.0.min.js"></script>
    <script type="text/javascript" src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
    <script type="text/javascript" src="../js/slick.min.js"></script>
     <script src="/js/js.js"></script>
     <script src="/bootstrap/js/bootstrap.min.js"></script>
     <script src="/bootstrap-material/js/material.js"></script>
     <script src="/bootstrap-material/js/ripples.js"></script>
  </body>
</html>
