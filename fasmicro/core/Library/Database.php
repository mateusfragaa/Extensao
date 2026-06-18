<?php

namespace Core\Library;

use PDO;
use PDOException;
use Exception;

class Database 
{
    private $conexao;
    private static $dbdrive  = "";
    private static $host     = "";
    private static $port     = "";
    private static $user     = "";
    private static $password = "";
    private static $db       = "";
    
    protected $table;
    private $select = "*";
    private $join = "";
    private $where = "";
    private $groupBy = "";
    private $having = "";
    private $orderBy = "";
    private $limit = "";
    private $offset = null;
    private $params = [];
    private $unions = [];
    private bool $inTransaction = false;

    /**
     * construct
     *
     * @param string $db_dbdrive 
     * @param string $db_host 
     * @param string $db_port 
     * @param string $db_bdados 
     * @param string $db_user 
     * @param string $db_password 
     */
    public function __construct(
        $db_dbdrive,
        $db_host,
        $db_port,
        $db_bdados,
        $db_user,
        $db_password       
    ) {
        self::$dbdrive  = $db_dbdrive;
        self::$host     = $db_host;
        self::$port     = $db_port;
        self::$db       = $db_bdados;  
        self::$user     = $db_user;
        self::$password = $db_password;
    }

    /**
     * clone - Evita que a classe seja clonada
     *
     * @return void
     */
    private function __clone() 
    {
    }

    /**
     * destruct - Método que destroi a conexão com banco de dados e remove da memória todas as variáveis setadas
     */
    public function __destruct() {
        if ($this->inTransaction) {
            return;
        }
        $this->disconnect();
    }

    /*Metodos que trazem o conteudo da variavel desejada
    @return   $xxx = conteudo da variavel solicitada*/
    private function getDBDrive() {return self::$dbdrive;}
    private function getHost()    {return self::$host;}
    private function getPort()    {return self::$port;}
    private function getUser()    {return self::$user;}
    private function getPassword(){return self::$password;}
    private function getDB()      {return self::$db;}

    /**
     * connect
     *
     * @return object
     */
    public function connect()
    {
        // Reutiliza a conexão aberta enquanto uma transação estiver ativa
        if ($this->inTransaction && $this->conexao !== null) {
            return $this->conexao;
        }

        try {
            if ( $this->getDBDrive() == 'mysql' ) {            // MySQL

                $this->conexao = new PDO(
                                            $this->getDBDrive().":host=".$this->getHost().";port=".$this->getPort().";dbname=".$this->getDB().";charset=utf8mb4",
                                            $this->getUser(),
                                            $this->getPassword()
                                        );

            } else if ( $this->getDBDrive() == 'sqlsrv' ) {    // SQL Server

                $this->conexao = new PDO(
                                            $this->getDBDrive().":Server=".$this->getHost().",".$this->getPort().";DataBase=".$this->getDB(),
                                            $this->getUser(),
                                            $this->getPassword()
                                        );

            }

            $this->conexao->setAttribute(PDO::ATTR_ERRMODE, $this->conexao::ERRMODE_EXCEPTION);

        } catch (PDOException $i) {
            throw new \RuntimeException("Falha na conexão com o banco de dados: " . $i->getMessage(), 500, $i);
        }

        return ($this->conexao);

    }

    /**
     * disconnect
     *
     * @return void
     */
    private function disconnect()
    {
        if (!$this->inTransaction) {
            $this->conexao = null;
        }
    }

    /**
     * beginTransaction - Inicia uma transação no banco de dados
     *
     * @return void
     * @throws \Exception se já houver uma transação ativa
     */
    public function beginTransaction(): void
    {
        if ($this->inTransaction) {
            throw new \Exception("Já existe uma transação ativa.");
        }
        $this->connect()->beginTransaction();
        $this->inTransaction = true;
    }

    /**
     * commit - Confirma a transação ativa
     *
     * @return void
     */
    public function commit(): void
    {
        if ($this->inTransaction && $this->conexao !== null) {
            $this->conexao->commit();
            $this->inTransaction = false;
            $this->conexao = null;
        }
    }

    /**
     * rollback - Desfaz a transação ativa
     *
     * @return void
     */
    public function rollback(): void
    {
        if ($this->inTransaction && $this->conexao !== null) {
            $this->conexao->rollBack();
            $this->inTransaction = false;
            $this->conexao = null;
        }
    }

    /**
     * Método select que retorna um array de objetos
    *   @param string $sql
    *   @param array $params
    *   @return void
    */
    public function dbSelect($sql, $params = null)
    {
        if ((gettype($params) != 'array') && (gettype($params) != "NULL") ) {
            $params = [$params];
        }
        
        $query = $this->connect()->prepare( $sql , array( PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL ) );
        $query->execute( $params );
        $rs = $query;

        $this->disconnect();

        return $rs;
        
    }
    
    /**
     * dbInsert - Método insert que insere valores no banco de dados e retorna o último id inserido
     *
     * @param string $sql 
     * @param mixed $params 
     * @return void
     */
    public function dbInsert($sql, $params = null)
    {
        try {        
            $conexao = $this->connect();
            $query   = $conexao->prepare($sql);
            $query->execute($params);
            
            $rs      = $conexao->lastInsertId(); // or die(print_r($query->errorInfo(), true));
            
            $this->disconnect();
            
            return $rs;

        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * dbUpdate - Método update que altera valores do banco de dados e retorna o número de linhas afetadas
     *
     * @param string $sql 
     * @param mixed $params 
     * @return void
     */
    public function dbUpdate($sql, $params = null)
    {
        try {
            $query = $this->connect()->prepare($sql);
            $query->execute($params);
            
            $rs = $query->rowCount();// or die(print_r($query->errorInfo(), true));
            $this->disconnect();            
            
            return $rs;

        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * dbDelete - Método delete que exclusão valores do banco de dados retorna o número de linhas afetadas
     *
     * @param string $sql 
     * @param mixed $params 
     * @return int|bool
     */
    public function dbDelete($sql, $params=null)
    {
        $query=$this->connect()->prepare($sql);
        
        try {
            
            $query->execute($params);
            $rs = $query->rowCount(); 
            
        } catch (Exception $exc) {
            throw $exc;
        }

        $this->disconnect();

        if ($rs == array()) {
            return false;
        } else {
            return $rs;
        }       
    }

    /**
     * dbBuscaDados - Método que retornar a posição atual do registro (OBJ)
     *
     * @param object $rscPdo 
     * @return object
     */
    public function dbBuscaDados($rscPdo)
    {
        return $rscPdo->fetch(PDO::FETCH_OBJ);
    }
    
    /**
     * dbBuscaDadosAll - Método que retornar todos os registros (OBJ)
     *
     * @param object $rscPdo 
     * @return object
     */
    public function dbBuscaDadosAll($rscPdo)
    {
        return $rscPdo->fetchAll(PDO::FETCH_OBJ);
    }
    
    /**
     * dbBuscaArray - Método que retornar a posição atual do registro (array)
     *
     * @param object $rscPdo 
     * @return array
     */
    public function dbBuscaArray( $rscPdo )
    {
        $aRegistro = $rscPdo->fetch(PDO::FETCH_ASSOC);

        if ($aRegistro === false) {
            return [];
        } else {
            return $aRegistro;
        }
    }

    /**
     * dbBuscaArrayAll - Método que retornar a posição atual do registro (array)
     *
     * @param object $rscPdo 
     * @return array
     */
    public function dbBuscaArrayAll($rscPdo)
    {
        return $rscPdo->fetchall(PDO::FETCH_ASSOC);
    }
    
    /**
     * dbNumeroLinhas - Método que retornar o Numero de linhas Selecionadas
     *
     * @param object $rscPdo 
     * @return int
     */    
    public function dbNumeroLinhas($rscPdo)
    {
        return $rscPdo->rowCount();
    }

    /**
     * dbNumeroColunas - Método que retornar o Numero de Colunas Selecionadas
     *
     * @param object $rscPdo 
     * @return int
     */
    public function dbNumeroColunas($rscPdo)
    {
        return $rscPdo->columnCount();
    }            
    
    /**
     * dbResultado
     *
     * @param object $rscRes 
     * @param array|string|int $CampoRetorno 
     * @return void
     */
    public function dbResultado($rscRes, $CampoRetorno)
    {
        $rowResX = $this->dbBuscaArray( $rscRes );
        
        return $rowResX[ $CampoRetorno ];
    }

    /**
     * select
     *
     * @param string $columns 
     * @return object
     */
    public function select($columns = "*")
    {
        $this->select = $columns;
        return $this;
    }

    /**
     * table
     *
     * @param string $table 
     * @return object
     */
    public function table($table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * union
     *
     * @param string $sql
     * @param array $params
     * @return object
     */
    public function union(string $sql, array $params = [])
    {
        $this->unions[] = ['type' => 'UNION', 'sql' => $sql, 'params' => $params];
        return $this;
    }

    /**
     * unionAll
     *
     * @param string $sql
     * @param array $params
     * @return object
     */
    public function unionAll(string $sql, array $params = [])
    {
        $this->unions[] = ['type' => 'UNION ALL', 'sql' => $sql, 'params' => $params];
        return $this;
    }

    /**
     * join
     *
     * @param string $table
     * @param string $condition
     * @param string $tipoJoin
     * @return object
     */
    public function join($table, $condition, $tipoJoin = "INNER")
    {
        $this->join .= ' ' . $tipoJoin . " JOIN " . $table . " ON " . $condition;
        return $this;
    }

    /**
     * buildCondition - base privada compartilhada por where/orWhere e having/orHaving
     *
     * @param string $target       referência à propriedade alvo ($where ou $having)
     * @param string $keyword      palavra-chave SQL (WHERE ou HAVING)
     * @param string|array $condition
     * @param string $params
     * @param string $operadorLogico
     * @return object
     */
    private function buildCondition(&$target, $keyword, $condition, $params, $operadorLogico)
    {
        $operadores = ["=", ">=", "<=", ">", "<", "<>"];

        if ($target == "") {
            $target = " {$keyword} ";
        } else {
            $target .= " {$operadorLogico} ";
        }

        if (is_string($condition)) {

            $aKey = explode(" ", $condition);

            if (count($aKey) > 1 && in_array(end($aKey), $operadores)) {
                $target .= $condition . " ? ";
            } else {
                $target .= $condition . " = ? ";
            }

            $this->params = array_merge($this->params, [$params]);

        } else {

            $lAnd = false;

            foreach ($condition as $key => $value) {

                if ($lAnd) {
                    $target .= " {$operadorLogico} ";
                } else {
                    $lAnd = true;
                }

                $aKey = explode(" ", $key);

                if (count($aKey) > 1 && in_array(end($aKey), $operadores)) {
                    $target .= $key . " ? ";
                } else {
                    $target .= $key . " = ? ";
                }
            }

            $this->params = array_merge($this->params, array_values($condition));
        }

        return $this;
    }

    /**
     * buildWhereIn - base privada compartilhada por whereIn e whereNotIn
     *
     * @param string $field
     * @param array $params
     * @param string $operadorLogico
     * @param bool $notIn
     * @return object
     */
    private function buildWhereIn($field, $params, $operadorLogico, $notIn)
    {
        $placeholders = array_fill(0, count($params), "?");
        $this->params = array_merge($this->params, $params);

        $clause = "{$field} " . ($notIn ? "NOT " : "") . "IN (" . implode(', ', $placeholders) . ")";

        if (empty($this->where)) {
            $this->where = " WHERE {$clause}";
        } else {
            $this->where .= " {$operadorLogico} {$clause}";
        }

        return $this;
    }

    /**
     * where
     *
     * @param string|array $condition
     * @param string $params
     * @return object
     */
    public function where($condition, $params = "")
    {
        return $this->buildCondition($this->where, 'WHERE', $condition, $params, 'AND');
    }

    /**
     * orWhere
     *
     * @param string|array $condition
     * @param string $params
     * @return object
     */
    public function orWhere($condition, $params = "")
    {
        return $this->buildCondition($this->where, 'WHERE', $condition, $params, 'OR');
    }

    /**
     * whereIn
     *
     * @param string $field
     * @param array $params
     * @param string $operadorLogico
     * @return object
     */
    public function whereIn($field, $params, $operadorLogico = 'AND')
    {
        return $this->buildWhereIn($field, $params, $operadorLogico, false);
    }

    /**
     * whereNotIn
     *
     * @param string $field
     * @param array $params
     * @param string $operadorLogico
     * @return object
     */
    public function whereNotIn($field, $params, $operadorLogico = 'AND')
    {
        return $this->buildWhereIn($field, $params, $operadorLogico, true);
    }

    /**
     * whereLike
     *
     * @param mixed $field 
     * @param strung $value 
     * @param string $operadorLogico 
     * @return object
     */
    public function whereLike($field, $value, $operadorLogico = "AND")
    {
        // Monta cláusula IN
        $clause = " {$field} LIKE ? ";

        // Setando valores
        $this->params[] = "%$value%";

        // Adiciona a cláusula WHERE
        if (empty($this->where)) {
            $this->where = " WHERE {$clause}";
        } else {
            $this->where .= " {$operadorLogico} {$clause}";
        }

        return $this;
    }

    /**
     * whereBetween
     *
     * @param string $field 
     * @param mixed $valorIni 
     * @param mixed $valorFim 
     * @param string $operadorLogico 
     * @return object
     */
    public function whereBetween($field, $valorIni, $valorFim, $operadorLogico = "AND")
    {
        // Monta cláusula IN
        $clause = " {$field} BETWEEN ? AND  ? ";

        // Setando valores
        $this->params[] = $valorIni;
        $this->params[] = $valorFim;

        // Adiciona a cláusula WHERE
        if (empty($this->where)) {
            $this->where = " WHERE {$clause}";
        } else {
            $this->where .= " {$operadorLogico} {$clause}";
        }

        return $this;
    }

    /**
     * group
     *
     * @param string $operadorLogico - AND, OR
     * @return object
     */
    public function group($operadorLogico = "AND")
    {
        $this->where .= $operadorLogico . " ( ";
        return $this;
    }

    /**
     * endGroup
     *
     * @return object
     */
    public function endGroup()
    {
        $this->where .= " ) ";
        return $this;
    }

    /**
     * groupBy
     *
     * @param string $column 
     * @return object
     */
    public function groupBy($column)
    {
        $this->groupBy = " GROUP BY $column";
        return $this;
    }

    /**
     * having
     *
     * @param string|array $condition
     * @param string $params
     * @return object
     */
    public function having($condition, $params = "")
    {
        return $this->buildCondition($this->having, 'HAVING', $condition, $params, 'AND');
    }

    /**
     * orHaving
     *
     * @param string|array $condition
     * @param string $params
     * @return object
     */
    public function orHaving($condition, $params = "")
    {
        return $this->buildCondition($this->having, 'HAVING', $condition, $params, 'OR');
    }

    /**
     * orderBy
     *
     * @param string $column
     * @param string $direction
     * @return object
     */
    public function orderBy($column, $direction = "ASC")
    {
        $this->orderBy = " ORDER BY " . $column . " " . $direction;
        return $this;
    }

    /**
     * limit
     *
     * @param int $limit
     * @param int|null $offset
     * @return object
     */
    public function limit($limit, $offset = null)
    {
        $this->limit  = (int) $limit;
        $this->offset = $offset !== null ? (int) $offset : null;
        return $this;
    }

    /**
     * prepareSelect
     *
     * @param string $tipoRetorno
     * @return array|int
     */
    public function prepareSelect($tipoRetorno = "all")
    {
        // Query base sem ORDER BY e LIMIT (necessário para posicionar UNIONs antes deles)
        $baseQuery = "SELECT {$this->select} FROM {$this->table} {$this->join} {$this->where} {$this->groupBy} {$this->having}";

        // Acumula parâmetros de cada cláusula UNION
        $allParams = $this->params;
        foreach ($this->unions as $union) {
            $baseQuery .= " {$union['type']} {$union['sql']}";
            $allParams  = array_merge($allParams, $union['params']);
        }

        if ($this->limit !== "") {
            if (self::$dbdrive === 'sqlsrv') {
                if ($this->offset !== null) {
                    $orderBy = $this->orderBy ?: " ORDER BY (SELECT NULL)";
                    $cSql = $baseQuery . $orderBy . " OFFSET {$this->offset} ROWS FETCH NEXT {$this->limit} ROWS ONLY";
                } else {
                    // TOP não pode ser usado diretamente com UNION — envolve em subquery
                    $inner = empty($this->unions) ? $baseQuery : "({$baseQuery}) AS __union_result";
                    $cSql  = empty($this->unions)
                        ? "SELECT TOP {$this->limit} {$this->select} FROM {$this->table} {$this->join} {$this->where} {$this->groupBy} {$this->having} {$this->orderBy}"
                        : "SELECT TOP {$this->limit} * FROM ({$baseQuery}) AS __union_result {$this->orderBy}";
                }
            } else {
                $limitSql = " LIMIT {$this->limit}";
                if ($this->offset !== null) {
                    $limitSql .= " OFFSET {$this->offset}";
                }
                $cSql = $baseQuery . $this->orderBy . $limitSql;
            }
        } else {
            $cSql = $baseQuery . $this->orderBy;
        }

        $query = $this->connect()->prepare($cSql, [PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL]);
        $query->execute($allParams);

        $this->dbClear();

        if ($tipoRetorno == "all") {
            return $this->dbBuscaArrayAll($query);
        } elseif ($tipoRetorno == "first") {
            return $this->dbBuscaArray($query);
        } elseif ($tipoRetorno == "count") {
            return $this->dbNumeroLinhas($query);
        }
    }

    /**
     * dbClear
     *
     * @return void
     */
    public function dbClear()
    {
        $this->select = "*";
        $this->join = "";
        $this->where = "";
        $this->groupBy = "";
        $this->having = "";
        $this->orderBy = "";
        $this->limit  = "";
        $this->offset = null;
        $this->params = [];
        $this->unions = [];
    }

    /**
     * findAll
     *
     * @return array
     */
    public function findAll()
    {
        return $this->prepareSelect();
    }

    /**
     * first
     *
     * @return array
     */
    public function first()
    {
        return $this->prepareSelect("first");
    }

    /**
     * findCount
     *
     * @return int
     */
    public function findCount()
    {
        return $this->prepareSelect("count");
    }

    /**
     * insert
     *
     * @param array $data 
     * @return int
     */
    public function insert(array $data)
    {
        try {
            $columns = implode(", ", array_keys($data));
            $placeHolders = ":" . implode(", :", array_keys($data));
            $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeHolders)";

            $conexao = $this->connect();
            $query = $conexao->prepare($sql);
            $query->execute($data);

            $rs = $conexao->lastInsertId();

            $this->dbClear();

        } catch (\Exception $err) {
            Session::set("msgError", "Erro ao inserir dados na base de dados: " . $err->getMessage());
            $rs = 0;
        }

        return $rs;
    }

    /**
     * update
     *
     * @param array $data
     * @return int
     */
    public function update(array $data)
    {
        try {
            $fields = implode(" = ?, ", array_keys($data)) . " = ?";

            if ($this->join !== "") {
                if (self::$dbdrive === 'sqlsrv') {
                    $sql = "UPDATE {$this->table} SET {$fields} FROM {$this->table} {$this->join} {$this->where}";
                } else {
                    $sql = "UPDATE {$this->table} {$this->join} SET {$fields} {$this->where}";
                }
            } else {
                $sql = "UPDATE {$this->table} SET {$fields} {$this->where}";
            }

            $updData = array_merge(array_values($data), $this->params);

            $query  = $this->connect()->prepare($sql);
            $query->execute($updData);

            $rs = $query->rowCount();

            $this->dbClear();

        } catch (\Exception $err) {
            Session::set("msgError", "Erro ao Atualizar dados na base de dados: " . $err->getMessage());
            $rs = -1;
        }

        return $rs;
    }

    /**
     * delete
     *
     * @return int
     */
    public function delete()
    {
        try {
            if ($this->join !== "") {
                $sql = "DELETE {$this->table} FROM {$this->table} {$this->join} {$this->where}";
            } else {
                $sql = "DELETE FROM {$this->table} {$this->where}";
            }

            $query  = $this->connect()->prepare($sql);
            $query->execute($this->params);

            $rs = $query->rowCount();

            $this->dbClear();

        } catch (\Exception $err) {
            Session::set("msgError", "Erro ao Excluir dados na base de dados: " . $err->getMessage());
            $rs = 0;
        }

        return $rs;
    }
}