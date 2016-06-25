<?php

namespace Tests;

use App\Entities\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

class AcceptanceTestCase extends BaseTestCase
{
    use DatabaseTransactions;
    
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */

    public function createApplication()
    {
        $app = require __DIR__.'/../vendor/laravel/laravel/bootstrap/app.php';

        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }
    
    public function setUp()
    {
        parent::setUp();
        $this->be(User::find(1));
        \DB::connection()->enableQueryLog();
    }

    public function tearDown()
    {
        echo ' ' . count(\DB::getQueryLog()) . ' queries in '
            . (array_sum(array_column(\DB::getQueryLog(), 'time'))/1000) . ' seconds';
        //var_dump( \DB::getQueryLog() );
        parent::tearDown();
    }
    
    public function createExecutive()
    {
        $user = factory(\App\Entities\User::class)->create([
            'name' => 'Nome Usuario Executive',
            'email' => 'executive@alientronics.com.br',
            'password' => 'admin',
            'language' => 'pt-br',
            'contact_id' => 1,
            'company_id' => 1,
        ]);
    
        $user->assignRole('executive');
        return $user;
    }
    
    /**
     * Assert that a given where condition matches a soft deleted record
     *
     * @param  string $table
     * @param  array  $data
     * @param  string $connection
     * @return $this
     */
    protected function seeIsSoftDeletedInDatabase($table, array $data, $connection = null)
    {
        $database = $this->app->make('db');
    
        $connection = $connection ?: $database->getDefaultConnection();
    
        $count = $database->connection($connection)
            ->table($table)
            ->where($data)
            ->whereNotNull('deleted_at')
            ->count();
    
        $this->assertGreaterThan(0, $count, sprintf(
            'Found unexpected records in database table [%s] that matched attributes [%s].',
            $table,
            json_encode($data)
        ));
    
        return $this;
    }
}
