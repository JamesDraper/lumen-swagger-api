<?php
declare(strict_types=1);

namespace Lib;

use InvalidArgumentException;

use stdClass;

class JsonResponseBody
{
    private $success;

    private $vars;

    private $errs;

    public static function createSuccess(array $vars)
    {
        return new self(true, $vars, []);
    }

    public static function createFailure(array $errs)
    {
        return new self(false, [], $errs);
    }

    private function __construct(bool $success, ?array $vars, ?array $errs)
    {
        $this->success = $success;
        $this->vars    = $this->parseVars($vars);
        $this->errs    = $this->parseErrs($errs);
    }

    public function toString(): string
    {
        return $this->__toString();
    }

    public function __toString(): string
    {
        return json_encode([
            'success' => $this->success,
            'vars' => $this->vars,
            'errs' => $this->errs
        ]);
    }

    private function parseVars(?array $vars): stdClass
    {
        if (empty($vars)) {
            return (object)[];
        }

        foreach (array_keys($vars) as $k) {
            if (!is_string($k)) {
                throw new InvalidArgumentException('All $vars must have a name and a value, got a number.');
            }
        }

        return (object)$vars;
    }

    private function parseErrs(?array $errs): array
    {
        foreach (array_keys($errs) as $k) {
            if (is_string($k)) {
                throw new InvalidArgumentException('No $errs can have names, the $errs array must be numeric.');
            }
        }

        return $errs;
    }
}
