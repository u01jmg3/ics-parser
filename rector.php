<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Php53\Rector\Ternary\TernaryToElvisRector;
use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

// rector process src

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::IMPORT_SHORT_CLASSES, false);

    $parameters->set(Option::PHP_VERSION_FEATURES, '5.6');

    $parameters->set(Option::AUTOLOAD_PATHS, array(__DIR__ . '/vendor/autoload.php'));

    $parameters->set(Option::EXCLUDE_RECTORS, array(
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
        Rector\CodeQuality\Rector\Return_\SimplifyUselessVariableRector::class,
        Rector\CodingStyle\Rector\ClassConst\VarConstantCommentRector::class,
        Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector::class,
        Rector\CodingStyle\Rector\FuncCall\ConsistentPregDelimiterRector::class,
        Rector\CodingStyle\Rector\Function_\CamelCaseFunctionNamingToUnderscoreRector::class,
        Rector\CodingStyle\Rector\String_\SymplifyQuoteEscapeRector::class,
        Rector\DeadCode\Rector\Assign\RemoveUnusedVariableAssignRector::class,
        Rector\DeadCode\Rector\ClassConst\RemoveUnusedClassConstantRector::class,
        Rector\DeadCode\Rector\ClassMethod\RemoveUnusedParameterRector::class,
        Rector\Naming\Rector\Property\UnderscoreToCamelCasePropertyNameRector::class,
        Rector\Naming\Rector\Variable\UnderscoreToCamelCaseVariableNameRector::class,
        Rector\Php56\Rector\FuncCall\PowToExpRector::class,
        Rector\Php70\Rector\FuncCall\NonVariableToVariableOnFunctionCallRector::class,
        Rector\Php70\Rector\MethodCall\ThisCallOnStaticMethodToStaticCallRector::class,
        Rector\Php70\Rector\StaticCall\StaticCallOnNonStaticToInstanceCallRector::class,
        Rector\Php71\Rector\FuncCall\CountOnNullRector::class,
        Rector\Php73\Rector\FuncCall\JsonThrowOnErrorRector::class,
        Rector\TypeDeclaration\Rector\FunctionLike\ParamTypeDeclarationRector::class,
        // PHP 5.6 incompatible
        Rector\CodeQuality\Rector\Ternary\ArrayKeyExistsTernaryThenValueToCoalescingRector::class, // PHP 7
        Rector\Php70\Rector\If_\IfToSpaceshipRector::class,
        Rector\Php70\Rector\Ternary\TernaryToSpaceshipRector::class,
        Rector\Php71\Rector\BinaryOp\IsIterableRector::class,
        Rector\Php71\Rector\List_\ListToArrayDestructRector::class,
        Rector\Php71\Rector\TryCatch\MultiExceptionCatchRector::class,
        Rector\Php73\Rector\FuncCall\ArrayKeyFirstLastRector::class,
        Rector\Php73\Rector\BinaryOp\IsCountableRector::class,
    ));

    $parameters->set(Option::SETS, array(
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
        SetList::DEAD_CODE,
        SetList::LARAVEL_50,
        SetList::LARAVEL_51,
        SetList::LARAVEL_52,
        SetList::LARAVEL_53,
        SetList::LARAVEL_54,
        SetList::PHP_56,
        SetList::PHP_70,
        SetList::PHP_71,
        SetList::PHP_72,
        SetList::PHP_73,
        SetList::PHP_74,
    ));

    $services = $containerConfigurator->services();

    $services->set(TernaryToElvisRector::class);
};
