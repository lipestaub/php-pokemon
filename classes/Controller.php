<?php
    require_once './classes/RequestPokemonApi.php';

    class Controller {
        private RequestPokemonApi $requestPokemonApi;

        public function __construct()
        {
            $this->requestPokemonApi = new RequestPokemonApi();
        }

        public function getPokemons() {
            $message = 'Read from file.';

            $filePath = realpath('.') . '/files/all.txt';

            if (!file_exists($filePath)) {
                $limit = 150;

                $message = 'Fetched from API.';

                $response = $this->requestPokemonApi->getResponse("pokemon?limit=$limit");

                $data = array_map(function ($data) {
                    return $data["name"];
                }, $response['results']);

                file_put_contents($filePath, json_encode($data));
            }

            $fileContent = file_get_contents($filePath);

            $all = json_decode($fileContent, true);

            $page = (int)$_GET['page'] ?? 1;

            $resultsPerPage = 15;

            if ($page < 1) {
                $page = 1;
            }

            if ($page * $resultsPerPage > count($all)) {
                $page = ceil(count($all) / $resultsPerPage);
            }

            $data = array_slice($all, ($page - 1) * $resultsPerPage, $resultsPerPage);

            echo json_encode([
                'message' => $message,
                'page' => $page,
                'data' => $data
            ]);

            exit;
        }

        public function getPokemon(string $pokemonName) {
            $message = 'Read from file.';

            $filePath = realpath('.') . "/files/$pokemonName.txt";

            if (!file_exists($filePath)) {
                $message = 'Fetched from API.';

                $response = $this->requestPokemonApi->getResponse("pokemon/$pokemonName");

                $formatted = [
                    'name' => $response['name'],
                    'stats' => []
                ];

                foreach ($response['stats'] as $stat) {
                    $formatted['stats'][$stat['stat']['name']] = $stat['base_stat'];
                }

                file_put_contents($filePath, json_encode($formatted));
            }

            $fileContent = file_get_contents($filePath);

            echo json_encode([
                'message' => $message,
                'pokemon' => json_decode($fileContent)
            ]);

            exit;
        }
    }
?>