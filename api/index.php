<?php
use Illuminate\Support\Carbon;
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

$app->get('/tags', function(Request $request, Response $response, $args) {

    $data = Tags::get( ['ID','tag','color']);
    
    $payload = json_encode( $data );
    $response->getBody()->write( $payload );
    return $response->withHeader( 'Content-Type', 'application/json' );
});

$app->post('/tags', function(Request $request, Response $response, $args) {
    $data = $request->getParsedBody();

    $ID = Tags::insertGetId( $data );

    $payload = json_encode( [$ID] );
    $response->getBody()->write( $payload );
    return $response->withHeader( 'Content-Type', 'application/json' );
});

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

$app->post('/tasks', function(Request $request, Response $response, $args) {
    $data = $request->getParsedBody();

    $ID = Tasks::insertGetId( $data );

    $payload = json_encode( [$ID] );
    $response->getBody()->write( $payload );
    return $response->withHeader( 'Content-Type', 'application/json' );
});

$app->delete( '/tasks/{ID}', function(Request $request, Response $response, $args) {
    $ID = $args['ID'];

    Tasks::where( 'ID', $ID )->delete();

    $payload = json_encode( [$ID] );
    $response->getBody()->write( $payload );
    return $response->withHeader( 'Content-Type', 'application/json' );
});

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

$app->run();