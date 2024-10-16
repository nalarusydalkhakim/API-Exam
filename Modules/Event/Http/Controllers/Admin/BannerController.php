<?php

namespace Modules\Event\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Event\Entities\Banner;
use Modules\Event\Http\Requests\Admin\CreateBannerRequest;
use Modules\Event\Http\Requests\Admin\UpdateBannerRequest;
use Modules\Event\Http\Requests\Admin\SwitchBannerSequenceRequest;

class BannerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role_or_permission:Super Admin|Lihat Banner'])->only('index', 'show');
        $this->middleware(['role_or_permission:Super Admin|Tambah Banner'])->only('store');
        $this->middleware(['role_or_permission:Super Admin|Edit Banner'])->only('update');
        $this->middleware(['role_or_permission:Super Admin|Hapus Banner'])->only('destroy');
    }

    public function index(Request $request)
    {
        return $this->ok(
            Banner::orderBy('sequence', 'asc')
                ->when($request->search, function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                })
                ->paginate($request->get('per_page', 10)),
            'Data banner'
        );
    }

    public function store(CreateBannerRequest $requestBanner)
    {
        $inputBanner = $requestBanner->validated();
        $inputBanner['sequence'] = 1;
        if ($requestBanner->has('photo')) {
            $inputBanner['photo'] = $requestBanner->file('photo')->store('banner-photos');
        }
        $inputBanner['status'] = 'Belum Mulai';
        $banner = Banner::create($inputBanner);

        return $this->ok($banner, 'berhasil menyimpan data');
    }

    public function show(Banner $banner)
    {
        return $this->ok(($banner), 'detail data banner');
    }

    public function update(UpdateBannerRequest $request, Banner $banner)
    {
        $input = $request->validated();
        if ($request->file('photo')) {
            $input['photo'] = $request->file('photo')->store('banner-photos');
        } else {
            $input['photo'] = $banner->getRawOriginal('photo');
        }

        return $this->ok($banner->update($input), 'berhasil menyimpan data');
    }

    public function swithBannerSequence(SwitchBannerSequenceRequest $request, Banner $banner)
    {
        return $this->ok($banner->switchSequence($request->target_id), 'berhasil menyimpan data');
    }

    public function destroy(Banner $banner)
    {
        return $this->ok($banner->delete(), 'Data dihapus');
    }
}
