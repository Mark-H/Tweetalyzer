<?php

class Tweetalyzer {
    /* @var modX $modx */
    public $modx = null;
    public $config = array();
    public $chunks = array();

    /* @var Sentiment $sentiment */
    public $sentiment = null;

    public $resultsMeta = array();
    public $results = array();
    public $resultsCount = array();

    public function __construct(modX $modx, $config) {
        $this->modx = &$modx;
        $basePath = $this->modx->getOption('tweetalyzer.core_path',$config,$this->modx->getOption('core_path').'components/tweetalyzer/');
        $assetsUrl = $this->modx->getOption('tweetalyzer.assets_url',$config,$this->modx->getOption('assets_url').'components/tweetalyzer/');
        $assetsPath = $this->modx->getOption('tweetalyzer.assets_path',$config,$this->modx->getOption('assets_path').'components/tweetalyzer/');
        $this->config = array_merge(array(
            'base_bath' => $basePath,
            'core_path' => $basePath,
            'model_path' => $basePath.'model/',
            'processors_path' => $basePath.'processors/',
            'elements_path' => $basePath.'elements/',
            'assets_path' => $assetsPath,
            'js_url' => $assetsUrl.'js/',
            'css_url' => $assetsUrl.'css/',
            'assets_url' => $assetsUrl,
            'connector_url' => $assetsUrl.'connector.php',
        ),$config);

        require_once 'sentiment.class.php';
        $this->sentiment = new Sentiment();
    }

    public function getTweets($search = '', $amount = 50) {
        $url = 'http://search.twitter.com/search.json?q=';
        $url .= urlencode($search);
        $url .= '&rpp='.$amount;
        $url .= '&include_entities=true&result_type=recent';

        $tweets = $this->curlRequest($url);
        $tweets = $this->modx->fromJSON($tweets);

        $counts = array(
            'neu' => 0,
            'pos' => 0,
            'neg' => 0,
        );
        foreach ($tweets['results'] as $tweet) {
            $tweet['sentiment'] = $this->sentiment->categorise($tweet['text']);
            $counts[$tweet['sentiment']]++;
            $this->results[] = $tweet;
        }
        $this->resultsCount = $counts;

        unset($tweets['results']);

        $this->resultsMeta = $tweets;

    }

    private function curlRequest ($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
    * Gets a Chunk and caches it; also falls back to file-based templates
    * for easier debugging.
    *
    * @author Shaun McCormick
    * @access public
    * @param string $name The name of the Chunk
    * @param array $properties The properties for the Chunk
    * @return string The processed content of the Chunk
    */
    public function getChunk($name,$properties = array()) {
        $chunk = null;
        if (!isset($this->chunks[$name])) {
            $chunk = $this->modx->getObject('modChunk',array('name' => $name),true);
            if (empty($chunk)) {
                $chunk = $this->_getTplChunk($name);
                if ($chunk == false) return false;
            }
            $this->chunks[$name] = $chunk->getContent();
        } else {
            $o = $this->chunks[$name];
            $chunk = $this->modx->newObject('modChunk');
            $chunk->setContent($o);
        }
        $chunk->setCacheable(false);
        return $chunk->process($properties);
    }

    /**
    * Returns a modChunk object from a template file.
    *
    * @author Shaun McCormick
    * @access private
    * @param string $name The name of the Chunk. Will parse to name.chunk.tpl
    * @param string $postFix The postfix to append to the name
    * @return modChunk/boolean Returns the modChunk object if found, otherwise
    * false.
    */
    private function _getTplChunk($name,$postFix = '.tpl') {
        $chunk = false;
        $f = $this->config['elements_path'].'chunks/'.$name.$postFix;
        if (file_exists($f)) {
            $o = file_get_contents($f);
            /* @var modChunk $chunk */
            $chunk = $this->modx->newObject('modChunk');
            $chunk->set('name',$name);
            $chunk->setContent($o);
        }
        return $chunk;
    }
}
