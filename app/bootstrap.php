<?php

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Provider\FormServiceProvider;

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

require __DIR__ . '/routes.php';

$app->view( function( \Sabre\VObject\Component\VCalendar $calendar, Request $request) use($app) {
    // TODO make content negotiation work
    /*
    $acceptHeader = $request->headers->get('Accept');
    $bestFormat = $app['negotiator']->getBestFormat($acceptHeader, ['json'] );

    if ('json' === $bestFormat) {
        return new JsonResponse( $calendar->jsonSerialize(), Response::HTTP_OK, [], true );
    }
    */
    return new Response( $calendar->serialize(), Response::HTTP_OK, [
        'Content-Type' => 'text/calendar',
        'Content-Disposition' =>'attachment; filename="tasks.ics"'
    ] );
} );


return $app;