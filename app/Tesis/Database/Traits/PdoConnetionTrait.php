<?php //app/Tesis/Database/Traits/adapterInterface.php
namespace Tesis\Database\Traits;

use PDO;

//using charset - very important to use for security reasons
//http://wiki.hashphp.org/PDO_Tutorial_for_MySQL_Developers

trait PDOconnection{
    /**
     * openConnection
     *
     * @access protected
     *
     * @return @conn
     */
    public function openConnection($dbHost, $dbName, $dbCharset, $dbUser, $dbPass)
    {
        $dns = 'mysql:host=' . $dbHost . ';dbName=' . $dbName;
        $options = array(
            PDO::ATTR_PERSISTENT => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . $dbCharset
        );
        try {
            $conn = new PDO($dns, $dbUser , $dbPass, $options);
            return $conn;
        }
        catch(PDOException $e) {
            $errors[] =  $e->getTrace();
            $errors[] =  $e->getMessage();
            $errors[] =  $e->getLine();
            return $errors;
        }
    }
    /**
     * closeConnection
     *
     * @access private
     *
     * @return void
     *
     */
    public function closeConnection($conn)
    {
        $conn = null;
    }
}
