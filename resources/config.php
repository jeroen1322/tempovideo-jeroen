<?php

defined("LIBRARY_PATH")
    or define("LIBRARY_PATH", realpath(dirname(__FILE__) . '/library'));

defined("TEMPLATES_PATH")
    or define("TEMPLATES_PATH", realpath(dirname(__FILE__) . '/templates'));

defined("VIEWS")
    or define("VIEWS", realpath(dirname(__FILE__) . '/views'));

defined("MAIL")
    or define("MAIL", realpath(dirname(__FILE__) . '/mail'));

defined("FOTO")
    or define("FOTO", realpath(dirname(__FILE__) . '/storage/film_foto'));


error_reporting(-1);

?>
