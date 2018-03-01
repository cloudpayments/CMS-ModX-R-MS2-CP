<?php
/**
 * CloudPayments Plugin for MiniShop2 build script
 *
 * @package    mspcloudpayments
 * @subpackage build
 */
$mtime  = microtime();
$mtime  = explode(' ', $mtime);
$mtime  = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);

/* define package */
define('PKG_NAME', 'mspCloudPayments');
define('PKG_NAME_LOWER', strtolower(PKG_NAME));
define('PKG_VERSION', '1.0.1');
define('PKG_RELEASE', 'pl');
define('PKG_NAME_LOWER_MINISHOP', 'minishop2');

/* define sources */
$root    = dirname(dirname(__FILE__)) . '/';
$sources = array(
    'root'                        => $root,
    'docs'                        => $root . 'docs/',
    'build'                       => $root . '_build/',
    'data'                        => $root . '_build/data/',
    'resolvers'                   => $root . '_build/resolvers/',
    'chunks'                      => $root . 'core/components/' . PKG_NAME_LOWER . '/elements/chunks/',
    'snippets'                    => $root . 'core/components/' . PKG_NAME_LOWER . '/elements/snippets/',
    'plugins'                     => $root . 'core/components/' . PKG_NAME_LOWER . '/elements/plugins/',
    'source_assets'               => $root . 'assets/components/' . PKG_NAME_LOWER,
    'source_core'                 => $root . 'core/components/' . PKG_NAME_LOWER,
    'payment_source_assets_files' => array(
        'components/' . PKG_NAME_LOWER_MINISHOP . '/payment/cloudpayments.php'
    ),
    'payment_source_core_files'   => array(
        'components/' . PKG_NAME_LOWER_MINISHOP . '/custom/payment/cloudpayments.class.php',
        'components/' . PKG_NAME_LOWER_MINISHOP . '/lexicon/en/msp.cloudpayments.inc.php',
        'components/' . PKG_NAME_LOWER_MINISHOP . '/lexicon/ru/msp.cloudpayments.inc.php'
    )
);

if (!file_exists($sources['build'] . '/build.config.php')) {
    die('Copy build.config.example.php to build.config.php and configure.');
}
/* override with your own defines here (see build.config.sample.php) */
require_once $sources['build'] . '/build.config.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
require_once $sources['build'] . '/includes/functions.php';

$modx = new modX();
$modx->initialize('mgr');
echo '<pre>'; /* used for nice formatting of log messages */
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');

$modx->loadClass('transport.modPackageBuilder', '', false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_NAME_LOWER, PKG_VERSION, PKG_RELEASE);
//$builder->registerNamespace(PKG_NAME_LOWER,false,true,'{core_path}components/'.PKG_NAME_LOWER.'/');
$modx->log(modX::LOG_LEVEL_INFO, 'Created Transport Package.');

/* load system settings */
$settings = include $sources['data'] . 'transport.settings.php';
if (!is_array($settings)) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'Could not package in settings.');
} else {
    $attributes = array(
        xPDOTransport::UNIQUE_KEY    => 'key',
        xPDOTransport::PRESERVE_KEYS => true,
        xPDOTransport::UPDATE_OBJECT => BUILD_SETTING_UPDATE,
    );
    foreach ($settings as $setting) {
        $vehicle = $builder->createVehicle($setting, $attributes);
        $builder->putVehicle($vehicle);
    }
    $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($settings) . ' System Settings.');
}
unset($settings, $setting, $attributes);

// Create category
$modx->log(xPDO::LOG_LEVEL_INFO, 'Created category.');
/** @var modCategory $category */
$category = $modx->newObject('modCategory');
$category->set('id', 1);
$category->set('category', PKG_NAME);

// Add snippets
$snippets = include $sources['data'] . 'transport.snippets.php';
if (!is_array($snippets)) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'Could not package in snippets.');
} else {
    $category->addMany($snippets);
    $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($snippets) . ' snippets.');
}

// Add chunks
$chunks = include $sources['data'] . 'transport.chunks.php';
if (!is_array($chunks)) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'Could not package in chunks.');
} else {
    $category->addMany($chunks);
    $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($chunks) . ' chunks.');
}

// Add plugins
$plugins = include $sources['data'] . 'transport.plugins.php';
if (!is_array($plugins)) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'Could not package in plugins.');
} else {
    $category->addMany($plugins);
    $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($plugins) . ' plugins.');
}

// Load statuses
$statuses = include $sources['data'] . 'transport.statuses.php';

// Create category vehicle
$attr    = array(
    xPDOTransport::UNIQUE_KEY                => 'category',
    xPDOTransport::PRESERVE_KEYS             => false,
    xPDOTransport::UPDATE_OBJECT             => true,
    xPDOTransport::RELATED_OBJECTS           => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array(
        'Snippets'     => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => BUILD_SNIPPET_UPDATE,
            xPDOTransport::UNIQUE_KEY    => 'name',
        ),
        'Chunks'       => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => BUILD_CHUNK_UPDATE,
            xPDOTransport::UNIQUE_KEY    => 'name',
        ),
        'Plugins'      => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => BUILD_PLUGIN_UPDATE,
            xPDOTransport::UNIQUE_KEY    => 'name',
        ),
        'PluginEvents' => array(
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => BUILD_EVENT_UPDATE,
            xPDOTransport::UNIQUE_KEY    => array('pluginid', 'event'),
        ),
    ),
);
$vehicle = $builder->createVehicle($category, $attr);
$builder->putVehicle($vehicle);

/* create payment */
$payment = $modx->newObject('msPayment');
$payment->set('id', 1);
$payment->set('name', 'CloudPayments');
$payment->set('active', 0);
$payment->set('class', 'CloudPayments');
$payment->set('rank', 100);

/* create payment vehicle */
$attributes = array(
    xPDOTransport::UNIQUE_KEY    => 'name',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => false
);
$vehicle    = $builder->createVehicle($payment, $attributes);

$modx->log(modX::LOG_LEVEL_INFO, 'Adding file resolvers to payment...');

$vehicle->resolve('file', array(
    'source' => $sources['source_core'],
    'target' => "return MODX_CORE_PATH . 'components/';",
));

foreach ($sources['payment_source_assets_files'] as $file) {
    $dir = dirname($file) . '/';
    $vehicle->resolve('file', array(
        'source' => $root . 'assets/' . $file,
        'target' => "return MODX_ASSETS_PATH . '{$dir}';",
    ));
}
foreach ($sources['payment_source_core_files'] as $file) {
    $dir = dirname($file) . '/';
    $vehicle->resolve('file', array(
        'source' => $root . 'core/' . $file,
        'target' => "return MODX_CORE_PATH . '{$dir}';"
    ));
}

$modx->log(modX::LOG_LEVEL_INFO, 'Adding in PHP resolvers...');
$vehicle->resolve('php', array(
    'source' => $sources['resolvers'] . 'resolve.twosteps.php',
));

$vehicle->resolve('php', array(
    'source' => $sources['resolvers'] . 'resolve.make_first.php',
));

$vehicle->resolve('php', array(
    'source' => $sources['resolvers'] . 'resolve.uninstall.php',
));

$builder->putVehicle($vehicle);
unset($file, $attributes);
/* now pack in the license file, readme and setup options */
$builder->setPackageAttributes(array(
    'changelog'     => file_get_contents($sources['docs'] . 'changelog.txt'),
    'license'       => file_get_contents($sources['docs'] . 'license.txt'),
    'readme'        => file_get_contents($sources['docs'] . 'readme.txt'),
    'statuses'      => $BUILD_STATUSES,
    'setup-options' => array(
        'source' => $sources['build'] . 'setup.options.php',
    ),
));
$modx->log(modX::LOG_LEVEL_INFO, 'Added package attributes and setup options.');

/* zip up package */
$modx->log(modX::LOG_LEVEL_INFO, 'Packing up transport package zip...');
$builder->pack();

$mtime     = microtime();
$mtime     = explode(" ", $mtime);
$mtime     = $mtime[1] + $mtime[0];
$tend      = $mtime;
$totalTime = ($tend - $tstart);
$totalTime = sprintf("%2.4f s", $totalTime);

$modx->log(modX::LOG_LEVEL_INFO, "\n<br />Package Built.<br />\nExecution time: {$totalTime}\n");

exit ();