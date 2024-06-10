<?php
defined('ABSPATH') || exit;

// https://packagist.org/packages/illuminate/database
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

$capsule = new Capsule;

$capsule->addConnection([
    'driver' => 'sqlite',
    'host' => '',
    'database' =>  'task-logger.db',
    'username' => '',
    'password' => '',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => ''
]);

// Set the event dispatcher used by Eloquent models... (optional)
$capsule->setEventDispatcher(new Dispatcher(new Container));

// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();

/**
 * Installation
 */

/*if( false ) {
    Capsule::schema()->create( 'tags', function($table) {
        $table->increments( 'ID' );
        $table->string( 'tag' );
        $table->string( 'color' );
        $table->timestamps();
    });

    Capsule::schema()->create( 'tasks', function($table) {
        $table->increments( 'ID' );
        $table->string( 'title' );
        $table->string( 'description' );
        $table->integer( 'tag' );
        $table->dateTime( 'start' );
        $table->dateTime( 'end' );
        $table->timestamps();
    });
}*/