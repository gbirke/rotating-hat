<?php
use Gbirke\TaskHat\Recurrence;
use Gbirke\TaskHat\TaskSpec;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

require_once __DIR__.'/../vendor/autoload.php';

$filename = __DIR__.preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

$app = new Silex\Application();
$app['debug'] = true;

$app->register( new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../app/templates',
));
$app->register(new Silex\Provider\ValidatorServiceProvider());

$app->post( '/create-calendar', function (Application $app, Request $request ) {
    $newTaskConstraints = new Assert\Collection(['fields' => [
        'name' => new Assert\Optional( new Assert\NotBlank() ),
        'people' => [
            new Assert\NotBlank(),
            new Assert\Regex( ['pattern' => '/\w+\r?\n\w+/', 'message' => 'You must specify at least 2 names' ])
        ],
        // TODO check allowed values
        'duration' => new Assert\Optional( new Assert\NotBlank() ),
        'startOn' => [
            new Assert\NotBlank(),
            new Assert\Date()
        ],
        'startOnTimezone' => [
            new Assert\NotBlank(),
            // Todo validate timezone options
        ],
        'recurrence' => [
            new Assert\NotBlank(),
            // TODO validate recurrence options
        ],
        'endDate' => new Assert\Optional( new Assert\Date() ),
    ]] );
    $errors = $app['validator']->validate( $request->request->all(), $newTaskConstraints );

    if (count($errors) > 0) {
        return $app['twig']->render( 'form.twig', [ 'errors' => $errors ] );

    } else {
        $prefix = trim( $request->request->get('name') );
        $prefix = $prefix ? $prefix . ': ' : '';
        $labels = array_filter(
            array_map(
                function($name) use ($prefix) {
                    $name = trim($name);
                    return $name ? $prefix.$name : '';
                    },
                explode( "\n", $request->get('people') )
            )
        );

        $startOn = new DateTime(
            $request->get('startOn' ),
            new DateTimeZone( $request->get( 'startOnTimezone' ) )
        );
        $recurrence = Recurrence::newOnce();
        switch ( $request->get('recurrence') ) {
            case '2':
                $recurrence = Recurrence::newUntil( new DateTime( $request->get( 'endDate'), $startOn->getTimezone() ) );
                break;
            case '3':
                $recurrence = Recurrence::newForever();
        }

        $spec = new TaskSpec( $labels, $startOn, (int) $request->get('duration'), $recurrence );
        $generator = new \Gbirke\TaskHat\CalendarGenerator();
        $calendar = $generator->createCalendarObject( $spec );
        return new Response( $calendar->serialize(), Response::HTTP_OK, [
            'Content-Type' => 'text/calendar',
            'Content-Disposition' =>'attachment; filename="tasks.ics"'
        ] );
    }
} );

$app->get( '/', function ( Application $app )  {
    return $app['twig']->render( 'form.twig' );
} );

$app->run();