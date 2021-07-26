<?php

namespace Ocd\PersonnalDataBundle\Archiver;


class Anonymizer
{
    static function string(string $string): string
    {
        return '__ANONYMIZED_STRING__';
    }
    static function datetime(DateTime $dateTime): DateTime
    {
        return (new DateTime())->setTimestamp('1234567890');
    }
    static function integer(int $integer): int
    {
        return 1;
    }
    static function boolean(bool $boolean): bool
    {
        return true;
    }
    static function float(float $float): float
    {
        return 3.141593;
    }

    static function latitude(float $latitude): float
    {
        return 48.853402;
    }
    static function longitude(float $longitude): float
    {
        return 2.348785;
    }
    static function email(string $email): float
    {
        return 'anonymous@mail.example';
    }

}