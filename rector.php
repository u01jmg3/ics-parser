<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php53\Rector\Ternary\TernaryToElvisRector;
use Rector\Set\ValueObject\SetList;
use Rector\ValueObject\PhpVersion;

// phpcs:disable Generic.Arrays.DisallowLongArraySyntax

// rector process src

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->disableParallel();

    $rectorConfig->importShortClasses(false);

    $rectorConfig->phpVersion(PhpVersion::PHP_56);

    $rectorConfig->skip(
        array(
            Rector\CodeQuality\Rector\Class_\CompleteDynamicPropertiesRector::class,
            Rector\CodeQuality\Rector\Concat\JoinStringConcatRector::class,
            Rector\CodeQuality\Rector\FuncCall\ChangeArrayPushToArrayAssignRector::class,
            Rector\CodeQuality\Rector\FuncCall\CompactToVariablesRector::class,
            Rector\CodeQuality\Rector\FuncCall\InlineIsAInstanceOfRector::class,
            Rector\CodeQuality\Rector\FunctionLike\SimplifyUselessVariableRector::class,
            Rector\CodeQuality\Rector\Identical\BooleanNotIdenticalToNotIdenticalRector::class,
            Rector\CodeQuality\Rector\Identical\SimplifyBoolIdenticalTrueRector::class,
            Rector\CodeQuality\Rector\If_\CombineIfRector::class,
            Rector\CodeQuality\Rector\If_\ExplicitBoolCompareRector::class,
            Rector\CodeQuality\Rector\If_\SimplifyIfElseToTernaryRector::class,
            Rector\CodeQuality\Rector\If_\SimplifyIfReturnBoolRector::class,
            Rector\CodeQuality\Rector\Isset_\IssetOnPropertyObjectToPropertyExistsRector::class,
            Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector::class,
            Rector\CodingStyle\Rector\Stmt\NewlineAfterStatementRector::class,
            Rector\CodingStyle\Rector\String_\SymplifyQuoteEscapeRector::class,
            Rector\DeadCode\Rector\Assign\RemoveUnusedVariableAssignRector::class,
            Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPromotedPropertyRector::class,
            Rector\DeadCode\Rector\ClassMethod\RemoveUselessParamTagRector::class,
            Rector\DeadCode\Rector\ClassMethod\RemoveUselessReturnTagRector::class,
            Rector\DeadCode\Rector\StaticCall\RemoveParentCallWithoutParentRector::class,
            Rector\Php70\Rector\MethodCall\ThisCallOnStaticMethodToStaticCallRector::class,
            Rector\Php70\Rector\StaticCall\StaticCallOnNonStaticToInstanceCallRector::class,
            Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector::class,
            // PHP 5.6 incompatible
            Rector\CodeQuality\Rector\Ternary\ArrayKeyExistsTernaryThenValueToCoalescingRector::class, // PHP 7
            Rector\Php70\Rector\If_\IfToSpaceshipRector::class,
            Rector\Php70\Rector\Ternary\TernaryToSpaceshipRector::class,
            Rector\Php71\Rector\BooleanOr\IsIterableRector::class,
            Rector\Php71\Rector\List_\ListToArrayDestructRector::class,
            Rector\Php71\Rector\TryCatch\MultiExceptionCatchRector::class,
            Rector\Php73\Rector\FuncCall\ArrayKeyFirstLastRector::class,
            Rector\Php73\Rector\BooleanOr\IsCountableRector::class,
            Rector\Php74\Rector\Assign\NullCoalescingOperatorRector::class,
            Rector\Php74\Rector\StaticCall\ExportToReflectionFunctionRector::class,
            Rector\CodingStyle\Rector\ClassConst\RemoveFinalFromConstRector::class, // PHP 8
        )
    );

    $rectorConfig->sets(
        array(
            SetList::CODE_QUALITY,
            SetList::CODING_STYLE,
            SetList::DEAD_CODE,
            SetList::PHP_70,
            SetList::PHP_71,
            SetList::PHP_72,
            SetList::PHP_73,
            SetList::PHP_74,
            SetList::PHP_80,
            SetList::PHP_81,
            SetList::PHP_82,
        )
    );

    $rectorConfig->rule(TernaryToElvisRector::class);
};
