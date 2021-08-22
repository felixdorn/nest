<?php

namespace Felix\Nest\Concerns;

trait HandlesTokenization
{
    public function takeUntilChar(string $stop): string
    {
        $carry = '';

        while (!$this->eof()) {
            $previous = $this->lookBehind();
            $char     = $this->current();
            $this->next();

            if ($char === $stop) {
                if ($previous !== '\\') {
                    return $carry;
                }

                // We remove the escape control character.
                $carry = substr($carry, 0, -1);
            }

            $carry .= $char;
        }

        return $carry;
    }

    public function takeUntilSequence(string|array $sequences): string
    {
        if (is_string($sequences)) {
            $sequences = [$sequences];
        }

        $carry = '';

        while (!$this->eof()) {
            $char = $this->current();
            $this->next();

            foreach ($sequences as $sequence) {
                if (str_ends_with($carry, $sequence)) {
                    $sequenceLength = strlen($sequence);
                    $this->rewind($sequenceLength);

                    return substr($carry, 0, -$sequenceLength);
                }
            }

            $carry .= $char;
        }

        return $carry;
    }
}
