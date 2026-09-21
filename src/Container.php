<?php

declare(strict_types=1);

namespace Shovhan\SlimSkeleton;

use Psr\Container\ContainerInterface;

readonly class Container implements ContainerInterface
{
    public function __construct(private ContainerInterface $innerContainer)
    {
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $className
     *
     * @return T
     */
    public function getObject(string $className): object
    {
        $object = $this->innerContainer->get($className);
        assert($object instanceof $className);

        /** @var T $object */
        return $object;
    }

    public function get(string $identifier): mixed
    {
        return $this->innerContainer->get($identifier);
    }

    public function has(string $identifier): bool
    {
        return $this->innerContainer->has($identifier);
    }
}
