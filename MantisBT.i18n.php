<?php
# =============================================================================
# MantisBT - MediaWiki Extension for Integration with MantisBT
# =============================================================================
# @file     MantisBT.i18n.php
# @brief    Internationalization for the extension
# @author   Paul Dugas <paul@dugas.cc>
# =============================================================================
 
if (!defined('MEDIAWIKI')) {
    echo("This is an extension to the MediaWiki package and ".
         "cannot be run standalone.\n");
    die(-1);
}

$magicWords = array();

$magicWords['en'] = array(
        'mantis' => array( 0, 'mantis' ),
        'issue' => array( 0, 'issue' ),
);

# =============================================================================
# vim: set et sw=4 ts=4 :
