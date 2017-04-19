<?php
/**
 * PDO Socket handle comunications and interactions
 * with database via PDO library.
 *
 * PHP version 5.6
 *
 * @author Francesco Bianco
 */

namespace Javanile\Moldable\Database\Socket;

use PDO;
use PDOException;
use Javanile\Moldable\Exception;

class PdoSocket implements SocketInterface
{	 
    /**
     *
     * @var type
     */
    private $_pdo = null;

    /**
     * 
     *
     */
    private $_args = null;

    /**
     *
     * @var type
     */
    private $_prefix = null;

    /**
     *
     * @var type
     */
    private $_database = null;

    /**
     *
     * @
     */
    public function __construct($database, $args)
    {
        $this->_database = $database;

        if (!$args || !is_array($args)) {
            $this->_database->errorConnect("required connection arguments");
        }

        //
        foreach (['host', 'dbname', 'username'] as $attr) {
            if (!isset($args[$attr])) {
                $this->_database->errorConnect("required connection attribute '{$attr}'");
            }
        }

        //
        $this->_args = $args;
        $this->_prefix = isset($args['prefix']) ? $args['prefix'] : '';

        // now connect to database
        $this->connect();
    }

    /**
     * Start PDO connection.
     */
    private function connect()
    {
        // get connection arguments
        $dsn = "mysql:host={$this->_args['host']};dbname={$this->_args['dbname']}";
        $username = $this->_args['username'];
        $password = $this->_args['password'];
        $options = [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'];

        // try to connect and create singletone
        try {
            $this->_pdo = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $ex) {
            $this->_database->errorConnect($ex);
        }

        // set PDO attributes
        $this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    /**
     *
     * @param type $sql
     * @return type
     */
    public function getRow($sql, $params=null)
    {
        $stmt = $this->execute($sql, $params);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     *
     * @param type $sql
     * @return type
     */
    public function getResults($sql, $params=null)
    {
        $stmt = $this->execute($sql, $params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     *
     * @param type $sql
     * @return type
     */
    public function getResultsAsObjects($sql, $params=null)
    {
        $stmt = $this->execute($sql, $params);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get single column elements.
     *
     * @param type $sql
     * @return type
     */
    public function getColumn($sql, $params=null)
    {
        $stmt = $this->execute($sql, $params);
        $column = array();

        while ($row = $stmt->fetch()) {
            $column[] = $row[0];
        }

        return $column;
    }

    /**
     *
     *
     * @param type $sql
     * @return type
     */
    public function getValue($sql, $params=null)
    {
        //
        $stmt = $this->execute($sql, $params);

        //
        return $stmt->fetchColumn(0);
    }

    /**
     * Return prefix passed on init attribute
     *
     * @return type
     */
    public function getPrefix()
    {
        return $this->_prefix;
    }

    /**
     * Return prefix passed on init attribute
     *
     * @return type
     */
    public function setPrefix($prefix)
    {
        $this->_prefix = $prefix;
    }

    /**
     * Return last insert id
     *
     * @return type
     */
    public function lastInsertId()
    {
        return $this->_pdo->lastInsertId();
    }

    /**
     * Return last insert id
     *
     * @return type
     */
    public function quote($string)
    {
        return $this->_pdo->quote($string);
    }

    /**
     * Starting a transaction to DB.
     *
     */
    public function transact()
    {
        $this->_pdo->beginTransaction();
    }

    /**
     * Transactional commit.
     *
     */
    public function commit()
    {
        $this->_pdo->commit();
    }

    /**
     * Transactional rollback.
     *
     */
    public function rollback()
    {
        $this->_pdo->rollBack();
    }

    /**
     * Execute a SQL query on DB with binded values
     *
     */
    public function execute($sql, $params=null)
    {
        $stmt = $this->_pdo->prepare($sql);

        if (is_array($params)) {
            foreach($params as $token => $value) {
                $stmt->bindValue($token, $value);
            }
        }

        try {
            $stmt->execute();
        } catch (PDOException $ex) {
            $this->_database->errorExecute($ex);
        }

        return $stmt;
    }
}