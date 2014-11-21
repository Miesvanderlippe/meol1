<?php

require_once('../Slim/Slim.php');

\Slim\Slim::registerAutoloader();

$slim = new \Slim\Slim();

$slim->get('/', function(){
	print('Slim werkt');
});

$slim->get('/form', function(){
	$form = "<!DOCTYPE html><html><head><title>Opdracht 3.0</title></head><body><h2>Formulier</h2><form method='post' action='post'><table><tr><td>Voornaam</td><td><input type='text' name='voornaam' /></td></tr><tr><td>Tussenvoegsel</td><td><input type='text' name='tussenvoegsel' /></td></tr><tr><td>Achternaam</td><td><input type='text' name='achternaam' /></td></tr><tr><td>Studentnummer</td><td><input type='text' name='studentnummer' /></td></tr><tr><td>Email</td><td><input type='text' name='email' /></td></tr><tr><td>&nbsp;</td><td>&nbsp;</td></tr></tr><td>&nbsp;</td><td><input type='submit' value='verzenden' /></td></tr></table></form></body></html>";
	print($form);
});

$slim->post("/post", function() use($slim) {
        print('<h2>Gegevens</h2>');
        print('Voornaam: ' . $slim->request()->post('voornaam') . '<br />');
        print('Tussenvoegsel: ' . $slim->request()->post('tussenvoegsel') . '<br />');
        print('Achternaam: ' . $slim->request()->post('achternaam') . '<br />');
        print('Studentnummer: ' . $slim->request()->post('studentnummer') . '<br />');
        print('Email: ' . $slim->request()->post('email') . '<br />');
    }
);

$slim->run();