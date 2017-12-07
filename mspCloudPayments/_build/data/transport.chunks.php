<?php

$chunks = array();

$tmp = array(
    'tpl.mspCloudPaymentsWidget' => 'msp_cloudpayments_widget',
);

// Save chunks for setup options
$BUILD_CHUNKS = array();

foreach ($tmp as $k => $v) {
    /** @var modChunk $chunk */
    $chunk = $modx->newObject('modChunk');
    $chunk->fromArray(array(
        'id'          => 0,
        'name'        => $k,
        'description' => '',
        'snippet'     => file_get_contents($sources['source_core'] . '/elements/chunks/chunk.' . $v . '.tpl'),
        'static'      => BUILD_CHUNK_STATIC,
        'source'      => 1,
        'static_file' => 'core/components/' . PKG_NAME_LOWER . '/elements/chunks/chunk.' . $v . '.tpl',
    ), '', true, true);
    $chunks[] = $chunk;

    $BUILD_CHUNKS[$k] = file_get_contents($sources['source_core'] . '/elements/chunks/chunk.' . $v . '.tpl');
}
ksort($BUILD_CHUNKS);

return $chunks;