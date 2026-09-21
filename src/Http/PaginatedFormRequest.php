<?php

declare(strict_types=1);

namespace Shovhan\SlimSkeleton\Http;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Query-string pagination shared by every list route.
 *
 * The cap is a trust boundary, not a default: an unbounded `limit` hydrates the whole table into one JSON
 * document and exhausts memory before the response is written (ADR 0005).
 */
abstract class PaginatedFormRequest extends FormRequest
{
    /** @api referenced by the tests that pin the cap */
    public const int MAX_LIMIT = 1000;

    private const int DEFAULT_LIMIT = 100;

    /** @return array<string, Constraint|list<Constraint>> */
    protected function rules(): array
    {
        return [
            'limit' => [
                new Assert\Type('digit'),
                new Assert\Range(min: 1, max: self::MAX_LIMIT),
            ],
            'offset' => [
                new Assert\Type('digit'),
                new Assert\PositiveOrZero(),
            ],
        ];
    }

    public function getLimit(): int
    {
        return $this->toIntOrDefault($this->input('limit'), self::DEFAULT_LIMIT);
    }

    public function getOffset(): int
    {
        return $this->toIntOrDefault($this->input('offset'), 0);
    }

    private function toIntOrDefault(mixed $value, int $default): int
    {
        return is_numeric($value) ? (int) $value : $default;
    }
}
