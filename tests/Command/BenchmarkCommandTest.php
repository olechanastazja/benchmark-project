<?php

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class BenchmarkCommandTest extends KernelTestCase
{
    public function testExecute()
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('app:benchmark');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command'  => $command->getName(),
            'main-url' => 'http://www.google.com',
        ]);

        $output = $commandTester->getDisplay();
        $this->assertContains('Execution time', $output);
        $this->assertContains('Url', $output);
        $this->assertContains('Difference', $output);
        $this->assertContains('Date of the test', $output);
    }
}