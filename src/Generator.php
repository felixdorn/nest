<?php

namespace Felix\Nest;

use Carbon\CarbonInterface;

class Generator
{
    public function generate(array $event, CarbonInterface $current): array
    {
        return [
            'label'       => $event['label'] ?? '',
            'now'         => $current->toDateTimeString(),
            'occurrences' => [],
        ];
    }
}
