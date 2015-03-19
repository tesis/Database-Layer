<?php //spec/Tesis/Tables/PDORepositorySpec.php

namespace spec\Tesis\Tables;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Tesis\Database\PDORepository as DataObject;

class UserSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Tesis\Tables\User');
    }
    function it_stores_a_user()
    {
        $data = ['table'=>'users', 'username' => 'tesi'.time(), 'password' => 'ssss', 'email'=>'tesi'.time()];

        $create = $this->create($data);
        $create->shouldBeString();
    }
    function it_does_not_store_a_user_throw_exception()
    {
        $data = ['table'=>'users', 'username' => 'tesis5'.time(), 'password' => 'ssss', 'email'=>'tesis5'.time()];

        $create = $this->create($data);
        $create->shouldBeString();

    }
    function it_does_find_all_users()
    {
        $this->orderBy('uid')->groupBy('username')->all();

        $result = $this->fetch();
        $this->shouldHaveCount(sizeof($result));
    }
    function it_does_find_a_user()
    {
        $this->orderBy('uid')->groupBy('username')->first();

        $result = $this->fetch();
        $this->shouldHaveCount(sizeof($result));
    }

}
