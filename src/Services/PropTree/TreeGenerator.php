<?php

namespace App\Services\PropTree;

class TreeGenerator
{
    public function generate(array $data): array
    {
        // Fill indexed arrays
        /** @var array<int, Node> $rootNodes */
        $rootNodes = [];
        /** @var array<int, Node> $nodes */
        $nodes = [];
        foreach ($data as $row) {
            $ancestorId = $row['ancestorId'];
            $propId = $row['propId'];
            $propName = $row['propName'];
            $childId = $row['childId'];

            if (!isset($nodes[$propId])) {
                $node = new Node($propId, $propName);
                $nodes[$propId] = $node;
                if ($ancestorId === $propId && !isset($rootNodes[$propId])) {
                    $rootNodes[$propId] = $node;
                }
            }

            if (!empty($childId)) {
                $nodes[$propId]->children[] = $childId;
            }
        }

        // Set children nodes
        foreach ($nodes as $node) {
            $children = [];
            foreach ($node->children as $childId) {
                $children[] = $nodes[$childId];
            }
            $node->children = $children;
        }

        return array_values($rootNodes);
    }
}
