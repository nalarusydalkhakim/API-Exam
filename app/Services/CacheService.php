<?php

namespace App\Services;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CacheService
{
    public static function purgeSchoolModules(String $schoolId) : void
    {
        Cache::forget('schoolModules-'.$schoolId);
    }

    public static function purgeCanAccessThisSchoolCache(String $schoolId, String $userId) : void
    {
        Cache::forget('canAccessThisSchool-'.$schoolId.$userId);
    }
}
