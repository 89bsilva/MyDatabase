<?php
/**
 * Classe para gerenciamento da base de dados
 * 
 * @author Bruno Silva Santana <brunoss.789@gmail.com>
 */


class MyDatabase
{
    /**
     * Informações da conexão com a base de dados.
     *
     * @property array
     */
    private $connectionData;

    /**
     * Conexão entre PHP a base de dados.
     *
     * @property object|null
     */
    private $pdo;

    /**
     * Informações de erros.
     *
     * @property bool|array
     */
    protected $error = false;

    /**
     * Sinaliza para o sistema interromper (se true) caso haja erro.
     *
     * @property bool
     */
    public $errorInterrupt;
     
    /**
     * Configura o objeto
     * Faz um teste de conexão
     *
     * @param  string|array  $address  String com o host ou um Array associativo com o host e porta
     * @param  array  $database  Nome da base de dados, usuário e charset da base de dados
     * @param  bool   $errorInterrupt  Sinaliza para o sistema interromper (se TRUE) caso haja erro
     */
    public function __construct(
        $address,
        array $database,
        bool $errorInterrupt = false
    ) {
        $this->errorInterrupt = $errorInterrupt;
        $this->handleConnectionData($address, $database);
        $this->connect();
        $this->disconnect();
    } // Fim -> __construct

    /**
     * Lida com os dados necessários para a conexão da base de dados
     *
     * @param  string|array  $address  Endereço e porta do servidor
     * @param  array  $database  Nome do banco de dado, usuário e charset da base de dados
     * @return void
     */
    protected function handleConnectionData($address, array $database): void {
        if(is_string($address)) {
            $this->connectionData["host"] = $address;
            $this->connectionData["port"] = "3306";
        } else {
            $this->connectionData["host"] = $address[0];
            $this->connectionData["port"] = $address[1];
        }

        $this->connectionData["db"]       = $database[0];
        $this->connectionData["user"]     = $database[1];
        $this->connectionData["password"] = isset($database[2]) ? $database[2] : "";
        $this->connectionData["charset"]  = isset($database[3])  ? $database[3]  : "utf8";
    } // Fim -> handleConnectionData

    /**
     * Lança um Exception caso a propiedade $errorInterrupt seja TRUE
     * Armezena os erros na propriedade $error caso a propiedade $errorInterrupt seja FALSE
     * 
     * @param  PDOException  $error O Erro gerado
     * @param  string  $origin String que itentifica a função que gerou o erro
     * @param  string|array  $payload O que estava sendo utilizado no momento do erro
     * @return void
     */
    public function handleError($error, $origin = null, $payload = null) {
        if($this->errorInterrupt) {
            throw new Exception($error->getMessage());
        }

        if($this->error) {
            $this->error['others'][] = [
                'origin'  => $this->error['origin'],
                'code'    => $this->error['code'],
                'message' => $this->error['message'],
                'payload' => $this->error['payload']
            ];
        }
        
        $this->error['origin']  = $origin;
        $this->error['code']    = $error->getCode();
        $this->error['message'] = $error->getMessage();
        $this->error['payload'] = $payload;
    } // Fim -> handleError

    /**
     * Realiza a conexão ao banco
     *
     * @return bool
     */
    protected function connect(): bool
    {
        try {
            extract($this->connectionData, EXTR_OVERWRITE);

            $this->pdo = new PDO(
                "mysql:host={$host};port={$port};dbname={$db};charset={$charset}",
                $user, 
                $password, 
                array(
                    PDO::MYSQL_ATTR_FOUND_ROWS   => true,
                    PDO::ATTR_EMULATE_PREPARES   => true,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION
                )
            );
            
            return true;
        } catch (PDOException $e) {
            $this->handleError($e, "CONNECT");
            return false;
        } 
    } // Fim -> connect

    /**
     * Destroi o objeto PDO
     *
     * @return void
     */
    protected function disconnect(): void
    {
        $this->pdo = null; 
    } // Fim -> disconnect
    
    /**
     * Prepara a declaração
     * Caso sucesso retorna objeto pronto para ser executado
     * Caso falha retorna false 
     * 
     * @param string  $statement  String com a declaração da consulta
     * @return PDOStatement|false
     */

    public function prepareStatement($statement) 
    {
        $query = false;
        echo $statement;
        if($this->connect()) {
        
            try {
                $query = $this->pdo->prepare($statement);
            } catch (PDOException $e) {
                $this->handleError($e, "PREPARESTATEMENT");
            }
            
            $this->disconnect();
        }
        return $query;
    } // Fim -> prepareStatement

    /**
     * Retorna um objeto CRUD\Insert para realizar inserção(ões)
     * 
     * @param  array  $data  Array associativo com o(s) valor(es) que será(ão) inserido(s)
     * @return MyDatabase\CRUD\Insert
     */

    public function insert(array $data): MyDatabase\CRUD\Insert
    {
        return new MyDatabase\CRUD\Insert($data, $this);
    } // Fim -> insert

    /**
     * Retorna um objeto CRUD\Select para realizar consulta(s)
     * 
     * @param string|array  $column  String|Array com a(s) coluna(s) requisitada(s) na consulta
     * @return MyDatabase\CRUD\Select
     */

    public function select($column = "*"): MyDatabase\CRUD\Select
    {
        $column = is_array($column) ? implode(", ", $column) : $column;
        return new MyDatabase\CRUD\Select($column, $this);
    } // Fim -> select

    /**
     * Retorna um objeto CRUD\Update para realizar atualização(ões)
     * 
     * @param string  $table  String com o nome da tabela que deverá ser atualizada.
     * @return MyDatabase\CRUD\Update
     */

    public function update(string $table): MyDatabase\CRUD\Update
    {
        return new MyDatabase\CRUD\Update($table, $this);
    } // Fim -> update

    /**
     * Retorna um objeto CRUD\Delete para realizar exclusão(ões)
     * 
     * @param string  $where  Condição(ões) para localizar o(s) dado(s) que será(ão) deletado(s)
     * @return MyDatabase\CRUD\Delete
     */

    public function delete(string $where): MyDatabase\CRUD\Delete
    {
        return new MyDatabase\CRUD\Delete($where, $this);
    } // Fim -> delete
} //Fim -> Class MyDatabase