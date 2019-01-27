<?php

namespace App\Tests\Service;

use App\Service\BenchmarkingService;
use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Templating\EngineInterface;

class BenchmarkingServiceTest extends WebTestCase
{

    private $notificationService;
    private $benchmarkingService;


    /**
     * @dataProvider urlProvider
     */
    public function testHandleProcess($url)
    {
        $result = $this->benchmarkingService->handleProcess($url,[]);
        $this->assertContains("Url | Execution time | Difference\n", $result);
        $this->assertContains($url, $result);
        $this->assertContains('Date of the test', $result);
    }

    /**
     * @dataProvider urlProvider
     */
    public function testGetTime($url)
    {
        $this->assertInternalType('float', $this->invokeMethod($this->benchmarkingService,'getTime',
            [$url]));
    }

    /**
     * @dataProvider transformIntoTextProvider
     */
    public function testTransformIntoText($content, $expected)
    {
        $this->assertEquals($expected, $this->invokeMethod($this->benchmarkingService,'transformIntoText',
            [$content]));
    }


    public function urlProvider()
    {
        return [
            ['http://www.codewars.com'],
            ['http://www.google.com'],
            ['http://www.youtube.com'],
        ];
    }

    public function transformIntoTextProvider()
    {
        return [
            [[['Execution', 'Speed', 'Time'],['Url', 'Date', 'Difference']],
                "Execution | Speed | Time\nUrl | Date | Difference\n"],
        ];
    }

    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    protected function setUp()
    {
        parent::setUp();
        $mailer = $this->createMock(\Swift_Mailer::class);
        $templating = $this->createMock(EngineInterface::class);
        $this->notificationService = new NotificationService($templating, $mailer);
        $this->benchmarkingService = new BenchmarkingService($this->notificationService);
    }
}