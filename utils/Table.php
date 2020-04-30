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
     * Lista com as tabelas usadas por essa classe.
     *
     * @property array
     */
    protected $tables = array();

    /**
     * Lista de tabelas aguardando a criação.
     *
     * @property array
     */
    protected $tablesToCreate = array();

    /**
     * Nome da coluna selecionada.
     *
     * @property array
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
        $this->tables[$tableName] = $tableName;
        return $this;
    } // FIM -> table
    
    /**
     * Nome da coluna que deseja ser criada
     *
     * @param  string  $column  Nome da coluna que deseja ser criada
     * @return $this 
     */
    public function addColumn(string $column)
    {
        $this->column = $column;
        return $this;
    } // FIM -> addColumn
    
    /**
     * Define coluna a ser criada com o tipo INT e tamanho argumento $size
     *
     * @param  int  $size  Tamanho da coluna a ser criada
     * @return $this 
     */
    public function int(int $size = 0)
    {
        $this->setTypeAndSize("INT", $size);
        return $this;
    } // FIM -> int
    
    /**
     * Define coluna a ser criada com o tipo FLOAT com precisão argumento $precision
     *
     * @param  int|string  $precision  Tamanho da coluna a ser criada
     * @return $this 
     */
    public function float($precision = 0)
    {
        $this->setTypeAndSize("FLOAT", $precision);
        return $this;
    } // FIM -> float

    /**
     * Define coluna a ser criada com o tipo DOUBLE com precisão argumento $precision
     *
     * @param  int|string  $precision  Tamanho da coluna a ser criada
     * @return $this 
     */
    public function double($precision = 0)
    {
        $this->setTypeAndSize("FLOAT", $precision);
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
        $this->setTypeAndSize("VARCHAR", $size);
        return $this;
    } // FIM -> varchar
    
    /**
     * Define coluna a ser criada com o tipo TEXT e tamanho argumento $size
     *
     * @param  int  $size  Tamanho da coluna a ser criada
     * @return $this 
     */
    public function text(int $size = 1)
    {
        $this->setTypeAndSize("TEXT", $size);
        return $this;
    } // FIM -> text
    
    /**
     * Define coluna a ser criada com o tipo TIMESTAMP
     * 
     * @return $this 
     */
    public function timestamp()
    {
        $this->setTypeAndSize("TIMESTAMP", 0);
        return $this;
    } // FIM -> timestamp
    
    /**
     * Define que coluna a ser criada não pode ser nula
     *
     * @return $this 
     */
    public function notNull()
    {
        $this->columnConfig("notNull", true);
        return $this;
    } // FIM -> notNull

    /**
     * Define que coluna a ser criada será auto encrementada
     *
     * @return $this 
     */
    public function autoIncrement()
    {
        $this->columnConfig("autoIncrement", true);
        return $this;
    } // FIM -> autoIncrement

    /**
     * Define que coluna a ser criada será chave primária
     *
     * @return $this 
     */
    public function primary()
    {
        $table  = $this->table;
        $column = $this->column;

        $this->columnConfig("autoIncrement", true);
        $this->tablesToCreate[$table]["primary"][$column] = $column;    
        $this->tablesToCreate[$table]["unique"][$column]  = $column;    

        return $this;
    } // FIM -> unique

    /**
     * Define que coluna a ser criada terá valor unico
     *
     * @return $this 
     */
    public function unique()
    {
        $table  = $this->table;
        $column = $this->column;

        $this->tablesToCreate[$table]["unique"][$column] = $column;    
          
        return $this;
    } // FIM -> unique
    
    /**
     * Define coluna a ser criada com o tipo TEXT e tamanho argumento $size
     *
     * @param  int  $size  Tamanho da coluna a ser criada
     * @return $this 
     */
    public function addTimes()
    {
        $table  = $this->table;
        $column = $this->column;

        $this->tablesToCreate[$table]["created_at"] = array(
            "type"    => "TIMESTAMP",
            "default" => "CURRENT_TIMESTAMP",
            "notNull" => true
        );

        $this->tablesToCreate[$table]["updated_at"] = array(
            "type"    => "TIMESTAMP",
            "on"      => "on update CURRENT_TIMESTAMP",
            "default" => "CURRENT_TIMESTAMP",
            "notNull" => true
        );

        return $this;
    } // FIM -> addTimes
    
    /**
     * Define o valor padrão da coluna a ser criada
     *
     * @param  int|string  $value  Valor padrão da coluna a ser criada
     * @return $this 
     */
    public function default($value)
    {
        $this->columnConfig("default", $value);
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
        $this->columnConfig("type", $type);

        if ($size) {
            $this->columnConfig("size", $size);
        }
    } // FIM -> setTypeAndSize

    /**
     * Configura a coluna a ser criada
     *
     * @param  string  $target  Configuração
     * @param  int  $value  Valor da configuração
     */
    public function columnConfig(string $target, $value)
    {
        $table  = $this->table;
        $column = $this->column;

        $this->tablesToCreate[$table][$column][$target] = $value;
    } // FIM -> columnConfig

    /**
     * Configura a coluna a ser criada
     * 
     */
    public function getColumns(): array
    {
        $columns = array();
     
        if (!$this->table) {
            return $columns;
        }
        
        $dbName = $this->db["name"];
        $table  = $this->table;
        $select = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '{$dbName}' AND TABLE_NAME = '{$table}';";

        $this->statement($select, false);

        $query = $this->prepare();
    
        if ($query) {
            try {
                $query->execute();
                
                while ($column = $query->fetch()) {
                    $columns[]= $column["COLUMN_NAME"];
                }
            } catch (\PDOException $e) {
                $this->mydatabase->handleError($e, "TABLE->getColumns()", $this->statement);
            }
        }
        return $columns;
    } // FIM -> getColumns
    
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
        $statement = array();
        $return    = array();
        $i         = 0;
        
        foreach ($this->tablesToCreate as $table => $columns) {
            
            if (!isset($this->tablesToCreate[$table]["primary"])) {
                $return[$table] = array(
                    "message" => "Table: {$table} does not have a primary key!",
                    "created" => false
                );
                continue;
            }
            
            $statement = "USE `{$this->db["name"]}`; CREATE TABLE IF NOT EXISTS `{$this->db["name"]}`.`{$table}` (";

            foreach ($columns as $column => $config) {

                if ($column === "unique" || $column === "primary") {
                    continue;
                }

                $statement .= "`{$column}` {$config["type"]}";
                $statement .= isset($config["size"]) ? "({$config["size"]})" : "";
                $statement .= isset($config["autoIncrement"]) && $config["autoIncrement"] ? " AUTO_INCREMENT" : "";

                if ($config["type"] === "VARCHAR" || $config["type"] === "TEXT") {
                    $statement .= " CHARACTER SET '{$this->db["charset"]}' COLLATE '{$this->db["charset"]}_{$this->db["collation"]}'";
                }

                $statement .= isset($config["on"]) ? " {$config["on"]}" : "";
                $statement .= isset($config["notNull"]) && $config["notNull"] ? " NOT NULL" : "";

                if (isset($config["default"])) {
                    $statement .= " DEFAULT ";
                    $statement .= $config["default"] !== "CURRENT_TIMESTAMP" ? "'{$config["default"]}'" : $config["default"];
                }

                $statement .= ", ";
            }

            foreach ($columns["primary"] as $primary) {
                $statement .= "PRIMARY KEY (`{$primary}`), ";
                $statement .= "UNIQUE INDEX `{$primary}_UNIQUE` (`{$primary}` ASC), ";
                unset($columns["unique"][$primary]);
            }

            foreach ($columns["unique"] as $unique) {
                $statement .= "UNIQUE INDEX `{$unique}_UNIQUE` (`{$unique}` ASC), ";
            }
            $statement  = substr($statement, 0, -2);
            $statement .= ") ENGINE = {$this->db["engine"]} DEFAULT CHARACTER SET = {$this->db["charset"]};";
            

            $this->table = $table;
            $columnsInDB = $this->getColumns();

            unset($columns["primary"]);
            unset($columns["unique"]);

            $columnsToCreate = \array_keys($columns);
            $return[$table]  = isset($return[$table]) ? $return[$table] : array();

            if ($showStatement) {
                $return[$table]["statement"] = $statement;
            }

            $return[$table]["message"] = isset($return[$table]["message"]) ? " | " : "";
            
            if ($columnsInDB == $columnsToCreate) {
                $return[$table]["message"] .= "Already exists in database!!!";
                $return[$table]["created"]  = false;

                continue;
            }
            
            $this->statement($statement, false);

            $query = $this->prepare();

            if ($query) {
                try {
                    $query->execute();
                    
                    $columnsInDB    = $this->getColumns();

                    if ($columnsInDB == $columnsToCreate) {
                        $i++;
                        $return[$table]["message"] .= "Successfully created!!!";
                        $return[$table]["created"]  = true;
                    } else {
                        $return[$table]["message"] .= "Could not create table!!!";
                        $return[$table]["created"]  = false;
                    }

                } catch (\PDOException $e) {
                    $this->mydatabase->handleError($e, "TABLE->create()", $this->statement);
                }
            }
        }
        $return["rows"] = $i;

        return $return;
    } // FIM -> getColumns

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
