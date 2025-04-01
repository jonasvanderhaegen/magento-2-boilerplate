<?php

declare(strict_types=1);
use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\CodeQuality\Rector\ClassMethod\OptionalParametersAfterRequiredRector;
use Rector\CodingStyle\Rector\ClassConst\VarConstantCommentRector;
use Rector\Config\RectorConfig;
use Rector\EarlyReturn\Rector\If_\ChangeAndIfToEarlyReturnRector;
use Rector\Php80\Rector\Class_\StringableForToStringRector;
use Rector\Php80\Rector\ClassMethod\FinalPrivateToPrivateVisibilityRector;
use Rector\Php80\Rector\ClassMethod\SetStateToStaticRector;
use Rector\Php81\Rector\ClassConst\FinalizePublicClassConstantRector;
use Rector\Php81\Rector\FuncCall\Php81ResourceReturnToObjectRector;
use Rector\Php81\Rector\Property\ReadOnlyPropertyRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/app/code'
    ]);

    // register a rules
    $rectorConfig->rules([
        InlineConstructorDefaultToPropertyRector::class,
        FinalPrivateToPrivateVisibilityRector::class,
        OptionalParametersAfterRequiredRector::class,
        SetStateToStaticRector::class,
        StringableForToStringRector::class,
        Php81ResourceReturnToObjectRector::class
    ]);

    $rectorConfig->skip([
        ReadOnlyPropertyRector::class,
        FinalizePublicClassConstantRector::class,
        ChangeAndIfToEarlyReturnRector::class,
        VarConstantCommentRector::class
    ]);

    // define sets of rules
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_81,
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::CODING_STYLE,
        SetList::EARLY_RETURN
    ]);
};
