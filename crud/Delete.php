<?php
/**
 * Classe para realização de exclusão
 * 
 * @author Bruno Silva Santana <brunoss.789@gmail.com>
 */

namespace MyDatabase\CRUD;

class Delete extends \MyDatabase\Utils\Where
{
    /**
     * Coloca a referência do objeto MyDatabase na propriedade $db
     * Indica a(s) condição(ões) para localizar o(s) dado(s) que será(ão) deletado(s)
     * 
     * @param string  $table  Condição(ões) para localizar o(s) dado(s) que será(ão) deletado(s)
     * @param MyDatabase  $mydatabase  Referencia do objeto que esta criando um novo objeto dessa classe
     */
    public function __construct(string $table, &$mydatabase)
    {
        parent::__construct("DELETE FROM", 1, $mydatabase);

        $this->table = $table;
        $this->statement("`{$table}`");
    } // FIM -> __construct
     
    /**
     * Executa a exclusão
     * 
     * @return int  Retorna a quantida de linhas afetadas pela atualização 
     */
    public function execute(): int
    {
        if (!$this->table) {
            return 0;
        }

        $where = $this->getWhere();

        $this->statement($where);
        $this->statement($this->limit);
        
        $query = $this->prepare();
        if ($query) {
            try {
                $query->execute();
                
                return $query->rowCount();
            } catch (\PDOException $e) {
                $this->mydatabase->handleError($e, "DELETE-EXECUTE", $this->statement);
            }
        }
        
        return 0;
    } // FIM -> execute
}