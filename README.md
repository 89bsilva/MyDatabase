# MyDatabase

MyDatabase é uma ferramenta desenvolvida em PHP para realizar operações muito simples em base de dados MySQL

# Instalação

```bash
$ composer require 89bsilva/my-database
```

# Primeiro Passo

Importar o autoload do composer

```php
<?php
require './vendor/autoload.php';
```

# Criando o obejto MyDatabase

Ao instânciar a classe você deve passar dois parâmetros:

```php
<?php
$primeiroParametro  = Array(
    [0] => "enderecoServidor"  //  Obrigatório
    [1] => "numeroDaPorta"     //  Obrigatório
);
# ou
$primeiroParametro = (string) "enderecoServidor"; //Nesse caso o número da porta será 3306.  
```

```php
<?php
$segundoParametro  = Array(
    [0] => "nomeDoBanco"  //  Obrigatório
    [1] => "usuario"      //  Obrigatório
    [2] => "senha"        //  Opcional - Valor Padrão : ""
    [3] => "charset"      //  Opcional - Valor Padrão : "utf8"
    [4] => "collation"    //  Opcional - Valor Padrão : "general_ci"
    [5] => "engine"       //  Opcional - Valor Padrão : "InnoDB"
);   
```

#### Exemplos:

Criando conexão no servidor: **"localhost"**, porta: **"3308"**, banco: **"loja"**, usuário: **"admin"**, senha: **"123"**, charset: **"utf8mb4"**, collation: **"unicode_ci"** e engine: **"MyISAM"**

```php
<?php
$db = new MyDatabase(
    Array(
        "localhost",
        "3308"
    ), 
    Array(
        "loja", 
        "admin",
        "123",
        "utf8mb4",
        "unicode_ci",
        "MyISAM"
    )
);
```

Criando conexão no servidor: **"localhost"**, porta: **"3306"**, banco: **"loja"**, usuário: **"admin"**, senha: **""**, charset: **"utf8"**, collation: **"general_ci"** e engine: **"InnoDB"**

```php
<?php
$db = new MyDatabase(
    "localhost", 
    Array(
        "loja", 
        "admin"
    )
);
``` 

# MyDatabase

Métodos da classe MyDatabase

##### _table(**$tabela**)_

**@param**  string  **$tabela**  Nome da tabela que deseja realizar alguma(s) alteração(ões)
**@return**  Objeto da classe **[Table](#table)**

##### _insert(**$dados**)_

**@param**  array  **$dados**  Dados que será(ão) inserido(s)
**@return**  Objeto da classe **[Insert](#insert)** 


##### _select(**$coluna [, $coluna, $...]**)_

**@param**  string  **$coluna**  Declaração com a(s) coluna(s) que será(ão) selecionada(s)
**@return**  Objeto da classe **[Select](#select)** 

##### _update(**$tabela**)_

**@param**  string  **$tabela**  Nome da tabela que contém o(s) registro(s) será(ão) atualizado(s)
**@return**  Objeto da classe **[Update](#update)**

##### _delete(**$tabela**)_

**@param**  string  **$tabela**  Nome da tabela que contém o(s) registro(s) será(ão) deletado(s)
**@return**  Objeto da classe **[Delete](#delete)** 

# Table

Métodos da classe Table

##### _addColumn(**$coluna**)_

**@param**  string  **$coluna**  Nome da coluna que será adicionada
**@return**  Objeto atual

##### _dropColumn(**$coluna**)_

**@param**  string  **$coluna**  Nome da coluna que será removida
**@return**  bool  **true** se deletou a coluna **false** caso contrário 

##### _changeColumn(**$coluna**, **$novoNome**)_

**@param**  string  **$coluna**  Nome da coluna que será modificada
**@param**  string  **$novoNome** (Opcional)  Novo nome da coluna que será modificada
**@return**  Objeto atual  

##### _showColumn(**$coluna**)_

**@param**  string  **$coluna**  Nome da coluna que será obtida as informações
**@return**  array
```php
<?php
// Caso a tabela ou a coluna não exista na tabela um array vazio será retornado
// Se a coluna existir será retornado um array com as informações dessa coluna
array(
    'Field'   =>'nome da coluna',
    'Type'    => 'tipo da coluna',
    'Null'    => 'se a coluna pode receber valor nulo',
    'Key'     => 'indice presente na coluna',
    'Default' => 'valor padro da coluna',
    'Extra'   => 'extra'
);
```   

##### _showColumns()_

**@return**  array
```php
<?php
// Caso não exista a tabela um array vazio será retornado
// Se a tabela existir será retornado um array com as informações de todas as colunas dessa tabela
array(
    0 => array(
        'Field'   => 'nome da coluna',
        'Type'    => 'tipo da coluna',
        'Null'    => 'se a coluna pode receber valor nulo',
        'Key'     => 'indice presente na coluna',
        'Default' => 'valor padro da coluna',
        'Extra'   => 'extra'
    ),
    1 => array(
        'Field'   => 'nome da coluna',
        'Type'    => 'tipo da coluna',
        'Null'    => 'se a coluna pode receber valor nulo',
        'Key'     => 'indice presente na coluna',
        'Default' => 'valor padro da coluna',
        'Extra'   => 'extra'
    ),
    2 => array(
        'Field'   => 'nome da coluna',
        'Type'    => 'tipo da coluna',
        'Null'    => 'se a coluna pode receber valor nulo',
        'Key'     => 'indice presente na coluna',
        'Default' => 'valor padro da coluna',
        'Extra'   => 'extra'
    ),
    ...
);
```  

##### _int(**$tamanho**)_

**@param**  int  **$tamanho**  Tamanho máximo para coluna
**@return**  Objeto atual
Obs.: **addColumn()** ou **changeColumn()** DEVE ser chamado antes desse método
Define o tipo: INT e tamanho: $tamanho para coluna que será adicionada ou alterada

##### _bool()_

**@return**  Objeto atual
Obs.: **addColumn()** ou **changeColumn()** DEVE ser chamado antes desse método
Define o tipo: TINYINT e tamanho: 1 para coluna que será adicionada ou alterada

##### _float(**$precisao**)_

**@param**  int|string  **$precisao**  Precisão para o armazenamento do tipo Float
**@return**  Objeto atual
Obs.: **addColumn()** ou **changeColumn()** DEVE ser chamado antes desse método 
Define o tipo: FLOAT e com precisão: $precisao para coluna que será adicionada ou alterada

##### _double(**$precisao**)_

**@param**  int|string  **$precisao**  Precisão para o armazenamento do tipo Double
**@return**  Objeto atual
Obs.: **addColumn()** ou **changeColumn()** DEVE ser chamado antes desse método 
Define o tipo: DOUBLE e com precisão: $precisao para coluna que será adicionada ou alterada

##### _text()_

**@return**  Objeto atual
Obs.: **addColumn()** ou **changeColumn()** DEVE ser chamado antes desse método 
Define o tipo: TEXT para coluna que será adicionada ou alterada

##### _mediumtext()_

**@return**  Objeto atual
Obs.: **addColumn()** ou **changeColumn()** DEVE ser chamado antes desse método 
Define o tipo: MEDIUMTEXT para coluna que será adicionada ou alterada

##### _longtext()_

**@return**  Objeto atual
Obs.: **addColumn()** ou **changeColumn()** DEVE ser chamado antes desse método 
Define o tipo: LONGTEXT para coluna que será adicionada ou alterada

##### _varchar(**$tamanho**)_

**@param**  int  **$tamanho**  Tamanho máximo para coluna
**@return**  Objeto atual
Obs.: **addColumn()** ou **changeColumn()** DEVE ser chamado antes desse método 
Define o tipo: VARCHAR e tamanho: $tamanho para coluna que será adicionada ou alterada

##### _timestamp()_

**@param**  int  **$tamanho**  Tamanho máximo para coluna
**@return**  Objeto atual
Obs.: **addColumn()** ou **changeColumn()** DEVE ser chamado antes desse método 
Define o tipo: TIMESTAMP para coluna que será adicionada ou alterada

##### _addTimes(**$ptBR**)_

**@param**  bool  **$ptBR**  (Opcional) Caso seja true os nomes das colunas serão (criado_em, atualizado_em)
**@return**  Objeto atual
Obs.: **addColumn()** ou **changeColumn()** DEVE ser chamado antes desse método
Adiciona na tabela duas colunas do tipo timestamp (created_at, updated_at).
As colunas terão valor padrão CURRENT_TIMESTAMP
A coluna updated_at será atualizada automáticamente com CURRENT_TIMESTAMP quando a tabela for atualizada

##### _default(**$valorPadrao**)_

**@param**  int|string  **$valorPadrao**  Valor padrão da coluna que será criada/alterada
**@return**  Objeto atual
Obs.: **addColumn()** ou **changeColumn()** DEVE ser chamado antes desse método

##### _after(**$coluna**)_

**@param**  int|string  **$coluna**  Nome da coluna que antecede a nova coluna a ser adicionada ou movida
**@return**  Objeto atual
Obs.: **addColumn()** ou **changeColumn()** DEVE ser chamado antes desse método  

##### _autoIncrement()_

**@return**  Objeto atual
Obs.: **addColumn()** ou **changeColumn()** DEVE ser chamado antes desse método
Define que coluna a ser adicionada/alterada será auto encrementada. 

##### _notNull()_

**@return**  Objeto atual
Obs.: **addColumn()** ou **changeColumn()** DEVE ser chamado antes desse método
Define que o valor armazenado na coluna a ser adicionada/alterada não pode ser nulo. 

##### _primary()_

**@return**  Objeto atual
Obs.: **addColumn()** ou **changeColumn()** DEVE ser chamado antes desse método
Define a coluna a ser adicionada/alterada será chave primária. 

##### _unique()_

**@return**  Objeto atual
Obs.: **addColumn()** ou **changeColumn()** DEVE ser chamado antes desse método
Define que o valor armazenado na coluna a ser adicionada/alterada será unico na tabela. 

##### _create(**$mostrarDeclaracao**)_

**@param**  bool  **$mostrarDeclaracao** (Opcional, Padrão: false)  Sinaliza se a declaração que será executada irá retornar na resposta
**@return**  array
Obs.: **addColumn()** DEVE ser chamado antes desse método
```php
<?php
array (
    // Lista com a(s) tabela(s) que não foi(ram) criada(s)
    'error' => array (
        'nome da tabela com erro' => 'mensagem do erro',
        'nome da tabela com erro' => 'mensagem do erro',
        ...
    ),
    // Lista com a(s) tabela(s) que foi(ram) criada(s)
    'success' => array (
        'nome da tabela criada' => 'mensagem',
        'nome da tabela criada' => 'mensagem',
        ...
    ),
    // Só existirá esse índice se $mostrarDeclaracao = true
    'statement' => array (
        'nome da tabela' => 'declaração SQL',
        'nome da tabela' => 'declaração SQL',
        ...
    ), 
);
```  

##### _alter(**$mostrarDeclaracao**)_

**@param**  bool  **$mostrarDeclaracao** (Opcional, Padrão: false)   Sinaliza se a declaração que será executada irá retornar na resposta
**@return**  array
Obs.: **addColumn()** ou **changeColumn()** DEVE ser chamado antes desse método
```php
<?php
array (
    // Lista com a(s) tabela(s) que não foi(ram) criada(s)
    'error' => array (
        'nome da tabela com erro' => array(
                'nome da coluna' => 'mensagem do erro',
                'nome da coluna' => 'mensagem do erro',
                ...
            ),
        'nome da tabela com erro' => array(
                'nome da coluna' => 'mensagem do erro',
                'nome da coluna' => 'mensagem do erro',
                ...
            ),
        ...
    ),
    // Lista com a(s) tabela(s) que foi(ram) criada(s)
    'success' => array (
        'nome da tabela alterada' => array(
                'nome da coluna' => 'mensagem',
                'nome da coluna' => 'mensagem',
                ...
            ),
        'nome da tabela alterada' => array(
                'nome da coluna' => 'mensagem',
                'nome da coluna' => 'mensagem',
                ...
            ),
        ...
    ),
    // Só existirá esse índice se $mostrarDeclaracao = true
    'statement' => array (
        'nome da tabela' => array(
                'nome da coluna' => 'declaração SQL',
                'nome da coluna' => 'declaração SQL',
                ...
            ),
        'nome da tabela' => array(
                'nome da coluna' => 'declaração SQL',
                'nome da coluna' => 'declaração SQL',
                ...
            ),
        ...
    ), 
);
```

##### _drop()_

**@return** bool
Deleta a tabela

##### _clean()_

**@return** bool
Remove todos os registros da tabela

#### Exemplos:

* Criar tabela: **"cliente"** com:
    * coluna: **id**, tipo: **int**, tamanho: **11**, não pode ter valor nulo, auto incrementado e chave primária
    * coluna: **nome**, tipo: **varchar**, tamanho: **25** e não pode ter valor nulo
    * coluna: **cidade**, tipo: **varchar**, tamanho: **25** e não pode ter valor nulo
    * coluna: **CPF**, tipo: **int**, tamanho: **11**, não pode ter valor nulo e valor único 
    * coluna: **preferencial**, tipo: **tinyint**, tamanho: **1**, pode ter valor nulo e valor padrão 0 

```php
<?php
$tbCliente = $db->table("cliente")
                    ->addColumn("id")->int(11)->notNull()->autoIncrement()->primary()
                    ->addColumn("nome")->varchar(25)->notNull()
                    ->addColumn("cidade")->varchar(25)->notNull()
                    ->addColumn("CPF")->varchar(11)->notNull()->unique()
                    ->addColumn("preferencial")->bool()->default(0)
                ->create(true);

// Valor em $tbClientte em caso de sucesso
array (
    'error' => array (),
    'success' => array ('cliente' => "The 'cliente' table was created successfully!!!"),
    'statement' => array (
        'cliente' => "USE `loja`; CREATE TABLE IF NOT EXISTS `loja`.`cliente` (`id` int(11) AUTO_INCREMENT NOT NULL, `nome` varchar(25) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NOT NULL, `cidade` varchar(25) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NOT NULL, `CPF` varchar(11) NOT NULL, `preferencial` tinyint(1) DEFAULT '0', PRIMARY KEY (`id`), UNIQUE INDEX `id_UNIQUE` (`id` ASC), UNIQUE INDEX `CPF_UNIQUE` (`CPF` ASC)) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8;",
    ),
);
```

* Criar tabela: **"produto"** com:
    * coluna: **id**, tipo: **int**, tamanho: **11**, não pode ter valor nulo, auto incrementado e chave primária
    * coluna: **codigo**, tipo: **int**, tamanho: **11**, não pode ter valor nulo e valor único
    * coluna: **descricao**, tipo: **varchar**, tamanho: **100** e não pode ter valor nulo
    * coluna: **preco**, tipo: **float**, precisão: **7,2**, não pode ter valor nulo 
    * coluna: **quantidade**, tipo: **int**, tamanho: **11** e não pode ter valor nulo 
    * coluna: **criado_em**, tipo: **timestamp** e como valor padrão o timestamp atual 
    * coluna: **atualizado_em**, tipo: **timestamp** e como valor padrão o timestamp atual 
* Criar tabela: **"usuario"** com:
    * coluna: **id**, tipo: **int**, tamanho: **11**, não pode ter valor nulo, auto incrementado e chave primária
    * coluna: **nome**, tipo: **varchar**, tamanho: **25** e não pode ter valor nulo
    * coluna: **email**, tipo: **varchar**, tamanho: **50**, não pode ter valor nulo e com valor único
    * coluna: **ativo**, tipo: **tinyint**, tamanho: **1**, pode ter valor nulo e valor padrão 1 

```php
<?php
$criar = $db->table("produto")
                ->addColumn("id")->int(11)->notNull()->autoIncrement()->primary()
                ->addColumn("codigo")->int(11)->notNull()->unique()
                ->addColumn("descricao")->varchar(100)->notNull()
                ->addColumn("preco")->float("7,2")->notNull()
                ->addColumn("quantidade")->int(11)->notNull()
                ->addTimes(true) // TRUE passado para criar as colunas criado_em e atualizado_em
            ->table("usuario")
                ->addColumn("id")->int(11)->notNull()->autoIncrement()->primary()
                ->addColumn("nome")->varchar(25)->notNull()
                ->addColumn("emil")->varchar(50)->notNull()->unique()
                ->addColumn("ativo")->bool()->default(1)
            ->create();

// Valor em $criar em caso de sucesso
array (
    'error' => array (),
    'success' => array (
        'produto' => "The 'produto' table was created successfully!!!",
        'usuario' => "The 'usuario' table was created successfully!!!",
    )
);
```

Realizando alterações na tabela **usuario**: Alterar o nome da coluna **emil** para **email**, mudar o tamanho dela de **varchar(50)** para **varchar(100)** e adicionar após a coluna **email** a coluna: **senha**, tipo: **varchar**, tamanho: **10**, não pode ter valor nulo
```php
$alteracao = $db->table("usuario")->changeColumn("emil", "email")->varchar(100)
                                  ->addColumn("senha")->varchar(10)->notNull()->after("email")
                ->alter(true);

// Valor em $alteracao em caso de sucesso
array (
    'error' => array (),
    'success' => array (
            'usuario' => array(
                "email" => "The 'email' column was changed  successfully!!!",
                "senha" => "The 'senha' column was added successfully!!!"
            ) 
        ),
    'statement' => array (
            'usuario' => array(
                "email" => "ALTER TABLE `usuario` CHANGE `emil` `email` varchar(100) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NOT NULL, DROP INDEX `emil_UNIQUE`, ADD UNIQUE INDEX `email_UNIQUE` (`email` ASC)",
                "senha" => "ALTER TABLE `usuario` ADD `senha` varchar(10) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NOT NULL AFTER `email`"
            ) 
        ),
);
```

Deletar a tabela: "usuario"

```php
<?php
$deletado = $db->table("usuario")->drop();
// Valor em $deletado em caso de sucesso
true
```

# Insert

##### _into(**$tabela**)_

**@param**  string  **$tabela**  Nome da tabela que deseja onde será realizado a inserção de dados
**@return**  Objeto atual

##### _execute()_

**@return**  int  O número de registros inseridos 

#### Exemplos:

Cadastro de **UM** cliente na tabela "**cliente**"

```php
<?php
$cliente  = Array(
        "nome"   => "Nome do Vendedor",
        "cidade" => "São Paulo",
        "CPF"    => "12345678900"
);
$resposta = $db->insert($cliente)->into("cliente")->execute(); // Valor em $resposta em caso de sucesso
1
```

Cadastro de **TRÊS** produtos na tabela "**produto**"

```php
<?php
$produtos  = Array(
    Array(
        "codigo"     => 123456,
        "descricao"  => "Teclado",
        "preco"      => 59.99,
        "quantidade" => 5
    ),
    Array(
        "codigo"     => 234567,
        "descricao"  => "Monitor",
        "preco"      => 199.90,
        "quantidade" => 2
    ),
    Array(
        "codigo"     => 890123,
        "descricao"  => "Mouse",
        "preco"      => 45.50,
        "quantidade" => 5
    )
);
$resposta = $db->insert($produtos)->into("produto")->execute(); // Valor em $resposta em caso de sucesso
2
```

# Select

##### _from(**$tabela**)_

**@param**  string  **$tabela**  Tabela onde a consulta será realizada
**@return**  Objeto atual

##### _from(**$tabela**)_

**@param**  string  **$tabela**  Tabela onde a consulta será realizada
**@return**  Objeto atual

##### _where(**$coluna**)_

**@param**  string  **$coluna**  Coluna utilizada para comparar
**@return**  Objeto atual
[Veja como construir a cláusula WHERE](#where)

##### _limit(**$limite**, **$deslocamento**)_

**@param**  int  **$limite**  Limite para consulta
**@param**  int  **$deslocamento**  Início da consulta
**@return**  Objeto atual
Obs.: Se esse método não for chamada antes de executar a consulta o limite padrão será 100 e o início da consulta padrão será 0 

##### _order(**$coluna**, **$ordenar**)_

**@param**  string  **$coluna**  Nome(s) da(s) coluna(s) pela qual a consulta será ordenada
**@param**  string  **$ordenar**  Direção da ordenação (Padrão: ASC)
**@return**  Objeto atual

##### _execute()_

**@return**  Array
Executa o select, retorna uma lista com os registros do SELECT ou array vazio

#### Exemplos:

Buscar na tabela "**produto**" **TODOS** os dados de produtos com **QUANTIDADE MAIOR OU IGUAL 1 e com PREÇO MENOR que R$60**  e **ordernar** por **preço**

```php
<?php
$produtos = $db->select("*")->from("produto")->where("quantidade")->biggerEqual(1)->and("preco")->less(60)->order("preco")->limit(0)->execute();
// Valor em $produtos em caso de sucesso
Array(
    0 => Array(
        "id"            => "2",
        "codigo"        => "890123",
        "descricao"     => "Mouse",
        "preco"         => "45.50",
        "quantidade"    => "5",
        "criado_em"     => "2020-05-19 11:02:53",
        "atualizado_em" => "2020-05-19 11:02:53",
    ),
    0 => Array(
        "id"            => "1",
        "codigo"        => "123456",
        "descricao"     => "Teclado",
        "preco"         => "59.99",
        "quantidade"    => "5",
        "criado_em"     => "2020-05-19 11:02:53",
        "atualizado_em" => "2020-05-19 11:02:53",
    )
);
``` 

Buscar na tabela "**cliente**" o **NOME** dos clientes da cidade de **São Paulo**. No máximo 100 registros (**limite padrão**).

```php
<?php
$clientes = $db->select("nome")->from("cliente")->where("cidade")->equals("São Paulo")->execute();
// Valor em $clientes em caso de sucesso
Array(
    0 => Array("nome" => "Nome do Vendedor")
);
```

##### UPDATE

##### _set(**$dadosAtualizados**)_

**@param**  array  **$dadosAtualizados**  Array associativo com o(s) dado(s) que será(ão) atualizado(s)
**@return**  Objeto atual

##### _where(**$coluna**)_

**@param**  string  **$coluna**  Coluna utilizada para comparar
**@return**  Objeto atual
[Veja como construir a cláusula WHERE](#where)

##### _limit(**$limite**, **$deslocamento**)_

**@param**  int  **$limite**  Limite para atualização
**@param**  int  **$deslocamento**  Início da atualização
**@return**  Objeto atual
Obs.: Se esse método não for chamada antes de executar a atualização o limite padrão será 1 e o início da consulta padrão será 0 

##### _execute()_
**@return**  int Retorna o número de linhas que a atualização afetou      

#### Exemplo:

Atualizar na tabela "**produto**" a quantidade do **produto** com **código** igual a **123456** para **4**

```php
<?php
$novoValor   = Array("quantidade" => 4);
$atualizacao = $db->update("produto")->set($novoValor)->where("codigo")->equals(123456)->execute();
// Valor em $atualizacao em caso de sucesso
1
```

##### DELETE

##### _where(**$coluna**)_

**@param**  string  **$coluna**  Coluna utilizada para comparar
**@return**  Objeto atual
[Veja como construir a cláusula WHERE](#where)

##### _limit(**$limite**, **$deslocamento**)_

**@param**  int  **$limite**  Limite para exclusão
**@param**  int  **$deslocamento**  Início da exclusão
**@return**  Objeto atual
Obs.: Se esse método não for chamada antes de executar a exclusão o limite padrão será 1 e o início da consulta padrão será 0 

##### _execute()_
**@return**  int Retorna o número de linhas que o delete afetou     

#### Exemplo:

Deletar na tabela "**produto**" o produto com **código igual** a **234567**

```php
<?php
$exclusao = $db->delete("produto")->where("codigo")->equals(234567)->execute();
// Valor em $exclusao em caso de sucesso
1
```

# WHERE 

##### _where(**$coluna**)_

**@param**  string  **$coluna**  Coluna utilizada para comparar
**@return**  Objeto atual

##### _between(**$valor1**, **$valor2**)_

**@param**  string | int  **$valor1**  Primeiro valor do intervalo
**@param**  string | int  **$valor2**  Segundo valor do intervalo
**@return**  Objeto atual
Obs.: **where()** DEVE ser chamado antes desse método

##### _notBetween(**$valor1**, **$valor2**)_

**@param**  string | int  **$valor1**  Primeiro valor do intervalo
**@param**  string | int  **$valor2**  Segundo valor do intervalo
**@return**  Objeto atual
Obs.: **where()** DEVE ser chamado antes desse método

##### _equals(**$valor**)_

**@param**  string | int  **$valor**  Valor que será comparado utilizando o operador =
**@return**  Objeto atual
Obs.: **where()** DEVE ser chamado antes desse método

##### _notEquals(**$valor**)_

**@param**  string | int  **$valor**  Valor que será comparado utilizando o operador !=
**@return**  Objeto atual
Obs.: **where()** DEVE ser chamado antes desse método

##### _different(**$valor**)_

**@param**  string | int  **$valor**  Valor que será comparado utilizando o operador <>
**@return**  Objeto atual
Obs.: **where()** DEVE ser chamado antes desse método

##### _less(**$valor**)_

**@param**  string | int  **$valor**  Valor que será comparado utilizando o operador <
**@return**  Objeto atual
Obs.: **where()** DEVE ser chamado antes desse método

##### _lessEqual(**$valor**)_

**@param**  string | int  **$valor**  Valor que será comparado utilizando o operador <=
**@return**  Objeto atual
Obs.: **where()** DEVE ser chamado antes desse método

##### _bigger(**$valor**)_

**@param**  string | int  **$valor**  Valor que será comparado utilizando o operador >
**@return**  Objeto atual
Obs.: **where()** DEVE ser chamado antes desse método

##### _in(**$lista**)_

**@param**  array  **$lista**  Valores que serão comparados 
**@return**  Objeto atual
É uma abreviação para várias condições OR onde o obejtivo são valores que estão na lista    
Obs.: **where()** DEVE ser chamado antes desse método

##### _notIn(**$lista**)_

**@param**  array  **$lista**  Valores que serão comparados 
**@return**  Objeto atual
É uma abreviação para várias condições OR onde o obejtivo são valores que NÃO estão na lista                
Obs.: **where()** DEVE ser chamado antes desse método

##### _isNull()_

**@return**  Objeto atual
Obejtiva registro(s) com valor NULL                
Obs.: **where()** DEVE ser chamado antes desse método

##### _isNotNull()_

**@return**  Objeto atual
Obejtiva registro(s) com valor diferente de NULL                
Obs.: **where()** DEVE ser chamado antes desse método

##### _like(**$valor**)_

**@param**  string  **$valor**  Valor que será comparado utilizando o coringa "%" antes e depois de valor: "%$valor%" 
**@return**  Objeto atual
Obs.: **where()** DEVE ser chamado antes desse método

##### _likeStart(**$valor**)_

**@param**  string  **$valor**  Valor que será comparado utilizando o coringa "%" depois de valor: "$valor%" 
**@return**  Objeto atual
Obs.: **where()** DEVE ser chamado antes desse método

##### _likeEnd(**$valor**)_

**@param**  string  **$valor**  Valor que será comparado utilizando o coringa "%" antes de valor: "%$valor" 
**@return**  Objeto atual
Obs.: **where()** DEVE ser chamado antes desse método

##### _likeStartEnd(**$valor1**, **$valor2**)_

**@param**  string  **$valor1**  Primeiro valor da comparação
**@param**  string  **$valor2**  Segundo valor da comparação
**@return**  Objeto atual
A comparação será realizada utilizando o coringa "%" entre valor1 e valor2: "$valor1%$valor2" 
Obs.: **where()** DEVE ser chamado antes desse método

##### _and(**$coluna**)_

**@param**  string  **$coluna**  Coluna da nova condição
**@return**  Objeto atual
Inicia uma nova condição de comparação com o operador AND
Obs.: **where()** e algum método de comparação "**between()**, **notBetween()**, **equals()**, ..." DEVEM ser chamados antes desse método

##### _or(**$coluna**)_

**@param**  string  **$coluna**  Coluna da nova condição
**@return**  Objeto atual
Inicia uma nova condição de comparação com o operador OR
Obs.: **where()** e algum método de comparação "**between()**, **notBetween()**, **equals()**, ..." DEVEM ser chamados antes desse método

### Autor

Bruno Silva Santana - <ibrunosilvas@gmail.com> - <https://github.com/ibrunosilvas>

### Licença

MyDatabase está licenciado sob a licença MIT - consulte o arquivo `LICENSE` para mais detalhes.
