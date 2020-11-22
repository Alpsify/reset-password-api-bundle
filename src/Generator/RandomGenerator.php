<?php

namespace Alpsify\ResetPasswordAPIBundle\Generator;

class RandomGenerator
{
    private int $selectorSize;

    public function __construct(int $selectorSize)
    {
        $this->selectorSize = $selectorSize;
    }

    public function getRandomStr(): string
    {
        $string = '';

        while (($len = \strlen($string)) < $this->selectorSize) {
            $size = $this->selectorSize - $len;

            $bytes = \random_bytes($size);

            $string .= \substr(
                \str_replace(['/', '+', '='], '', \base64_encode($bytes)), 0, $size);
        }

        return $string;
    }
}
