var server = require("./server");
var router = require("./router");
var requestHandlers = require("./requestHandlers");

var handle = {};
handle["/"] = requestHandlers.home;
handle["/filmaanbod"] = requestHandlers.filmaanbod;
handle["/inschrijven"] = requestHandlers.inschrijven;
handle["/inloggen"] = requestHandlers.inloggen;
handle["/tarieven"] = requestHandlers.tarieven;
handle["/over_ons"] = requestHandlers.over_ons;
handle["/contact"] = requestHandlers.contact;
handle["/film_details"] = requestHandlers.film_details;
handle["/inschrijven/betalen"] = requestHandlers.betalen;
handle["/winkelmand"] = requestHandlers.winkelmand;
handle["/winkelmand/betalen"] = requestHandlers.winkelmandBetalen;
handle['/overzicht'] = requestHandlers.overzichtGebruiker;
handle['/overzicht/gegevens_wijzigen'] = requestHandlers.gegevensWijzigen;
handle['/overzicht/gehuurde_films'] = requestHandlers.gehuurdeFilms;
handle['/eigenaar/overzicht'] = requestHandlers.overzichtEigenaar;
handle['/eigenaar/video_toevoegen'] = requestHandlers.videoToevoegen;
handle['/eigenaar/video_verwijderen'] = requestHandlers.videoVerwijderen;
handle['/eigenaar/video_informatie_wijzigen'] = requestHandlers.videoInformatieWijzigen;
handle['/eigenaar/klant_blokkeren'] = requestHandlers.klantBlokkeren;
console.log(handle);

server.start(router.route, handle);
