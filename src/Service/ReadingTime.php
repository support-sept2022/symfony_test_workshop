<?php

namespace App\Service;

class ReadingTime
{
    public function calculate(string $content): string
    {

        return ceil(count(explode(" ", $content))/250) . " min";
    }
}
