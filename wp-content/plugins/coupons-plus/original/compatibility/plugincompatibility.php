<?php

namespace CouponsPlus\Original\Compatibility;

use CouponsPlus\Original\Compatibility\BuiltIn\GlobalCompatibility;
use CouponsPlus\Original\Compatibility\CompatibilityManager;
use CouponsPlus\Original\Utilities\TypeChecker;

/**
 * GlobalCompatibility will *always* run 
 * DefaultCompatibilty will only run when no other CompatibiltyManagers have run (excluding GlobalCompatibility).
 */
Class PluginCompatibility
{
    use TypeChecker;

    public static function handle(array $compatibilityManagers)
    {
        (boolean) $defaultShouldRun = true;

        foreach ($compatibilityManagers as $compatibilityManager) {
            $compatibilityManager = static::expectValue($compatibilityManager)->toBe(CompatibilityManager::class);

            if (($compatibilityManager instanceof GlobalCompatibility) || 
                $compatibilityManager->shouldHandle($defaultShouldRun)) {

                $defaultShouldRun = $compatibilityManager->shouldDefaultBeHandled($defaultShouldRun);
                $compatibilityManager->handle();
            }
        }
    }
    
}