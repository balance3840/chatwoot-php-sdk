<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\DTO;

use ReflectionClass;
use ReflectionProperty;

abstract class BaseDTO
{
    /**
     * Hydrate a DTO from an API response array.
     */
    public static function fromArray(array $data): static
    {
        $dto        = new static();
        $reflection = new ReflectionClass($dto);

        foreach ($data as $key => $value) {
            if (!property_exists($dto, $key)) {
                continue;
            }

            $property = $reflection->getProperty($key);

            try {
                $dto->$key = self::castValue($property, $value);
            } catch (\TypeError) {
                // Last-resort fallback: API returned an unexpected type we couldn't
                // coerce. Leave the property at its default (null) rather than crash.
            }
        }

        return $dto;
    }

    /**
     * Hydrate an array of DTOs from an array of response arrays.
     *
     * @return static[]
     */
    public static function collect(array $items): array
    {
        return array_map(
            fn (array $item) => static::fromArray($item),
            array_filter($items, 'is_array')
        );
    }

    /**
     * Serialize the DTO back to an array (useful for API calls).
     */
    public function toArray(bool $excludeNull = false): array
    {
        $result = [];

        foreach (get_object_vars($this) as $key => $value) {
            if ($excludeNull && $value === null) {
                continue;
            }

            $result[$key] = self::serializeValue($value, $excludeNull);
        }

        return $result;
    }

    // ------------------------------------------------------------------
    // Casting internals
    // ------------------------------------------------------------------

    private static function castValue(ReflectionProperty $property, mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        $type = $property->getType();

        if (!$type) {
            return $value;
        }

        if ($type instanceof \ReflectionUnionType) {
            return self::castUnion($type, $property, $value);
        }

        if (!$type instanceof \ReflectionNamedType) {
            return $value;
        }

        return self::castNamed($type, $property, $value);
    }

    private static function castNamed(\ReflectionNamedType $type, ReflectionProperty $property, mixed $value): mixed
    {
        $typeName = $type->getName();

        if ($type->isBuiltin()) {
            if ($typeName === 'array') {
                if (is_array($value)) {
                    return self::castArrayFromDocblock($property, $value);
                }
                // Non-array value for array property — wrap scalar or return empty
                return is_scalar($value) ? [$value] : [];
            }

            // Coerce scalar mismatches (int/bool/string/float)
            return self::coerceBuiltin($typeName, $value);
        }

        $class = $typeName;

        // Backed enum
        if ((is_string($value) || is_int($value)) && enum_exists($class) && is_subclass_of($class, \BackedEnum::class)) {
            return $class::tryFrom($value);
        }

        // Nested DTO
        if (is_array($value) && is_subclass_of($class, self::class)) {
            return $class::fromArray($value);
        }

        return $value;
    }

    private static function castUnion(\ReflectionUnionType $union, ReflectionProperty $property, mixed $value): mixed
    {
        foreach ($union->getTypes() as $type) {
            if (!$type instanceof \ReflectionNamedType || $type->getName() === 'null') {
                continue;
            }

            if ($type->isBuiltin()) {
                if ($type->getName() === 'array' && is_array($value)) {
                    return self::castArrayFromDocblock($property, $value);
                }
                continue;
            }

            $class = $type->getName();

            if (is_array($value) && class_exists($class) && is_subclass_of($class, self::class)) {
                return $class::fromArray($value);
            }

            if ((is_string($value) || is_int($value)) && enum_exists($class) && is_subclass_of($class, \BackedEnum::class)) {
                $result = $class::tryFrom($value);
                if ($result !== null) {
                    return $result;
                }
            }
        }

        return $value;
    }

    private static function castArrayFromDocblock(ReflectionProperty $property, array $value): array
    {
        $doc = $property->getDocComment();

        if ($doc && preg_match('/@var\s+([\\\\\w]+)\[\]/', $doc, $m)) {
            $class = $m[1];

            if (!class_exists($class)) {
                $class = $property->getDeclaringClass()->getNamespaceName() . '\\' . $class;
            }

            if (class_exists($class) && is_subclass_of($class, self::class)) {
                return $class::collect($value);
            }
        }

        return $value;
    }

    /**
     * Safely coerce a scalar value to the expected builtin PHP type.
     *
     * Handles common Chatwoot API quirks:
     *   - bool fields returned as 0/1 integers
     *   - int fields returned as numeric strings
     *   - string fields returned as integers
     */
    private static function coerceBuiltin(string $typeName, mixed $value): mixed
    {
        return match ($typeName) {
            'bool'   => match (true) {
                is_bool($value)    => $value,
                is_int($value)     => $value !== 0,
                is_string($value)  => !in_array(strtolower($value), ['false', '0', '', 'null'], true),
                default            => (bool) $value,
            },
            'int'    => match (true) {
                is_int($value)     => $value,
                is_bool($value)    => (int) $value,
                is_numeric($value) => (int) $value,
                default            => $value,  // leave as-is; TypeError catch handles the rest
            },
            'float'  => is_numeric($value) ? (float) $value : $value,
            'string' => is_scalar($value)  ? (string) $value : $value,
            default  => $value,
        };
    }

    // ------------------------------------------------------------------
    // Serialization internals
    // ------------------------------------------------------------------

    private static function serializeValue(mixed $value, bool $excludeNull): mixed
    {
        if ($value instanceof self) {
            return $value->toArray($excludeNull);
        }

        if ($value instanceof \BackedEnum) {
            return $value->value;
        }

        if (is_array($value)) {
            return array_map(
                fn ($item) => self::serializeValue($item, $excludeNull),
                $value
            );
        }

        return $value;
    }
}
