<?php

namespace RPillz\FeatureAccess\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as Orchestra;
use RPillz\FeatureAccess\FeatureAccessServiceProvider;

class TestCase extends Orchestra
{
    protected $testUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Factory::guessFactoryNamesUsing(
        //     fn (string $modelName) => 'RPillz\\FeatureAccess\\Database\\Factories\\'.class_basename($modelName).'Factory'
        // );

        $this->setUpDatabase($this->app);

        $this->testUser = User::where('email', 'test@example.com')->first();
        $this->testAdmin = User::where('email', 'admin@example.com')->first();
    }

    protected function getPackageProviders($app)
    {
        return [
            FeatureAccessServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        $migration = include __DIR__.'/../database/migrations/create_feature_access_table.php.stub';
        $migration->up();
    }

    protected function setUpDatabase($app)
    {
        $app['db']->connection()->getSchemaBuilder()->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email');
            $table->softDeletes();
        });

        User::create(['email' => 'test@example.com']);
        User::create(['email' => 'admin@example.com']);
    }

    public function loginWithFakeUser()
    {
        $user = User::first();

        $this->be($user);
    }
}
