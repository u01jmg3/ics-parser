<?php

declare(strict_types=1);

use PHP_CodeSniffer\Standards\Generic\Sniffs\Classes\SelfMemberReferenceSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting\SpaceAfterNotSniff;
use PhpCsFixer\Fixer\Alias\NoAliasFunctionsFixer;
use PhpCsFixer\Fixer\Alias\NoMixedEchoPrintFixer;
use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\ArrayNotation\NoMultilineWhitespaceAroundDoubleArrowFixer;
use PhpCsFixer\Fixer\ArrayNotation\NormalizeIndexBraceFixer;
use PhpCsFixer\Fixer\ArrayNotation\NoTrailingCommaInSinglelineArrayFixer;
use PhpCsFixer\Fixer\ArrayNotation\TrailingCommaInMultilineArrayFixer;
use PhpCsFixer\Fixer\ArrayNotation\TrimArraySpacesFixer;
use PhpCsFixer\Fixer\Basic\EncodingFixer;
use PhpCsFixer\Fixer\Basic\Psr4Fixer;
use PhpCsFixer\Fixer\Casing\LowercaseConstantsFixer;
use PhpCsFixer\Fixer\Casing\LowercaseKeywordsFixer;
use PhpCsFixer\Fixer\Casing\LowercaseStaticReferenceFixer;
use PhpCsFixer\Fixer\Casing\MagicConstantCasingFixer;
use PhpCsFixer\Fixer\Casing\MagicMethodCasingFixer;
use PhpCsFixer\Fixer\Casing\NativeFunctionCasingFixer;
use PhpCsFixer\Fixer\Casing\NativeFunctionTypeDeclarationCasingFixer;
use PhpCsFixer\Fixer\CastNotation\CastSpacesFixer;
use PhpCsFixer\Fixer\CastNotation\NoShortBoolCastFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer;
use PhpCsFixer\Fixer\ClassNotation\MethodSeparationFixer;
use PhpCsFixer\Fixer\ClassNotation\SingleClassElementPerStatementFixer;
use PhpCsFixer\Fixer\Comment\NoTrailingWhitespaceInCommentFixer;
use PhpCsFixer\Fixer\Comment\SingleLineCommentStyleFixer;
use PhpCsFixer\Fixer\ControlStructure\ElseifFixer;
use PhpCsFixer\Fixer\ControlStructure\IncludeFixer;
use PhpCsFixer\Fixer\ControlStructure\NoTrailingCommaInListCallFixer;
use PhpCsFixer\Fixer\ControlStructure\NoUnneededControlParenthesesFixer;
use PhpCsFixer\Fixer\ControlStructure\NoUnneededCurlyBraces;
use PhpCsFixer\Fixer\ControlStructure\SwitchCaseSemicolonToColonFixer;
use PhpCsFixer\Fixer\ControlStructure\SwitchCaseSpaceFixer;
use PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer;
use PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationIndentationFixer;
use PhpCsFixer\Fixer\FunctionNotation\FunctionDeclarationFixer;
use PhpCsFixer\Fixer\FunctionNotation\FunctionTypehintSpaceFixer;
use PhpCsFixer\Fixer\FunctionNotation\MethodArgumentSpaceFixer;
use PhpCsFixer\Fixer\FunctionNotation\NoSpacesAfterFunctionNameFixer;
use PhpCsFixer\Fixer\FunctionNotation\NoUnreachableDefaultArgumentValueFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Import\SingleImportPerStatementFixer;
use PhpCsFixer\Fixer\Import\SingleLineAfterImportsFixer;
use PhpCsFixer\Fixer\ListNotation\ListSyntaxFixer;
use PhpCsFixer\Fixer\NamespaceNotation\NoLeadingNamespaceWhitespaceFixer;
use PhpCsFixer\Fixer\NamespaceNotation\SingleBlankLineBeforeNamespaceFixer;
use PhpCsFixer\Fixer\Operator\AlignEqualsFixerHelper;
use PhpCsFixer\Fixer\Operator\ObjectOperatorWithoutWhitespaceFixer;
use PhpCsFixer\Fixer\Operator\StandardizeNotEqualsFixer;
use PhpCsFixer\Fixer\Phpdoc\NoEmptyPhpdocFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocIndentFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocInlineTagFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocNoAccessFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocNoPackageFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocSingleLineVarSpacingFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocToCommentFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTrimFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTypesFixer;
use PhpCsFixer\Fixer\PhpdocNoUselessInheritdocFixer;
use PhpCsFixer\Fixer\PhpTag\FullOpeningTagFixer;
use PhpCsFixer\Fixer\PhpTag\NoClosingTagFixer;
use PhpCsFixer\Fixer\ReturnNotation\NoUselessReturnFixer;
use PhpCsFixer\Fixer\Semicolon\NoEmptyStatementFixer;
use PhpCsFixer\Fixer\Semicolon\NoMultilineWhitespaceBeforeSemicolonsFixer;
use PhpCsFixer\Fixer\Semicolon\SpaceAfterSemicolonFixer;
use PhpCsFixer\Fixer\StringNotation\HeredocToNowdocFixer;
use PhpCsFixer\Fixer\StringNotation\SingleQuoteFixer;
use PhpCsFixer\Fixer\Whitespace\BlankLineBeforeStatementFixer;
use PhpCsFixer\Fixer\Whitespace\CompactNullableTypehint;
use PhpCsFixer\Fixer\Whitespace\LineEndingFixer;
use PhpCsFixer\Fixer\Whitespace\NoExtraConsecutiveBlankLinesFixer;
use PhpCsFixer\Fixer\Whitespace\NoSpacesInsideParenthesisFixer;
use PhpCsFixer\Fixer\Whitespace\NoWhitespaceInBlankLineFixer;
use PhpCsFixer\Fixer\Whitespace\SingleBlankLineAtEofFixer;
use SlevomatCodingStandard\ControlStructures\AssignmentInConditionSniff;
use SlevomatCodingStandard\Namespaces\AlphabeticallySortedUsesSniff;
use SlevomatCodingStandard\Namespaces\UnusedUsesSniff;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;

// ecs check --fix .

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::LINE_ENDING, "\n");

    // https://github.com/symplify/easy-coding-standard/blob/master/config/set/psr12.php
    $parameters->set(Option::SETS, array('psr12'));

    $parameters->set(Option::SKIP, array(
        'PhpCsFixer\Fixer\Basic\BracesFixer'                           => null,
        'PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer'          => null,
        'PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer' => null,
        'PhpCsFixer\Fixer\Operator\PreIncrementFixer'                  => null,
        'PhpCsFixer\Fixer\Phpdoc\PhpdocScalarFixer'                    => null,
        'PhpCsFixer\Fixer\Phpdoc\PhpdocSummaryFixer'                   => null,
        'PhpCsFixer\Fixer\Phpdoc\PhpdocVarAnnotationCorrectOrderFixer' => null,
        'PhpCsFixer\Fixer\Phpdoc\PhpdocVarWithoutNameFixer'            => null,
        'PhpCsFixer\Fixer\ReturnNotation\SimplifiedNullReturnFixer'    => null,
        // Requires PHP 7.1 and above
        'PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer' => null,
    ));

    $services = $containerConfigurator->services();

    $services->set(SpaceAfterNotSniff::class)
        ->property('spacing', 0);

    $services->set(ArraySyntaxFixer::class)
        ->call('configure', array(array('syntax' => 'long')));

    $services->set(YodaStyleFixer::class)
        ->call('configure', array(array('equal' => false, 'identical' => false, 'less_and_greater' => false)));

    $services->set(BlankLineBeforeStatementFixer::class)
        ->call('configure', array(array('statements' => array('continue', 'declare', 'return', 'throw', 'try'))));

    $services->set(AssignmentInConditionSniff::class);

    $services->set(AlphabeticallySortedUsesSniff::class);

    $services->set(UnusedUsesSniff::class);

    $services->set(SelfMemberReferenceSniff::class);

    $services->set(NoAliasFunctionsFixer::class);

    $services->set(NoMixedEchoPrintFixer::class);

    $services->set(NoMultilineWhitespaceAroundDoubleArrowFixer::class);

    $services->set(NoTrailingCommaInSinglelineArrayFixer::class);

    $services->set(NormalizeIndexBraceFixer::class);

    $services->set(TrailingCommaInMultilineArrayFixer::class);

    $services->set(TrimArraySpacesFixer::class);

    $services->set(EncodingFixer::class);

    $services->set(Psr4Fixer::class);

    $services->set(LowercaseConstantsFixer::class);

    $services->set(LowercaseKeywordsFixer::class);

    $services->set(LowercaseStaticReferenceFixer::class);

    $services->set(MagicConstantCasingFixer::class);

    $services->set(MagicMethodCasingFixer::class);

    $services->set(NativeFunctionCasingFixer::class);

    $services->set(NativeFunctionTypeDeclarationCasingFixer::class);

    $services->set(CastSpacesFixer::class);

    $services->set(NoShortBoolCastFixer::class);

    $services->set(ClassDefinitionFixer::class);

    $services->set(MethodSeparationFixer::class);

    $services->set(SingleClassElementPerStatementFixer::class);

    $services->set(NoTrailingWhitespaceInCommentFixer::class);

    $services->set(SingleLineCommentStyleFixer::class);

    $services->set(ElseifFixer::class);

    $services->set(IncludeFixer::class);

    $services->set(NoTrailingCommaInListCallFixer::class);

    $services->set(NoUnneededControlParenthesesFixer::class);

    $services->set(NoUnneededCurlyBraces::class);

    $services->set(SwitchCaseSemicolonToColonFixer::class);

    $services->set(SwitchCaseSpaceFixer::class);

    $services->set(DoctrineAnnotationIndentationFixer::class);

    $services->set(FunctionDeclarationFixer::class);

    $services->set(FunctionTypehintSpaceFixer::class);

    $services->set(MethodArgumentSpaceFixer::class);

    $services->set(NoSpacesAfterFunctionNameFixer::class);

    $services->set(NoUnreachableDefaultArgumentValueFixer::class);

    $services->set(NoUnusedImportsFixer::class);

    $services->set(SingleImportPerStatementFixer::class);

    $services->set(SingleLineAfterImportsFixer::class);

    $services->set(ListSyntaxFixer::class);

    $services->set(NoLeadingNamespaceWhitespaceFixer::class);

    $services->set(SingleBlankLineBeforeNamespaceFixer::class);

    $services->set(AlignEqualsFixerHelper::class);

    $services->set(ObjectOperatorWithoutWhitespaceFixer::class);

    $services->set(StandardizeNotEqualsFixer::class);

    $services->set(FullOpeningTagFixer::class);

    $services->set(NoClosingTagFixer::class);

    $services->set(PhpdocNoUselessInheritdocFixer::class);

    $services->set(NoEmptyPhpdocFixer::class);

    $services->set(PhpdocIndentFixer::class);

    $services->set(PhpdocInlineTagFixer::class);

    $services->set(PhpdocNoAccessFixer::class);

    $services->set(PhpdocNoPackageFixer::class);

    $services->set(PhpdocSingleLineVarSpacingFixer::class);

    $services->set(PhpdocToCommentFixer::class);

    $services->set(PhpdocTrimFixer::class);

    $services->set(PhpdocTypesFixer::class);

    $services->set(NoUselessReturnFixer::class);

    $services->set(NoEmptyStatementFixer::class);

    $services->set(NoMultilineWhitespaceBeforeSemicolonsFixer::class);

    $services->set(SpaceAfterSemicolonFixer::class);

    $services->set(HeredocToNowdocFixer::class);

    $services->set(SingleQuoteFixer::class);

    $services->set(CompactNullableTypehint::class);

    $services->set(LineEndingFixer::class);

    $services->set(NoExtraConsecutiveBlankLinesFixer::class);

    $services->set(NoSpacesInsideParenthesisFixer::class);

    $services->set(NoWhitespaceInBlankLineFixer::class);

    $services->set(SingleBlankLineAtEofFixer::class);
};
