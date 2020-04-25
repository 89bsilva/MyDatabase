# MyDatabase

MyDatabase é uma clase com um CRUD para realizar consultas simples em MySQL

## Instalação

```bash
$ composer require 89bsilva/my-database
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
2) Um array com o nome do banco no índice 0, usuário no índice 1, senha no índice 2 e o charset no índice 3. É possível informar somente o índice 0 (banco) e índice 1 (usuário) assim ficaria sem senha e charset="utf8"

###### Exemplo de conexão servidor: "localhost", porta: "3308", banco: "loja", usuário: "admin" senha: "123", charset: "utf8mb4"
```php
# Conexão localhost com a porta do MySQL 3308 
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

# Conexão localhost com a porta do MySQL 3306 
$db2 = new MyDatabase(
    "localhost", 
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

Ao chamar o método insert(array $dados) é necessário passar um array associativo onde, no(s) índice(s) representa(m) o(s) nome(s) da(s) coluna(s) e o(s) valor(es) representa(m) o(s) dado(s) que será(ão) inserido(s). 
Caso queira inserir mais de um dado na mesma tabela é só passar uma lista com arrays associativo.
O método retornará instância da classe Insert e através desse novo objeto deverá ser realizado a inserção.

| Métodos da Classe Insert            | Descrição                                                                                          | 
| ----------------------------------- | -------------------------------------------------------------------------------------------------- | 
| into(string $tabela)                | Nome da tabela em que a inserção será realizada                                                    | 
| execute(): int                      | Executa o insert, retorna um int com o número de linhas que a inserção realizou                    | 

###### Exemplo 1: Cadastro de UM cliente na tabela "cliente"

```php
$cliente  = Array(
        "nome"   => "Nome do Vendedor",
        "cidade" => "São Paulo",
        "CPF"    => 12345678900
);
$query = $db->insert($cliente)->into("cliente")->execute();
```

###### Exemplo 2: Cadastro de DOIS produtos na tabela "produto"

```php
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

Ao chamar o método select(string $colunas) é necessário passar uma string com os nomes colunas que serão selecionadas separada por vírgula.
O método retornará instância da classe Select e através desse novo objeto deverá ser realizado a consulta.

| Métodos da Classe Select                     | Descrição                                                                                 | 
| -------------------------------------------- | ----------------------------------------------------------------------------------------- | 
| from(string $tabela)                         | Nome da tabela em que a consulta será realizada                                           | 
| where(string $coluna)                        | Nome da coluna que será utilizada para comparar                                           |
| between($valor1, $valor2)                    | Retorna registro(s) dentro do intervalo entre o $valor1 e $valor2                         | 
| notBetween($valor1, $valor2)                 | Retorna registro(s) fora do intervalo entre o $valor1 e $valor2                           |
| equals(mixed $valor)                         | Operador = será utilizado para comparar o valor informado                                 | 
| notEquals(mixed $valor)                      | Operador != será utilizado para comparar o valor informado                                | 
| less(mixed $valor)                           | Operador < será utilizado para comparar o valor informado                                 |
| lessEqual(mixed $valor)                      | Operador <= será utilizado para comparar o valor informado                                |
| bigger(mixed $valor)                         | Operador > será utilizado para comparar o valor informado                                 | 
| biggerEqual(mixed $valor)                    | Operador >= será utilizado para comparar o valor informado                                | 
| different(mixed $valor)                      | Operador <> será utilizado para comparar o valor informado                                | 
| and(string $coluna)                          | Condição AND será utilizada para comparar; Coluna da nova condição                        | 
| or(string $coluna)                           | Condição OR será utilizada para comparar; Coluna da nova condição                         |  
| in(array $lista)                             | Retorna registro(s) que estajam na lista                                                  |
| notIn(array $lista)                          | Retorna registro(s) que NÃO estajam na lista                                              |
| isNull()                                     | Procura o valor NULL na coluna da consulta                                                | 
| isNotNull()                                  | Procura valor DIFERENTE DE NULL na coluna da consulta                                     |
| like(string $valor)                          | Utiliza operador LIKE e insere o coringa "%" antes e depois do valor. Ex.: %$valor%       |
| likeStart(string $valor1, string $valor2)    | Utiliza operador LIKE e insere o coringa "%" depois do valor. Ex.: $valor%                |
| likeEnd(string $valor1, string $valor2)      | Utiliza operador LIKE e insere o coringa "%" antes do valor. Ex.: %$valor                 |
| likeStartEnd(string $valor1, string $valor2) | Utiliza operador LIKE e insere o coringa "%" entre valores. Ex.: $valor1%$valor2          |
| limit(int $limite)                           | Limite da consulta. Se for passado 0 a consulta será sem limite, o limite padrão é 100    | 
| order(string $coluna, string $sort)          | Nome(s) da(s) coluna(s) pela qual a consulta será ordenada; Direção da ordenação          |
| execute(): array                             | Executa o select, retorna uma lista com os registros do SELECT                            | 

###### Exemplo 1: Buscar na tabela "produto" TODOS os dados de produtos com QUANTIDADE MAIOR OU IGUAL 1 e com PREÇO MENOR que R$60 e ordernar por preço

```php
$produtos = $db->select("*")->from("produto")->where("quantidade")->biggerEqual(1)->and("preco")->less(60)->order("preco")->limit(0)->execute();
```
###### Exemplo 2: Buscar na tabela "cliente" o NOME dos clientes da cidade de São Paulo. No máximo 100 registros (limite padrão).

```php
$clientes = $db->select("nome")->from("cliente")->where("cidade")->equals("São Paulo")->execute();
```

##### UPDATE

Ao chamar o método update(string $tabela) é necessário passar uma string com o nome da tabela que deverá ser atualizada. 
O método retornará instância da classe Update e através desse novo objeto deverá ser realizado a atualização.

| Métodos da Classe Update                     | Descrição                                                                                 | 
| -------------------------------------------- | ----------------------------------------------------------------------------------------- | 
| set(array $dadosAtualizados)                 | Array associativo com o(s) dado(s) que será(ão) atualizado(s)                             |  
| where(string $coluna)                        | Nome da coluna que será utilizada para comparar                                           |
| between($valor1, $valor2)                    | Retorna registro(s) dentro do intervalo entre o $valor1 e $valor2                         | 
| notBetween($valor1, $valor2)                 | Retorna registro(s) fora do intervalo entre o $valor1 e $valor2                           |
| equals(mixed $valor)                         | Operador = será utilizado para comparar o valor informado                                 | 
| notEquals(mixed $valor)                      | Operador != será utilizado para comparar o valor informado                                | 
| less(mixed $valor)                           | Operador < será utilizado para comparar o valor informado                                 |
| lessEqual(mixed $valor)                      | Operador <= será utilizado para comparar o valor informado                                |
| bigger(mixed $valor)                         | Operador > será utilizado para comparar o valor informado                                 | 
| biggerEqual(mixed $valor)                    | Operador >= será utilizado para comparar o valor informado                                | 
| different(mixed $valor)                      | Operador <> será utilizado para comparar o valor informado                                | 
| and(string $coluna)                          | Condição AND será utilizada para comparar; Coluna da nova condição                        | 
| or(string $coluna)                           | Condição OR será utilizada para comparar; Coluna da nova condição                         |  
| in(array $lista)                             | Retorna registro(s) que estajam na lista                                                  |
| notIn(array $lista)                          | Retorna registro(s) que NÃO estajam na lista                                              |
| isNull()                                     | Procura o valor NULL na coluna da consulta                                                | 
| isNotNull()                                  | Procura valor DIFERENTE DE NULL na coluna da consulta                                     |
| like(string $valor)                          | Utiliza operador LIKE e insere o coringa "%" antes e depois do valor. Ex.: %$valor%       |
| likeStart(string $valor1, string $valor2)    | Utiliza operador LIKE e insere o coringa "%" depois do valor. Ex.: $valor%                |
| likeEnd(string $valor1, string $valor2)      | Utiliza operador LIKE e insere o coringa "%" antes do valor. Ex.: %$valor                 |
| likeStartEnd(string $valor1, string $valor2) | Utiliza operador LIKE e insere o coringa "%" entre valores. Ex.: $valor1%$valor2          |
| limit(int $limite)                           | Limite da consulta. Se for passado 0 a consulta será sem limite, o limite padrão é 100    |  
| execute(): int                               | Executa o update, retorna um int com o número de linhas que a atualização afetou          | 

###### Exemplo: Atualizar na tabela "produto" a quantidade do produto com código 123456 para 5

```php
$novoValor   = Array("quantidade" => 5);
$atualizacao = $db->update("produto")->set($novoValor)->where("codigo")->equals(123456)->execute();
```

##### DELETE

Ao chamar o método delete($tabela) é necessário passar uma string com o nome da tabela onde o(s) dado(s) será(ão) excluído(s).

coluna que será utilizada para localizar o(s) registro(s) que será(ão) deletado(s).
O método retornará instância da classe Delete e através desse novo objeto deverá ser realizado a exclusão.

| Métodos da Classe Delete                     | Descrição                                                                                 | 
| -------------------------------------------- | ----------------------------------------------------------------------------------------- |  
| where(string $coluna)                        | Nome da coluna que será utilizada para comparar                                           |
| between($valor1, $valor2)                    | Retorna registro(s) dentro do intervalo entre o $valor1 e $valor2                         | 
| notBetween($valor1, $valor2)                 | Retorna registro(s) fora do intervalo entre o $valor1 e $valor2                           |
| equals(mixed $valor)                         | Operador = será utilizado para comparar o valor informado                                 | 
| notEquals(mixed $valor)                      | Operador != será utilizado para comparar o valor informado                                | 
| less(mixed $valor)                           | Operador < será utilizado para comparar o valor informado                                 |
| lessEqual(mixed $valor)                      | Operador <= será utilizado para comparar o valor informado                                |
| bigger(mixed $valor)                         | Operador > será utilizado para comparar o valor informado                                 | 
| biggerEqual(mixed $valor)                    | Operador >= será utilizado para comparar o valor informado                                | 
| different(mixed $valor)                      | Operador <> será utilizado para comparar o valor informado                                | 
| and(string $coluna)                          | Condição AND será utilizada para comparar; Coluna da nova condição                        | 
| or(string $coluna)                           | Condição OR será utilizada para comparar; Coluna da nova condição                         |  
| in(array $lista)                             | Retorna registro(s) que estajam na lista                                                  |
| notIn(array $lista)                          | Retorna registro(s) que NÃO estajam na lista                                              |
| isNull()                                     | Procura o valor NULL na coluna da consulta                                                | 
| isNotNull()                                  | Procura valor DIFERENTE DE NULL na coluna da consulta                                     |
| like(string $valor)                          | Utiliza operador LIKE e insere o coringa "%" antes e depois do valor. Ex.: %$valor%       |
| likeStart(string $valor1, string $valor2)    | Utiliza operador LIKE e insere o coringa "%" depois do valor. Ex.: $valor%                |
| likeEnd(string $valor1, string $valor2)      | Utiliza operador LIKE e insere o coringa "%" antes do valor. Ex.: %$valor                 |
| likeStartEnd(string $valor1, string $valor2) | Utiliza operador LIKE e insere o coringa "%" entre valores. Ex.: $valor1%$valor2          |
| limit(int $limite)                           | Limite da consulta. Se for passado 0 a consulta será sem limite, o limite padrão é 100    |  
| execute()                                    | Executa o delete, retorna um int com o número de linhas que o delete afetou               | 

###### Exemplo: Deletar na tabela "produto" o produto com código 234567

```php
$exclusao = $db->delete("produto")->where("codigo")->equals(234567)->execute();
```

### Autor

Bruno Silva Santana - <brunoss.789@gil.com> - <https://github.com/89bsilva>

### Licença

MyDatabase está licenciado sob a licença MIT - consulte o arquivo `LICENSE` para mais detalhes.
