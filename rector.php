<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\Class_\EmptyGuardedPropertyToUnguardedAttributeRector;
use RectorLaravel\Rector\Class_\FillablePropertyToFillableAttributeRector;
use RectorLaravel\Rector\Class_\TablePropertyToTableAttributeRector;
use RectorLaravel\Rector\Class_\WithoutTimestampsPropertyToWithoutTimestampsAttributeRector;

/*
 * Library, not an application: no privatization and no "treat classes as
 * final" here. Apps extend a plugin's classes and override its protected
 * methods; Rector cannot see those subclasses, so narrowing visibility or
 * finalizing classes would break them without a failing test in this repository.
 */
return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    // `laravel: true` applies the driftingly/rector-laravel sets for the
    // installed laravel/framework version.
    ->withComposerBased(laravel: true)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,
        earlyReturn: true,
    )
    ->withPhpSets()
    ->withSkip([
        // Laravel 13 Eloquent attributes (#[Table], #[Fillable], ...): the test
        // models must keep working on Laravel 12, which CI still covers.
        EmptyGuardedPropertyToUnguardedAttributeRector::class,
        FillablePropertyToFillableAttributeRector::class,
        TablePropertyToTableAttributeRector::class,
        WithoutTimestampsPropertyToWithoutTimestampsAttributeRector::class,
    ]);
