<?php

namespace App\Helpers;

use App\Models\Team;

class ActivityLogHelper
{
    /**
     * @param $oldArray
     * @param $newArray
     * @return string
     */
    public function createContentUpdate($oldArray, $newArray)
    {
        $oldArray = $this->convertDataFromKeyConfig($oldArray);
        $newArray = $this->convertDataFromKeyConfig($newArray);
        $content = '<ul>';
        $arrayDiff = array_diff($newArray, $oldArray);
        if (count($arrayDiff) > 0) {
            foreach ($arrayDiff as $key => $value) {
                $oldValue = strlen($oldArray[$key]) > 0 ? $oldArray[$key] : 'empty';
                $content .= "<li><b>$key: </b>". $oldValue ." => $value</li>";
            }
        }
        return $content . '</ul>';
    }

    /**
     * @param $dataArray
     * @return array
     */
    public function convertDataFromKeyConfig($dataArray)
    {
        $teams = Team::all()->toArray();
        $teamArray = [];
        foreach ($teams as $team) {
            $teamArray[$team['id']] = $team['title'];
        }
        $arrayKeyData = [
            'role' => config('constant.PROJECT_ROLES'),
            'rank' => config('constant.PROJECT_RANKS'),
            'team_id' => $teamArray,
            'contract' => config('constant.PROJECT_CONTRACTS'),
            'status' => config('constant.PROJECT_STATUS')
        ];
        foreach ($arrayKeyData as $key => $value) {
            if (array_key_exists($key, $dataArray) && array_key_exists($dataArray[$key], $value)) {
                $dataArray[$key] = $value[$dataArray[$key]];
            }
        }
        return $dataArray;
    }

    /**
     * @param $fields
     * @param $object
     * @return array
     */
    public function getObjectArrayByFields($fields, $object)
    {
        $objectArray = [];
        foreach ($fields as $field) {
            $objectArray[$field] = $object->$field;
        }
        return $objectArray;
    }
}
