<?php return array (
  'app' => 
  array (
    'id' => 'couponsplus',
    'shortId' => 'cp',
    'pluginFileName' => 'coupons-plus',
    'textDomain' => 'coupons-plus-international',
    'translationFiles' => 
    array (
      'production' => 'international/coupons-plus-international.pot',
      'main' => 'international/main-source.pot',
      'scripts' => 'international/scripts-source.pot',
    ),
  ),
  'events' => 
  array (
    'globalValidator' => 'CouponsPlus\\App\\Events\\CustomGlobalEventsValidator',
  ),
  'schema' => 
  array (
    'applicationDatabase' => 'CouponsPlus\\App\\Data\\Schema\\ApplicationDatabase',
  ),
  'directories' => 
  array (
    'app' => 
    array (
      'schema' => 'data/schema',
      'scripts' => 'scripts',
      'dashboard' => 'scripts/dashboard',
    ),
    'storage' => 
    array (
      'branding' => 'storage/branding',
    ),
  ),
  'environment' => 'production',
);