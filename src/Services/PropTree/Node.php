<?php

namespace App\Services\PropTree;

class Node
{
    public function __construct(
        public int $id,
        public string $name,
        public array $children = []
    ) {}
}
