<?php
/**
 * A test to ensure that variables conform to the naming coding standard.
 */

namespace PHP_CodeSniffer\Standards\Custom\Sniffs\NamingConventions;

use PHP_CodeSniffer\Sniffs\AbstractVariableSniff;
use PHP_CodeSniffer\Files\File;

/**
 * ValidVariableNameSniff
 *
 * Checks the naming of member variables.
 */
class ValidVariableNameSniff extends AbstractVariableSniff
{
    /**
     * Processes class member variables.
     *
     * @param \PHP_CodeSniffer\Files\File   $phpcsFile  The file being scanned.
     * @param int                           $stackPtr   The position of the
     *                                                  current token in the
     *                                                  stack passed in $tokens.
     *
     * @return void
     */
    protected function processMemberVar(File $phpcsFile, $stackPtr)
    {
        $this->checkCamelCase($phpcsFile, $stackPtr, true);
    }//end processMemberVar()

    /**
     * Processes normal variables.
     *
     * @param \PHP_CodeSniffer\Files\File   $phpcsFile  The file where this
     *                                                  token was found.
     * @param int                           $stackPtr   The position where the
     *                                                  token was found.
     *
     * @return void
     */
    protected function processVariable(File $phpcsFile, $stackPtr)
    {
        $this->checkCamelCase($phpcsFile, $stackPtr);
    }//end processVariable()

    /**
     * Processes variables in double quoted strings.
     *
     * @param \PHP_CodeSniffer\Files\File   $phpcsFile  The file where this
     *                                                  token was found.
     * @param int                           $stackPtr   The position where the
     *                                                  token was found.
     *
     * @return void
     */
    protected function processVariableInString(File $phpcsFile, $stackPtr)
    {
        $this->checkCamelCase($phpcsFile, $stackPtr);
    }//end processVariableInString()

    /**
     * Check if variable name is snake_case
     *
     * @param \PHP_CodeSniffer\Files\File   $phpcsFile              The file
     *                                                              being
     *                                                              scanned.
     * @param int                           $stackPtr               The position
     *                                                              of the
     *                                                              current
     *                                                              token in the
     *                                                              stack passed
     *                                                              in $tokens.
     * @param boolean                       $isProperty (optional)  Whether the
     *                                                              variable is
     *                                                              a class
     *                                                              property or
     *                                                              not.
     *
     * @return void
     */
    private function checkSnakeCase(File $phpcsFile, $stackPtr, $isProperty = false)
    {
        $tokens = $phpcsFile->getTokens();

        $variableName = ltrim($tokens[$stackPtr]['content'], '$');
        $variableType = 'Variable';

        if ($isProperty) {
            $variableType = 'Property';
        }

        $phpSuperglobals = [
            '_SERVER',
            '_GET',
            '_POST',
            '_FILES',
            '_COOKIE',
            '_SESSION',
            '_REQUEST',
            '_ENV',
        ];

        $excludedVariables = array_merge(
            $phpSuperglobals,
            [
                'CI',
            ]
        );

        // Variable name must follow snake_case naming convention
        if (!in_array($variableName, $excludedVariables) &&
            !preg_match(
                '/\b^[a-z][a-z0-9]*(?:[a-z0-9]*|(?:_[a-z0-9]+)*)\b/',
                $variableName
            )
        ) {
            $error = $variableType . ' "%s" must follow snake_case naming convention';
            $data  = [
                $variableName,
            ];
            $phpcsFile->addError($error, $stackPtr, 'SnakeCaseVariable', $data);
        }
    }//end checkSnakeCase()

    /**
     * Check if variable name is camelCase
     *
     * @param \PHP_CodeSniffer\Files\File   $phpcsFile              The file
     *                                                              being
     *                                                              scanned.
     * @param int                           $stackPtr               The position
     *                                                              of the
     *                                                              current
     *                                                              token in the
     *                                                              stack passed
     *                                                              in $tokens.
     * @param boolean                       $isProperty (optional)  Whether the
     *                                                              variable is
     *                                                              a class
     *                                                              property or
     *                                                              not.
     *
     * @return void
     */
    private function checkCamelCase(File $phpcsFile, $stackPtr, $isProperty = false)
    {
        $tokens = $phpcsFile->getTokens();

        $variableName = ltrim($tokens[$stackPtr]['content'], '$');
        $variableType = 'Variable';

        if ($isProperty) {
            $variableType = 'Property';
        }

        $phpSuperglobals = [
            '_SERVER',
            '_GET',
            '_POST',
            '_FILES',
            '_COOKIE',
            '_SESSION',
            '_REQUEST',
            '_ENV',
        ];

        $excludedVariables = array_merge(
            $phpSuperglobals,
            [
                'CI',
            ]
        );

        // Variable name must follow snake_case naming convention
        if (!in_array($variableName, $excludedVariables) &&
            !preg_match(
                '/\b^[a-z][a-zA-Z0-9]*\b/',
                $variableName
            )
        ) {
            $error = $variableType . ' "%s" must follow camelCase naming convention';
            $data  = [
                $variableName,
            ];
            $phpcsFile->addError($error, $stackPtr, 'CamelCaseVariable', $data);
        }
    }//end checkCamelCase()
}//end class
