<?php
/**
 * Classe para realização de exclusão
 * 
 * @author Bruno Silva Santana <brunoss.789@gmail.com>
 */

namespace CRUD;

class Delete
{

    /**
     * Referencia do objeto MyDatabase.
     *
     * @property MyDatabase
     */
    private $db;

    /**
     * Declaração da consulta a base de dados.
     *
     * @property string
     */
    private $statement = "DELETE FROM ";

    /**
     * Limite para exclusão.
     *
     * @property int
     */
    private $limit = 1;

    /**
     * Nome da tabela onde o(s) dado(s) será(ão) excluído(s).
     *
     * @property string|bool
     */
    private $table = false;

    /**
     * Coluna(s) que será(ão) inserido(s).
     *
     * @property string
     */
    private $where;

    /**
     * Coloca a referência do objeto MyDatabase na propriedade $db
     * Indica a(s) condição(ões) para localizar o(s) dado(s) que será(ão) deletado(s)
     * 
     * @param string  $where  Condição(ões) para localizar o(s) dado(s) que será(ão) deletado(s)
     * @param MyDatabase  $mydatabase  Referencia do objeto que esta criando um novo objeto dessa classe
     */
    public function __construct(string $where, &$mydatabase)
    {
        $this->db    = $mydatabase;
        $this->where = " WHERE {$where}";
    } // FIM -> __construct
     
    /**
     * Insere na declaração de exclusão o nome da tabela
     * Insere na propriedade $table o nome da tabela onde o(s) dado(s) será(ão) excluído(s)
     * 
     * @param  string  $table  String com o nome da tabela onde o(s) dado(s) será(ão) excluído(s)
     * @return Delete  Retorna $this 
     */
    public function in(string $table): Delete
    {
        $this->table      = $table;
        $this->statement .= "`{$table}`";
        return $this;
    } // FIM -> in

    /**
     * Passa para a propriedade $limit um novo limite.
     *
     * @param  int  $limit  Novo limite para exclusão
     * @return Delete  Retorna $this 
     */
    public function limit(int $limit): Delete
    {
        $this->limit = $limit; 
        return $this;
    } // FIM -> limit
     
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

        $this->statement .= $this->where;
        $this->statement .= $this->limit > 0 ? " LIMIT {$this->limit}" : "";
        $query = $this->db->prepareStatement($this->statement);
        if ($query) {
            try {
                $query->execute();
                return $query->rowCount();
            } catch (\PDOException $e) {
                $this->db->handleError($e, "DELETE-EXECUTE", $this->statement);
            }
        }
        
        return 0;
    } // FIM -> execute
}
