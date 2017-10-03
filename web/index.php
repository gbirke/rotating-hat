<?php
use Gbirke\TaskHat\Recurrence;
use Gbirke\TaskHat\TaskSpec;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Provider\FormServiceProvider;

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
$app->register(new FormServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'translator.domains' => array(),
    'locale' =>'en_US'
));

$app->post( '/create-calendar', function (Application $app, Request $request ) {
    /** @var \Symfony\Component\Form\Form $form */
    $form = $app['form.factory']->createBuilder(\Gbirke\TaskHat\Form\Task::class, [
        'startOn' => new DateTime()
    ] )->getForm();
    $form->handleRequest( $request );

    if (!$form->isValid()) {
        return $app['twig']->render( 'form.twig', [ 'form' => $form->createView() ] );

    } else {
        $data = $form->getData();
        $prefix = empty( $data['name'] ) ?  '' : $data['name'] . ': ';
        $labels = array_filter(
            array_map(
                function($name) use ($prefix) {
                    $name = trim($name);
                    return $name ? $prefix.$name : '';
                    },
                explode( "\n", $data['people'] )
            )
        );

        $startOn = new DateTime( $data['startOn']->format( 'Y-m-d' ), new DateTimeZone( $data['startOnTimezone'] ) );
        $recurrence = Recurrence::newOnce();
        switch ( (int) $data['recurrence'] ) {
            case Recurrence::UNTIL:
                $recurrence = Recurrence::newUntil( new DateTime( $data['endDate']->format('Y-m-d'), $startOn->getTimezone() ) );
                break;
            case Recurrence::FOREVER:
                $recurrence = Recurrence::newForever();
        }

        $spec = new TaskSpec( $labels, $startOn, (int) $data['duration'], $recurrence );
        $generator = new \Gbirke\TaskHat\CalendarGenerator();
        $calendar = $generator->createCalendarObject( $spec );
        return new Response( $calendar->serialize(), Response::HTTP_OK, [
            'Content-Type' => 'text/calendar',
            'Content-Disposition' =>'attachment; filename="tasks.ics"'
        ] );
    }
} );

$app->get( '/', function ( Application $app )  {
    /** @var \Symfony\Component\Form\Form $form */
    $form = $app['form.factory']->createBuilder(\Gbirke\TaskHat\Form\Task::class, [
        'startOn' => new DateTime()
    ] )->getForm();
    return $app['twig']->render( 'form.twig', [ 'form' => $form->createView() ] );
} );

$app->run();