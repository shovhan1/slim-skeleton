<?php

declare(strict_types=1);

namespace Shovhan\SlimSkeleton\Http;

use Closure;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use ReflectionFunction;
use ReflectionNamedType;
use Slim\Interfaces\InvocationStrategyInterface;

final class FormRequestInvocationStrategy implements InvocationStrategyInterface
{
    /** @param array<string, string> $routeArguments */
    public function __invoke(callable $callable, Request $request, Response $response, array $routeArguments): Response
    {
        foreach ($routeArguments as $name => $value) {
            $request = $request->withAttribute($name, $value);
        }

        $formRequestClass = $this->resolveFormRequestClass($callable);

        if ($formRequestClass === null) {
            /** @var Response */
            return $callable($request, $response, $routeArguments);
        }

        $formRequest = new $formRequestClass($request);
        $formRequest->validate();

        /** @var Response */
        return $callable($formRequest, $response);
    }

    /**
     * @return class-string<FormRequest>|null
     *
     * @SuppressWarnings("PHPMD.StaticAccess") Closure::fromCallable() normalizes any callable shape for reflection
     */
    private function resolveFormRequestClass(callable $callable): ?string
    {
        $parameters = (new ReflectionFunction(Closure::fromCallable($callable)))->getParameters();
        $firstParameter = $parameters[0] ?? null;
        $type = $firstParameter?->getType();

        if (! $type instanceof ReflectionNamedType || $type->isBuiltin()) {
            return null;
        }

        $typeName = $type->getName();

        if (! is_subclass_of($typeName, FormRequest::class)) {
            return null;
        }

        return $typeName;
    }
}
