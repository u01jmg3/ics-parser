<?php

declare(strict_types=1);

use Rector\Php53\Rector\Ternary\TernaryToElvisRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

// rector process app

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('import_short_classes', false);

    $parameters->set('php_version_features', '5.6');

    $parameters->set('autoload_paths', array(__DIR__ . '/vendor\autoload.php'));

    $parameters->set('exclude_rectors', array(
        'Rector\CodeQuality\Rector\Class_\CompleteDynamicPropertiesRector',
        'Rector\CodeQuality\Rector\Concat\JoinStringConcatRector',
        'Rector\CodeQuality\Rector\FuncCall\ChangeArrayPushToArrayAssignRector',
        'Rector\CodeQuality\Rector\FuncCall\CompactToVariablesRector',
        'Rector\CodeQuality\Rector\FuncCall\IntvalToTypeCastRector',
        'Rector\CodeQuality\Rector\Identical\SimplifyBoolIdenticalTrueRector',
        'Rector\CodeQuality\Rector\If_\CombineIfRector',
        'Rector\CodeQuality\Rector\If_\ExplicitBoolCompareRector',
        'Rector\CodeQuality\Rector\If_\SimplifyIfElseToTernaryRector',
        'Rector\CodeQuality\Rector\If_\SimplifyIfReturnBoolRector',
        'Rector\CodeQuality\Rector\Return_\SimplifyUselessVariableRector',
        'Rector\CodingStyle\Rector\ClassConst\VarConstantCommentRector',
        'Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector',
        'Rector\CodingStyle\Rector\FuncCall\ConsistentPregDelimiterRector',
        'Rector\CodingStyle\Rector\Function_\CamelCaseFunctionNamingToUnderscoreRector',
        'Rector\CodingStyle\Rector\Identical\IdenticalFalseToBooleanNotRector',
        'Rector\CodingStyle\Rector\Property\UnderscoreToPascalCasePropertyNameRector',
        'Rector\CodingStyle\Rector\String_\SymplifyQuoteEscapeRector',
        'Rector\CodingStyle\Rector\Variable\UnderscoreToPascalCaseVariableNameRector',
        'Rector\DeadCode\Rector\Assign\RemoveUnusedVariableAssignRector',
        'Rector\DeadCode\Rector\ClassConst\RemoveUnusedClassConstantRector',
        'Rector\DeadCode\Rector\ClassMethod\RemoveUnusedParameterRector',
        'Rector\Php56\Rector\FuncCall\PowToExpRector',
        'Rector\Php70\Rector\FuncCall\NonVariableToVariableOnFunctionCallRector',
        'Rector\Php70\Rector\MethodCall\ThisCallOnStaticMethodToStaticCallRector',
        'Rector\Php70\Rector\StaticCall\StaticCallOnNonStaticToInstanceCallRector',
        'Rector\Php71\Rector\FuncCall\CountOnNullRector',
        'Rector\TypeDeclaration\Rector\FunctionLike\ParamTypeDeclarationRector',
        // PHP 5.6 incompatible
        'Rector\CodeQuality\Rector\Ternary\ArrayKeyExistsTernaryThenValueToCoalescingRector', // PHP 7
        'Rector\Php70\Rector\If_\IfToSpaceshipRector',
        'Rector\Php70\Rector\Ternary\TernaryToSpaceshipRector',
        'Rector\Php71\Rector\BinaryOp\IsIterableRector',
        'Rector\Php71\Rector\List_\ListToArrayDestructRector',
        'Rector\Php71\Rector\TryCatch\MultiExceptionCatchRector',
        'Rector\Php73\Rector\FuncCall\ArrayKeyFirstLastRector',
        'Rector\Php73\Rector\BinaryOp\IsCountableRector',
        'Rector\Php73\Rector\FuncCall\JsonThrowOnErrorRector',
    ));

    $parameters->set('sets', array(
        'code-quality',
        'coding-style',
        'dead-code',
        'laravel50',
        'laravel51',
        'laravel52',
        'laravel53',
        'laravel54',
        'php56',
        'php70',
        'php71',
        'php72',
        'php73',
        'php74',
    ));

    $services = $containerConfigurator->services();

    $services->set(TernaryToElvisRector::class);
};
