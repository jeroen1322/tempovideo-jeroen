<div class="panel panel-default">

<?php
if(!empty($_GET)){
  if($_GET['action'] == 'activate'){
    $stmt = DB::conn()->prepare("UPDATE `Persoon` SET active=1 WHERE wachtwoordid=?");
    $stmt->bind_param("s", $_GET['code']);
    $stmt->execute();
    $stmt->close();
    echo "<div class='succes'>ACCOUNT GEACTIVEERD | <b>LOG IN</b></div>";
  }
}
if(!empty($_POST)){
  $email = $_POST['email'];
  $wachtwoord = $_POST['wachtwoord'];
  if($email && $wachtwoord != ''){
    //Pak het wachtwoordid dat bij de ingevoerde email hoort
    $stmt = DB::conn()->prepare("SELECT wachtwoordid, id, naam, active, rolid FROM Persoon WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $stmt->bind_result($wachtwoordid, $klantId, $naam, $active, $klantRolId);
    $stmt->fetch();
    $stmt->close();

    //Haal het wachtwoord op dat bij het ID hoort
    $ww_stmt = DB::conn()->prepare("SELECT wachtwoord FROM Wachtwoord WHERE id=?");
    $ww_stmt->bind_param("i", $wachtwoordid);
    $ww_stmt->execute();

    $ww_stmt->bind_result($ww);
    $ww_stmt->fetch();
    $ww_stmt->close();

    //Controlleer het opgehaalde wachtwoord met het ingevoerde wachtwoord overeenkomt
    if (password_verify($wachtwoord, $ww)) {
      if(!$active){
        echo '<div class="alert"><b>UW ACCOUNT IS NOG NIET GEACTIVEERD. CONTROLLEER UW EMAIL</b></div>';
      }else{
        $_SESSION['login'] = array();
        $_SESSION['login'][] = $klantId; //Zet de session die we kunnen checken in de functie isIngelogd();
        $_SESSION['login'][] = $naam;
        $_SESSION['login'][] = $klantRolId;
        header("Refresh:0; url=/");
      }
    } else {
        echo '<div class="alert">Deze email en wachtwoord combinatie is niet bij ons geregistreerd.</div>';
    }

    DB::conn()->close();
  }else{
    echo '<div class="alert">Controleer of u alle velden correct heeft ingevuld.</div>';
  }
}

if(!empty($_SESSION['login'])){
  echo "<div class='warning'><b>U BENT AL INGELOGD</b></div>";
}else{
?>
  <div class="panel-body login-panel">
    <h1>LOGIN</h1>
    <form method="post">
      <input type="email" name="email" placeholder="Email" class="form-control">
      <input type="password" name="wachtwoord" placeholder="Wachtwoord" class="form-control">
      <input type="submit" name="submit" class="btn btn-primary form-knop" value="LOGIN">
    </form>
  </div>
</div>
<?php
}
