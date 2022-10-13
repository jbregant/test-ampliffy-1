<?php

namespace App\Entity;

use App\Repository\LocalGitRepoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LocalGitRepoRepository::class)
 */
class LocalGitRepo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true,length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", unique=true, length=255)
     */
    private $url;

    /**
     * @ORM\ManyToOne(targetEntity=LocalGitRepo::class, inversedBy="dependencies")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity=LocalGitRepo::class, mappedBy="parent", cascade={"persist"})
     */
    private $dependencies;

    public function __construct()
    {
        $this->dependencies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getDependencies(): Collection
    {
        return $this->dependencies;
    }

    public function addDependency(self $dependency): self
    {
        if (!$this->dependencies->contains($dependency)) {
            $this->dependencies[] = $dependency;
            $dependency->setParent($this);
        }

        return $this;
    }

    public function removeDependency(self $dependency): self
    {
        if ($this->dependencies->removeElement($dependency)) {
            // set the owning side to null (unless already changed)
            if ($dependency->getParent() === $this) {
                $dependency->setParent(null);
            }
        }

        return $this;
    }
}
