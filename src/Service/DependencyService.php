<?php

namespace App\Service;

class DependencyService
{
    private $repositories;
    private $commonDirectory;
    private $directories = [
        '/home/kahless/Projectos/docker/AmpliffyTest/repositories/Proyecto1',
        '/home/kahless/Projectos/docker/AmpliffyTest/repositories/Proyecto2',
        '/home/kahless/Projectos/docker/AmpliffyTest/repositories/Libreria1',
        '/home/kahless/Projectos/docker/AmpliffyTest/repositories/Libreria2',
        '/home/kahless/Projectos/docker/AmpliffyTest/repositories/Libreria4'
    ];

    /**
     * DependencyService constructor.
     */
    public function __construct()
    {
        $this->commonDirectory = $this->getCommonDirectory();
        $this->repositories = $this->getRepositories();
    }

    /**
     * @return string
     */
    private function getCommonDirectory(): string
    {
        $directory = explode('/', $this->directories[0]);
        return $directory[count($directory) - 2];
    }

    /**
     * Parse composer.json and return an array with every repository with their dependencies
     * @return array
     */
    private function getRepositories(): array
    {
        $repositories = [];
        foreach ($this->directories as $directory) {
            $composerJsonFile = file_get_contents("$directory/composer.json");
            $composerJsonFileArray = json_decode($composerJsonFile, true);
            $dependencies = [];

            foreach ($composerJsonFileArray['require'] as $dependency => $version) {
                if ($this->isInHouse($dependency)) {
                    $dependencies[] = $dependency;
                }
            }

            $repositories[] = [
                'name' => $composerJsonFileArray['name'],
                'dependencies' => $dependencies
            ];
        }

        return $repositories;
    }

    /**
     * @param $dependency
     * @return bool
     */
    private function isInHouse($dependency): bool
    {
        $commonDependencyDirectory = explode('/', $dependency)[0];

        return $commonDependencyDirectory == $this->commonDirectory;
    }

    /**
     * Return repositories related to $name
     *
     * @param $name
     * @return array
     */
    public function getDependentRepositories($name): array
    {
        $dependentRepositories = [];

        foreach ($this->repositories as $repository) {
            if ($repository['name'] == $name) {
                continue;
            }

            if (in_array($name, $repository['dependencies'])) {
                $dependentRepositories[] = $repository['name'];
                $innerDependencies = $this->getDependentRepositories($repository['name']);
                $dependentRepositories = array_merge($dependentRepositories, $innerDependencies);
            }
        }

        return array_unique($dependentRepositories);
    }

    /**
     * @param $gitHubUrl
     * @return string|null
     */
    public function getComposerNameFromGitHubUrl($gitHubUrl): ?string
    {
        $composerName = null;

        foreach ($this->directories as $directory) {
            $fileName = "$directory/.git/config";

            if (file_exists($fileName)) {
                $gitConfigFile = parse_ini_file($fileName);

                if ($gitConfigFile['url'] === $gitHubUrl) {
                    $composerJsonFile = file_get_contents("{$directory}/composer.json");
                    $composerJsonFileArray = json_decode($composerJsonFile, true);
                    $composerName = $composerJsonFileArray['name'];
                }
            }
        }

        return $composerName;
    }
}