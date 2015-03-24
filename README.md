## Project PDO database layer

#### Database Layer:
Database Layer is based on PDO
it contains all methods to access the database
performing CRUD operations and searches.
It is part of the framework as an external library.


#### User class:
User class is a base class for TDD and BDD tests
Each database table can have a class as an extension of PDO layer,
including Table name, Table primary key name, Table fields as an array

#####TODO:
including validation class

unique fields checking (username/email)


#### TDD and BDD tests

TDD tests include option to create database and table to perform
basic tests for PDO layer and User class

#### USAGE
##### change config file for database settings
##### CRUD
* **create** $this->user->create(['name'=>'test',])
* **insert**
* **update**
* **delete**

##### Example

$user = new User();

$params = ['name'=>'tesi', 'username'=>'tesis'];

$create = $user->create($params);//returns lastInsertId

$update = $user->update(['uid'=>$create, 'name'=>'tesi2']);

//for delete you may pass unique id, or an array if you have compound PK
$delete = $user->delete(['uid'=>$create]);


###### SEARCH options

* **select first**
* **select all**
* **select where**
* **order by**
* **group by**
* **limit**

* select specific fields
$user->select('id, username')->where(['tag'=>'test'])->orderBy('tag',DESC);

* select may be ommitted in case you want to select all fields
$user->where(['tag'=>'test'])->orderBy('tag',DESC);

* arguments for where: single or multiple array
$user->where(['tag'=>'test', 'username'=>'test'])->orderBy('tag',DESC);

* Where OR example
$user->whereOR(['tag'=>'test', 'username'=>'test'])->orderBy('tag',DESC);

* Without 'where', using limit:
$user->select('id, username')->orderBy('tag',DESC)->get(10);
$user->fetch();

* fetching results:
1 - with get and limit:
$user-> ... ->get(10);

2 - only first row:
$user-> ... ->first();

3- all rows:
$user-> ... ->all();

* after queries are build, run fetch:
$user->fetch();

Fetch options: you can include type of PDO search, default is **PDO::FETCH_OBJ**
$user->fetch(PDO::FETCH_ASSOC);

##### Please take a look at tests performed.

Website: [TesisPro](http://tesispro.net)
