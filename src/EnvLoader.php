<?php

declare(strict_types=1);

namespace Shovhan\SlimSkeleton;

use Dotenv\Dotenv;
use Dotenv\Repository\Adapter\PutenvAdapter;
use Dotenv\Repository\RepositoryBuilder;

final class EnvLoader
{
    public function load(string $path): void
    {
        $repository = RepositoryBuilder::createWithDefaultAdapters()
            ->addAdapter(PutenvAdapter::class)
            ->immutable()
            ->make();

        Dotenv::create($repository, $path)->safeLoad();
    }
}
