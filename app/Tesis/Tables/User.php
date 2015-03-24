<?php //app/lib/Tesis/Tables/User.php

/**
 * This file is part of the Tesis framework.
 *
 * PHP version 5.6
 *
 * @author     Tereza Simcic <tereza.simcic@gmail.com>
 * @copyright  2014-2015 Tesis, Tereza Simcic
 * @license    MIT
 * @link       https://github.com/tesis/login
 *
 */
namespace Tesis\Tables;

use Tesis\Database\PDORepository as DataObject;

/**
 * Class User
 *
 * PHP version 5.6
 *
 * @package    Database_Layer
 * @subpackage Login_System
 * @author     Tereza Simcic <tereza.simcic@gmail.com>
 *
 * Short description
 * an example class to perform test
 *
 */
class User extends DataObject
{
    /**
     * @access protected
     * @var string
     */
    public $table = 'users';

    /**
     * @access protected
     * @var string
     *
     * for compound keys use array:
     * $tablePK = ['uid','anotherId']
     */
    public $tablePK = 'uid';
    /**
     * @access public
     * @var array
     */
    public $dbFields = ['uid', 'username', 'fname', 'lname', 'email', 'password', 'created', 'updated', 'salt', 'act'];
    /**
     * @access public
     * @var array
     */
    public $required = ['username', 'email', 'password', 'password_confirm'];

    /**
     * __construct
     *
     * @param array $dataArray an array passed to the object
     *
     * @return none
     *
     * @access public
     *
     *
     */
    public function __construct(array $dataArray = null)
    {
        parent::__construct($dataArray);

        $this->date = date('Y-m-d H:i:s');
        $this->salt = session_id();

    }

}
