<?php

declare(strict_types=1);

use PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting\SpaceAfterNotSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Classes\SelfMemberReferenceSniff;
use PhpCsFixer\Fixer\Alias\NoAliasFunctionsFixer;
use PhpCsFixer\Fixer\Alias\NoMixedEchoPrintFixer;
use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\ArrayNotation\NoMultilineWhitespaceAroundDoubleArrowFixer;
use PhpCsFixer\Fixer\ArrayNotation\NormalizeIndexBraceFixer;
use PhpCsFixer\Fixer\ArrayNotation\TrimArraySpacesFixer;
use PhpCsFixer\Fixer\Basic\EncodingFixer;
use PhpCsFixer\Fixer\Basic\NoTrailingCommaInSinglelineFixer;
use PhpCsFixer\Fixer\Casing\ConstantCaseFixer;
use PhpCsFixer\Fixer\Casing\LowercaseKeywordsFixer;
use PhpCsFixer\Fixer\Casing\LowercaseStaticReferenceFixer;
use PhpCsFixer\Fixer\Casing\MagicConstantCasingFixer;
use PhpCsFixer\Fixer\Casing\MagicMethodCasingFixer;
use PhpCsFixer\Fixer\Casing\NativeFunctionCasingFixer;
use PhpCsFixer\Fixer\Casing\NativeFunctionTypeDeclarationCasingFixer;
use PhpCsFixer\Fixer\CastNotation\CastSpacesFixer;
use PhpCsFixer\Fixer\CastNotation\NoShortBoolCastFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer;
use PhpCsFixer\Fixer\ClassNotation\SingleClassElementPerStatementFixer;
use PhpCsFixer\Fixer\Comment\NoTrailingWhitespaceInCommentFixer;
use PhpCsFixer\Fixer\Comment\SingleLineCommentStyleFixer;
use PhpCsFixer\Fixer\ControlStructure\ElseifFixer;
use PhpCsFixer\Fixer\ControlStructure\IncludeFixer;
use PhpCsFixer\Fixer\ControlStructure\NoUnneededControlParenthesesFixer;
use PhpCsFixer\Fixer\ControlStructure\NoUnneededCurlyBracesFixer;
use PhpCsFixer\Fixer\ControlStructure\SwitchCaseSemicolonToColonFixer;
use PhpCsFixer\Fixer\ControlStructure\SwitchCaseSpaceFixer;
use PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer;
use PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer;
use PhpCsFixer\Fixer\FunctionNotation\FunctionDeclarationFixer;
use PhpCsFixer\Fixer\FunctionNotation\LambdaNotUsedImportFixer;
use PhpCsFixer\Fixer\FunctionNotation\MethodArgumentSpaceFixer;
use PhpCsFixer\Fixer\FunctionNotation\NoSpacesAfterFunctionNameFixer;
use PhpCsFixer\Fixer\FunctionNotation\NoUnreachableDefaultArgumentValueFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Import\SingleImportPerStatementFixer;
use PhpCsFixer\Fixer\Import\SingleLineAfterImportsFixer;
use PhpCsFixer\Fixer\ListNotation\ListSyntaxFixer;
use PhpCsFixer\Fixer\NamespaceNotation\BlankLinesBeforeNamespaceFixer;
use PhpCsFixer\Fixer\NamespaceNotation\NoLeadingNamespaceWhitespaceFixer;
use PhpCsFixer\Fixer\Operator\ObjectOperatorWithoutWhitespaceFixer;
use PhpCsFixer\Fixer\Operator\StandardizeNotEqualsFixer;
use PhpCsFixer\Fixer\Phpdoc\NoEmptyPhpdocFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocIndentFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocInlineTagNormalizerFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocNoAccessFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocNoPackageFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocNoUselessInheritdocFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocParamOrderFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocSingleLineVarSpacingFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocToCommentFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTrimFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTypesFixer;
use PhpCsFixer\Fixer\PhpTag\FullOpeningTagFixer;
use PhpCsFixer\Fixer\PhpTag\NoClosingTagFixer;
use PhpCsFixer\Fixer\ReturnNotation\NoUselessReturnFixer;
use PhpCsFixer\Fixer\Semicolon\MultilineWhitespaceBeforeSemicolonsFixer;
use PhpCsFixer\Fixer\Semicolon\NoEmptyStatementFixer;
use PhpCsFixer\Fixer\Semicolon\SpaceAfterSemicolonFixer;
use PhpCsFixer\Fixer\StringNotation\HeredocToNowdocFixer;
use PhpCsFixer\Fixer\StringNotation\SingleQuoteFixer;
use PhpCsFixer\Fixer\Whitespace\BlankLineBeforeStatementFixer;
use PhpCsFixer\Fixer\Whitespace\CompactNullableTypehintFixer;
use PhpCsFixer\Fixer\Whitespace\LineEndingFixer;
use PhpCsFixer\Fixer\Whitespace\NoExtraBlankLinesFixer;
use PhpCsFixer\Fixer\Whitespace\NoSpacesInsideParenthesisFixer;
use PhpCsFixer\Fixer\Whitespace\NoWhitespaceInBlankLineFixer;
use PhpCsFixer\Fixer\Whitespace\SingleBlankLineAtEofFixer;
use PhpCsFixer\Fixer\Whitespace\TypeDeclarationSpacesFixer;
use SlevomatCodingStandard\Sniffs\Namespaces\AlphabeticallySortedUsesSniff;
use SlevomatCodingStandard\Sniffs\Variables\UnusedVariableSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

// ecs check --fix .

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->disableParallel();

    // https://github.com/easy-coding-standard/easy-coding-standard/blob/main/config/set/psr12.php
    $ecsConfig->import(SetList::PSR_12);

    $ecsConfig->lineEnding("\n");

    $ecsConfig->skip(array(
        // Fixers
        'PhpCsFixer\Fixer\Whitespace\StatementIndentationFixer'        => array('examples/index.php'),
        'PhpCsFixer\Fixer\Basic\BracesFixer'                           => null,
        'PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer'          => null,
        'PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer' => null,
        'PhpCsFixer\Fixer\Phpdoc\PhpdocScalarFixer'                    => null,
        'PhpCsFixer\Fixer\Phpdoc\PhpdocSummaryFixer'                   => null,
        'PhpCsFixer\Fixer\Phpdoc\PhpdocVarWithoutNameFixer'            => null,
        'PhpCsFixer\Fixer\ReturnNotation\SimplifiedNullReturnFixer'    => null,
        // Requires PHP 7.1 and above
        'PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer' => null,
    ));

    $ecsConfig->ruleWithConfiguration(SpaceAfterNotSniff::class, array('spacing' => 0));

    $ecsConfig->ruleWithConfiguration(ArraySyntaxFixer::class, array('syntax' => 'long'));

    $ecsConfig->ruleWithConfiguration(
        YodaStyleFixer::class,
        array(
            'equal'            => false,
            'identical'        => false,
            'less_and_greater' => false,
        )
    );

    $ecsConfig->ruleWithConfiguration(ListSyntaxFixer::class, array('syntax' => 'long')); // PHP 5.6

    $ecsConfig->ruleWithConfiguration(
        BlankLineBeforeStatementFixer::class,
        array(
            'statements' => array(
                'continue',
                'declare',
                'return',
                'throw',
                'try',
            ),
        )
    );

    $ecsConfig->rules(
        array(
            AlphabeticallySortedUsesSniff::class,
            UnusedVariableSniff::class,
            SelfMemberReferenceSniff::class,
            BlankLinesBeforeNamespaceFixer::class,
            CastSpacesFixer::class,
            ClassDefinitionFixer::class,
            CompactNullableTypehintFixer::class,
            ConstantCaseFixer::class,
            ElseifFixer::class,
            EncodingFixer::class,
            FullOpeningTagFixer::class,
            FunctionDeclarationFixer::class,
            HeredocToNowdocFixer::class,
            IncludeFixer::class,
            LambdaNotUsedImportFixer::class,
            LineEndingFixer::class,
            LowercaseKeywordsFixer::class,
            LowercaseStaticReferenceFixer::class,
            MagicConstantCasingFixer::class,
            MagicMethodCasingFixer::class,
            MethodArgumentSpaceFixer::class,
            MultilineWhitespaceBeforeSemicolonsFixer::class,
            NativeFunctionCasingFixer::class,
            NativeFunctionTypeDeclarationCasingFixer::class,
            NoAliasFunctionsFixer::class,
            NoClosingTagFixer::class,
            NoEmptyPhpdocFixer::class,
            NoEmptyStatementFixer::class,
            NoExtraBlankLinesFixer::class,
            NoLeadingNamespaceWhitespaceFixer::class,
            NoMixedEchoPrintFixer::class,
            NoMultilineWhitespaceAroundDoubleArrowFixer::class,
            NoShortBoolCastFixer::class,
            NoSpacesAfterFunctionNameFixer::class,
            NoSpacesInsideParenthesisFixer::class,
            NoTrailingCommaInSinglelineFixer::class,
            NoTrailingWhitespaceInCommentFixer::class,
            NoUnneededControlParenthesesFixer::class,
            NoUnneededCurlyBracesFixer::class,
            NoUnreachableDefaultArgumentValueFixer::class,
            NoUnusedImportsFixer::class,
            NoUselessReturnFixer::class,
            NoWhitespaceInBlankLineFixer::class,
            NormalizeIndexBraceFixer::class,
            ObjectOperatorWithoutWhitespaceFixer::class,
            PhpdocIndentFixer::class,
            PhpdocInlineTagNormalizerFixer::class,
            PhpdocNoAccessFixer::class,
            PhpdocNoPackageFixer::class,
            PhpdocNoUselessInheritdocFixer::class,
            PhpdocParamOrderFixer::class,
            PhpdocSingleLineVarSpacingFixer::class,
            PhpdocToCommentFixer::class,
            PhpdocTrimFixer::class,
            PhpdocTypesFixer::class,
            SingleBlankLineAtEofFixer::class,
            SingleClassElementPerStatementFixer::class,
            SingleImportPerStatementFixer::class,
            SingleLineAfterImportsFixer::class,
            SingleLineCommentStyleFixer::class,
            SingleQuoteFixer::class,
            SpaceAfterSemicolonFixer::class,
            StandardizeNotEqualsFixer::class,
            SwitchCaseSemicolonToColonFixer::class,
            SwitchCaseSpaceFixer::class,
            TrailingCommaInMultilineFixer::class,
            TrimArraySpacesFixer::class,
            TypeDeclarationSpacesFixer::class,
        )
    );
};
