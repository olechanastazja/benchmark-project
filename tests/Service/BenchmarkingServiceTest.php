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


    protected function setUp()
    {
        parent::setUp();
        $mailer = $this->createMock(\Swift_Mailer::class);
        $templating = $this->createMock(EngineInterface::class);
        $this->notificationService = new NotificationService($templating, $mailer);
        $this->benchmarkingService = new BenchmarkingService($this->notificationService);
    }

    /**
     * @dataProvider handleProcessProvider
     */
    public function testHandleProcess($url, $others)
    {
        $result = $this->benchmarkingService->handleProcess($url, $others);
        $this->assertContains("Url | Execution time | Difference\n", $result);
        $this->assertContains($url, $result);
        $this->assertContains('Date of the test', $result);
    }

    /**
     * @dataProvider handleProcessFailProvider
     */
    public function testHandleProcessFail($url, $others)
    {
        $result = $this->benchmarkingService->handleProcess($url, $others);
        $this->assertContains($result, 'Remember that all arguments has to be urls in a proper form.');
    }

    /**
     * @dataProvider getTimeProvider
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

    public function handleProcessProvider()
    {
        return [
            ['http://www.codewars.com', ['http://www.google.com']],
            ['http://www.youtube.com', ['https://www.onet.pl']],
        ];
    }

    public function handleProcessFailProvider()
    {
        return [
            ['odewar.som', ['htcp:www.goocle.com']],
            ['not-a-url', ['goocle.com']],
        ];
    }


    public function getTimeProvider()
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
}