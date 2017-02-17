<div class="panel panel-default">
  <?php
  if(!empty($_POST)){
    $naam = $_POST['naam'];
    $email = $_POST['mail'];
    $onderwerp = $_POST['onderwerp'];
    $bericht = $_POST['bericht'];
    if(contactMail($naam, $email, $onderwerp, $bericht)){
      echo "<div class='succes'><b>UW BERICHT IS VERSTUURD</b></div>";
    }
  }

  $stmt = DB::conn()->prepare("SELECT email, telefoonnummer FROM Persoon where rolid=4");
  $stmt->execute();
  $stmt->bind_result($adminEmail, $adminTel);
  $stmt->fetch();
  $stmt->execute();
  $stmt->close();

  ?>
  <div class="panel-body">
    <h2> Contactformulier:</h2>
        <div class="inner contact">
            <div class="contact-form">
                <form id="contact-us" method="post">
                    <div class="col-xs-6 wow animated slideInLeft" data-wow-delay=".5s">
                        <input type="text" name="naam" id="name" required="required" class="form" placeholder="Naam" />
                        <input type="email" name="mail" id="mail" required="required" class="form" placeholder="Email" />
                        <input type="text" name="onderwerp" id="subject" required="required" class="form" placeholder="Onderwerp" />
                    </div>
                    <div class="col-xs-6 wow animated slideInRight" data-wow-delay=".5s">
                        <textarea name="bericht" id="message" class="form textarea"  placeholder="Uw bericht"></textarea>
                    </div>
                    <div class="relative fullwidth col-xs-12">
                        <button type="submit" id="submit" name="submit" class="form-btn semibold">Verzenden</button>
                    </div>
                    <div class="clear"></div>
                </form>
            </div>
        </div>
      <div>
        <h2>Contactgegevens: </h2>
        <b>TempoVideo</b> <br>
        <?php echo $adminEmail?> <br>
        <?php echo $adminTel?>
      </div>
  </div>
</div>
