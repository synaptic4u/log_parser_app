<?php

namespace Synaptic4UParser\DB;

use Exception;
use PDO;
use Synaptic4UParser\Logs\Activity;
use Synaptic4UParser\Logs\Error;
use Synaptic4UParser\Logs\Log;

class SQLITE implements IDBInterface
{
    protected $lastinsertid = -1;
    protected $rowcount = -1;
    protected $conn;
    protected $status;
    protected $pdo;

    public function __construct()
    {
        try {
            $filepath = dirname(__FILE__, 4).'/db_sqlite_config.json';

            //  Returns associative array.
            $this->conn = json_decode(file_get_contents($filepath), true);

            $dsn = 'mysql:host='.$this->conn['host'].';dbname='.$this->conn['dbname'];

            //  Create PDO instance.
            $this->pdo = new PDO($dsn, $this->conn['user'], $this->conn['pass']);
        } catch (Exception $e) {
            $this->error([
                'Location' => __METHOD__.'()',
                'error' => $e->__toString(),
            ]);

            $result = null;
        }
    }

    public function query($params, $sql): mixed
    {
        try {
            $result = [];
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare($sql);

            $this->status = ($stmt->execute($params)) ? 'true' : 'false';

            $this->lastinsertid = $this->pdo->lastInsertId();

            if (sizeof($params) > 0) {
                $this->pdo->commit();
            }

            $this->rowcount = $stmt->rowCount();

            $result = $stmt->fetchAll();

            $stmt = null;
        } catch (Exception $e) {
            $this->error([
                'Location' => __METHOD__.'()',
                'pdo->errorInfo' => $this->pdo->errorInfo(),
                'error' => $e->__toString(),
                'stmt' => $stmt,
                'sql' => $sql,
                'params' => $params,
            ]);

            $result = null;
            $stmt = null;
            $this->pdo = null;
        } finally {
            return $result;
        }
    }

    public function getLastId(): int
    {
        return $this->lastinsertid;
    }

    public function getrowCount(): int
    {
        return $this->rowcount;
    }

    public function getStatus(): mixed
    {
        return $this->status;
    }

    /**
     * Error logging.
     *
     * @param array $msg : Error message
     */
    protected function error($msg)
    {
        new Log($msg, new Error());
    }

    /**
     * Activity logging.
     *
     * @param array $msg : Message
     */
    protected function log($msg)
    {
        new Log($msg, new Activity());
    }
}
