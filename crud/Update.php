<?php
/**
 * Classe para realização de atualizações
 * 
 * @author Bruno Silva Santana <brunoss.789@gmail.com>
 */

namespace MyDatabase\CRUD;

class Update extends \MyDatabase\Utils\Where
{
    /**
     * Coloca a referência do objeto MyDatabase na propriedade $db
     * Indica qual(is) o(s) valor(es) que será(ão) atualizado(s)
     * 
     * @param string  $table  String com o nome da tabela que deverá ser atualizada
     * @param MyDatabase  $mydatabase  Referencia do objeto que esta criando um novo objeto dessa classe
     */
    public function __construct(string $table, &$mydatabase)
    {
        parent::__construct("UPDATE", 1, $mydatabase);
        
        $this->table = $table;

        $this->statement("`{$table}`");
    } // FIM -> __construct
     
    /**
     * Monta a declaração da atualização
     * Insere os valores novos na propriedade $values
     * 
     * @param  array  $from  String com o host ou um Array associativo com o host e porta
     * @return Update  Retorna $this 
     */
    public function set(array $data): Update
    {
        $maskData     = $this->maskData($data);
        $this->values = $maskData["values"];

        $set = Array();
        $i   = 0;
        foreach ($maskData["values"] as $bind => $value) {
            $set[] = "{$maskData["keys"][$i]} = {$bind}";
            $i++;
        }
        $set = implode(', ', $set);
        
        $this->statement("SET {$set}");
        return $this;
    } // FIM -> set
     
    /**
     * Executa a atualização
     * 
     * @return int  Retorna a quantida de linhas afetadas pela atualização 
     */
    public function execute(): int
    {
        if (
            (!$this->where || !$this->where['complete']) 
            || !$this->values) {
            return 0;
        }
        $where = $this->getWhere();

        $this->statement("{$where} {$this->limit}");

        $query = $this->prepare();
        if ($query) {
            try {
                $query->execute();
                
                return $query->rowCount();
            } catch (\PDOException $e) {
                $this->mydatabase->handleError($e, "UPDATE-EXECUTE", $this->statement);
            }
        }
        
        return 0;
    } // FIM -> execute
}
