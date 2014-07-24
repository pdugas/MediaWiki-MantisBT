<?php
# =============================================================================
# MantisBT - MediaWiki Extension for Integration with MantisBT
# =============================================================================
# @file     MantisBT.php
# @brief    Setup for the extension
# @author   Paul Dugas <paul@dugas.cc>
# =============================================================================
 
if (!defined('MEDIAWIKI')) {
    echo("This is an extension to the MediaWiki package and ".
         "cannot be run standalone.\n");
    die(-1);
}

$wgMantisBtURL = '//mantis';  // (without trailing slash please)
$wgMantisBtUser = NULL;
$wgMantisBtPass = NULL;

$wgExtensionCredits['parserhook'][] = array(
    'path'          => __FILE__,
    'name'          => 'MantisBT',
    'author'        => array('[mailto:paul@dugas.cc Paul Dugas]'),
    'url'           => 'https://github.com/pdugas/MediaWiki-MantisBT',
    'description'   => 'Adds <nowiki><mantis/> tag and {{#mantis}}</nowiki> '.
                       ' parser function for integration with MantisBT.',
    'version'       => 0.1,
    'license-name'  => 'GPLv2',
);

$wgHooks['ParserFirstCallInit'][] = 'MantisBtParserInit';
$wgExtensionMessagesFiles['MantisBt'] = __DIR__ . '/MantisBT.i18n.php';


function MantisBtParserInit(Parser $parser)
{
    $parser->setHook('mantis', 'MantisBtTag');
    $parser->setHook('issue', 'MantisBtTag');
    $parser->setFunctionHook('mantis', 'MantisBtFunc');
    $parser->setFunctionHook('issue', 'MantisBtFunc');
    return true;
}

function MantisBtTag($input, array $args, Parser $parser, PPFrame $frame)
{
  $id = $args['id'];
  if (!is_numeric($id)) { $id = $input; }
  if (!is_numeric($id)) { return ''; }
  return $parser->recursiveTagParse(MantisBtFunc($parser, $id, $input));
}

function MantisBtFunc($parser, $id = '', $text = '')
{
  global $wgMantisBtURL;
  global $wgMantisBtUser;
  global $wgMantisBtPass;

  $parser->disableCache();

  if (!is_numeric($id)) { return ''; }
  $id = intval($id);

  $url = sprintf('%s/view.php?id=%d', $wgMantisBtURL, $id);

  $issue = null;
  if (!is_null($wgMantisBtUser) && !is_null($wgMantisBtPass)) {
    try {
      $soap = new SoapClient($wgMantisBtURL.'/api/soap/mantisconnect.php?wsdl');
      $issue = $soap->mc_issue_get($wgMantisBtUser, $wgMantisBtPass, $id);
    } catch (Exception $e) { /* ignored */ }
  }
  
  if (empty($text)) {
    $ret = sprintf('[%s Issue-%d "%s" (%s)]', $url, $id, 
                   $issue->summary, $issue->status->name);
    if ($issue->status->name == 'resolved' || 
        $issue->status->name == 'closed') {
      $ret = "<strike>$ret</strike>";
    }
  } else {
    $ret = "[$url $text]";
  }  
  return $ret;
}

# =============================================================================
# vim: set et sw=2 ts=2 :
