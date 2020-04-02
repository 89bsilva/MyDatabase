<?php
/**
 * Classe para realização de inserções
 * 
 * @author Bruno Silva Santana <brunoss.789@gmail.com>
 */

namespace CRUD;

class Insert
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
    private $statement = "INSERT ";

    /**
     * Tabela alvo da consulta.
     *
     * @property string|bool
     */
    private $table = false;

    /**
     * Dado(s) que será(ão) inserido(s).
     *
     * @property array
     */
    private $values;

    /**
     * Mascara(s) para o(s) dado(s) que será(ão) inserido(s).
     *
     * @property string
     */
    private $placeHolders;

    /**
     * Coluna(s) que será(ão) inserido(s).
     *
     * @property string
     */
    private $columns;

    /**
     * Coloca a referência do objeto MyDatabase na propriedade $db
     * Indica qual(is) o(s) valor(es) que será(ão) inserido(s)
     * 
     * @param string  $data  Array associativo com o(s) valor(es) que será(ão) inserido(s)
     * @param MyDatabase  $mydatabase  Referencia do objeto que esta criando um novo objeto dessa classe
     */
    public function __construct(array $data, &$mydatabase)
    {
        $this->db = $mydatabase;

        $insertionData      = $this->handleInsertionData($data);
        $this->values       = $insertionData['values'];
        $this->placeHolders = $insertionData['placeHolders'];
        $this->columns      = $insertionData['columns'];
    } // FIM -> __construct

    /**
     * Separa a(s) coluna(s) e o(s) valor(es) que será(ão) inserido(s)
     *
     * @param array $data Array com o(s) dado(s) necessário(s) para realizar o insert
     * @return array
     */

    protected function handleInsertionData(array $data): array
    {
        $dimension     = array_key_exists(0, $data) ? 2 : 1;
        $insertionData = Array();

        if ($dimension === 2) {
            $dataList = Array();
            
            foreach ($data as $newData) {
                $dataList[] = $this->getInsertionData($newData);
            }

            foreach ($dataList as $key => $data) {
                if ($key === 0) {
                    $insertionData['values']       = $data['values'];
                    $insertionData['placeHolders'] = $data['placeHolders'];
                    $insertionData['columns']      = $data['columns'];
                } else {
                    $insertionData['values']        = array_merge($insertionData['values'], $data['values']);
                    $insertionData['placeHolders'] .= ", {$data['placeHolders']}";
                }
            }   
        } else {
            $insertionData = $this->getInsertionData($data);
        }

        return $insertionData;
    } // FIM -> handleInsertionData

    /**
     * Separa a(s) coluna(s) e o(s) valor(es) que será(ão) inserido(s)
     *
     * @param array $data Array com o(s) dado(s) necessário(s) para realizar o insert
     * @return array
     */

    protected function getInsertionData(array $data): array
    { 
        $columns      = "(";
        $values       = array();
        $placeHolders = '(';

        foreach ($data as $column => $value) {
            $columns      .= "{$column}, ";
            $placeHolders .= "?, ";
            $values[]     = $value; 
        }
        $columns      = \substr($columns, 0, -2) . ")";
        $placeHolders = \substr($placeHolders, 0, -2) . ")";

        return Array(
            'columns'      => $columns,
            'values'       => $values,
            'placeHolders' => $placeHolders,
        );

    } // FIM -> handleInsertionData
     
    /**
     * Passa para propriedade $table o nome da tabela em que a inserção será realizada
     * Monta na propriedade $statement o nome da tabela
     * 
     * @param  string  $from  String com o nome da tabela em que a inserção será realizada
     * @return Insert  Retorna $this 
     */
    public function into(string $table): Insert
    {
        $this->table = $table; 
        $this->statement .= "INTO `{$table}`";
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

        $this->statement .= " {$this->columns} VALUES {$this->placeHolders}";
        $query = $this->db->prepareStatement($this->statement);
        if ($query) {
            try {
                $query->execute($this->values);
                return $query->rowCount();
            } catch (\PDOException $e) {
                $this->db->handleError($e, "INSERT-EXECUTE", $this->statement);
            }
        }
        
        return 0;
    }
}
