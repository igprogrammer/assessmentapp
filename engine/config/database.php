<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],

        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST_MS', '127.0.0.1'),
            'port' => env('DB_PORT_MS', '3306'),
            'database' => env('DB_DATABASE_MS', 'forge'),
            'username' => env('DB_USERNAME_MS', 'forge'),
            'password' => env('DB_PASSWORD_MS', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DATABASE_URL_PG'),
            'host' => env('DB_HOST_PG', '127.0.0.1'),
            'port' => env('DB_PORT_PG', '5432'),
            'database' => env('DB_DATABASE_PG', 'forge'),
            'username' => env('DB_USERNAME_PG', 'forge'),
            'password' => env('DB_PASSWORD_PG', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'schema' => 'ors',
            'sslmode' => 'prefer',
        ],

        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
        ],

        'sqlsrv_r' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL_R'),
            'host' => env('DB_HOST_R', 'localhost'),
            'port' => env('DB_PORT_R', '1433'),
            'database' => env('DB_DATABASE_R', 'forge'),
            'username' => env('DB_USERNAME_R', 'forge'),
            'password' => env('DB_PASSWORD_R', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
        ],

        'mysql_b' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL_B'),
            'host' => env('DB_HOST_B', '127.0.0.1'),
            'port' => env('DB_PORT_B', '3306'),
            'database' => env('DB_DATABASE_B', 'forge'),
            'username' => env('DB_USERNAME_B', 'forge'),
            'password' => env('DB_PASSWORD_B', ''),
            'unix_socket' => env('DB_SOCKET_B', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        'mysql_b' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL_MS'),
            'host' => env('DB_HOST_MS', '127.0.0.1'),
            'port' => env('DB_PORT_MS', '3306'),
            'database' => env('DB_DATABASE_MS', 'forge'),
            'username' => env('DB_USERNAME_MS', 'forge'),
            'password' => env('DB_PASSWORD_MS', ''),
            'unix_socket' => env('DB_SOCKET_MS', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        'mysql_rec' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL_REC'),
            'host' => env('DB_HOST_REC', '127.0.0.1'),
            'port' => env('DB_PORT_REC', '3306'),
            'database' => env('DB_DATABASE_REC', 'forge'),
            'username' => env('DB_USERNAME_REC', 'forge'),
            'password' => env('DB_PASSWORD_REC', ''),
            'unix_socket' => env('DB_SOCKET_REC', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        'sqlsrv_recon' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST_RECON', 'localhost'),
            'port' => env('DB_PORT_RECON', '1433'),
            'database' => env('DB_DATABASE_RECON', 'forge'),
            'username' => env('DB_USERNAME_RECON', 'forge'),
            'password' => env('DB_PASSWORD_RECON', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
        ],

        'sqlsrv_t' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST_T', 'localhost'),
            'port' => env('DB_PORT_T', '1433'),
            'database' => env('DB_DATABASE_T', 'forge'),
            'username' => env('DB_USERNAME_T', 'forge'),
            'password' => env('DB_PASSWORD_T', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
        ],

        'sqlsrv_orsreg' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST_OReg', 'localhost'),
            'port' => env('DB_PORT_OReg', '1433'),
            'database' => env('DB_DATABASE_OReg', 'forge'),
            'username' => env('DB_USERNAME_OReg', 'forge'),
            'password' => env('DB_PASSWORD_OReg', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
        ],
        'sqlsrv_ors' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST_ORS', 'localhost'),
            'port' => env('DB_PORT_ORS', '1433'),
            'database' => env('DB_DATABASE_ORS', 'forge'),
            'username' => env('DB_USERNAME_ORS', 'forge'),
            'password' => env('DB_PASSWORD_ORS', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],

    ],

];
