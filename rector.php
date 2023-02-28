<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Core\Configuration\Option;
use Rector\Core\ValueObject\PhpVersion;
use Rector\Php53\Rector\Ternary\TernaryToElvisRector;
use Rector\Set\ValueObject\SetList;

// rector process src

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->disableParallel();

    $parameters = $rectorConfig->parameters();

    $parameters->set(Option::IMPORT_SHORT_CLASSES, false);

    $parameters->set(Option::PHP_VERSION_FEATURES, PhpVersion::PHP_56);

    $parameters->set(Option::AUTOLOAD_PATHS, array(__DIR__ . '/vendor/autoload.php'));

    $parameters->set(Option::SKIP, array(
        // Rectors
        Rector\CodeQuality\Rector\Class_\CompleteDynamicPropertiesRector::class,
        Rector\CodeQuality\Rector\Concat\JoinStringConcatRector::class,
        Rector\CodeQuality\Rector\FuncCall\ChangeArrayPushToArrayAssignRector::class,
        Rector\CodeQuality\Rector\FuncCall\CompactToVariablesRector::class,
        Rector\CodeQuality\Rector\FuncCall\IntvalToTypeCastRector::class,
        Rector\CodeQuality\Rector\Identical\BooleanNotIdenticalToNotIdenticalRector::class,
        Rector\CodeQuality\Rector\Identical\SimplifyBoolIdenticalTrueRector::class,
        Rector\CodeQuality\Rector\If_\CombineIfRector::class,
        Rector\CodeQuality\Rector\If_\ExplicitBoolCompareRector::class,
        Rector\CodeQuality\Rector\If_\SimplifyIfElseToTernaryRector::class,
        Rector\CodeQuality\Rector\If_\SimplifyIfReturnBoolRector::class,
        Rector\CodeQuality\Rector\Isset_\IssetOnPropertyObjectToPropertyExistsRector::class,
        Rector\CodeQuality\Rector\PropertyFetch\ExplicitMethodCallOverMagicGetSetRector::class,
        Rector\CodeQuality\Rector\FunctionLike\SimplifyUselessLastVariableAssignRector::class,
        Rector\CodeQuality\Rector\FunctionLike\SimplifyUselessVariableRector::class,
        Rector\CodingStyle\Rector\ClassConst\VarConstantCommentRector::class,
        Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector::class,
        Rector\CodingStyle\Rector\FuncCall\ConsistentPregDelimiterRector::class,
        Rector\CodingStyle\Rector\String_\SymplifyQuoteEscapeRector::class,
        Rector\DeadCode\Rector\Assign\RemoveUnusedVariableAssignRector::class,
        Rector\Php70\Rector\FuncCall\NonVariableToVariableOnFunctionCallRector::class,
        Rector\Php70\Rector\MethodCall\ThisCallOnStaticMethodToStaticCallRector::class,
        Rector\Php70\Rector\StaticCall\StaticCallOnNonStaticToInstanceCallRector::class,
        Rector\Php71\Rector\FuncCall\CountOnNullRector::class,
        Rector\Php73\Rector\FuncCall\JsonThrowOnErrorRector::class,
        Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector::class,
        Rector\Php74\Rector\Property\TypedPropertyRector::class,
        Rector\TypeDeclaration\Rector\FunctionLike\ParamTypeDeclarationRector::class,
        Rector\CodingStyle\Rector\ClassMethod\UnSpreadOperatorRector::class,
        Rector\CodingStyle\Rector\PostInc\PostIncDecToPreIncDecRector::class,
        Rector\DeadCode\Rector\ClassMethod\RemoveUselessParamTagRector::class,
        Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPromotedPropertyRector::class,
        Rector\CodingStyle\Rector\Stmt\NewlineAfterStatementRector::class,
        Rector\DeadCode\Rector\StaticCall\RemoveParentCallWithoutParentRector::class,
        Rector\Transform\Rector\String_\StringToClassConstantRector::class,
        Rector\CodeQuality\Rector\FuncCall\InlineIsAInstanceOfRector::class,
        Rector\CodingStyle\Rector\Closure\StaticClosureRector::class,
        Rector\DeadCode\Rector\ClassMethod\RemoveUselessReturnTagRector::class,
        Rector\CodeQuality\Rector\ClassMethod\ReturnTypeFromStrictScalarReturnExprRector::class,
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
        Rector\Php74\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector::class,
        Rector\Php74\Rector\LNumber\AddLiteralSeparatorToNumberRector::class,
        Rector\Php74\Rector\MethodCall\ChangeReflectionTypeToStringToGetNameRector::class,
        Rector\Php74\Rector\StaticCall\ExportToReflectionFunctionRector::class,
        Rector\CodingStyle\Rector\ClassConst\RemoveFinalFromConstRector::class, // PHP 8
    ));

    $rectorConfig->import(SetList::CODE_QUALITY);
    $rectorConfig->import(SetList::CODING_STYLE);
    $rectorConfig->import(SetList::DEAD_CODE);
    $rectorConfig->import(SetList::PHP_70);
    $rectorConfig->import(SetList::PHP_71);
    $rectorConfig->import(SetList::PHP_72);
    $rectorConfig->import(SetList::PHP_73);
    $rectorConfig->import(SetList::PHP_74);
    $rectorConfig->import(SetList::PHP_80);
    $rectorConfig->import(SetList::PHP_81);
    $rectorConfig->import(SetList::PHP_82);

    $services = $rectorConfig->services();

    $services->set(TernaryToElvisRector::class);
};
