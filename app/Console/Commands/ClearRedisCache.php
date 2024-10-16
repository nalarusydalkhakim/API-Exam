<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class ClearRedisCache extends Command
{
    protected $signature = 'cache:clear-redis';
    protected $description = 'Clear the Redis cache';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Redis::flushdb(); // Clears the entire Redis database
        $this->info('Redis cache cleared.');
    }
}
