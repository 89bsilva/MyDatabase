<?php
/**
 * Classe com propriedadedes e metodos utilizado para manipular tabelas
 * 
 * @author Bruno Silva Santana <brunoss.789@gmail.com>
 */

namespace MyDatabase\Utils;

class Table extends Statement
{
    /**
     * Array com o nome da base de dados, charset, collation e engine.
     *
     * @property array
     */
    protected $db;

    /**
     * Lista com o(s) nome(s) da(s) coluna(s) que está(ão) aguardando para ser(em) alterada(s).
     *
     * @property array
     */
    protected $changes = array();

    /**
     * Array utilizado para montar a cláusula AFTER no método alter().
     *
     * @property string
     */
    protected $after = array();

    /**
     * Lista de tabelas aguardando a criação.
     *
     * @property array
     */
    protected $tablesToDo = array();

    /**
     * Nome da coluna selecionada.
     *
     * @property string
     */
    protected $column;

    /**
     * Inicia a montagem da declaração
     * Indica qual o limite padrão da declaração
     * @param string  $init  Início da declaração. Ex.: USE|SELECT|INSERT|UPDATE|DELETE FROM
     * @param MyDatabase  $mydatabase  Referencia do objeto que esta criando um novo objeto dessa classe
     */
    public function __construct($tableName, $dbInfo, &$mydatabase)
    {
        parent::__construct("", 0, $mydatabase);
        $this->db = $dbInfo;
        $this->table($tableName);
    } // FIM -> __construct

    /**
     * Atribui à propriedade $table o nome da tabela ativa para ações
     *
     * @param  string  $tableName  String com o nome da tabela ativa para ações
     */
    public function table(string $tableName): \MyDatabase\Utils\Table
    {
        $this->table = $tableName;
        return $this;
    } // FIM -> table
    
    /**
     * Nome da coluna que será criada
     *
     * @param  string  $column  Nome da coluna que será criada
     * @return $this 
     */
    public function addColumn(string $column)
    {
        $this->column = $column;

        $this->tablesToDo[$this->table][$column] = array(
            'Field'   => $column,
            'Type'    => false,
            'Null'    => 'YES',
            'Key'     => '',
            'Default' => NULL,
            'Extra'   => ''
        );
        return $this;
    } // FIM -> addColumn
    
    /**
     * Nome da coluna que será removida
     *
     * @param  string  $column  Nome da coluna que será removida
     * @return bool  true se deletou a coluna false caso contrário  
     */
    public function dropColumn(string $column): bool
    {
        $this->statement("ALTER TABLE `{$this->table}` DROP `{$column}`", false);
        
        $query = $this->prepare();

        if ($query) {
            try {
                $query->execute();
                
                return $query->rowCount() === 0;
            } catch (\PDOException $e) {
                $this->mydatabase->handleError($e, "TABLE->dropColumn()", $this->statement);
            }
        }

        return false;
    } // FIM -> removeColumn

    /**
     * Altera a coluna passada no parâmetro $original da tabela ativa na propriedade $table
     * 
     * @param  string  $original  Nome da coluna que deseja realizar alteração
     * @param  string  $change  Novo nome da deseja realizar alteração
     * @return $this 
     */
    public function changeColumn(string $original, string $change = "")
    {
        $new = $change === "" ? $original : $change;
        $col = $this->showColumn($original);
        if ($col) {
            $this->changes[$this->table][$new] = $original;
            $col["Field"] = $new;
            $this->column = $new;
            $this->tablesToDo[$this->table][$new] = $col;
        }
        
        return $this;
    } // FIM -> changeColumn

    /**
     * Passa para o parâmetro $after o nome da coluna que antecede a nova coluna a ser adicionada ou movida
     * 
     * @param  string  $column  Nome da coluna que antecede a nova coluna a ser adicionada ou movida
     * @return $this 
     */
    public function after(string $column)
    {
        $this->after[$this->table][$this->column] = $column;

        return $this;
    } // FIM -> after 

    /**
     * Exibi informações de uma coluna
     *
     * @param  string  $column  Nome da coluna que deseja exibir informações
     * @return array 
     */
    public function showColumn(string $column): array
    {
        if (empty($column)) {
            return array();
        }

        $response = $this->getColumns($column);
        return isset($response[0]) ? $response[0] : array();
    } // FIM -> showColumn

    /**
     * Exibi informações de todas as colunas da tabela ativa na propriedade $table
     * 
     * @return $this 
     */
    public function showColumns(): array
    {
        return $this->getColumns();
    } // FIM -> showColumns
    
    /**
     * Define coluna a ser criada com o tipo INT e tamanho argumento $size
     *
     * @param  int  $size  Tamanho da coluna a ser criada
     * @return $this 
     */
    public function int(int $size = 0)
    {
        $this->setTypeAndSize("int", $size);
        return $this;
    } // FIM -> int
    
    /**
     * Define coluna a ser criada com o tipo FLOAT com precisão argumento $precision
     *
     * @param  int|string  $precision  Tamanho da coluna a ser criada
     * @return $this 
     */
    public function float($precision = 0, $value2 = 0)
    {
        if ($value2) {
            $precision = "{$precision}, {$value2}";
        }

        $this->setTypeAndSize("float", $precision);
        return $this;
    } // FIM -> float
    
    /**
     * Define coluna a ser criada com o tipo TINYINT(1)
     *
     * @return $this 
     */
    public function bool()
    {
        $this->setTypeAndSize("tinyint", "1");
        return $this;
    } // FIM -> tinyint

    /**
     * Define coluna a ser criada com o tipo DOUBLE com precisão argumento $precision
     *
     * @param  int|string  $precision  Tamanho da coluna a ser criada
     * @return $this 
     */
    public function double($precision = 0, $value2 = 0)
    {
        if ($value2) {
            $precision = "{$precision}, {$value2}";
        }

        $this->setTypeAndSize("double", $precision);
        return $this;
    } // FIM -> double 
    
    /**
     * Define coluna a ser criada com o tipo VARCHAR e tamanho argumento $size
     *
     * @param  int  $size  Tamanho da coluna a ser criada
     * @return $this 
     */
    public function varchar(int $size = 1)
    {
        $this->setTypeAndSize("varchar", $size);
        return $this;
    } // FIM -> varchar
    
    /**
     * Define coluna a ser criada com o tipo TEXT e tamanho argumento $size
     *
     * @return $this 
     */
    public function text()
    {
        $this->setTypeAndSize("text", 0);
        return $this;
    } // FIM -> text
    
    /**
     * Define coluna a ser criada com o tipo MEDIUMTEXT e tamanho argumento $size
     *
     * @return $this 
     */
    public function mediumtext()
    {
        $this->setTypeAndSize("mediumtext", 0);
        return $this;
    } // FIM -> mediumtext
    
    /**
     * Define coluna a ser criada com o tipo LONGTEXT e tamanho argumento $size
     *
     * @return $this 
     */
    public function longtext()
    {
        $this->setTypeAndSize("longtext", 0);
        return $this;
    } // FIM -> longtext
    
    
    /**
     * Define coluna a ser criada com o tipo TIMESTAMP
     * 
     * @return $this 
     */
    public function timestamp()
    {
        $this->setTypeAndSize("timestamp", 0);
        return $this;
    } // FIM -> timestamp
    
    /**
     * Define que coluna a ser criada não pode ser nula
     *
     * @return $this 
     */
    public function notNull()
    {
        $this->columnConfig("Null", "NO");
        return $this;
    } // FIM -> notNull

    /**
     * Define que coluna a ser criada será auto encrementada
     *
     * @return $this 
     */
    public function autoIncrement()
    {
        $this->columnConfig("Extra", "auto_increment");
        return $this;
    } // FIM -> autoIncrement

    /**
     * Define que coluna a ser criada será chave primária
     *
     * @return $this 
     */
    public function primary()
    {
        $this->columnConfig("Key", "PRI"); 

        return $this;
    } // FIM -> unique

    /**
     * Define que coluna a ser criada terá valor unico
     *
     * @return $this 
     */
    public function unique()
    {
        $this->columnConfig("Key", "UNI");    
          
        return $this;
    } // FIM -> unique
    
    /**
     * Define coluna a ser criada com o tipo TEXT e tamanho argumento $size
     *
     * @param  bool  $ptBR  Sinaliza se o nome das colunas serão em Português ou Inglês
     * @return $this 
     */
    public function addTimes($ptBR = false)
    {
        $table   = $this->table;
        $column  = $this->column;
        $created = $ptBR ? "criado_em"     : "created_at";
        $updated = $ptBR ? "atualizado_em" : "updated_at";

        $this->tablesToDo[$table][$created] = array (
            'Field'   => $created,
            'Type'    => 'timestamp',
            'Null'    => 'NO',
            'Key'     => '',
            'Default' => 'CURRENT_TIMESTAMP',
            'Extra'   => '',
          );

        $this->tablesToDo[$table][$updated] = array (
          'Field'   => $updated,
          'Type'    => 'timestamp',
          'Null'    => 'NO',
          'Key'     => '',
          'Default' => 'CURRENT_TIMESTAMP',
          'Extra'   => 'on update CURRENT_TIMESTAMP',
        );

        return $this;
    } // FIM -> addTimes
    
    /**
     * Define o valor padrão da coluna a ser criada
     *
     * @param string  $value  Valor padrão da coluna a ser criada
     * @return $this 
     */
    public function default(string $value)
    {
        $this->columnConfig("Default", $value);
        return $this;
    } // FIM -> default
    
    /**
     * Define o tipo e tamanho da coluna a ser criada
     *
     * @param  string  $type  Tipo da coluna a ser criada
     * @param  int|string  $size  Tamanho da coluna a ser criada
     */
    public function setTypeAndSize(string $type, $size)
    {
        $type = $size > 0 ? "{$type}({$size})" : $type;

        $this->columnConfig('Type', $type);
    } // FIM -> setTypeAndSize

    /**
     * Configura a coluna a ser criada
     *
     * @param  string  $target  Configuração
     * @param  string  $value  Valor da configuração
     */
    public function columnConfig(string $target, string $value)
    {
        $table  = $this->table;
        $column = $this->column;
        $this->tablesToDo[$this->table][$column][$target] = $value;
    } // FIM -> columnConfig

    /**
     * Busca na tabela ativa na propriedade $table as informações de sua(s) coluna(s)
     * 
     * @param  string  $column  Nome de uma coluna específica que deseja obter
     */
    public function getColumns(string $column = ""): array
    {
        $columns = array();
     
        if (!$this->table) {
            return $columns;
        }

        $db    = $this->db["name"];
        $table = $this->table;
        $stmt  = "SHOW COLUMNS FROM {$db}.{$table}";
        $stmt .= !!$column ? " WHERE Field = '{$column}';" : ";";
        
        $this->statement($stmt, false);
        
        $query = $this->prepare();
    
        if ($query) {
            try {
                $query->execute();
                
                return $query->fetchAll();
            } catch (\PDOException $e) {
                $this->mydatabase->handleError($e, "TABLE->getColumns()", $this->statement);
            }
        }
        
        return $columns;
    } // FIM -> getColumns
    
    /**
     * Altera a coluna passada no parâmetro $original da tabela ativa na propriedade $table
     * 
     * @param  bool  $showStatement  Indica se a declaração deve ser retornada
     * @param  string  $change  Novo nome da deseja realizar alteração
     * @return $this 
     */
    public function alter(bool $showStatement = false)
    {
        $response = array(
            "error"   => array(),
            "success" => array()
        );

        $abortMsg  = "So the creation of this table was aborted!!!";
        $statement = array();
        
        foreach ($this->tablesToDo as $table => $columns) {

            $this->table = $table;

            foreach ($columns as $columnName => $columnInformation) {
                $change = "";

                if (
                    isset($this->changes[$table])
                    && isset($this->changes[$table][$columnName])
                ) {
                    $change = $this->changes[$table][$columnName];
                }
                
                $primary = isset($columnInformation["Key"]) && $columnInformation["Key"] === "PRI";
                $unique  = isset($columnInformation["Key"]) && $columnInformation["Key"] === "UNI";

                $stmtPrimary = array();
                $stmtUnique  = array();
                $stmtColums  = array();

                if ($primary) {

                    $stmt = $this->createPrimaryStatement($columnInformation['Field']);

                    if (
                        isset($this->changes[$table]) 
                        && isset($this->changes[$table][$columnInformation['Field']])
                    ) {
                        $stmt = "DROP PRIMARY KEY, ADD {$stmt}";
                        $stmt .= ", DROP INDEX `";
                        $stmt .= $this->changes[$table][$columnInformation['Field']];
                        $stmt .= "_UNIQUE`";
                    } else {
                        $stmt = "ADD {$stmt}, ";
                        $stmt .= $columnInformation['Field'];
                    }
                    $stmt .= ", ADD ";
                    $stmt .= $this->createUniqueStatement($columnInformation['Field']);

                    $stmtPrimary[] = $stmt;
                }

                if ($unique) {
                    
                    if (
                        isset($this->changes[$table]) 
                        && isset($this->changes[$table][$columnInformation['Field']])
                    ) { 
                        $stmt  = "DROP INDEX `{$this->changes[$table][$columnInformation['Field']]}_UNIQUE`, ADD ";
                    } else {
                        $stmt  = "ADD ";
                    }
                    $stmt .= $this->createUniqueStatement($columnInformation['Field']);

                    $stmtUnique[] = $stmt;
                }
                
                $stmtColums[] = $this->createAlterStatement($columnInformation, $change);
                $mountStmt    = $this->joinColsPrimaryUnique($stmtColums, $stmtPrimary, $stmtUnique);

                $this->statement("ALTER TABLE `{$this->table}` {$mountStmt}", false);

                if ($showStatement) {
                    $response["statement"][$table][$columnName] = $this->statement;
                }

                $query = $this->prepare();
                $alter = $change ? "changed" : "added";
                
                if ($query) {
                    try {
                        $query->execute();
                        $columnsDataBase = $this->showColumn($columnName);
                        if ($columnsDataBase == $this->tablesToDo[$table][$columnName]) {
                            $response["success"][$table][$columnName] = "The '{$columnName}' column was {$alter} successfully!!!";
                        } else {
                            $response["error"][$table][$columnName] = "Could not {$alter} column: '{$columnName}'";
                        }
                    } catch (\PDOException $e) {
                        $this->mydatabase->handleError($e, "TABLE->alter()", $this->statement);
                        $response["error"][$table][$columnName] = "Could not {$alter} column: '{$columnName}'";
                    }
                } else {
                    $response["error"][$table][$columnName] = "An unexpected error occurred before attempting to alter the '{$columnName}' column!!";
                }
            }
        }

        return $response;
    } // FIM -> change

    /**
     * Executa a criação da tabela
     * 
     * @param  bool  $showStatement  Indica se a declaração para cada tabela deve ser retornada
     * @return array  Um array com o índice row que contém um int com o número de linhas afetadas pela declaração
     *                E os demais índices será(ão) o(s) nome(s) da(s) tabela(s) cada um contento um array com as chaves: 
     *                "message"   => string com a mensagem relacionada a tabela, 
     *                "created"   => booleano TRUE se a tabela foi criada FALSE caso contrário
     *                "rows"      => int quantidade de linhas afetadas
     *                "statement" => string com a declaração da tabela. Obs.: Esse índice somente se o parâmetro $showStatement for TRUE
     */
    public function create(bool $showStatement = false): array
    {
        $response = array(
            "error"   => array(),
            "success" => array()
        );

        $abortMsg  = "So the creation of this table was aborted!!!";
        $statement = array();
        

        foreach ($this->tablesToDo as $table => $columns) {

            $this->table  = $table;
            $columnInDB   = $this->showColumns();
            $primaryKey   = false;
            $columnCreate = array();
            
            if ($columnInDB) {
                $response["error"][$table] = "The '{$table}' table already exists in the database. {$abortMsg}";
                        
                continue;
            }

            foreach ($columns as $column => $information) {
                $primary = $information["Key"] === "PRI";
                $unique  = $information["Key"] === "UNI";

                $columnCreate[] = $information;

                if ($primary) {
                    $primaryKey = true;
                    $statement[$table]["primary"][] = $this->createPrimaryStatement($information['Field']);
                    $statement[$table]["primary"][] = $this->createUniqueStatement($information['Field']);
                }

                if ($unique) {
                    $statement[$table]["unique"][] = $this->createUniqueStatement($information['Field']);
                }

                $statement[$table]["columns"][] = $this->createColumnStatement($information);
            }
            
            if (!$primaryKey) {
                $response["error"][$table] = "No primary keys were found for the '{$table}' table. {$abortMsg}";
                continue;
            }

            $stmtUnique = isset($statement[$table]["unique"]) ? $statement[$table]["unique"] : array();
            $mountStmt  = $this->joinColsPrimaryUnique($statement[$table]["columns"], $statement[$table]["primary"], $stmtUnique);
            
            $this->statement("USE `{$this->db["name"]}`; CREATE TABLE IF NOT EXISTS `{$this->db["name"]}`.`{$table}`", false);
            $this->statement("({$mountStmt})");
            $this->statement("ENGINE = {$this->db["engine"]} DEFAULT CHARACTER SET = {$this->db["charset"]};");

            if ($showStatement) {
                $response["statement"][$table] = $this->statement;
            }
            
            $query = $this->prepare();

            if ($query) {
                try {
                    $query->execute();
                    
                    $columnsDataBase = $this->showColumns();
                    $columnsEquals   = true;

                    foreach ($columnCreate as $key => $columnInformation) {
                        $columnName = $columnInformation["Field"];
                        if(isset($columnsDataBase[$key]) && isset($columnsDataBase[$key][$columnName])) {
                            if(
                                $columnsDataBase[$key][$columnName]["Type"]       != $columnsDataBase[$key][$columnName]["Type"] 
                                || $columnsDataBase[$key][$columnName]["Null"]    != $columnsDataBase[$key][$columnName]["Null"] 
                                || $columnsDataBase[$key][$columnName]["Key"]     != $columnsDataBase[$key][$columnName]["Key"] 
                                || $columnsDataBase[$key][$columnName]["Default"] != $columnsDataBase[$Default][$columnName]["Key"] 
                            ) {
                                $columnsEquals = false;
                            }
                        }
                    }
                    
                    if ($columnsEquals) {
                        $response["success"][$table] = "The '{$table}' table was created successfully!!!";
                    } else {
                        $response["error"][$table] = "Could not create table: '{$table}'";
                    }

                } catch (\PDOException $e) {
                    $this->mydatabase->handleError($e, "TABLE->create()", $this->statement);
                    $response["error"][$table] = "Could not create table: '{$table}'";
                }
            } else {
                $response["error"][$table] = "An unexpected error occurred before attempting to create the '{$table}' table!!";
            }
        }

        return $response;
    } // FIM -> create

    /**
     * Junta as declaraçoes de configuração das colunas
     *
     * @param  array  $columns Array com as declarações com as configurações das colunas para criação da tabela
     * @param  array  $primary Array com as declarações da(s) coluna(s) com chave primária para criação da tabela
     * @param  array  $unique Array com as declarações da(s) coluna(s) com valor único para criação da tabela
     */
    private function joinColsPrimaryUnique(array $columns, array $primary, array $unique): string
    {
        $stmtColumns = implode(", ", $columns);
        $stmtPrimary = implode(", ", $primary);
        $stmtUnique  = implode(", ", $unique);
        $statement   = $stmtColumns;
        $statement  .= $stmtPrimary ? ", {$stmtPrimary}" : "";
        $statement  .= $stmtUnique  ? ", {$stmtUnique}"  : "";

        return $statement;
    } // FIM -> joinColsPrimaryUnique

    /**
     * Cria a declaração PRIMARY KEY e UNIQUE INDEX da(s) coluna(s) que será(ão) criada(s)
     *
     * @param  string  $primaryKey  Nome da coluna que é chave primária
     */
    private function createPrimaryStatement(string $primaryKey): string
    {
        $statement = "PRIMARY KEY (`{$primaryKey}`)";

        return $statement;
    } // FIM -> createPrimaryStatement
    
    /**
     * Cria a declaração UNIQUE INDEX da(s) coluna(s) que será(ão) criada(s)
     *
     * @param  string  $unique  Nome da coluna que terá valor único
     */
    private function createUniqueStatement(string $unique): string
    {
        return "UNIQUE INDEX `{$unique}_UNIQUE` (`{$unique}` ASC)";
    } // FIM -> createUniqueStatement

    /**
     * Cria a declaração PRIMARY KEY e UNIQUE INDEX da(s) coluna(s) que será(ão) criada(s)
     *
     * @param  string  $primaryKey  Nome da coluna que é chave primária
     */
    private function createAlterStatement(array $column,  string $change): string
    {
        $statement = ""; 

        if ($change) {
            $statement .= "CHANGE `{$change}` ";
            $statement .= $this->createColumnStatement($column);
        } else {
            $statement  .= "ADD ";
            $statement  .= $this->createColumnStatement($column);

            if (isset($this->after[$this->table]) && isset($this->after[$this->table][$column['Field']])) {
                $after = $this->after[$this->table][$column['Field']];
                $statement .= $after === "first" ? " FIRST" : " AFTER `{$after}`";
            } else {
                $allColumns = $this->showColumns();
                $endColumn  = end($allColumns);
                $statement .= $endColumn ? " AFTER `{$endColumn["Field"]}`" : "FIRST"; 
            }
        }
        
        return $statement;
    } // FIM -> createAlterStatement

     
    /**
     * Cria a declaração da(s) coluna(s) que será(ão) criada(s)
     *
     * @param  array  $config  Array com as configurações da coluna
     */
    private function createColumnStatement(array $config): string
    {
        $statement  = "`{$config["Field"]}` {$config["Type"]}";
        $statement .= $config["Extra"] === "auto_increment" ? " AUTO_INCREMENT" : "";

        if (
            strpos($config["Type"], "varchar") !== false 
            || strpos($config["Type"], "text") !== false
        ) {
            $statement .= " CHARACTER SET '{$this->db["charset"]}' COLLATE '{$this->db["charset"]}_{$this->db["collation"]}'";
        }

        $statement .= $config["Extra"] === "on update CURRENT_TIMESTAMP" ? " on update CURRENT_TIMESTAMP" : "";
        $statement .= $config["Null"]  === "NO" ? " NOT NULL" : "";

        if ($config["Default"] || $config["Default"] === '0') {
            $statement .= " DEFAULT ";
            $statement .= $config["Default"] === "CURRENT_TIMESTAMP" ? "CURRENT_TIMESTAMP" : "'{$config["Default"]}'";
        }

        return $statement;
    } // FIM -> createColumnStatement

    /**
     * Deleta a tabela
     */
    public function drop(): bool
    {
        $this->statement("DROP TABLE `{$this->table}`;", false);
        
        $query = $this->prepare();

        if ($query) {
            try {
                $query->execute();
                
                return $query->rowCount() === 0;
            } catch (\PDOException $e) {
                $this->mydatabase->handleError($e, "TABLE->drop()", $this->statement);
            }
        }

        return false;
    } // FIM -> drop

    /**
     * Limpa a tabela
     */
    public function clean(): bool
    {
        $this->statement("TRUNCATE `{$this->table}`;", false);
        
        $query = $this->prepare();

        if ($query) {
            try {
                $query->execute();
                
                return $query->rowCount() === 0;
            } catch (\PDOException $e) {
                $this->mydatabase->handleError($e, "TABLE->clean()", $this->statement);
            }
        }

        return false;
    } // FIM -> drop
}
