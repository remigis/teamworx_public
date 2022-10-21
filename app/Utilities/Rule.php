<?php

namespace App\Utilities;

class Rule
{
    private string $start = '/^';
    private string $end = '\b/';
    private array $chunks;

    private string $rule;

    public string $regex;

    public function __construct($rule)
    {
        $this->rule = $rule;
        $this->makeChunks();
        $this->regex = $this->makeRegex();
    }

    public function makeRegex()
    {
        $regex = $this->start;

        foreach ($this->chunks as $chunk) {

            if (strpos($chunk, '-') !== 1 || !str_contains($chunk, '-')) {
                $regex .= $chunk;
            } else {
                $regex .= $this->letterOrNumberRegexChunk($chunk);
            }

        }
        $regex .= $this->end;

        return $regex;

    }

    private function makeChunks()
    {
        $this->chunks = explode(',', $this->rule);
    }

    private function letterOrNumberRegexChunk(mixed $chunk)
    {
        $bite = explode('-', $chunk, 2);

        $this->checkBite($bite);

        if ($bite[0] === 'L') {
            return '[a-zA-Z]{' . $bite[1] . '}';
        }

        if ($bite[0] === 'N') {
            return '[0-9]{' . $bite[1] . '}';
        }

        abort(422, 'Bad rule format, only L and N allowed (L - Letters, N - Numbers)'); //this will never be reached

    }

    private function checkBite($bite)
    {
        if (count($bite) !== 2) {
            abort(422, 'Bad rule format');
        }
        if (!in_array($bite[0], ['L', 'N'])) {
            abort(422, 'Bad rule format, only L and N allowed (L - Letters, N - Numbers)');
        }
        if (!is_numeric($bite[1]) || $bite[1] < 1) {
            abort(422, 'Bad rule format (Bad numbering)');
        }


    }

    public static function fails(mixed $item, array $regexes): bool
    {
        foreach ($regexes as $regex) {
            if (preg_match($regex, strtoupper($item['seriennummer']))) {
                return false;
            }
        }

        return true;
    }


}
