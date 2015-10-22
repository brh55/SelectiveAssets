<?php
/** 
 *
 * Selective Assets: Freeware
 *
 * A simple snippet that let's you selectively choose when to load particular JS and CSS files
 * Licenses under GPLv2, modifiying this code must have the GPLv2 or later licenses attached.
 * All is welcomed to contribute to this plugin at http://github.com/brh55/SelectiveAssets, please submit any issues or bugs on there.
 *
 *
 * 
 * @ SelectiveAssets by Brandon Him (Twitter: @himbrandon)/(MODX: brandonhim)
 * @ version 1.0.0
 * 
 * SelectiveAssets is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY.
 * For more information regarding the licenses attached, please check out: www.gnu.org/licenses/gpl-2.0.html
 * 
 * Parameters:
 *
 * Required:
 * $filePath = specify the path of the stylesheet or js file.
 * 
 * Optional:
 * &runLocation = (default is head), user can specify ending of body tag
 * &preComment = String of Desired Comment to be placed before file is added
 * &noComment = turn off html comments
**/

//Shortened SP
$sp = $scriptProperties;

//Required Parameter
$filePath = $modx->getOption('filePath', $sp);

//REGEX match function to search in string
preg_match('/\.css|\.js/', $filePath, $match);

$fileType = $match[0];

//Optional Parameters to allow user to set comment string or to turn off default string
$rawComment = $modx->getOption('preComment', $sp);

$formattedComment = '<!-- ' . $rawComment . ' -->';

//Comment in correct format
$comment = $formattedComment; 

//commentEcho Function
if (!function_exists("commentEcho")) {
  function commentEcho($commentState, $comment, $runLocation, $cssException) {
	  if ($commentState == '0') {
		  global $modx;
		  if ($runLocation == 'body' && empty($cssException)) {
			  $modx->regClientHTMLBlock($comment);
		  } else {
			  $modx->regClientStartupHTMLBlock($comment);
		  }
	  }
  }
}


if (empty($rawComment)) {
  global $comment;
  //Based on file extension define a comment
  switch ($fileType) {
	  case '.js':
		  $comment = '<!--Page Specific JS File -->';
		  break;
	  case '.css':
		  $comment = '<!--Page Specific CSS File -->';
		  break;
	  //non-traditional extension
	  default:
		  $comment = '<!--Page Specific File -->';
		  break;
  }
}

//Determine user desired RunLocation if body run JS before body
if ($runLocation == 'body') {
	$noComment = $modx->getOption('commentOff', $scriptProperties, '0', true);
	$runLocation = $modx->getOption('runLocation', $sp, 'head', true);
	switch ($fileType) { 
		case '.js':
			commentEcho($noComment, $comment, $runLocation);
			$modx->regClientScript($filePath);
			break;
		//Place CSS call in head, it shouldn't be placed in body
		case '.css':
			commentEcho($noComment, $comment, $runLocation, 1);
			$modx->regClientCSS($filePath);
			break;
	}
} else {
	//Default RunLocation is in Head of HTML
	$noComment = $modx->getOption('commentOff', $scriptProperties, '0', true);
	$runLocation = $modx->getOption('runLocation', $sp, 'head', true);
	switch ($fileType) {
		case '.js':
			commentEcho($noComment, $comment, $runLocation);
			$modx->regClientStartupScript($filePath);
			break;
		case '.css':
			commentEcho($noComment, $comment, $runLocation);
			$modx->regClientCSS($filePath);
			break;
	}
}

?>
