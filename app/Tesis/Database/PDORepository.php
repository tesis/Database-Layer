<?php //app/Tesis/Database/PDORepository.php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Class PDORepository
 *
 * PHP version 5.6
 *
 * @category   Login_System
 * @package    Login_System
 * @subpackage User_Class
 * @author     Tereza Simcic <tereza.simcic@gmail.com>
 * @copyright  2015 Tereza Simcic
 * @license    Tereza Simcic
 *
 *
 * @link       https://github.com/tesis/login
 * @name       PDORepository.php
 *
 *
 *
 * Short description
 * a database layer based on PDO library
 *
 * Long description
 * a database layer based on PDO library
 * including all actions to perform CRUD and search processes
 * TDD tests can be found in tests folder, BDD in spec folder
 *
 */

namespace Tesis\Database;

use \PDO;
use Tesis\Database\Faces\AdapterInterface;
use Tesis\Database\Traits\PDOconnection;

//using const just for test
require_once('app/config/config.php');

class PDORepository implements AdapterInterface, \Countable
{
    use PDOconnection;

    /**
     * @access protected
     * @var string
     *
     */
    public $conn;
    /**
     * fields from original db table
     * @access private
     * @var array
     *
     */
    public $fields;
    /**
     * @access protected
     * @var array
     *
     * data passed
     *
     */
    public $data = [];

    /**
     * @access protected
     * @var array
     *
     */
    public $tableFields = [];

    /**
     * @access protected
     * @var array
     *
     */
    public $tableFieldsType = [];
    /**
     * @access protected
     * @var string
     *
     */
    public $query;
    /**
     * where statement from child class
     * @access private
     * @var string
     *
     */
    public $where = '';
    /**
     * @access private
     * @var string
     *
     */
    public $limit = 1;
    /**
     * @access private
     * @var string
     *
     */
    public $select = '*';
    /**
     * @access private
     * @var string
     *
     */
    public $orderBy = '';
    /**
     * @access private
     * @var string
     *
     */
    public $groupBy = '';
    /**
     * @access private
     * @var string
     * TODO:join statements
     */
    public $join = '';
    /**
     * @access protected
     * @var string
     *
     */
    public $bind;
    /**
     * @access protected
     * @var array
     *
     */
    protected $errors = [];
    /**
     * @access protected
     * @var int
     *
     */
    protected $countable;


    public function __construct(array $data=null)
    {
        $this->conn = $this->openConnection(DB_HOST,DB_NAME,DB_CHARSET,DB_USER, DB_PASS);

        $this->dbName = DB_NAME;

        if(empty($this->dbName))
        {
            throw new \Exception ('Database name seems to be empty');
        }

        if(!is_null($data))
        {
            $this->data = $data;
            if (!empty($data))
            {
                foreach ($data as $key => $value)
                {
                    if (!array_key_exists($key, $data)) continue;

                    if ($key == 'table')
                    {
                        $this->table = $value;
                    }
                    if (isset($this->tablePK) && $key == $this->tablePK)
                    {
                        $this->id = $value;
                    }
                    if (isset($this->dbFields) && in_array($key, $this->dbFields))
                    {
                        $this->$key = $value;
                    }
                }
            }
        }
    }
    public function __destruct()
    {
        //echo "call __destruct method";

    }
    /**
     * exec execute query
     *
     * @param string $sql
     *
     * @return
     *
    */
    public function exec($sql)
    {
        if(empty($sql))
        {
            return false;
        }
        $affectedRows = $this->conn->query($sql);
        return $affectedRows;
    }
    /**
     * count count items implementing php interface 'countable'
     *
     * @return int
     *
    */
    public function count()
    {
        return count($this->countable);
    }
    /**
     * describeTable
     *
     * @access protected
     *
     * @return array
     *
     */
    public function describeTable()
    {
        $driver = $this->conn->getAttribute(PDO::ATTR_DRIVER_NAME);

        $sql = "DESCRIBE " . $this->dbName . '.' . $this->table;

        try {
            return $this->describeTableExecute($sql);
        }
        catch(PDOException $e) {
            $this->errors[] = $e->getMessage();
            return false;
        }
    }
    /**
     * describeTableExecute: from describe input child class execute query
     *                       we get a list of all fields in the table
     *                       we need to compare with data sent to the object
     *
     *                       ex: if we have a field in data that is not
     *                       in the table, would be left out
     *
     * @param string $sql describe statement for particular table
     *
     * @access private
     *
     * @return void
     *
     *
     */
    public function describeTableExecute($sql = '')
    {
        if (empty($sql))
        {
            return false;
        }
        $sth = $this->conn->prepare($sql);

        if ($sth->execute() !== false)
        {
            if (false !== ($list = $sth->fetchAll(PDO::FETCH_ASSOC)))
            {
                $this->assignTableProperties($list);
                return $this->fieldsToProcess();
            }
        }
        return false;
    }
    /**
     * assignTableProperties: get list of table columns
     *
     * @param array $list list of fields from db table
     *
     * @access private
     *
     * @return void
     *
     */
    public function assignTableProperties(array $list = null)
    {
        if ($list == null)
        {
            return false;
        }
        $keyField = "Field";
        $keyType = "Type";
        foreach ($list as $key => $record)
        {
            $this->tableFields[$key] = $record[$keyField];

            $this->tableFieldsType[$key] = $record[$keyType];
        }
    }
    /**
     * fieldsToProcess: compare fields of db table and data
     *                  passed from child class/outside
     *
     * @access private
     *
     * @return array of fields to proces (insert/update)
     *
     */
    public function fieldsToProcess()
    {
        $arr = array_intersect($this->tableFields, array_keys($this->data));
        $this->fields = array_values($arr);
        return ($this->fields != null) ? $this->fields : false;
    }

    /*________________________ CRUD ___________________________*/
    /**
     * create using execute method instead of bindValue/bindParam...
     *        thus all inserted values are treated as stings
     *        PDO::PARAM_STR
     *
     * @param array $data array of data to compare with table field
     *
     * @access public
     *
     * @return string
     *
     *
    */
    public function create(array $data=null)
    {
        if(!is_null($data))
        {
            $this->data = $data;
        }
        $this->describeTable();
        $this->insertSql();
        $this->processBind();
        try {
            $sth = $this->conn->prepare($this->query);
            if ($sth->execute($this->bind) !== false)
            {
                //returns a string
                $this->countable = $this->conn->lastInsertId();
                return $this->conn->lastInsertId();
            }
        }
        catch(PDOException $e) {
            $this->errors[] = $e->getMessage();
            return false;
        }
    }
    /**
     * insertSql prepare insert query using named placeholders
     *
     * @access protected
     *
     * @return string
     *
     */
    public function insertSql()
    {
        $sql = "INSERT INTO " . $this->dbName . '.' . $this->table
                . " (" . implode($this->fields, ", ") . ")
                VALUES (:" . implode($this->fields, ", :") . ");";

        $this->query = $sql;
    }
    /**
     * processBind - bind values with named placeholders
     *              (:username => 'tesss', ...)
     *
     * @access protected
     *
     * @return void
     *
     */
    public function processBind()
    {
        $bind = [];

        foreach ($this->fields as $field)
        {

            if($field == $this->tablePK)
            {
                $bind[":id"] = $this->data[$field];
            }
            else
            {
                $bind[":$field"] = $this->data[$field];
            }
        }
        $this->bind = $bind;
    }
    /**
    * update
    *
    * @param $data array of data to process
    *
    * @return int
    *
    */
    public function update(array $data=null)
    {
        if(!is_null($data))
        {
            $this->data = $data;
        }
        if(empty($this->fields))
        {
            $this->describeTable();
        }
        $this->updateSql();
        $this->processBind();

        try {
            $sth = $this->conn->prepare($this->query);
            $sth->bindValue(':id', 16, PDO::PARAM_STR);
            if ($sth->execute($this->bind) !== false)
            {
                return $sth->rowCount();
            }
        }
        catch(PDOException $e) {
            $this->errors[] = $e->getMessage();
            return false;
        }
    }
    /**
     * prepareNamedPlaceholder update record
     *
     * @param bool $update true
     *
     * @access private
     *
     * @return string
     *
     */
    public function prepareNamedPlaceholder($update=true)
    {
        $arr = [];

        for ($i = 0; $i < sizeof($this->fields); ++$i)
        {
            if($update == true && $this->fields[$i] == $this->tablePK)
            {
                //no need to update PK
                unset($this->fields[$this->tablePK]);
            }
            else
            {
                $arr[] = $this->fields[$i] . " = :" . $this->fields[$i];
            }
        }
        return implode(',', $arr);
    }
    /**
     * updateSql - build sql query for update
     *
     * @access protected
     *
     * @return void
     *
     */
    protected function updateSql()
    {
        $sql = "UPDATE " . $this->dbName . "." . $this->table . " SET ";
        $preparedPlaceholders = $this->prepareNamedPlaceholder();

        $sql.= $preparedPlaceholders . " WHERE " . $this->tablePK . "=:id;";

        $this->query = $sql;
    }
    /**
     * delete
     *
     * @param int $id id of the record
     *
     * @access public
     *
     * @return int/bool
     *
    */
    public function delete($id ='')
    {
        if(empty($id)) return false;

        $sql = "DELETE FROM " . $this->dbName . '.' . $this->table
                . " WHERE " . $this->tablePK . '= :id';
        try {
            $sth = $this->conn->prepare($sql);
            $sth->bindValue(':id', $id, PDO::PARAM_STR);
            if ($sth->execute() !== false)
            {
                return $sth->rowCount();
            }
        }
        catch(PDOException $e){
            $this->errors[] = $e->getMessage();
            return false;
        }
    }

    /*__________________ build select statements ____________________*/
    /**
    * checkInput check if fields requested by user exists in DB table
    *
    * @param string $fields comma separated fields, or |
    *
    * @return string/bool
    *
    */
    public function checkInput($fields = '')
    {
        if($fields == '*') return false;
        $this->describeTable();

        //$fields = explode(', ', $fields);
        $fields = preg_split('/ ?[,|] ?/', $fields);

        $arr = [];
        foreach($fields as $f)
        {
            if(!in_array($f, $this->tableFields))
            {
                continue;//just remove unsafe fields
            }
            else
            {
                $arr[] = $f;
            }
        }
        return (!empty($arr)) ? implode(',' , $arr) : false;
    }
    /**
     * checkInputArray
     *
     * @param array $fields an array of fields with values
     *
     * @return array
     *
    */
    public function checkInputArray(array $fields = null)
    {
        if(is_null($fields)) return false;

        $this->describeTable();

        $arr = [];
        foreach($fields as $key => $val)
        {
            if(!in_array($key, $this->tableFields))
            {
                continue;//just remove unsafe fields
            }
            $arr[] = $key . '="' . $val . '"';
        }

        return (!empty($arr)) ? $arr : false;
    }
    /**
     * select
     *
     * @param string $fields comma or pipe separated fields
     *
     * @return
     *
    */
    public function select($fields='*')
    {
        if($fields == '*')
        {
            $this->select = '*';
        }
        else
        {
            $this->select = $this->checkInput($fields);
        }
        return $this;
    }
    /**
     * where
     *
     * @param array $array an array of filelds and values
     *
     * @return
     *
    */
    public function where(array $array=null)
    {
        //$array - array of keys(fields) and values
        $checkInput = $this->checkInputArray($array);

        $this->where = ' WHERE ' . implode($checkInput);
        return $this;
    }
    /**
     * groupBy
     *
     * @param string $field
     *
     * @return
     * TODO: expand options
    */
    public function groupBy($field)
    {
        $this->groupBy = ' GROUP BY ' . $field ;
        return $this;
    }
    /**
     * orderBy
     *
     * @param string $field
     * @param string $mode optional, by default ASC
     *
     * @return
     *
    */
    public function orderBy($field, $mode='ASC')
    {
        $this->orderBy = ' ORDER BY ' . $field . ' ' . $mode;
        return $this;
    }
    /**
     * get concatenate all options and output final sql
     *     get() cannot be combined with first() or all()
     *
     * @return string
     *
     * example: $test->where(['uid'=>1])->groupBy('username')->orderBy('uid')->get(3);
     * $test is an instance of child class
     *
    */
    public function get($limit='')
    {
        if(!empty($limit))
        {
            $limit = ' LIMIT ' . $limit;
        }

        $sql = 'SELECT ' . $this->select
               . ' FROM ' . $this->dbName. '.'.$this->table
               . $this->join
               . $this->where
               . $this->groupBy
               . $this->orderBy
               . $limit;

        $this->query = $sql;
        return $this->query;
    }
    /**
     * first find only one row
     *
     * @return
     *
     * example: $test->where(['uid'=>1])->groupBy('username')->orderBy('uid')->first();
    */
    public function first()
    {
        $sql = $this->get() . ' LIMIT 1';
        $this->query = $sql;
    }
    /**
     * all find all rows with particular filters
     *
     * @return
     *
     * example: $this->db->groupBy('name')->all()
    */
    public function all()
    {
        $sql = $this->get();
        $this->query = $sql;
    }
    /**
     * all find all rows with particular filters
     *
     * @return
     *
     * example: $this->db->groupBy('name')->all()
    */
    public function fetch()
    {
        try {
            $sth = $this->conn->prepare($this->query);
                if ($sth->execute($this->bind) !== false)
                {
                    if ($sth->execute() !== false) {
                        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
                        $this->countable = sizeof($result);
                        echo 'Countable: ' . $this->countable;
                    return $result;
                }
            }
        }
        catch(PDOException $e) {
            $this->errors[] = $e->getMessage();
            return false;
        }
    }

}
