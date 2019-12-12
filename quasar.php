<?php

use Alfred\Workflows\Workflow;
use AlgoliaSearch\Client as Algolia;
use AlgoliaSearch\Version as AlgoliaUserAgent;

require __DIR__ . '/vendor/autoload.php';

$query = $argv[1];

$workflow = new Workflow();
$algolia = new Algolia('BH4D9OD16A', '5c15f3938ef24ae49e3a0e69dc4a140f');

AlgoliaUserAgent::addSuffixUserAgentSegment('Alfred Workflow', '0.2.3');

$index = $algolia->initIndex('quasar-framework');
$search = $index->search($query);
$results = $search['hits'];

$urls = [];

foreach ($results as $hit) {

    $url = $hit['url'];

    if (in_array($url, $urls, true)) {
        continue;
    }

    $urls[] = $url;

    $group = $hit['hierarchy']['lvl1'];
    $name = $hit['hierarchy']['lvl2'];

    $title = "{$group} » {$name}";

    $title = strip_tags(html_entity_decode($title, ENT_QUOTES, 'UTF-8'));

    $workflow->result()
        ->uid($hit['objectID'])
        ->title($title)
        ->autocomplete($title)
        ->arg($url)
        ->quicklookurl($url)
        ->valid(true);
}

echo $workflow->output();
