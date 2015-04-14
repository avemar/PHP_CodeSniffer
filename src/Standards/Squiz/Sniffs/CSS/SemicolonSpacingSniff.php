<?php

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\CSS;

use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Squiz_Sniffs_CSS_SemicolonSpacingSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Squiz_Sniffs_CSS_SemicolonSpacingSniff.
 *
 * Ensure each style definition has a semi-colon and it is spaced correctly.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class SemicolonSpacingSniff implements Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array('CSS');


    /**
     * Returns the token types that this sniff is interested in.
     *
     * @return int[]
     */
    public function register()
    {
        return array(T_STYLE);

    }//end register()


    /**
     * Processes the tokens that this sniff is interested in.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
     * @param int                  $stackPtr  The position in the stack where
     *                                        the token was found.
     *
     * @return void
     */
    public function process($phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $semicolon = $phpcsFile->findNext(T_SEMICOLON, ($stackPtr + 1));
        if ($semicolon === false || $tokens[$semicolon]['line'] !== $tokens[$stackPtr]['line']) {
            $error = 'Style definitions must end with a semicolon';
            $phpcsFile->addError($error, $stackPtr, 'NotAtEnd');
            return;
        }

        if ($tokens[($semicolon - 1)]['code'] === T_WHITESPACE) {
            $length = strlen($tokens[($semicolon - 1)]['content']);
            $error  = 'Expected 0 spaces before semicolon in style definition; %s found';
            $data   = array($length);
            $fix    = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceFound', $data);
            if ($fix === true) {
                $phpcsFile->fixer->replaceToken(($semicolon - 1), '');
            }
        }

    }//end process()


}//end class