<?php

namespace Modules\Event\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Modules\Event\Entities\Banner;

class BannerController extends Controller
{
    public function index()
    {
        return $this->ok(Banner::orderBy('sequence', 'asc')->get(), 'Data banner');
    }

    public function show(Banner $banner)
    {
        return $this->ok(($banner), 'detail data banner');
    }
}
