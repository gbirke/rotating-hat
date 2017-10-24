<?php

use Negotiation\Negotiator;
use Silex\Provider\FormServiceProvider;
use Symfony\Component\HttpFoundation\Request;

$app = new Silex\Application();
$app['debug'] = true;

$app->register( new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../app/templates',
));
$app->register(new Silex\Provider\ValidatorServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'translator.domains' => array(),
    'locale' =>'en_US'
));


// JSON content negotiation
$app->before( function ( Request $request ) {
    $negotiator = new Negotiator();
    $bestContentType = $negotiator->getBest( $request->headers->get('Accept' ), [ 'text/html', 'application/json' ] );
    if ( !is_null( $bestContentType) && $bestContentType->getValue() === 'application/json' ) {
        $request->attributes->set( 'is_json', true );
    }
} );

require __DIR__ . '/routes.php';

return $app;