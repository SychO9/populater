# populater
![GitHub workflow](https://img.shields.io/github/workflow/status/SychO9/populater/tests?style=flat-square)
![Latest Version](https://img.shields.io/github/release/SychO9/populater.svg?style=flat-square&color=orange)
![php](https://img.shields.io/badge/php-^7.2-red.svg?style=flat-square&color=blue)
![License](https://img.shields.io/badge/license-MIT-green.svg?style=flat-square&color=green)
![downloads](https://img.shields.io/packagist/dt/sycho/populater?color=%23f28d1a&style=flat-square)

:elephant: PHP script that populates your database tables with fake data using **[fzaninotto/faker](https://github.com/fzaninotto/Faker)**

## Installation
In progress...

## Usage
First of all, your **database schema** must be ready, the script does not currently help create it.

### 1. Adding a database connection
Inorder for the script to work, a database connection is required, to add a connection use the following command

```
$ php populater add:connection <connection_name> <driver> <database> <username> <password> [<prefix> [<host> [<charset> [<collation>]]]]
```

Example:

```shell
$ php populater add:connection fake_db mysql fake_db fake_user fake_password
```

### 2. Selecting a connection
You have added one or more connections to the script, now you have to select a connection to use before anything else,

to do so, use the following command
```
$ php populater use:connection <connection_name>
```

##### Example
```shell
$ php populater use:connection fake_db
```

### 3. Creating a blueprint
Every table will have a blueprint, a blueprint is used to determine the data format to generate for each column of the table,

to create a blueprint, use the following command

```
$ php populater create:blueprint <table_name> <number_of_columns> [<database>]
```

You can make the table blueprint database specific by filling the `database` argument, or make it a common blueprint by leaving it empty.

Next you'll have to fill in the information about the columns, a `name` and a `generator`, the generator is basically the formatter used from the **fzaninotto/faker** package, a list of all formatters are available in the [package's github repository](https://github.com/fzaninotto/Faker).

##### Example
```
Column n°1:
        name: id
        generator: autoIncrement
Column n°2:
        name: first_name
        generator: firstName
Column n°3:
        name: last_name
        generator: lastName
Column n°4:
        name: phone_number
        generator: phoneNumber
Column n°5:
        name: birth_date
        generator: dateTime:'2005-02-25 08:37:17':UTC
Column n°6:
        name: email
        generator: freeEmail
Column n°7:
        name: passwrd
        generator: password
```

The generator definition follows the following format: `method:param1:param2:param3...`, don't forget to surround a parameter with quotes if it contains a colon.

#### Manually adding a blueprint
Additionally you can manually create the blueprint:

1. Inside the storage folder create a folder by the name of your database name [optional]
2. Create a `{table_name}.yml` file and fill it with the information about you table columns

##### Example
```yaml
format:
    id: autoIncrement
    first_name: firstName
    last_name: lastName
    phone_number: phoneNumber
    birth_date: 'dateTime:''2005-02-25 08:37:17'':UTC'
    email: freeEmail
    passwrd: password
```

### 4. Populating your database table(s)
Now that the script has all the necessary information to run, it's time to populate your tables.

Currently you can only fill one table at a time, using the following command
```
$ php populate <blueprint> <rows>
```

##### Example
```
$ php populate fake_users 200
[▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰ ] 100% 11 secs 8.0 MiB
...
...
...
Finished in 14.583ms
```

## Commands
And of course the list of all commands can be found by running the following command

```
$ php populater
Console Tool

Usage:
  command [options] [arguments]

Options:
  -h, --help            Display this help message
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi            Force ANSI output
      --no-ansi         Disable ANSI output
  -n, --no-interaction  Do not ask any interactive question
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output,
 2 for more verbose output and 3 for debug

Available commands:
  help              Displays help for a command
  list              Lists commands
  populate          Populates a table
 add
  add:connection    Adds a new database connection
 create
  create:blueprint  Creates a blueprint
 list
  list:blueprints   Lists existing blueprints
  list:connections  Lists all added connections
 show
  show:connection   Shows info on the current connection
 use
  use:connection    Changes the current connection
```

## License
The MIT License.
