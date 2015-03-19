
building TDD BDD login system

## Project Login System

#### Database Layer: 
Database Layer is based on PDO
it contains all methods to access the database


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
create
insert
update 
delete


###### SEARCH options

select first
select all
select where
order by
group by
limit


