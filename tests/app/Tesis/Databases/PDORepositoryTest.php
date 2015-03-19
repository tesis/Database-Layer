<?php //tests/app/Tesis/Database/DataobjectTest.php

use Tesis\Database\PDORepository as DataObject;
use Mockery as m;

class DataobjectTest extends PHPUnit_Framework_TestCase
{

    public $classRepo;
    public $dbName;

    public function setUp()
    {
        $this->table = 'users';
        $this->dbName = 'pdoTest';

        $this->db = new DataObject();

        $this->classRepo = 'Tesis\Database\PDORepository';

        $this->colList = array(0=>['Fields'=>'uid', 'Type'=>'int(11)'], 1=>['username'=>'varchar(30)']);
        $this->data = ['table'=>$this->table, 'username' => 'tesi', 'password' => 'ssss'];

        //on finished tests you may drop database
        //before creating one, first remove old one
            //$this->dropDBTables();
            //$this->createTable();
    }

    public function tearDown()
    {
        unset($this->db);
    }
    /**
     * createTable create database and table
     *
    */
    protected function createTable()
    {
        $sql = "CREATE DATABASE IF NOT EXISTS " . $this->dbName;
        $execDB = $this->db->exec($sql);

        $sql = "CREATE TABLE IF NOT EXISTS ".$this->dbName.".".$this->table. "(
            `uid` int(11) NOT NULL AUTO_INCREMENT,
            `username` varchar(30) NOT NULL,
            `fname` varchar(15) NOT NULL,
            `lname` varchar(15) NOT NULL,
            `email` varchar(255) NOT NULL,
            `password` varchar(255) NOT NULL,
            `created` datetime NOT NULL,
            `updated` datetime NOT NULL,
            `salt` varchar(100) NOT NULL,
            `act` tinyint(2) NOT NULL DEFAULT '0',
            PRIMARY KEY (`uid`)
          ) ENGINE=InnoDB";
        $exec = $this->db->exec($sql);

        if($exec){
            $this->fillTable();
        }
        //echo 'Created db: ' . $this->dbName . ' and table ' . $this->table;
    }
    /**
     * fillTable fill table for testing
     *
    */
    protected function fillTable()
    {
        $sql = "INSERT INTO ".$this->dbName.".".$this->table." (`uid`, `username`, `fname`, `lname`, `email`, `password`, `created`, `updated`, `salt`, `act`) VALUES
(1, 'tesis', 'Tereza', 'Simcic', 'tereza.simcic@gmail.com', 'e2133e2b674cba6c568091e9b938db09db9a345d53d8edd79e3bb882bc17727f', '2014-12-18 21:09:10', '0000-00-00 00:00:00', '2911944a279485802f15cb0eb4e2467f', 1),
(2, 'test', 'tereza', 'tesss', 'test@test.si', '18389f7816a48e452bb78bae816543890d8f283b5d4cb05dfe91481622b1a554', '2014-12-20 11:55:57', '0000-00-00 00:00:00', '1761edeb98ab2f7393e5048c1bac1fa0', 0),
(3, 'tesi', 'test', 'tesi', 'test1@test.si', '87cf48e952430d6324356e036a9bf37709c095fd6ad9469c917df971b33c9e6f', '2014-12-22 12:26:35', '0000-00-00 00:00:00', '8ad20cf1816506262791450d0c107118', 0);
";
        $this->db->exec($sql);
        return true;
    }
    /**
     * dropDBTables drop database and tables on finishing tests
     *
    */
    protected function dropDBTables()
    {
        $sql = "DROP database " . $this->dbName;
        $this->db->exec($sql);
        return false; //to exclude all tests
    }
    /**
     * test_If_Variables_for_DB_AND_Tables_Defined
     *
     * @param $a variable to test
     * @param $expected the class we expected to be in
     *
     * @dataProvider variablesProvider
     *
    */
    public function test_If_Variables_for_DB_AND_Tables_Defined($a, $expected)
    {
        $actual = $this->classRepo;

        $this->assertClassHasAttribute($a, $actual, 'Expected Pass');
    }
    /**
    *
    * test_ProcessBind_If_Class_Has_Bind_Variable
    *
    */
    public function test_ProcessBind_If_Class_Has_Bind_Variable()
    {
        $this->assertClassHasAttribute('bind', $this->classRepo, 'Expected Pass');
    }
    /**
     * test_If_Variables_for_SQL_Defined
     *
     * @param $a variable to test
     * @param $expected the class we expected to be in
     *
     * @dataProvider variablesProvider
     *
    */
    public function test_If_Variables_for_SQL_Defined($a, $expected)
    {
        $actual = $this->classRepo;

        $this->assertClassHasAttribute($a, $actual, 'Expected Pass');
    }
    /**
    *
    * variablesProvider
    *
    * a provider for test_If_Variables_for_SQL_Defined
    *
    */
    public function variablesProvider()
    {
        return array(
            array('query', $this->classRepo, 'Expected Pass'),
            array('bind', $this->classRepo, 'Expected Pass'),
            array('select', $this->classRepo, 'Expected Pass'),
            array('where', $this->classRepo, 'Expected Pass'),
            array('orderBy', $this->classRepo, 'Expected Pass'),
            array('groupBy', $this->classRepo, 'Expected Pass'),
        );
    }
    /**
    *
    * test_Connection_Expected_Pass
    *
    */
    public function test_Connection_Expected_Pass()
    {
        $this->assertFalse(false, $this->db->conn);
        $this->assertInstanceOf('PDO', $this->db->conn);
    }
    /**
    *
    * test_DescribeTable_If_Driver_Is_Mysql_Expected_Pass
    *
    */
    public function test_DescribeTable_If_Driver_Is_Mysql_Expected_Pass()
    {
        $this->assertSame('mysql', $this->db->conn->getAttribute(PDO::ATTR_DRIVER_NAME));
    }
    /**
    *
    * @test_DescribeTableExecute_If_Empty_SQL_Expected_Fail
    *
    */
    public function test_DescribeTableExecute_If_Empty_SQL_Expected_Fail()
    {
        $sql = '';
        $result = $this->db->describeTableExecute($sql);
        $this->assertFalse(false, $result ,'Expected to fail');
        $this->assertSame(false, $result);
    }
    /**
    *
    * test_DescribeTableExecute_Expected_Pass
    *
    */
    public function test_DescribeTableExecute_Expected_Pass()
    {
        //data we pass to DB object
        $test = new DataObject($this->data);

        $result = $test->describeTable();
        //print_r($result);
        $this->assertFalse(false, 'Expected to pass');
        //fields in array that can be processed further
        $this->assertContains('username', $result, 'Expected to pass');
        $this->assertContains('password', $result, 'Expected to pass');
    }
    /**
    *
    * test_AssignTableProperties_Expected_Pass
    *
    */
    public function test_AssignTableProperties_Expected_Pass()
    {
        $list = $this->colList;

        $sql = 'DESCRIBE ' . $this->dbName . '.' . $this->table;
        $this->assertNotEmpty($list);

        $sth = $this->db->conn->prepare($sql);

        $this->assertNotEmpty($sth);
        $exec = $sth->execute();

        $this->assertTrue(true, $exec, 'Expected Pass');
        $this->assertEquals($sth->queryString, $sql, 'Expected Pass');
    }
    /**
     * test_If_DBName_NotIncluded_In_Query_Expected_Fail
     *
     * @expectedException     PDOException
     * @expectedExceptionCode 3D000
     *
     */
    public function test_If_DBName_NotIncluded_In_Query_Expected_Fail()
    {
        $sql = 'DESCRIBE '  . $this->table;
        $sth = $this->db->conn->prepare($sql);
        $this->assertTrue($sth);

    }
    /**
     * test_If_Table_NotIncluded_In_Query_Expected_Fail
     *
     * @expectedException     PDOException
     * @expectedExceptionCode 3D000
     *
     */
    public function test_If_Table_NotIncluded_In_Query_Expected_Fail()
    {
        $sql = 'DESCRIBE '  . $this->dbName;
        $sth = $this->db->conn->prepare($sql);
        $this->assertTrue($sth);
    }

}
