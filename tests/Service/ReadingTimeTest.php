<?php

namespace App\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Service\ReadingTime;

class ReadingTimeTest extends KernelTestCase
{

    /**
     * @dataProvider wordsRepeat
     */
    public function testCalculate($timeValue, $repeatValues): void
    {
        $kernel = self::bootKernel();
        $readingTime = static::getContainer()->get(ReadingTime::class);

        $this->assertSame($timeValue, $readingTime->calculate($repeatValues));
    }

    public function wordsRepeat()
    {
        return [
            ["1 min", str_repeat("test ", 249) . 'test'],
            ["2 min", str_repeat("test ", 499) . 'test'],
            ["3 min", str_repeat("test ", 649) . 'test']
        ];
    }
}
