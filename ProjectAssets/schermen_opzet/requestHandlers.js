fs = require("fs");

function home(res, ontvangenData){
  // var body = '<html>' +
  //            '<head>' +
  //            '<meta http-equiv="Content-Type" contact="text/html; charset=UTF-8"/>' +
  //            '<title>HOME</title>' +
  //            '</head>' +
  //            '<body>' +
  //            '<h1 style="text-align: center;">TempoVideo</h1>'+
  //            '<form action="/winkelmand" method="POST" style="float: right;">' +
  //            '<input type="submit" value="Winkelmand"/>'+
  //            '</form>'+
  //            '<form action="/filmaanbod" method="POST">' +
  //            '<input type="submit" value="Filmaanbod"/>'+
  //            '</form>'+
  //            '<form action="/inschrijven" method="POST">' +
  //            '<input type="submit" value="Inschrijven"/>'+
  //            '</form>'+
  //            '<form action="/inloggen" method="POST">' +
  //            '<input type="submit" value="Inloggen"/>'+
  //            '</form>'+
  //            '<form action="/tarieven" method="POST">' +
  //            '<input type="submit" value="Tarieven"/>'+
  //            '</form>'+
  //            '<form action="/over_ons" method="POST">' +
  //            '<input type="submit" value="Over ons"/>'+
  //            '</form>'+
  //            '<form action="/contact" method="POST">' +
  //            '<input type="submit" value="Contact"/>'+
  //            '</form>'+
  //            '</body>'+
  //            '</html>';
  fs.readFile('./home.php', function (err, data) {
   if (err) {
      return console.error(err);
   }
   console.log("Home");
   res.writeHead(200, {"Content-Type":"text/html"});
   res.write(data);
   res.end();
});

}
function filmaanbod(res){
  var body = '<html>' +
             '<head>' +
             '<title>FILMAANBOD</title>' +
             '</head>' +
             '<body>' +
             '<h1 style="text-align: center;">FILMAANBOD</h1>'+
             '<form action="/" method="POST">' +
             '<input type="submit" value="HOME"/>'+
             '</form>'+
             '<form action="/film_details" method="POST">' +
             '<input type="submit" value="Film details"/>'+
             '</form>'+
             '</body>'+
             '</html>';

  console.log("Filmaanbod");
  res.writeHead(200, {"Content-Type":"text/html"});
  res.write(body);
  res.end();
}

function inschrijven(res){
  var body = '<html>' +
             '<head>' +
             '<meta http-equiv="Content-Type" contact="text/html; charset=UTF-8"/>' +
             '<title>INSCHRIJVEN</title>' +
             '</head>' +
             '<body>' +
             '<h1 style="text-align: center;">INSCHRIJVEN</h1>'+
             '<form action="/" method="POST">' +
             '<input type="submit" value="Home"/>'+
             '</form>'+
             '<form action="/inschrijven/betalen" method="POST">' +
             '<input type="submit" value="Betalen"/>'+
             '</form>'+
             '</body>'+
             '</html>';
   console.log("Inschrijven");
   res.writeHead(200, {"Content-Type":"text/html"});
   res.write(body);
   res.end();
}

function inloggen(res){
  var body = '<html>' +
             '<head>' +
             '<meta http-equiv="Content-Type" contact="text/html; charset=UTF-8"/>' +
             '<title>INLOGGEN</title>' +
             '</head>' +
             '<body>' +
             '<h1 style="text-align: center;">INLOGGEN</h1>'+
             '<form action="/" method="POST">' +
             '<input type="submit" value="Home"/>'+
             '</form>'+
             '</body>'+
             '</html>';
   console.log("Inloggen");
   res.writeHead(200, {"Content-Type":"text/html"});
   res.write(body);
   res.end();
}

function tarieven(res){
  var body = '<html>' +
             '<head>' +
             '<meta http-equiv="Content-Type" contact="text/html; charset=UTF-8"/>' +
             '<title>TARIEVEN</title>' +
             '</head>' +
             '<body>' +
             '<h1 style="text-align: center;">TARIEVEN</h1>'+
             '<form action="/" method="POST">' +
             '<input type="submit" value="Home"/>'+
             '</form>'+
             '</body>'+
             '</html>';
   console.log("Inschrijven");
   res.writeHead(200, {"Content-Type":"text/html"});
   res.write(body);
   res.end();
}

function overOns(res){
  var body = '<html>' +
             '<head>' +
             '<meta http-equiv="Content-Type" contact="text/html; charset=UTF-8"/>' +
             '<title>OVER ONS</title>' +
             '</head>' +
             '<body>' +
             '<h1 style="text-align: center;">OVER ONS</h1>'+
             '<form action="/" method="POST">' +
             '<input type="submit" value="Home"/>'+
             '</form>'+
             '</body>'+
             '</html>';
   console.log("overOns");
   res.writeHead(200, {"Content-Type":"text/html"});
   res.write(body);
   res.end();
}

function contact(res){
  var body = '<html>' +
             '<head>' +
             '<meta http-equiv="Content-Type" contact="text/html; charset=UTF-8"/>' +
             '<title>CONTACT</title>' +
             '</head>' +
             '<body>' +
             '<h1 style="text-align: center;">CONTACT</h1>'+
             '<form action="/" method="POST">' +
             '<input type="submit" value="Home"/>'+
             '</form>'+
             '</body>'+
             '</html>';
   console.log("contact");
   res.writeHead(200, {"Content-Type":"text/html"});
   res.write(body);
   res.end();
}

function filmDetails(res){
  var body = '<html>' +
             '<head>' +
             '<meta http-equiv="Content-Type" contact="text/html; charset=UTF-8"/>' +
             '<title>FILM DETAILS</title>' +
             '</head>' +
             '<body>' +
             '<h1 style="text-align: center;">FILM DETAILS</h1>'+
             '<form action="/" method="POST">' +
             '<input type="submit" value="Home"/>'+
             '</form>'+
             '<form action="/filmaanbod" method="POST">' +
             '<input type="submit" value="Terug naar filmaanbod"/>'+
             '</form>'+
             '</body>'+
             '</html>';
   console.log("Film details");
   res.writeHead(200, {"Content-Type":"text/html"});
   res.write(body);
   res.end();
}

function betalen(res){
  var body = '<html>' +
             '<head>' +
             '<meta http-equiv="Content-Type" contact="text/html; charset=UTF-8"/>' +
             '<title>BETALEN</title>' +
             '</head>' +
             '<body>' +
             '<h1 style="text-align: center;">BETALEN</h1>'+
             '<form action="/" method="POST">' +
             '<input type="submit" value="Home"/>'+
             '</form>'+
             '<form action="/" method="POST">' +
             '<input type="submit" value="Afronden en terug naar home"/>'+
             '</form>'+
             '</body>'+
             '</html>';
   console.log("Betalen");
   res.writeHead(200, {"Content-Type":"text/html"});
   res.write(body);
   res.end();
}

function winkelmand(res){
  var body = '<html>' +
             '<head>' +
             '<meta http-equiv="Content-Type" contact="text/html; charset=UTF-8"/>' +
             '<title>WINKELMAND</title>' +
             '</head>' +
             '<body>' +
             '<h1 style="text-align: center;">WINKELMAND</h1>'+
             '<button style="float: right;">Bezorgtijd selecteren</button>'+
             '<br><br><button style="float: right;">Ophaaltijd selecteren</button>'+
             '<form action="/winkelmand/betalen" method="POST">' +
             '<input type="submit" value="Betalen"/>'+
             '</form>'+
             '</form>'+
             '<form action="/" method="POST">' +
             '<input type="submit" value="Home"/>'+
             '</form>'+
             '</body>'+
             '</html>';
   console.log("Winkelmand");
   res.writeHead(200, {"Content-Type":"text/html"});
   res.write(body);
   res.end();
}

function winkelmandBetalen(res){
  var body = '<html>' +
             '<head>' +
             '<meta http-equiv="Content-Type" contact="text/html; charset=UTF-8"/>' +
             '<title>BETALEN</title>' +
             '</head>' +
             '<body>' +
             '<h1 style="text-align: center;">BETALEN</h1>'+
             '<form action="/" method="POST">' +
             '<input type="submit" value="Home"/>'+
             '</form>'+
             '</body>'+
             '</html>';
   console.log("Betalen");
   res.writeHead(200, {"Content-Type":"text/html"});
   res.write(body);
   res.end();
}

function overzichtGebruiker(res){
  var body = '<html>' +
             '<head>' +
             '<meta http-equiv="Content-Type" contact="text/html; charset=UTF-8"/>' +
             '<title>OVERZICHT</title>' +
             '</head>' +
             '<body>' +
             '<h1 style="text-align: center;">OVERZICHT</h1>'+
             '<form action="/overzicht/gegevens_wijzigen" method="POST">' +
             '<input type="submit" value="Gegevens wijzigen"/>'+
             '</form>'+
             '<form action="/overzicht/gehuurde_films" method="POST">' +
             '<input type="submit" value="Gehuurde films"/>'+
             '</form>'+
             '<form action="/" method="POST">' +
             '<input type="submit" value="Home"/>'+
             '</form>'+
             '</body>'+
             '</html>';
   console.log("Betalen");
   res.writeHead(200, {"Content-Type":"text/html"});
   res.write(body);
   res.end();
}

function gegevensWijzigen(res){
  var body = '<html>' +
             '<head>' +
             '<meta http-equiv="Content-Type" contact="text/html; charset=UTF-8"/>' +
             '<title>GEGEVENS</title>' +
             '</head>' +
             '<body>' +
             '<h1 style="text-align: center;">GEGEVENS WIJZIGEN</h1>'+
             '<form action="/overzicht" method="POST">' +
             '<input type="submit" value="Overzicht"/>'+
             '</form>'+
             '</body>'+
             '</html>';
   console.log("Gegevens wijzigen");
   res.writeHead(200, {"Content-Type":"text/html"});
   res.write(body);
   res.end();
}

function gehuurdeFilms(res){
  var body = '<html>' +
             '<head>' +
             '<meta http-equiv="Content-Type" contact="text/html; charset=UTF-8"/>' +
             '<title>Gehuurde Films</title>' +
             '</head>' +
             '<body>' +
             '<h1 style="text-align: center;">GEHUURDE FILMS</h1>'+
             '<form action="/overzicht" method="POST">' +
             '<input type="submit" value="Overzicht"/>'+
             '</form>'+
             '</body>'+
             '</html>';
   console.log("Gehuurde Films");
   res.writeHead(200, {"Content-Type":"text/html"});
   res.write(body);
   res.end();
}

function overzichtEigenaar(res){
  var body = '<html>' +
             '<head>' +
             '<meta http-equiv="Content-Type" contact="text/html; charset=UTF-8"/>' +
             '<title>OVERZICHT EIGENAAR</title>' +
             '</head>' +
             '<body>' +
             '<h1 style="text-align: center;">OVERZICHT EIGENAAR</h1>'+
             '<form action="/eigenaar/video_toevoegen" method="POST">' +
             '<input type="submit" value="Video toevoegen"/>'+
             '</form>'+
             '<form action="/eigenaar/video_verwijderen" method="POST">' +
             '<input type="submit" value="Video verwijderen"/>'+
             '</form>'+
             '<form action="/eigenaar/video_informatie_wijzigen" method="POST">' +
             '<input type="submit" value="Video informatie wijzigen"/>'+
             '</form>'+
             '<form action="/eigenaar/klant_blokkeren" method="POST">' +
             '<input type="submit" value="Klant blokkeren"/>'+
             '</form>'+
             '<form action="/" method="POST">' +
             '<input type="submit" value="Home"/>'+
             '</form>'+
             '</body>'+
             '</html>';
   console.log("Overzicht Eigenaar");
   res.writeHead(200, {"Content-Type":"text/html"});
   res.write(body);
   res.end();
}
function videoToevoegen(res){
  var body = '<html>' +
             '<head>' +
             '<meta http-equiv="Content-Type" contact="text/html; charset=UTF-8"/>' +
             '<title>VIDEO TOEVOEGEN</title>' +
             '</head>' +
             '<body>' +
             '<h1 style="text-align: center;">VIDEO TOEVOEGEN</h1>'+
             '<form action="/eigenaar/overzicht" method="POST">' +
             '<input type="submit" value="Overzicht"/>'+
             '</form>'+
             '</body>'+
             '</html>';
   console.log("Overzicht Eigenaar");
   res.writeHead(200, {"Content-Type":"text/html"});
   res.write(body);
   res.end();
}

function videoVerwijderen(res){
  var body = '<html>' +
             '<head>' +
             '<meta http-equiv="Content-Type" contact="text/html; charset=UTF-8"/>' +
             '<title>VIDEO VERWIJDEREN</title>' +
             '</head>' +
             '<body>' +
             '<h1 style="text-align: center;">VIDEO VERWIJDEREN</h1>'+
             '<form action="/eigenaar/overzicht" method="POST">' +
             '<input type="submit" value="Overzicht"/>'+
             '</form>'+
             '</body>'+
             '</html>';
   console.log("Overzicht Eigenaar");
   res.writeHead(200, {"Content-Type":"text/html"});
   res.write(body);
   res.end();
}

function videoInformatieWijzigen(res){
  var body = '<html>' +
             '<head>' +
             '<meta http-equiv="Content-Type" contact="text/html; charset=UTF-8"/>' +
             '<title>VIDEO INFORMATIE WIJZIGEN</title>' +
             '</head>' +
             '<body>' +
             '<h1 style="text-align: center;">VIDEO INFORMATIE WIJZIGEN</h1>'+
             '<form action="/eigenaar/overzicht" method="POST">' +
             '<input type="submit" value="Overzicht"/>'+
             '</form>'+
             '</body>'+
             '</html>';
   console.log("Overzicht Eigenaar");
   res.writeHead(200, {"Content-Type":"text/html"});
   res.write(body);
   res.end();
}

function klantBlokkeren(res){
  var body = '<html>' +
             '<head>' +
             '<meta http-equiv="Content-Type" contact="text/html; charset=UTF-8"/>' +
             '<title>KLANT BLOKKEREN</title>' +
             '</head>' +
             '<body>' +
             '<h1 style="text-align: center;">KLANT BLOKKEREN</h1>'+
             '<form action="/eigenaar/overzicht" method="POST">' +
             '<input type="submit" value="Overzicht"/>'+
             '</form>'+
             '</body>'+
             '</html>';
   console.log("Overzicht Eigenaar");
   res.writeHead(200, {"Content-Type":"text/html"});
   res.write(body);
   res.end();
}

exports.home = home;
exports.filmaanbod = filmaanbod;
exports.inschrijven = inschrijven;
exports.inloggen = inloggen;
exports.tarieven = tarieven;
exports.over_ons = overOns;
exports.contact = contact;
exports.film_details = filmDetails;
exports.betalen = betalen;
exports.winkelmand = winkelmand;
exports.winkelmandBetalen = winkelmandBetalen;
exports.overzichtGebruiker = overzichtGebruiker;
exports.gegevensWijzigen = gegevensWijzigen;
exports.gehuurdeFilms = gehuurdeFilms;
exports.overzichtEigenaar = overzichtEigenaar;
exports.videoToevoegen = videoToevoegen;
exports.videoVerwijderen = videoVerwijderen;
exports.videoInformatieWijzigen = videoInformatieWijzigen;
exports.klantBlokkeren = klantBlokkeren;
