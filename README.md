# MyDatabase

MyDatabase é uma clase com um CRUD para realizar consultas simples em MySQL

## Instalação

Instale a versão de desenvolvimento

```bash
$ composer require 89bsilva/my-database:dev-master
```

## Primeiro Passo

Importar o autoload do composer

```php
<?php
require './vendor/autoload.php';
```

## Criando o obejto MyDatabase

###### Ao instânciar a classe você deve passar dois parâmetros:
1) Um array com o endereço do servidor na índice 0 e o número da porta no índice 1. Támbém é possível passar uma string somente com o endereço do servidor, nesse caso o número da porta será 3306.
2) Um array com o nome do banco no índice 0, usuário no índice 1, senha no índice 2 e p charset no índice 3. É possível informar somente o índice 0 (banco) e índice 1 (usuário) assim ficaria sem senha e charset="utf8"

###### Exemplo de conexão servidor: "localhost", porta: "3308", banco: "loja", usuário: "admin" senha: "123", charset: "utf8mb4"
```php
$db = new MyDatabase(
    Array(
        "localhost",
        "3308"
    ), 
    Array(
        "loja", 
        "admin",
        "123",
        "utf8mb4"
    )
);
```

## Operações

##### INSERT

Ao chamar o método insert() é necessário passar um array associativo onde, no(s) índice(s) representa(m) o(s) nome(s) da(s) coluna(s) e o(s) valor(es) representa(m) o(s) dado(s) que será(ão) inserido(s). 
Caso queira inserir mais de um dado na mesma tabela é só passar uma lista com arrays associativo.
O método retornará instância da classe Insert e através desse novo objeto deverá ser realizado a inserção.

| Métodos da Classe Insert            | Descrição                                                                                          | 
| ----------------------------------- | -------------------------------------------------------------------------------------------------- | 
| into(string $table)                 | Nome da tabela em que a inserção será realizada                                                    | 
| execute()                           | Executa o insert, retorna um int com o número de linhas que a inserção realizou                    | 

###### Exemplo: Cadastro na tabela "cliente" de um cliente e na tabela "produto" de dois produtos

```php
# Inserção de um item
$cliente  = Array(
        "nome"   => "Nome do Vendedor",
        "cidade" => "São Paulo",
        "CPF"    => 12345678900
);
$query = $db->insert($cliente)->into("cliente")->execute();

# Inserção de multiplos itens
$produtos  = Array(
    Array(
        "codigo"     => 123456
        "descricao"  => "Teclado",
        "preco"      => 59.99,
        "quantidade" => 0
    ),
    Array(
        "codigo"     => 234567,
        "descricao"  => "Monitor",
        "preco"      => 199.90,
        "quantidade" => 2
    )
);
$query = $db->insert($produtos)->into("produto")->execute();
```

##### SELECT

Ao chamar o método select() é necessário passar uma string com os nomes colunas que serão selecionadas separada por vírgula ou um array com os nomes das colunas. O método retornará instância da classe Select e através desse novo objeto deverá ser realizado a consulta.

| Métodos da Classe Select            | Descrição                                                                                          | 
| ----------------------------------- | -------------------------------------------------------------------------------------------------- | 
| into(string $table)                 | Nome da tabela em que a consulta será realizada                                                    | 
| where(string $conditions)           | Condição(ões) necessária(s) para montar a cláusula WHERE                                           | 
| order(string $column, string $sort) | Nome(s) da(s) coluna(s) pela qual a consulta será ordenada; Direção da ordenação                   | 
| limit(int $limit)                   | Limite da consulta. Se for passado 0 a consulta será sem limite, o limite padrão é 100             | 
| execute()                           | Executa o select, retorna um array                                                                 | 

###### Exemplo: Buscar na tabela "produto" todos os dados de produtos com preço menor que R$60 e ordernar por preço

```php
$produtos = $db->select("*")->from("produto")->where("preco < 60")->order("preco")->limit(0)->execute();
```

##### UPDATE

Ao chamar o método update() é necessário passar uma string com o nome da tabela que deverá ser atualizada. 
O método retornará instância da classe Update e através desse novo objeto deverá ser realizado a atualização.

| Métodos da Classe Update            | Descrição                                                                                          | 
| ----------------------------------- | -------------------------------------------------------------------------------------------------- | 
| set(array $data)                    | Nome da tabela em que deverá ser atualizada                                                        | 
| where(string $conditions)           | Condição para atualização                                                                          | 
| limit(int $limit)                   | Limite de atualização. Se for passado 0 a atualização será sem limite, o limite padrão é 1         | 
| execute()                           | Executa o update, retorna um int com o número de linhas que a atualização afetou                   | 

###### Exemplo: Atualizar na tabela "produto" a quantidade do produto com código 123456 para 5

```php
$atualizacao = $db->update("produto")->set(Array("quantidade" => 5))->where("codigo = 123456")->execute();
```

##### DELETE

Ao chamar o método delete() é necessário passar uma string com a(s) condição(ões) para localizar o(s) dado(s) que será(ão) deletado(s) 
O método retornará instância da classe Delete e através desse novo objeto deverá ser realizado a exclusão.

| Métodos da Classe Delete            | Descrição                                                                                          | 
| ----------------------------------- | -------------------------------------------------------------------------------------------------- | 
| in(string $table)                   | Nome da tabela onde o(s) dado(s) será(ão) excluído(s)                                              | 
| limit(int $limit)                   | Limite de exclusão. Se for passado 0 a exclusão será sem limite, o limite padrão é 1               | 
| execute()                           | Executa o delete, retorna um int com o número de linhas que o delete afetou                        | 

###### Exemplo: Deletar na tabela "produto" o produto com código 234567

```php
$exclusao = $db->delete("codigo = 234567")->in("produto")->execute();
```

### Autor

Bruno Silva Santana - <brunoss.789@gil.com> - <https://github.com/89bsilva>

### Licença

MyDatabase está licenciado sob a licença MIT - consulte o arquivo `LICENSE` para mais detalhes.
