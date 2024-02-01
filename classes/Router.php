<?php
    require_once './classes/Controller.php';

    class Router {
        public function getResponse(string $route) {
            $controller = new Controller();

            header('Content-Type: application/json');
            http_response_code(200);
            
            if (preg_match('/\/\?page=[0-9]+/', $route) ) {
                $controller->getPokemons();
            }

            if (preg_match('/\/pokemon\/.+/', $route) ) {
                $pokemonName = explode('/', substr($route, 1))[1];

                $controller->getPokemon($pokemonName);
            }
        }
    }
?>