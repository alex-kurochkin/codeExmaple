<?php

declare(strict_types=1);

namespace api\models;

class TextTableFormat
{
    private bool $usePseudoGraphics = false;

    public function usePseudoGraphics(): void
    {
        $this->usePseudoGraphics = true;
    }

    /**
     * @param string[] $headers - Name for table columns
     * @param array $body - content. Array of a scalar values.
     * @param string[] $format - "d" - digit, "s" - string. For each column. Used for justify to left or right.
     *  Strings justify to left and digits justify to right.
     * @return string
     */
    public function format(array $headers, array $body, array $format): string
    {
        $maxLengths = $this->findMaxLengths($headers, $body);

        $buffer[] = $this->dynamicLine($maxLengths);

        $headerFormat = $this->dynamicFormat(array_fill(0, count($format), 's'), $maxLengths, []);
        $buffer[] = sprintf($headerFormat, ...$headers);

        $buffer[] = $this->dynamicLine($maxLengths);

        foreach ($body as $record) {
            $bodyFormat = $this->dynamicFormat($format, $maxLengths, $record);
            $buffer[] = sprintf($bodyFormat, ...array_values($record));
        }

        $buffer[] = $this->dynamicLine($maxLengths);

        if ($this->usePseudoGraphics) {
            $buffer = $this->pseudoGraphics($buffer);
        }

        return implode(PHP_EOL, $buffer) . PHP_EOL;
    }

    private function findMaxLengths(array $headers, array $body): array
    {
        $maxLengths = [];

        foreach ($headers as $k => $v) {
            if (!array_key_exists($k, $maxLengths)) {
                $maxLengths[$k] = 0;
            }

            $length = strlen((string)$v);
            if ($length > $maxLengths[$k]) {
                $maxLengths[$k] = $length;
            }
        }

        foreach ($body as $record) {
            $keys = array_values($record);
            foreach ($keys as $k => $v) {
                $length = strlen((string)$v);
                if ($length > $maxLengths[$k]) {
                    $maxLengths[$k] = $length;
                }
            }
        }

        return $maxLengths;
    }

    private function dynamicLine(array $lengths): string
    {
        $s = '';
        foreach ($lengths as $length) {
            $s .= '+-' . str_repeat('-', $length) . '-';
        }

        return $s . '+';
    }

    private function dynamicFormat(array $formats, array $lengths, array $record): string
    {
        $values = array_values($record);

        $f = '';
        foreach ($formats as $k => $format) {
            $length = $lengths[$k];
            if (array_key_exists($k, $values) && null === $values[$k]) {
                $f .= '| ' . str_repeat(' ', $length - 4) . '%snull ';
            } elseif ('s' === $format) {
                $f .= '| %-' . $length . $format . ' ';
            } else {
                $f .= '| %' . $length . $format . ' ';
            }
        }

        return $f . '|';
    }

    private function pseudoGraphics(array $buffer): array
    {
        $buffSize = count($buffer);
        $i = 0;
        foreach ($buffer as &$string) {
            $i++;

            if (1 === $i) {
                $string = '┏' . substr($string, 1, -1) . '┓';
                $string = str_replace(array('+', '-', '|', '+'), array('┳', '━', '┃', '╋'), $string);
                continue;
            }

            if ($buffSize === $i) {
                $string = '┗' . substr($string, 1, -1) . '┛';
                $string = str_replace(array('+', '-', '|', '+'), array('┻', '━', '┃', '╋'), $string);
                continue;
            }

            if (' ' !== $string[1]) {
                $string = '┣' . substr($string, 1, -1) . '┫';
            }

            $string = str_replace(['-', '|', '+'], ['━', '┃', '╋'], $string);
        }

        return $buffer;
    }
}