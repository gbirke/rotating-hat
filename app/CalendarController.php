<?php
declare( strict_types = 1 );

namespace Gbirke\TaskHat\App;

use DateTime;
use DateTimeZone;
use Gbirke\TaskHat\CalendarGenerator;
use Gbirke\TaskHat\Form\Task;
use Gbirke\TaskHat\Recurrence;
use Gbirke\TaskHat\TaskSpec;
use Sabre\VObject\Component\VCalendar;
use Silex\Application;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;

class CalendarController
{
    public function create( Application $app, Request $request  ) {
        /** @var \Symfony\Component\Form\Form $form */
        $form = $app['form.factory']->createBuilder(
            Task::class,
            [
                'startOn' => new DateTime()
            ]
        )->getForm();
        $form->handleRequest( $request );

        if (!$form->isValid()) {
            return $this->createErrorResponse( $form, $app['twig'], $request );
        } else {
            $generator = new CalendarGenerator();
            return $this->createSuccessResponse(
                $generator->createCalendarObject( $this->getTaskSpecFromData( $form->getData() ) ),
                $request
            );
        }
    }

    private function getTaskSpecFromData( array $data ): TaskSpec {
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

        $timezone = new DateTimeZone( $data['timezone'] ?: $data['userTimezone'] );
        $startOn = new DateTime( $data['startOn']->format( 'Y-m-d' ), $timezone );

        $recurrence = Recurrence::newOnce();
        switch ( (int) $data['recurrence'] ) {
            case Recurrence::UNTIL:
                $recurrence = Recurrence::newUntil( new DateTime( $data['endDate']->format('Y-m-d'), $timezone ) );
                break;
            case Recurrence::FOREVER:
                $recurrence = Recurrence::newForever();
        }

        return new TaskSpec( $labels, $startOn, (int) $data['duration'], $recurrence );
    }

    private function createSuccessResponse( VCalendar $calendar, Request $request )
    {
        if ( $request->attributes->get('is_json', false ) ) {
            return new JsonResponse( $calendar->jsonSerialize() , Response::HTTP_OK, [ 'Content-Type' => 'application/json' ] );
        }

        return new Response( $calendar->serialize(), Response::HTTP_OK, [
            'Content-Type' => 'text/calendar',
            'Content-Disposition' =>'attachment; filename="tasks.ics"'
        ] );
    }

    private function createErrorResponse( Form $form, Twig_Environment $twig, Request $request )
    {
        if ( $request->attributes->get('is_json', false ) ) {
            $errors = [];
            foreach( $form->getErrors( true ) as $error ) {
                $errors[$error->getOrigin()->getName()] = $error->getMessage();
            }
            return new JsonResponse( [ 'errors' => $errors ], Response::HTTP_OK, [ 'Content-Type' => 'application/json' ] );
        }

        return $twig->render( 'form.twig', [ 'form' => $form->createView() ] );
    }
}