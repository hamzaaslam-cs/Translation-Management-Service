<?php

namespace App\Traits;

use ReflectionClass;

trait EnumHelpers
{
    public static function keys(): array
    {
        $reflection = new ReflectionClass(self::class);
        $constants = $reflection->getConstants();

        return array_keys($constants);
    }

    public static function toArray(): array
    {
        $reflection = new ReflectionClass(self::class);

        return $reflection->getConstants();
    }

    public static function valueArray(): array
    {
        $data = [];
        $enumArray = self::values();
        foreach ($enumArray as $status) {
            $data[] = $status->value;
        }

        return $data;
    }

    public static function values(): array
    {
        $reflection = new ReflectionClass(self::class);
        $constants = $reflection->getConstants();

        return array_values($constants);
    }
}
