<?php //app/lib/Tesis/Tables/User.php

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
 * @name       User.php
 *
 *
 *
 * Short description
 * a child class using DB layer
 *
 * Long description
 * a database layer is based on PDO library
 * a child class is a class for new table with specific variables
 * defined
 * 
 */
namespace Tesis\Tables;

use Tesis\Database\PDORepository as DataObject;

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
