<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Empty_\SimplifyEmptyCheckOnEmptyArrayRector;
use Rector\CodeQuality\Rector\FuncCall\SimplifyRegexPatternRector;
use Rector\CodeQuality\Rector\FunctionLike\SimplifyUselessVariableRector;
use Rector\CodeQuality\Rector\If_\ExplicitBoolCompareRector;
use Rector\CodeQuality\Rector\LogicalAnd\LogicalToBooleanRector;
use Rector\CodingStyle\Rector\FuncCall\CountArrayToEmptyArrayComparisonRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\Cast\RecastingRemovalRector;
use Rector\EarlyReturn\Rector\If_\ChangeOrIfContinueToMultiContinueRector;
use Rector\EarlyReturn\Rector\If_\RemoveAlwaysElseRector;
use Rector\Php74\Rector\Property\RestoreDefaultNullToNullableTypePropertyRector;
use Rector\Php80\Rector\Catch_\RemoveUnusedVariableInCatchRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php81\Rector\Property\ReadOnlyPropertyRector;
use Rector\Php82\Rector\Class_\ReadOnlyClassRector;
use Rector\TypeDeclaration\Rector\ArrowFunction\AddArrowFunctionReturnTypeRector;
use Rector\ValueObject\PhpVersion;

return RectorConfig::configure()
    ->withPhpVersion(PhpVersion::PHP_82)
    ->withPaths([
        __DIR__.'/src',
    ])
    ->withRules([
        ReadOnlyPropertyRector::class,
        ReadOnlyClassRector::class,
        RemoveUnusedVariableInCatchRector::class,
        RestoreDefaultNullToNullableTypePropertyRector::class,
        SimplifyEmptyCheckOnEmptyArrayRector::class,
        LogicalToBooleanRector::class,
        RecastingRemovalRector::class,
        ExplicitBoolCompareRector::class,
        SimplifyUselessVariableRector::class,
        CountArrayToEmptyArrayComparisonRector::class,
        SimplifyRegexPatternRector::class,
        ClassPropertyAssignToConstructorPromotionRector::class,
        RemoveAlwaysElseRector::class,
        ChangeOrIfContinueToMultiContinueRector::class,
        AddArrowFunctionReturnTypeRector::class,
    ]);
