<?php

namespace App\Entity;

use App\Repository\AncestorRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AncestorRepository::class)]
#[ORM\Table(name: "ancestors")]
class Ancestor
{
    #[ORM\Id, ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Prop $prop = null;

    #[ORM\Id, ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'ancestor_id', nullable: false)]
    private ?Prop $ancestor = null;

    public function getProp(): ?Prop
    {
        return $this->prop;
    }

    public function setProp(?Prop $prop): self
    {
        $this->prop = $prop;

        return $this;
    }

    public function getAncestor(): ?Prop
    {
        return $this->ancestor;
    }

    public function setAncestor(?Prop $ancestor): self
    {
        $this->ancestor = $ancestor;

        return $this;
    }
}
