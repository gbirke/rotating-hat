<?php

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Client;

class CreateCalendarRouteTest extends TestCase
{
    private $app;

    protected function setUp()
    {
        $this->createApplication();
    }

    public function testGivenIncompleteParams_theFormIsReturned() {
        $client = $this->createClient();
        $crawler = $client->request('POST', '/create-calendar');

        $this->assertTrue( $client->getResponse()->isOk() );
        $this->assertContains('text/html', $client->getResponse()->headers->get('Content-Type') );

        // This is very crude and should be refactored when we have better error indicators
        $this->assertCount(1, $crawler->filter('form'));
    }

    public function testGivenCompleteParams_aCalendarIsReturned() {
        $client = $this->createClient();
        $client->request('POST', '/create-calendar', [
            'task' => [
                'name' => 'Test task',
                'people' => "Alice\nBob",
                'duration' => '1',
                'startOn' => '2017-01-02',
                'recurrence' => '1',
                'timezone' => 'Europe/Berlin'
            ]
        ]);

        $this->assertTrue( $client->getResponse()->isOk() );
        $this->assertContains('text/calendar', $client->getResponse()->headers->get('Content-Type') );
        $this->assertSame( 2, substr_count( $client->getResponse()->getContent(), 'BEGIN:VEVENT') );

    }

    private function createApplication() {
        $this->app = require __DIR__ . '/../app/bootstrap.php';
    }

    public function createClient(array $server = array())
    {
        return new Client( $this->app, $server );
    }

}
