<?php //app/Tesis/Database/Faces/adapterInterface.php

namespace Tesis\Database\Faces;

interface AdapterInterface
{
    public function openConnection($dbHost, $dbName, $dbCharset, $dbUser, $dbPass);
    public function closeConnection($conn);
    public function create(array $data=null);
    public function update(array $data=null);
    public function delete($id='');
    public function get();
    public function first();
    public function all();
    public function fetch();
}
