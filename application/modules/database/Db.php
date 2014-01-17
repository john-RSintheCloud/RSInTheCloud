<?php


/**
 * Database object - basic query runner
 *
 * @author John
 */
class database_Db
{
    /**
     *
     * @var PDO
     */
    protected $_connection;

    protected $_stats = array();

    protected $_data;

    public function __construct($connection = null)
    {
        if ($connection instanceof PDO){
            $this->setConnection($connection);
        }

        $this->_stats['queryLog'] = array();
        $this->_stats['querytime'] = 0;

        return $this;
    }

    protected function getConnection()
    {
        if (!$this->_connection instanceof PDO){
            throw new InvalidArgumentException('Trying to get a connection before it is set');
        }
        return $this->_connection;
    }

    public function setConnection(PDO $connection)
    {
        $this->_connection = $connection;
        return $this;
    }


    public function getStats()
    {
        return $this->_stats;
    }

    public function getData()
    {
        return $this->_data;
    }


    public function sqlQuery($sql = '')
    {
        $qTime = new timer();
        if ( empty($sql)){
            throw new InvalidArgumentException('Empty query supplied to sqlQuery');
        }
        try {
            $data = $this->getConnection()
                ->query($sql );

        $this->_data = $data->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $exc) {
            throw new InvalidArgumentException ('Invalid sql statement supplied to sqlQuery: ' . $sql);
        }

        $this->saveStats($qTime->show(), $sql);

        return count($this->_data);
    }

    protected function saveStats($time, $sql)
    {
        $queryLog = $this->_stats['queryLog'];
        if (isset($queryLog[$sql])) {
            $queryLog[$sql]['dupe'] = $queryLog[$sql]['dupe'] + 1;
            $queryLog[$sql]['time'] = $queryLog[$sql]['time'] + $time;
        } else {
            $queryLog[$sql]['dupe'] = 1;
            $queryLog[$sql]['time'] = $time;
        }

        $this->_stats['querytime'] += $time;

        return $this;

    }

//     _____________________________________     Fetchers

    public function fetchAll($sql = '')
    {

        if ($sql){
            $this->sqlQuery($sql);
        }
        return $this->fetchSome();
    }


    public function fetchSome($length = 0 , $offset = 0)
    {
        if (empty($this->_data)|| !is_array($this->_data)){
            return false;
        }

        $length = intval($length);
        $offset = intval($offset) - 1;
        if ( $offset < 1) {
            $offset = 0;
        }

        $data = $this->getData();

        $count = count($data);

        if ($count <= $offset) {
             return false;
        }

        $end = $offset + $length;
        if ($length == 0 || $end >= $count){
            $end = $count;
        }

        $ret = array();
        for($nx= $offset; $nx < $end; $nx++){
            $ret[] = $data[$nx];
        }

        return $ret;
    }

}
