<?php

declare(strict_types=1);

namespace Shovhan\SlimSkeleton;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\ORMSetup;

final class EntityManagerFactory
{
    /** @SuppressWarnings("PHPMD.StaticAccess") ORMSetup/DriverManager are Doctrine's bootstrap API */
    public function create(): EntityManagerInterface
    {
        $config = ORMSetup::createXMLMetadataConfig(
            paths: [
            //                __DIR__ . '/Settlement/Infrastructure/Doctrine',
            ],
            isDevMode: getenv('APP_ENV') !== 'production',
        );
        $config->enableNativeLazyObjects(true);
        $config->setNamingStrategy(new UnderscoreNamingStrategy());

        $dsnParser = new DsnParser(['pgsql' => 'pdo_pgsql']);
        $connection = DriverManager::getConnection(
            $dsnParser->parse((string) getenv('DATABASE_URL')),
            $config,
        );

        return new EntityManager($connection, $config);
    }
}
