<?php
/* @var modX $modx
 * @var array $scriptProperties
 **/
$path = $modx->getOption('tweetalyzer.core_path',$config,$modx->getOption('core_path').'components/tweetalyzer/');
$defaults = include $path . '/elements/snippets/tweetalyzer.properties.php';
$scriptProperties = array_merge($defaults,$scriptProperties);
require_once $path . '/model/tweetalyzer.class.php';
$tweetalyzer = new Tweetalyzer($modx, $scriptProperties);

if ((bool)$scriptProperties['registerCss']) {
    $modx->regClientCSS($tweetalyzer->config['assets_url'] . 'css/main.css');
}

$tweetalyzer->getTweets($scriptProperties['search'],$scriptProperties['amount']);

$output = array();
foreach($tweetalyzer->results as $tweet) {
    $output[] = $tweetalyzer->getChunk($scriptProperties['tweetTpl'],$tweet);
}
$output = implode($scriptProperties['tweetSeparator'],$output);

$outerPhs = array_merge(
    $tweetalyzer->config,
    $tweetalyzer->resultsMeta,
    $tweetalyzer->resultsCount,
    array(
    'tweets' => $output,
    'total' => count($tweetalyzer->results)
));
$outer = $tweetalyzer->getChunk($scriptProperties['outerTpl'],$outerPhs);

return $outer;
