<?php
use Illuminate\Support\Carbon;
use rondeobalos\model\Notes;
define( 'ABSPATH', dirname(__FILE__) . '/' );

require '../vendor/autoload.php';
require_once '../connection.php';

use rondeobalos\model\Tags;
use rondeobalos\model\Tasks;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

$app = AppFactory::create();
$app->setBasePath("/api");

/**
 * Empty
 */
$app->get('/', function(Request $request, Response $response, $args) {
    $payload = json_encode( [] );
    $response->getBody()->write( $payload );
    return $response->withHeader( 'Content-Type', 'application/json' );
});

/**
 * Get all tags
 */
$app->get('/tags', function(Request $request, Response $response, $args) {

    $data = Tags::get( ['ID','tag','color']);
    
    $payload = json_encode( $data );
    $response->getBody()->write( $payload );
    return $response->withHeader( 'Content-Type', 'application/json' );
});

/**
 * Insert tag
 */
$app->post('/tags', function(Request $request, Response $response, $args) {
    $data = $request->getParsedBody();

    $ID = Tags::insertGetId( $data );

    $payload = json_encode( [$ID] );
    $response->getBody()->write( $payload );
    return $response->withHeader( 'Content-Type', 'application/json' );
});

/**
 * Delete tag by id
 */
$app->delete( '/tags/{ID}', function( Request $request, Response $response, $args ) {
    $ID = $args['ID'];

    Tags::where( 'ID', $ID )->delete();

    $payload = json_encode( [$ID] );
    $response->getBody()->write( $payload );
    return $response->withHeader( 'Content-Type', 'application/json' );
});

/**
 * Edit tag
 */
$app->post('/tags/{ID}', function(Request $request, Response $response, $args) {
    $ID = $args['ID'];
    $data = $request->getParsedBody();

    Tags::where( 'ID', $ID )->update( $data );

    $payload = json_encode( [$ID] );
    $response->getBody()->write( $payload );
    return $response->withHeader( 'Content-Type', 'application/json' );
});

/**
 * Retrive all tasks
 */
$app->get('/tasks', function(Request $request, Response $response, $args) {

    // Fetch all tasks
    //$firstDayOfMonth = Carbon::now()->startOfMonth()->toDateString(); // 'YYYY-MM-DD'
    //$lastDayOfMonth = Carbon::now()->endOfMonth()->toDateString(); // 'YYYY-MM-DD'
    $data = Tasks::whereBetween('start', [strtotime(date('Y-m-01')), strtotime(date('Y-m-t'))])->orderByDesc( 'start' )->get();

    if($data) {
        $data = $data->groupBy( function($task) {
            $date = Carbon::parse($task->start);
            return $date->format('W');
        });
        $data = $data->map( function($group) {
            return $group->groupBy( function($task) {
                $date = Carbon::parse($task->start);
                return $date->format('d');
            });
        });
    }

    $payload = json_encode( $data );
    $response->getBody()->write( $payload );
    return $response->withHeader( 'Content-Type', 'application/json' );
});

/**
 * Insert new task
 */
$app->post('/tasks', function(Request $request, Response $response, $args) {
    $data = $request->getParsedBody();

    $ID = Tasks::insertGetId( $data );

    $payload = json_encode( [$ID] );
    $response->getBody()->write( $payload );
    return $response->withHeader( 'Content-Type', 'application/json' );
});

/**
 * Delete task by id
 */
$app->delete( '/tasks/{ID}', function(Request $request, Response $response, $args) {
    $ID = $args['ID'];

    Tasks::where( 'ID', $ID )->delete();

    $payload = json_encode( [$ID] );
    $response->getBody()->write( $payload );
    return $response->withHeader( 'Content-Type', 'application/json' );
});

/**
 * Update task
 */
$app->post( '/tasks/{ID}', function(Request $request, Response $response, $args) {
    $ID = $args['ID'];
    $data = $request->getParsedBody();
    unset($data['start_raw']);
    unset($data['end_raw']);
    
    Tasks::where( 'ID', $ID )->update( $data );

    $payload = json_encode( [$ID] );
    $response->getBody()->write( $payload );
    return $response->withHeader( 'Content-Type', 'application/json' );
});

/**
 * All Notes
 */
$app->get('/notes', function(Request $request, Response $response, $args) {

    $data = Notes::orderBy( 'ID' )->get();

    $payload = json_encode( $data );
    $response->getBody()->write( $payload );
    return $response->withHeader( 'Content-Type', 'application/json' );
});

/**
 * Insert note
 */
$app->post('/notes', function(Request $request, Response $response, $args) {
    $data = $request->getParsedBody();

    $ID = Notes::insertGetId( $data );

    $payload = json_encode( [$ID] );
    $response->getBody()->write( $payload );
    return $response->withHeader( 'Content-Type', 'application/json' );
});

/**
 * Delete Note by Id
 */
$app->delete( '/notes/{ID}', function( Request $request, Response $response, $args ) {
    $ID = $args['ID'];

    Notes::where( 'ID', $ID )->delete();

    $payload = json_encode( [$ID] );
    $response->getBody()->write( $payload );
    return $response->withHeader( 'Content-Type', 'application/json' );
});

/**
 * Update note
 */
$app->post( '/notes/{ID}', function(Request $request, Response $response, $args) {
    $ID = $args['ID'];
    $data = $request->getParsedBody();
    
    Notes::where( 'ID', $ID )->update( $data );

    $payload = json_encode( [$ID] );
    $response->getBody()->write( $payload );
    return $response->withHeader( 'Content-Type', 'application/json' );
});

$app->run();