<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\FuncCall\ChangeArrayPushToArrayAssignRector;
use Rector\CodeQuality\Rector\FuncCall\CompactToVariablesRector;
use Rector\CodeQuality\Rector\FuncCall\InlineIsAInstanceOfRector;
use Rector\CodeQuality\Rector\FunctionLike\SimplifyUselessVariableRector;
use Rector\CodeQuality\Rector\Identical\BooleanNotIdenticalToNotIdenticalRector;
use Rector\CodeQuality\Rector\Identical\SimplifyBoolIdenticalTrueRector;
use Rector\CodeQuality\Rector\If_\SimplifyIfReturnBoolRector;
use Rector\CodeQuality\Rector\Isset_\IssetOnPropertyObjectToPropertyExistsRector;
use Rector\CodeQuality\Rector\Ternary\ArrayKeyExistsTernaryThenValueToCoalescingRector;
use Rector\CodingStyle\Rector\ClassConst\RemoveFinalFromConstRector;
use Rector\CodingStyle\Rector\ClassLike\NewlineBetweenClassLikeStmtsRector;
use Rector\CodingStyle\Rector\Stmt\NewlineAfterStatementRector;
use Rector\CodingStyle\Rector\String_\SimplifyQuoteEscapeRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\Assign\RemoveUnusedVariableAssignRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPromotedPropertyRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessParamTagRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessReturnTagRector;
use Rector\DeadCode\Rector\StaticCall\RemoveParentCallWithoutParentRector;
use Rector\Php54\Rector\Array_\LongArrayToShortArrayRector;
use Rector\Php70\Rector\If_\IfToSpaceshipRector;
use Rector\Php70\Rector\MethodCall\ThisCallOnStaticMethodToStaticCallRector;
use Rector\Php70\Rector\Ternary\TernaryToSpaceshipRector;
use Rector\Php71\Rector\BooleanOr\IsIterableRector;
use Rector\Php71\Rector\List_\ListToArrayDestructRector;
use Rector\Php71\Rector\TryCatch\MultiExceptionCatchRector;
use Rector\Php73\Rector\BooleanOr\IsCountableRector;
use Rector\Php73\Rector\FuncCall\ArrayKeyFirstLastRector;
use Rector\Php74\Rector\Assign\NullCoalescingOperatorRector;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use Rector\Php74\Rector\StaticCall\ExportToReflectionFunctionRector;
use Rector\Set\ValueObject\SetList;
use Rector\ValueObject\PhpVersion;

// phpcs:disable Generic.Arrays.DisallowLongArraySyntax

// rector process

return RectorConfig::configure()
    ->withParallel(120 * 2, 16, 16)
    ->withPhpVersion(PhpVersion::PHP_56)
    ->withPhpSets()
    ->withSets(
        array(
            SetList::CODE_QUALITY,
            SetList::CODING_STYLE,
            SetList::DEAD_CODE,
        )
    )
    ->withPaths(
        array(
            __DIR__ . DIRECTORY_SEPARATOR . 'src',
        )
    )
    ->withSkip(
        array(
            BooleanNotIdenticalToNotIdenticalRector::class,
            ChangeArrayPushToArrayAssignRector::class,
            ClosureToArrowFunctionRector::class,
            CompactToVariablesRector::class,
            InlineIsAInstanceOfRector::class,
            IssetOnPropertyObjectToPropertyExistsRector::class,
            LongArrayToShortArrayRector::class,
            NewlineAfterStatementRector::class,
            RemoveParentCallWithoutParentRector::class,
            RemoveUnusedPromotedPropertyRector::class,
            RemoveUnusedVariableAssignRector::class,
            RemoveUselessParamTagRector::class,
            RemoveUselessReturnTagRector::class,
            SimplifyBoolIdenticalTrueRector::class,
            SimplifyIfReturnBoolRector::class,
            SimplifyQuoteEscapeRector::class,
            SimplifyUselessVariableRector::class,
            ThisCallOnStaticMethodToStaticCallRector::class,
            // PHP 5.6 incompatible
            ArrayKeyExistsTernaryThenValueToCoalescingRector::class, // PHP 7
            ArrayKeyFirstLastRector::class,
            ExportToReflectionFunctionRector::class,
            IfToSpaceshipRector::class,
            IsCountableRector::class,
            IsIterableRector::class,
            ListToArrayDestructRector::class,
            MultiExceptionCatchRector::class,
            NewlineBetweenClassLikeStmtsRector::class,
            NullCoalescingOperatorRector::class,
            RemoveFinalFromConstRector::class, // PHP 8
            TernaryToSpaceshipRector::class,
        )
    )
    ->reportUnusedSkips();
