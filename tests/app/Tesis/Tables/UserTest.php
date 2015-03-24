<?php //tests/app/Tesis/Tables/UserTest.php

use Tesis\Tables\User as User;

//TODO: if email exists, if username exists
//TODO: validation
class UserTest extends PHPUnit_Framework_TestCase
{
    public $classRepo;
    public $user;
    public $dbName;
    public $table;

    public function setUp()
    {
        $this->table = 'users';
        $this->dbName = 'pdoTest';

        $this->classRepo = 'Tesis\Tables\User';

        $this->data = ['table'=> $this->table, 'username' => 'tesi', 'password' => 'ssss'];
        //class has to be initialized before any tests
        $this->user = new User($this->data);
    }

    public function tearDown()
    {
        unset($this->db);
    }

    /**
     * test_If_Variables_for_DB_AND_Tables_Defined
     *
     * @param $a variable to test
     * @param $expected the class we expected to be in
     *
     * @dataProvider variablesDBProvider
     *
    */
    public function test_If_Variables_for_User_Defined($a, $expected)
    {
        $actual = $this->classRepo;

        $this->assertClassHasAttribute($a, $actual, 'Expected Pass');
    }
    /**
    *
    * variablesDBProvider
    *
    * a provider for test_If_Variables_for_DB_AND_Tables_Defined
    *
    */
    public function variablesDBProvider()
    {
        return array(
            array('table', $this->classRepo, 'Expected Pass'),
            array('tablePK', $this->classRepo, 'Expected Pass'),
            array('dbFields', $this->classRepo, 'Expected Pass'),
            array('required', $this->classRepo, 'Expected Pass'),
        );
    }
    /**
    *
    * test_CheckInput_Expected_Pass
    *
    */
    public function test_CheckInput_Expected_Pass()
    {
        $fields = 'uid, username';

        $result = $this->user->checkInput($fields);
        $this->assertSame('uid,username', $result, 'Expected Pass');
    }
    /**
    *
    * testCheckInput_If_We_Expect_Space_After_Comma_Expected_Fail
    */
    public function testCheckInput_If_We_Expect_Space_After_Comma_Expected_Fail()
    {
        $fields = 'uid,username';

        $result = $this->user->checkInput($fields);
        $this->assertNotSame('uid, username', $result, 'Expected Fail');
    }
    /**
    *
    * test_CheckInputArray_Expected_Pass
    *
    */
    public function test_CheckInputArray_Expected_Pass()
    {
        $fields = ['username'=>'tesi','uid'=> 1];

        $result = $this->user->checkInputArray($fields);

        $this->assertSame('username="tesi" AND uid="1"', $result, 'Expected Pass - getting prepared array');

    }
    /**
    *
    * test_CheckInputArray_Passing_NON_Existing_Column_Expected_Pass
    *
    */
    public function test_CheckInputArray_Passing_NON_Existing_Column_Expected_Pass()
    {
        $fields = ['username'=>'tesi','uid'=> 1, 'test'=>'sss'];

        $result = $this->user->checkInputArray($fields);

        $this->assertSame('username="tesi" AND uid="1"', $result, 'Expected Pass - getting prepared array, redundant field removed');

    }
//User story test
    /**
     * countUsers count users to make comparisions
     * helper test
     *
    */
    public function test_countUsers_Expected_Pass()
    {
        $user = new User;
        $select = $user->select('uid,user')->all();
        $res = $user->fetch();
        $count = sizeof($res);

        $this->assertNotEquals(0, 'Expected Pass');
        return $count;
    }
    /**
     * test_Create_User_Pass
     *
     * dependency test: create user, then update user and delete a user
     * return lastUserId
     *
    */
    public function test_Create_User_Pass()
    {
        $data = ['table'=> $this->table, 'username' => 'tesi'.time(), 'email' => time().'@test.si', 'password' => 'ssss'];
        $create = $this->user->create($data);
        echo "\n user created: " . $create . "\n";

        $count = $this->test_countUsers_Expected_Pass();
        echo "\n size: " . $count;

        $this->assertGreaterThanOrEqual($count, $create, 'Expected Pass');

        return $create;
    }
    /**
     * test_Update_User_Pass
     *
     * @depends test_Create_User_Pass
     *
    */
    public function test_Update_User_Pass($userId)
    {
        $this->data = ['table'=>$this->table, 'uid' => $userId, 'username' => 'tesi'.time(), 'password' => '56sf15fssd'.time(), 'email' => 'test'.time().'@test.si'];
        $updated = $this->user->update($this->data);
        echo 'Updated user ' . $userId ;
        $this->assertSame(1, $updated, 'Expected Pass');
    }
    /**
     * test_Delete_User_Pass
     *
     * @depends test_Create_User_Pass
     *
    */
    public function test_Delete_User_Pass($userId)
    {
        $this->data = ['table'=>$this->table, 'uid' => '22', 'username' => 'tesi', 'password' => 'ssss', 'email' => 'test@test.si'];
        $delete = $this->user->delete($userId);
        echo 'Deleted user: ' . $userId;
        $this->assertSame(1, $delete, 'Expected Pass');

    }
    /**
    * test_Delete_Missing_ID_Expected_Fail
    *
    */
    public function test_Delete_Missing_ID_Expected_Fail()
    {
        $data = ['table'=> $this->table, 'username' => 'tesi'.time(), 'password' => 'ssss'];
        $test = $this->user;

        $result = $test->delete();

        $this->assertSame(false, $result, 'Expected fail');
    }
    public function test_find_user_Pass()
    {
        $this->user->where(['uid'=>1])->orderBy('uid')->groupBy('username')->all();

        $expected = 'SELECT * FROM '.$this->dbName.'.'.$this->table.' WHERE uid="1" GROUP BY username ORDER BY uid ASC';
        $this->assertSame($expected, $this->user->query, 'Expected Pass');
    }
    /**
    * testDelete_Record_That_Does_Not_Exist_Expected_Fail
    *
    */
    public function testDelete_Record_That_Does_Not_Exist_Expected_Fail()
    {
        $data = ['table'=>$this->table, 'username' => 'tesi', 'password' => 'ssss'];
        $test = $this->user;

        $result = $test->delete(10009);//output rowCount -> ie: 0

        $this->assertSame(0, $result, 'Expected fail');
    }
    /**
    *
    * test_Chained_Queries_Select_Where_GroupBy_OrderBy_first
    *
    */
    public function test_Chained_Queries_Select_Where_GroupBy_OrderBy_first()
    {
        $test = $this->user;
        $test->select('uid,user')->where(['uid'=>1])->groupBy('email')->orderBy('uid')->first();
        $result = $test->query;
        $expected = 'SELECT uid FROM '.$this->dbName.'.'.$this->table.' WHERE uid="1" GROUP BY email ORDER BY uid ASC LIMIT 1';
        $this->assertSame($expected, $result, 'Expected Pass');
    }
    /**
    *
    * test_Chained_Queries_Without_Select_first
    *
    */
    public function testChained_Queries_Without_Select_first()
    {
        $test = $this->user;
        $test->where(['uid'=>1])->groupBy('username')->orderBy('uid')->first();
        $result = $test->query;
        $expected = 'SELECT * FROM '.$this->dbName.'.'.$this->table.' WHERE uid="1" GROUP BY username ORDER BY uid ASC LIMIT 1';
        $this->assertSame($expected, $result, 'Expected Pass');
    }
    /**
    *
    * test_Chained_Queries_All_Pass
    *
    */
    public function test_Chained_Queries_All_Pass()
    {
        $test = $this->user;
        $test->where(['uid'=>1])->groupBy('username')->orderBy('uid')->all();
        $result = $test->query;
        $expected = 'SELECT * FROM '.$this->dbName.'.'.$this->table.' WHERE uid="1" GROUP BY username ORDER BY uid ASC';
        $this->assertSame($expected, $result, 'Expected Pass');
    }
    /**
     * test_if_Exception_is_Thown_Using_Get_And_First_In_Same_Query
     *
    */
    public function test_if_Exception_is_Thown_Using_Get_And_First_In_Same_Query()
    {
        $test = $this->user;
        $test->where(['uid'=>1])->orderBy('uid')->groupBy('username')->all();
        //print_r($test);
        $result = $test->fetch();
        $expected = 'SELECT * FROM '.$this->dbName.'.'.$this->table.' WHERE uid="1" GROUP BY username ORDER BY uid ASC';
    }
    /**
     * test_Get_User_By_Params_Where_Array_Pass
     *
    */
    public function test_Get_User_By_Params_Where_Array_Pass()
    {
        $params = ['uid'=>1,'username' => 'tesis'];
        $test = $this->user;

        $test->where($params)->first();

        $result = $test->fetch();
        $this->assertNotEmpty($result, 'Expected Pass');
    }
    /**
     * test_Get_User_By_Params_WhereOr_Array_Pass
     *
    */
    public function test_Get_User_By_Params_WhereOr_Array_Pass()
    {
        $params = ['username'=>'tesi','username' => 'tesis'];
        $test = $this->user;

        $test->whereOr($params)->first();

        $result = $test->fetch();
        $this->assertNotEmpty($result, 'Expected Pass');
        $this->assertNotEquals(0, sizeof($result), 'Expected Pass');
    }

}
