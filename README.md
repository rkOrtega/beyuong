# Instalação

1. Copiar environment

```sh
cp .env.example .env
```

2. Build docker

```sh
docker-compose up --build
```

3. Instalar dependências

```sh
docker-compose exec api composer install
```

4. Executar Migrations

```sh
docker exec api php artisan migrate
```

# Acessar o banco de dados

```sh
docker exec -it db cli
```

# Teste

Rota para cadastrar o pedido:
POST: 127.0.0.1/pedido
BODY EXEMPLO:

```json
{
  "cliente": {
    "nome": "João da Neves",
    "email": "joaozinho@hotmail.com",
    "cpf": "99000000000",
    "cep": "00000000"
  },
  "frete": "100.00",
  "itens": [
    {
      "sku": "MTC-6110",
      "descricao": "Um treco",
      "valor": "100.00",
      "quantidade": "1"
    },
    {
      "sku": "MTC-6111",
      "descricao": "Outro treco",
      "valor": "50.00",
      "quantidade": "2"
    }
  ]
}
```

Rota para ver o pedido cadastrado (trocar <ID_PEDIDO> pelo id do pedido cadastrado)
GET: 127.0.0.1/pedido/<ID_PEDIDO>

Na raiz do projeto tem a pasta postman, com o arquivo .json que pode ser inportado no postman para testar as rotas

# Testar via curl

1. Cadastrar pedido

```sh
curl --location --request POST '127.0.0.1/pedido' \
--header 'Content-Type: application/json' \
--data-raw '{
    "cliente": {
        "nome": "João da Neves",
        "email": "joaozinho@hotmail.com",
        "cpf": "99000000000",
        "cep": "00000000"
    },
    "frete": "100.00",
    "itens": [
        {
            "sku": "MTC-6110",
            "descricao": "Um treco",
            "valor": "100.00",
            "quantidade": "1"
        },
        {
            "sku": "MTC-6111",
            "descricao": "Outro treco",
            "valor": "50.00",
            "quantidade": "2"
        }
    ]
}'
```

2. Consultar pedido

```sh
curl --location --request GET '127.0.0.1/pedido/1'
```
