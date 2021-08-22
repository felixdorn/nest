<?php

namespace Felix\Nest\Compiler;

class Preprocessor
{
    public function process(string $code): string
    {
        $elements = explode(' ', strtolower($code));

        array_walk($elements, [$this, 'update'], $elements);

        return implode(' ', $elements);
    }

    public function update(string &$element, int $index, array $elements): void
    {
        if ($this->lookBehind($elements, $index) === 'at') {
            if (strlen($element) === 1) {
                $element .= ':00pm';

                return;
            }

            // 6AM, 2PM...
            if (strlen($element) === 3) {
                $element = $element[0] . ':00' . $element[1] /* A or P */ . $element[2] /* M */
                ;
            }

            if (preg_match('/^[0-9]:[0-9]{1,2}$/', $element)) {
                $element .= 'pm';

                return;
            }
        }
    }

    protected function lookBehind(array $elements, int $index, int $n = 1): ?string
    {
        return $elements[$index - $n] ?? null;
    }
}
