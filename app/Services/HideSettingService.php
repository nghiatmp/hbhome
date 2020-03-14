<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\HideSetting;

class HideSettingService
{
    private $hideSetting;

    public function __construct(HideSetting $hideSetting)
    {
        $this->hideSetting = $hideSetting;
    }

    public function getHideSettingIdsByType($type)
    {
        $projectHideIdString = $this->hideSetting
        ->where('type', $type)
        ->where('created_by', Auth::user()->id)
        ->pluck('ids')->first();
        $projectHideIds = [];
        if (!is_null($projectHideIdString) && $projectHideIdString != "") {
            $projectHideIds = array_map('intval', explode(',', $projectHideIdString));
        }
        return $projectHideIds;
    }

    /**
     * @return list hide setting
     *
     */
    public function index()
    {
        return $this->hideSetting->where('created_by', Auth::user()->id)->get();
    }

    /**
     * @param ['type', 'ids', 'created_by'] $params
     *
     */
    public function store($params)
    {
        $hideSettingByUser = $this->hideSetting
            ->where('created_by', $params['created_by'])
            ->where('type', $params['type'])->first();
        if ($hideSettingByUser == null) {
            return $this->hideSetting->create($params);
        }
        $hideSettingByUser->update($params);
        return $hideSettingByUser;
    }

    /**
     * @param integer $id
     * @param ['type', 'ids', 'created_by'] $params
     *
     */
    public function update($id, $params)
    {
        $hideSettingUpdate = $this->hideSetting->findOrFail($id);
        $hideSettingUpdate->update($params);

        return $hideSettingUpdate;
    }

    /**
     * @param integer $id
     */
    public function destroy($id)
    {
        $hideSettingDestroy = $this->hideSetting->findOrFail($id);
        return $hideSettingDestroy->delete();
    }
}
