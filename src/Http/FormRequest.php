<?php

declare(strict_types=1);

namespace Shovhan\SlimSkeleton\Http;

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class FormRequest
{
    private readonly ValidatorInterface $validator;

    /** @var array<array-key, mixed> */
    private readonly array $data;

    /** @SuppressWarnings("PHPMD.StaticAccess") Validation::createValidator() is Symfony's builder entry point */
    public function __construct(private readonly Request $request)
    {
        $parsedBody = $request->getParsedBody();

        $this->data = array_merge(
            $request->getQueryParams(),
            is_array($parsedBody) ? $parsedBody : [],
            $request->getAttributes(),
        );

        $this->validator = Validation::createValidator();
    }

    /** @return array<string, Constraint|list<Constraint>> */
    abstract protected function rules(): array;

    public function validate(): void
    {
        $violations = new ConstraintViolationList();

        foreach ($this->rules() as $field => $constraints) {
            $violations->addAll($this->validator->validate($this->input($field), $constraints));
        }

        if (count($violations) > 0) {
            throw new HttpBadRequestException($this->request, (string) $violations);
        }
    }

    protected function input(string $field): mixed
    {
        return $this->data[$field] ?? null;
    }
}
