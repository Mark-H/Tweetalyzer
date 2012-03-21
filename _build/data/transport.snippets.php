<?php
$snips = array(
    'Tweetalyzer' => 'Fetches tweets and analyzes them for sentiment.',
);

$snippets = array();
$idx = 0;

foreach ($snips as $sn => $sdesc) {
    $idx++;
    $snippets[$idx] = $modx->newObject('modSnippet');
    $snippets[$idx]->fromArray(array(
       'id' => $idx,
       'name' => $sn,
       'description' => $sdesc,
       'snippet' => getSnippetContent($sources['snippets'].strtolower($sn).'.snippet.php')
    ));

    $snippetProperties = array();
    $props = include $sources['snippets'].strtolower($sn).'.properties.php';
    foreach ($props as $key => $value) {
        if (is_string($value) || is_int($value)) { $type = 'textfield'; }
        elseif (is_bool($value)) { $type = 'combo-boolean'; }
        else { $type = 'textfield'; }
        $snippetProperties[] = array(
            'name' => $key,
            'desc' => 'tweetalyzer.prop_desc.'.$key,
            'type' => $type,
            'options' => '',
            'value' => ($value != null) ? $value : '',
            'lexicon' => 'tweetalyzer:properties'
        );
    }

    if (count($snippetProperties) > 0)
        $snippets[$idx]->setProperties($snippetProperties);
}

return $snippets;

?>
