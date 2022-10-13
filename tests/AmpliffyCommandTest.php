<?php

namespace App\Tests;

use App\Command\DependenciesChecker;
use App\Service\DependencyService;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class AmpliffyCommandTest extends KernelTestCase
{
    public function testCommandSuccess()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $application = new Application($kernel);
        $application->add(new DependenciesChecker(new DependencyService()));

        $command = $application->find('app:check-dependencies');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'git_url' => 'https://github.com/dunglas/libreria4.git',
                'commit_id' => '10a432dadb441a10d25f601961109900717a8603',
                'branch_name' => 'ExampleBranch'
            ]
        );

        $outputTest1 = $commandTester->getStatusCode();
        $this->assertIsInt(0, $outputTest1);
    }

    public function testCommandFailure()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $application = new Application($kernel);
        $application->add(new DependenciesChecker(new DependencyService()));

        $command = $application->find('app:check-dependencies');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'git_url' => 'https://github.com/dunglas/lib6.git',
                'commit_id' => '10a432dadb441a10d25f601961109900717a8603',
                'branch_name' => 'ExampleBranch'
            ]
        );

        $outputTest = $commandTester->getStatusCode();
        $this->assertEquals(1, $outputTest);
    }

    public function testCommandMissingParametersFailure()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $application = new Application($kernel);
        $application->add(new DependenciesChecker(new DependencyService()));

        $command = $application->find('app:check-dependencies');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'git_url' => '',
                'commit_id' => '10a432dadb441a10d25f601961109900717a8603',
                'branch_name' => 'ExampleBranch'
            ]
        );

        $outputTest = $commandTester->getStatusCode();
        $this->assertEquals(1, $outputTest);
    }

    public function testOneRepositoryResult()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $application = new Application($kernel);
        $application->add(new DependenciesChecker(new DependencyService()));

        $command = $application->find('app:check-dependencies');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'git_url' => 'https://github.com/dunglas/libreria2.git',
                'commit_id' => '10a432dadb441a10d25f601961109900717a8603',
                'branch_name' => 'ExampleBranch'
            ]
        );

        $outputTest = $commandTester->getDisplay();
        $rowCounter = substr_count($outputTest, 'executed');
        $this->assertEquals(1, $rowCounter);
    }

    public function testTwoRepositoryResult()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $application = new Application($kernel);
        $application->add(new DependenciesChecker(new DependencyService()));

        $command = $application->find('app:check-dependencies');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'git_url' => 'https://github.com/dunglas/libreria1.git',
                'commit_id' => '10a432dadb441a10d25f601961109900717a8603',
                'branch_name' => 'ExampleBranch'
            ]
        );

        $outputTest = $commandTester->getDisplay();
        $rowCounter = substr_count($outputTest, 'executed');
        $this->assertEquals(2, $rowCounter);
    }
}