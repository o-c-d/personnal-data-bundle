<?php

namespace Ocd\PersonnalDataBundle\Archiver;

class Nullifier
{
    public static function string(string $string): string
    {
        return '';
    }
    public static function datetime(DateTime $dateTime): DateTime
    {
        return (new DateTime())->setTimestamp('0');
    }
    public static function integer(int $integer): int
    {
        return 0;
    }
    public static function boolean(bool $boolean): bool
    {
        return false;
    }
    public static function float(float $float): float
    {
        return 0.0;
    }
}
