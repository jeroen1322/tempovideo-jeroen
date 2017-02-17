<h1>UITLOGGEN</h1>
<?php
  session_unset($_SESSION['login']);
  header("Refresh:0; url=/");
