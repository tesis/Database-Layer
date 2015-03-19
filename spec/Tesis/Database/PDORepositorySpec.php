<?php //spec/Tesis/Database/PDORepositorySpec.php

namespace spec\Tesis\Database;

use Tesis\Database\PDORepository as DataObject;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PDORepositorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Tesis\Database\PDORepository');
    }
    function it_has_DB_defined()
    {
        $this->dbName->shouldBe('pdoTest');
    }
    function it_has_database_name_should_not_be_empty()
    {
        $this->dbName->shouldNotBe('');
    }
}
