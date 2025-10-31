<?php

namespace Tests;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        $app = parent::createApplication();
        $this->clearCache($app);

        return $app;
    }

    protected function clearCache(Application $app): void
    {
        // If config is not cached, running tests won't wipe existing database
        if (!$app->configurationIsCached()) {
            return;
        }

        \Artisan::call('config:clear');
        // Since the config is already loaded in memory at this point,
        // we need to bail to let user run tests safely
        throw new \Exception('Your configuration values were cached and have now been cleared. Please rerun the test suite.');
    }
}
