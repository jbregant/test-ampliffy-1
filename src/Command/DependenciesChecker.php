<?php

namespace App\Command;

use App\Service\DependencyService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DependenciesChecker extends Command
{
    protected static $defaultName = 'app:check-dependencies';
    protected static $defaultDescription = 'Check for repositories dependencies.';
    protected $dependencyService;

    /**
     * DependenciesChecker constructor.
     * @param DependencyService $dependencyService
     */
    public function __construct(DependencyService $dependencyService)
    {
        $this->dependencyService = $dependencyService;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('This command allows you to check for repositories dependencies...')
            ->addArgument('git_url', InputArgument::REQUIRED, 'The GitHub URL.')
            ->addArgument('commit_id', InputArgument::REQUIRED, "Commit Identifier.")
            ->addArgument('branch_name', InputArgument::REQUIRED, 'Repository Branch Name.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $gitHubUrl = $input->getArgument('git_url');
        $composerName = $this->dependencyService->getComposerNameFromGitHubUrl($gitHubUrl);

        if (!$composerName) {
            return Command::FAILURE;
        }

        $repositoryDependencies = $this->dependencyService->getDependentRepositories($composerName);

        foreach ($repositoryDependencies as $repositoryDependency) {
            $output->writeln("$repositoryDependency CI/CD Pipeline should be executed.");
        }

        return Command::SUCCESS;
    }
}