<?php
    /**
     * @author http://netters.nl/nederlandse-datum-in-php
     */
    function nlDate($datum){
      /*
       // AM of PM doen we niet aan
       $parameters = str_replace("A", "", $parameters);
       $parameters = str_replace("a", "", $parameters);

      $datum = date($parameters);
     */
       // Vervang de maand, klein
      $datum = str_replace("january",     "januari",         $datum);
       $datum = str_replace("february",     "februari",     $datum);
      $datum = str_replace("march",         "maart",         $datum);
       $datum = str_replace("april",         "april",         $datum);
       $datum = str_replace("may",         "mei",             $datum);
       $datum = str_replace("june",         "juni",         $datum);
      $datum = str_replace("july",         "juli",         $datum);
      $datum = str_replace("august",         "augustus",     $datum);
       $datum = str_replace("september",     "september",     $datum);
       $datum = str_replace("october",     "oktober",         $datum);
       $datum = str_replace("november",     "november",     $datum);
      $datum = str_replace("december",     "december",     $datum);

      // Vervang de maand, hoofdletters
     $datum = str_replace("January",     "Januari",         $datum);
       $datum = str_replace("February",     "Februari",     $datum);
      $datum = str_replace("March",         "Maart",         $datum);
       $datum = str_replace("April",         "April",         $datum);
       $datum = str_replace("May",         "Mei",             $datum);
       $datum = str_replace("June",         "Juni",         $datum);
      $datum = str_replace("July",         "Juli",         $datum);
      $datum = str_replace("August",         "Augustus",     $datum);
       $datum = str_replace("September",     "September",     $datum);
       $datum = str_replace("October",     "Oktober",         $datum);
       $datum = str_replace("November",     "November",     $datum);
      $datum = str_replace("December",     "December",     $datum);

      // Vervang de maand, kort
       $datum = str_replace("Jan",         "Jan",             $datum);
       $datum = str_replace("Feb",         "Feb",             $datum);
       $datum = str_replace("Mar",         "Maa",             $datum);
       $datum = str_replace("Apr",         "Apr",             $datum);
       $datum = str_replace("May",         "Mei",             $datum);
       $datum = str_replace("Jun",         "Jun",             $datum);
       $datum = str_replace("Jul",         "Jul",             $datum);
       $datum = str_replace("Aug",         "Aug",             $datum);
       $datum = str_replace("Sep",         "Sep",             $datum);
       $datum = str_replace("Oct",         "Ok",             $datum);
     $datum = str_replace("Nov",         "Nov",             $datum);
       $datum = str_replace("Dec",         "Dec",             $datum);

      // Vervang de dag, klein
     $datum = str_replace("monday",         "maandag",         $datum);
       $datum = str_replace("tuesday",     "dinsdag",         $datum);
       $datum = str_replace("wednesday",     "woensdag",     $datum);
     $datum = str_replace("thursday",     "donderdag",     $datum);
     $datum = str_replace("friday",         "vrijdag",         $datum);
       $datum = str_replace("saturday",     "zaterdag",     $datum);
      $datum = str_replace("sunday",         "zondag",         $datum);

      // Vervang de dag, hoofdletters
       $datum = str_replace("Monday",         "Maandag",         $datum);
       $datum = str_replace("Tuesday",     "Dinsdag",         $datum);
       $datum = str_replace("Wednesday",     "Woensdag",     $datum);
     $datum = str_replace("Thursday",     "Donderdag",     $datum);
     $datum = str_replace("Friday",         "Vrijdag",         $datum);
       $datum = str_replace("Saturday",     "Zaterdag",     $datum);
      $datum = str_replace("Sunday",         "Zondag",         $datum);

      // Vervang de verkorting van de dag, hoofdletters
       $datum = str_replace("Mon",            "Maa",             $datum);
       $datum = str_replace("Tue",         "Din",             $datum);
       $datum = str_replace("Wed",         "Woe",             $datum);
       $datum = str_replace("Thu",         "Don",             $datum);
       $datum = str_replace("Fri",         "Vri",             $datum);
       $datum = str_replace("Sat",         "Zat",             $datum);
       $datum = str_replace("Sun",         "Zon",             $datum);

      return $datum;
  }
  class Afrekenen{
    public function getKlantInfo($klantId){
      $stmt = DB::conn()->prepare("SELECT id, naam, adres, postcode, woonplaats, telefoonnummer, email FROM `Persoon` WHERE id=?");
      $stmt->bind_param('i', $klantId);
      $stmt->execute();
      $stmt->bind_result($id, $naam, $adres, $postcode, $woonplaats, $telefoonnummer, $email);
      $stmt->fetch();
      $stmt->close();

      $klantInfoArray['id'] = $id;
      $klantInfoArray['naam'] = $naam;
      $klantInfoArray['adres'] = $adres;
      $klantInfoArray['postcode'] = $postcode;
      $klantInfoArray['woonplaats'] = $woonplaats;
      $klantInfoArray['telefoonnummer'] = $telefoonnummer;
      $klantInfoArray['email'] = $email;

      return $klantInfoArray;
    }

    public function getKlantOrders($klantId){
      $stmt = DB::conn()->prepare("SELECT id FROM `Order` WHERE klantid=? AND besteld=0");
      $stmt->bind_param("i", $klantId);
      $stmt->execute();
      $stmt->bind_result($order_id);
      $orderIdResult = array();
      while($stmt->fetch()){
        $orderIdResult[] = $order_id;
      }
      $stmt->close();
      $returns['order_id'] = $order_id;
      $returns['orderIdResult'] = $orderIdResult;
      return $returns;
    }

    public function countOrders($order_id){
      $stmt = DB::conn()->prepare("select count(exemplaarid) from Orderregel where orderid =?;");
      $stmt->bind_param("i", $order_id);
      $stmt->execute();
      $stmt->bind_result($count);
      $stmt->fetch();
      $stmt->close();

      return $count;
    }

    public function getOphaalData($klantId){
      $stmt = DB::conn()->prepare("SELECT ophaaldatum, ophaaltijd FROM `Order` WHERE besteld=1 AND klantid=?");
      $stmt->bind_param('i', $klantId);
      $stmt->execute();
      $stmt->bind_result($OHdata, $OHtijd);
      $data = array();
      while($stmt->fetch()){
        $data['OHdata'] = $OHdata;
        $data['OHtijd'] = $OHtijd;
      }
      $stmt->close();

      return $data;
    }

    public function controlleerBezetteAfleverTijden($afleverDatum){
      $stmt = DB::conn()->prepare("SELECT `aflevertijd` FROM `Order` WHERE afleverdatum=?");
      $stmt->bind_param('s', $afleverDatum);
      $stmt->execute();
      $bezetteAfleverTijd = array();
      $stmt->bind_result($f);
      while($stmt->fetch()){
        $bezetteAfleverTijd[] = $f;
      }
      $stmt->close();

      return $bezetteAfleverTijd;
    }

    public function controlleerBezetteOphaalTijden($ophaalDatum){
      $stmt = DB::conn()->prepare("SELECT `ophaaltijd` FROM `Order` WHERE ophaaldatum=?");
      $stmt->bind_param('s', $ophaalDatum);
      $stmt->execute();
      $bezetteOphaalTijd = array();
      $stmt->bind_result($f);
      while($stmt->fetch()){
        $bezetteOphaalTijd[] = $f;
      }
      $stmt->close();

      return $bezetteOphaalTijd;
    }

    public function updateAfleverdatum($afleverDatum, $order){
      $stmt = DB::conn()->prepare("UPDATE `Order` SET afleverdatum=? WHERE id=?");
      $stmt->bind_param("si", $afleverDatum, $order);
      $stmt->execute();
      $stmt->close();
    }

    public function updateAfleverTijd($afleverTijd, $order){
      $stmt = DB::conn()->prepare("UPDATE `Order` SET aflevertijd=? WHERE id=?");
      $stmt->bind_param("si", $afleverTijd, $order);
      $stmt->execute();
      $stmt->close();
    }

    public function updateAfleverDatumTijd($afleverDatum, $afleverTijd, $order){
      $stmt = DB::conn()->prepare("UPDATE `Order` SET afleverdatum=?, aflevertijd=? WHERE id=?");
      $stmt->bind_param("ssi", $afleverDatum, $afleverTijd, $order);
      $stmt->execute();
      $stmt->close();
    }

    public function updateOphaalDatum($ophaalDatum, $order){
      $stmt = DB::conn()->prepare("UPDATE `Order` SET ophaaldatum=? WHERE id=?");
      $stmt->bind_param("si", $ophaalDatum, $order);
      $stmt->execute();
      $stmt->close();
    }

    public function getExemplaarId($order){
      $stmt = DB::conn()->prepare("SELECT exemplaarid FROM `Orderregel` WHERE orderid=?");
      $stmt->bind_param('i', $order);
      $stmt->execute();
      $stmt->bind_result($exemplaarId);
      $stmt->fetch();
      $stmt->close();

      return $exemplaarId;
    }

    public function getGereserveerdeExemplaren($exemplaarId){
      $stmt = DB::conn()->prepare("SELECT id FROM `Exemplaar` WHERE id=? AND reservering=1");
      $stmt->bind_param('i', $exemplaarId);
      $stmt->execute();
      $stmt->bind_result($exemplaar);
      while($stmt->fetch()){
        $exemplaren[] = $exemplaar;
      }
      $stmt->close();
      if(!empty($exemplaren)){
        return $exemplaren;
      }
    }

    public function updateOrderTotaal($bedrag, $order){
      $stmt = DB::conn()->prepare("UPDATE `Order` SET bedrag=? WHERE id=?");
      $stmt->bind_param('di', $bedrag, $order);
      $stmt->execute();
      $stmt->close();
    }
  }
