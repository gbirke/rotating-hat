<?php

use Silex\Application;

$app->post( '/create-calendar', 'Gbirke\\TaskHat\\App\\CalendarController::create' );

$app->get( '/', function ( Application $app )  {
    /** @var \Symfony\Component\Form\Form $form */
    $form = $app['form.factory']->createBuilder(
        \Gbirke\TaskHat\Form\Task::class,
        [
            'startOn' => new DateTime()
        ]
    )->getForm();

    return $app['twig']->render( 'form.twig', [ 'form' => $form->createView() ] );
} );
