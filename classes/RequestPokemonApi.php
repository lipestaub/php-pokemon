<?php
    class RequestPokemonApi {
        private string $baseRoute;

        public function __construct() {
            $this->baseRoute = 'https://pokeapi.co/api/v2';
        }

        public function getPokemons(int $limit) {
            $url = $this->baseRoute . "/pokemon?limit=$limit";

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_RETURNTRANSFER => true,
            ]);

            $response = curl_exec($curl);

            if (curl_errno($curl)) {
                http_response_code(400);
                echo json_encode(['message' => 'Curl error: ' . curl_error($curl)]);
                exit;
            }

            curl_close($curl);

            $decoded = json_decode($response, true);

            if (!$decoded) {
                http_response_code(404);
                echo json_encode(['message' => 'Data not found']);
                exit;
            }

            return $decoded;
        }

        public function getPokemon(string $pokemonName) {
            $url = $this->baseRoute . "/pokemon/$pokemonName";

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_RETURNTRANSFER => true,
            ]);

            $response = curl_exec($curl);

            if (curl_errno($curl)) {
                http_response_code(400);
                echo json_encode(['message' => 'Curl error: ' . curl_error($curl)]);
                exit;
            }

            curl_close($curl);

            $decoded = json_decode($response, true);

            if (!$decoded) {
                http_response_code(404);
                echo json_encode(['message' => 'Data not found']);
                exit;
            }

            return $decoded;
        }
    }
?>