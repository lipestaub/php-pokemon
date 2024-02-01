<?php
    require_once './classes/RequestPokemonApi.php';

    $requestPokemonApi = new RequestPokemonApi();

    $method = $_SERVER['REQUEST_METHOD']; # Extração do método HTTP da requisição.
    $route = $_SERVER['REQUEST_URI']; # Extração da rota para qual foi endereçada a requisição.

    # Definição do tipo de retorno que vamos passar para o client. Esse cabeçalho ajuda o client a saber como tratar a
    # resposta que for enviada pela nossa API.
    header('Content-Type: application/json');

    # Definição código de status HTTP. Tem por função principal informar ao client se houve algum erro ou comportamento inesperado
    # durante a requisição.
    #
    # Exemplos:
    #    - Códigos 2xx: indicam que a requisição foi bem sucedida e a resposta pode ser processada.
    #    - Códigos 3xx: indicam que o recurso que foi solicitado não se econtra mais no endereço que a pessoa informou/foi
    #                   movido.
    #    - Códigos 4xx: indicam que a requisição não está de acordo com o formato esperado pelo servidor para aquele
    #                   endpoint/função/método.
    #    - Códigos 5xx: indicam que houve algum erro interno do servidor durante o processamento da requisição pelo
    #                   servidor. Normalmente acontecem por erros em rotinas do próprio servidor e não porque a requisição
    #                   enviada não está de acordo com o que é esperado.
    #
    # O código 200 - OK é informado supondo que tudo correrá bem durante a execução das rotinas, mas pode ser sobreescrito
    # ao longo do processo para outro código em caso de comportamentos adversos.
    http_response_code(200);

    # Validação do método HTTP informado pela requisição. Nossos endpoints esperam que a requisição seja feita pelo método
    # GET.
    if ($method !== 'GET') {
        # Se o método não for o correto, informamos o status HTTP 400 - Bad Request, para requisição mal-formatada.
        http_response_code(400);

        # Imprimimos um retorno na resposta para indicar qual o erro que ocorreu durante o processamento da requisição.
        echo json_encode(['message' => 'Invalid method provided.']);
        exit;
    }

    # DESAFIO NÚMERO 2
    if (preg_match('/\/\?page=[0-9]+/', $route) ) {
        $message = 'Read from file.';

        if (!file_exists('all.txt')) {
            $limit = 150;

            $message = 'Fetched from API.';

            $response = $requestPokemonApi->getPokemons($limit);

            $data = array_map(function ($data) {
                return $data["name"];
            }, $response['results']);

            file_put_contents('all.txt', json_encode($data));
        }

        $fileContent = file_get_contents('all.txt');

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

    # DESAFIO NÚMERO 3
    if (preg_match('/\/pokemon\/.+/', $route) ) {
        $message = 'Read from file.';

        $pokemonName = explode('/', substr($route, 1))[1];

        if (!file_exists("$pokemonName.txt")) {
            $message = 'Fetched from API.';

            $response = $requestPokemonApi->getPokemon($pokemonName);

            $formatted = [
                'name' => $response['name'],
                'stats' => []
            ];

            foreach ($response['stats'] as $stat) {
                $formatted['stats'][$stat['stat']['name']] = $stat['base_stat'];
            }

            file_put_contents("$pokemonName.txt", json_encode($formatted));
        }

        $fileContent = file_get_contents("$searched.txt");

        echo json_encode([
            'message' => $message,
            'pokemon' => json_decode($fileContent)
        ]);
        
        exit;
    }
?>