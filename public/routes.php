<?php

error_reporting(-1);
require_once(__dir__ . '/../vendor/autoload.php');
require(__dir__ . '/../resources/config.php');


$klein = new Klein\Klein;

$klein->respond(function ($request, $response, $service) {
    $service->layout('../resources/layouts/default.php');
});


$klein->respond('/', function ($request, $response, $service) {
    $service->pageTitle = 'TempoVideo';
    $service->render(VIEWS.'/home.php');
});
$klein->respond('/film/aanbod', function ($request, $response, $service) {
  $service->layout('../resources/layouts/default.php');
});

$klein->respond('/film/aanbod', function ($request, $response, $service) {
    $service->pageTitle = 'Filmaanbod';
    $service->render(VIEWS.'/filmaanbod.php');
});


$klein->respond('/film/[:naam]', function ($request, $response, $service) {
    $service->layout('../resources/layouts/film.php');
});

$klein->respond('/film/[:naam]', function ($request, $response, $service) {
    $naam = $request->naam;
    $titelNaam = $naam;
    $titelNaam = str_replace('_', ' ', $titelNaam);
    $titelNaam = strtoupper($titelNaam);
    $service->pageTitle = $titelNaam;
    $service->filmNaam = $naam;
    $service->render(VIEWS.'/filmdetail.php');
});
$klein->respond('/film/aanbod', function ($request, $response, $service) {
    $service->pageTitle = 'Filmaanbod';
    $service->render(VIEWS.'/filmaanbod.php');
    $service->render(VIEWS.'/filmdetail.php');
});


$klein->respond('/eigenaar/overzicht', function ($request, $response, $service) {
    $service->pageTitle = 'Overzicht';
    $service->render(VIEWS.'/eigenaar_overzicht.php');
});

$klein->respond('/eigenaar/film_toevoegen', function ($request, $response, $service) {
    $service->pageTitle = 'Film toevoegen';
    $service->render(VIEWS.'/filmtoevoegen.php');
});

$klein->respond('/eigenaar/film_verwijderen', function ($request, $response, $service) {
    $service->pageTitle = 'Film verwijderen';
    $service->render(VIEWS.'/filmverwijderen.php');
});

$klein->respond('/eigenaar/film_aanpassen', function ($request, $response, $service) {
    $service->pageTitle = 'Film aanpassen';
    $service->render(VIEWS.'/filmaanpassen.php');
});

$klein->respond('/eigenaar/klant_blokkeren', function ($request, $response, $service) {
    $service->pageTitle = 'Klant blokkeren';
    $service->render(VIEWS.'/klantblokkeren.php');
});
$klein->respond('/eigenaar/klacht_afhandelen', function ($request, $response, $service) {
    $service->pageTitle = 'Klacht afhandelen';
    $service->render(VIEWS.'/eigenaar_klacht_afhandelen.php');
});

$klein->respond('/klant/overzicht', function ($request, $response, $service) {
    $service->pageTitle = 'Overzicht';
    $service->render(VIEWS.'/klant_overzicht.php');
});
$klein->respond('/klant/klacht_indienen', function ($request, $response, $service) {
    $service->pageTitle = 'Klacht indienen';
    $service->render(VIEWS.'/klant_klacht.php');
});

$klein->respond('/winkelmand/afrekenen', function ($request, $response, $service) {
    $service->pageTitle = 'Afrekenen';
    $service->render(VIEWS.'/afrekenen.php');
});

$klein->respond('/winkelmand', function ($request, $response, $service) {
    $service->pageTitle = 'Winkelmand';
    $service->render(VIEWS.'/winkelmand.php');
});

$klein->respond('/contact', function ($request, $response, $service) {
    $service->pageTitle = 'Contact';
    $service->render(VIEWS.'/contact.php');
});

$klein->respond('/login', function ($request, $response, $service) {
    $service->pageTitle = 'Login';
    $service->render(VIEWS.'/login.php');
});

$klein->respond('/registreer', function ($request, $response, $service) {
    $service->pageTitle = 'Registreer';
    $service->render(VIEWS.'/registreer.php');
});

$klein->respond('/baliemedewerker/bezorgdata', function ($request, $response, $service) {
    $service->pageTitle = 'Bezorgdata';
    $service->render(VIEWS.'/baliemedewerkerbezorgdata.php');
});

$klein->respond('/baliemedewerker/extraopties', function ($request, $response, $service) {
    $service->pageTitle = 'Extra opties';
    $service->render(VIEWS.'/baliemedewerkerextraopties.php');
});

$klein->respond('/uitloggen', function ($request, $response, $service) {
    $service->pageTitle = 'Uitloggen';
    $service->render(VIEWS.'/uitloggen.php');
});

$klein->respond('/baliemedewerker/overzicht', function ($request, $response, $service) {
    $service->pageTitle = 'Overzicht';
    $service->render(VIEWS.'/baliemedewerker_overzicht.php');
});

$klein->respond('/baliemedewerker/afhandelen', function ($request, $response, $service) {
    $service->pageTitle = 'Overzicht';
    $service->render(VIEWS.'/baliemedewerker_afhandelen.php');
});


$klein->respond('/cover/[:naam]', function ($request, $response, $service) {
    $naam = $request->naam;
    $path = FOTO . "/" . $naam;

    $filename = basename($path);
    $file_extension = strtolower(substr(strrchr($filename,"."),1));

    switch( $file_extension ) {
        case "gif": $ctype="image/gif"; break;
        case "png": $ctype="image/png"; break;
        case "jpeg":
        case "jpg": $ctype="image/jpeg"; break;
        default:
    }

    header('Content-Type:'.$ctype);
    header('Content-Length: ' . filesize($path));
    readfile($path);
});


$klein->onHttpError(function ($code, $router) {
    switch ($code) {
        case 404:
            $router->response()->body(
                '<h1>404 - Ik kan niet vinden waar u naar zoekt.</h1>'
            );
            break;
        case 405:
            $router->response()->body(
                '<h1>405 - U heeft geen toestemming hier te komen.</h1>'
            );
            break;
        default:
            $router->response()->body(
                '<h1>Oh nee, er is iets ergs gebeurt! Errorcode:'. $code .'</h1>'
            );
    }
});

$klein->dispatch();
