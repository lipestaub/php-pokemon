<?php

# Leitura dos parâmetros do servidor. A variável $_SERVER é global do PHP e contém dados pertinentes ao servidor e
# à requisição que foi feita.

$method = $_SERVER['REQUEST_METHOD']; # Extração do método HTTP da requisição.
$route = $_SERVER['REQUEST_URI']; # Extração da rota para qual foi endereçada a requisição.

# Definição do tipo de retorno que vamos passar para o client. Esse cabeçalho ajuda o client a saber como tratar a
# resposta que for enviada pela nossa API.
header('Content-Type: application/json');

# Definição código de status HTTP. Tem por função principal informar ao client se houve algum erro durante a requisição.
# Exemplos:
#    - Códigos 2xx: indicam que a requisição foi bem sucedida e a resposta pode ser processada.
#    - Códigos 4xx: indicam que a requisição não está de acordo com o formato esperado pelo servidor para aquele
#                   endpoint/função/método.
#    - Códigos 5xx: indicam que houve algum erro interno do servidor durante o processamento da requisição pelo
#                   servidor. Normalmente acontecem por erros em rotinas do próprio servidor e não porque a requisição
#                    enviada não está de acordo com o que é esperado.
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

    # Mensagem para saber de onde os dados vieram.
    $message = 'Read from file.';

    # Aqui verificamos se o arquivo que salva os dados existe. Se não, indicado pela exclamação, entramos na sub-rotina
    # do if.
    if (!file_exists('all.txt')) {
        # Define o número de registros que queremos trazer da API do pokémon.
        $limit = 150;

        # Atualizando a mensagem para sabermos que a sub-rotina de busca da API rodou.
        $message = 'Fetched from API.';

        # Chamando o método que faz a consulta à API, passando o limite que definimos.
        $response = get("pokemon?limit=$limit");

        # Função utilizada para formatar os dados que obtivemos da API para um retorno mais próximo daquilo que
        # desejamos retornar na nossa própria API.
        $data = array_map(function ($data) {
            return $data["name"];
        }, $response['results']);

        # Escrevendo o nosso arquivo como todos os pokémon. Note que .txt é um arquivo de formatação livre, então
        # já converti os dados para JSON para facilitar busca e utilização mais a frente.
        file_put_contents('all.txt', json_encode($data));
    }

    # Buscando os dados do nosso arquivo criado.
    $fileContent = file_get_contents('all.txt');

    # Como os dados estão em JSON, vamos utilizar a função nativa para converter de volta à um array para facilitar a
    # manipulação.
    $todos = json_decode($fileContent, true);

    # Extraímos a página informada na busca. A variável $_GET é uma das variáveis globais do PHP, e carrega as informações
    # dos parâmetros que foram passados após o '?' em uma URL.
    $page = (int)$_GET['page'] ?? 1;

    # Definição de quantos resultados queremos por página
    $resultsPerPage = 15;

    # Validação para uma página mínima
    if ($page < 1) {
        $page = 1;
    }

    # Validação para uma página máxima. Primeiro é validado se o conjunto de dados do arquivo possui dados suficiente
    # para preencher a página desejada pelo usuário.
    if ($page * $resultsPerPage > count($todos)) {
        # Se não, é calculada a página máxima que teria resultados e é definida como a página escolhida.
        $page = ceil(count($todos) / $resultsPerPage);
    }

    # Função para pegar o subconjunto de dados da página informada pelo usuário
    $retorno = array_slice($todos, ($page - 1) * $resultsPerPage, $resultsPerPage);

    # Aqui são impressos os dados que coletamos no formato JSON e é isso aqui que o client receberá.
    echo json_encode([
        'message' => $message,
        'page' => $page,
        'data' => $retorno
    ]);
    exit;
}

# Função feita para buscar os dados da API do pokémon.
# O parâmetro endpoint indica qual dos recursos da API que deve ser chamado.
function get($endpoint): array
{
    # Como a parte que varia da nossa chamada é o endpoint, vamos setar a URL base para diminuirmos a probabilidade
    # de erros por digitar errado sempre que formos chamar esse método GET
    $base = 'https://pokeapi.co/api/v2';

    # Inicia o preparo da requisição à API pokémon.
    $ch = curl_init("$base/$endpoint");

    # Configurações diversas para a requisição.
    # Exemplo:
    #     - CURLOPT_RETURNTRANSFER: nos garante que vamos obter o resultado da chamada à API ao invés de somente
    #                               receber uma flag indicando se a chamada ocorreu corretamente ou não.
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    # Execução da chamada à API configurada acima.
    $response = curl_exec($ch);

    # Em caso de erro da chamada, ele é tratado aqui.
    if (curl_errno($ch)) {
        http_response_code(400);
        echo json_encode(['message' => 'Curl error: ' . curl_error($ch)]);
        exit;
    }

    # Fechamos a chamada que foi aberta e agora está concluída, para liberar recurso do servidor.
    curl_close($ch);

    # Retorno da resposta obtida da API para que possa ser tratada onde foi solicitada.
    return json_decode($response, true);
}

