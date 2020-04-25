<?php
/**
 * Classe para realização de inserções
 * 
 * @author Bruno Silva Santana <brunoss.789@gmail.com>
 */

namespace MyDatabase\CRUD;

class Insert extends \MyDatabase\Utils\Statement
{
    /**
     * Coloca a referência do objeto MyDatabase na propriedade $db
     * Indica qual(is) o(s) valor(es) que será(ão) inserido(s)
     * 
     * @param string  $data  Array associativo com o(s) valor(es) que será(ão) inserido(s)
     * @param MyDatabase  $mydatabase  Referencia do objeto que esta criando um novo objeto dessa classe
     */
    public function __construct(array $data, &$mydatabase)
    {
        parent::__construct("INSERT", 0, $mydatabase);

        $maskData      = $this->maskData($data);
        $this->columns = $maskData["keys"];
        $this->tokens  = $maskData["tokens"];
        $this->values  = $maskData["values"];
    } // FIM -> __construct

    
     
    /**
     * Passa para propriedade $table o nome da tabela em que a inserção será realizada
     * Monta na propriedade $statement o nome da tabela
     * 
     * @param  string  $from  String com o nome da tabela em que a inserção será realizada
     * @return Insert  Retorna $this 
     */
    public function into(string $table): \MyDatabase\CRUD\Insert
    {
        $this->table = $table; 
        $this->statement("INTO `{$table}`");
        return $this;
    } // FIM -> into
     
     
    /**
     * Executa o insert
     * 
     * @return int  Retorna a quantida de linhas afetadas pelo insert 
     */
    public function execute(): int
    {
        if (!$this->table) {
            return 0;
        }

        $columns = "(" . \implode(", ", $this->columns) . ")";
        $values  = \implode(", ", $this->tokens);

        $this->statement("{$columns} VALUES {$values}");

        $query = $this->prepare();
        
        if ($query) {
            try {
                $query->execute();
                return $query->rowCount();
            } catch (\PDOException $e) {
                $this->mydatabase->handleError($e, "INSERT-EXECUTE", $this->statement);
            }
        }
        
        return 0;
    }
}
