```
2 – Consumir a API do pokémon (https://pokeapi.co) e processar a resposta de acordo com os seguintes
    requisitos:
      1. Criar um endpoint para listar os pokémon.
      2. Caso não exista, gravar os registros da API em um arquivo .txt. Depois disso, as consultas devem
         retornar sempre os dados gravados e não os obtidos através da API.
      3. Exibir sempre 15 registros cada vez que o endpoint é chamado
      4. Deve ser possível paginar a consulta informado a página desejada, exemplo:
         localhost:8080/todos?page=7 retornará os registros da sétima página de pokémons.
      5. Os dados devem ser retornados no formato JSON e conter um array com o nome dos pokémon
         retornados pela API.

3 – Ainda seguindo na API do pokémon, vamos consultar as informações de um único pokémon:
      1. Criar um endpoint que vai receber o nome do pokémon (ex: localhost:8080/pokemon/pikachu) e
         retornar os dados do pokémon desejado.
      2. Os dados devem ser gravados em um arquivo .txt com o nome do respectivo pokémon, e uma vez
         criados, devem ser retornados os dados dos arquivos e não mais os obtidos através da API.
      3. Os dados devem ser retornados no formato JSON, e conter um objeto com os seguintes dados:
         ● Nome do pokémon
         ● Stats:
            ● [nome do stat]: [valor do stat]
            ● [nome do stat]: [valor do stat]
            ● [nome do stat]: [valor do stat]
