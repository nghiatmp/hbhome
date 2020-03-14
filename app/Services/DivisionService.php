<?php

namespace App\Services;

use App\Models\Division;

class DivisionService
{
    private $division;

    public function __construct(Division $division)
    {
        $this->division = $division;
    }

    /**
     *
     * @return list divisions data
     */
    public function index()
    {
        $divisions = $this->division->get(['id', 'title'])->toArray();

        return [
            'data' => $divisions
        ];
    }

    /**
     * @param ['title'] $params
     *
     */
    public function store($params)
    {
        return $this->division->create($params);
    }

    /**
     * @param integer $id
     * @param ['title'] $params
     *
     */
    public function update($id, $params)
    {
        $divisionObject = $this->find($id);
        $divisionObject->update($params);
        return $divisionObject;
    }

    public function find($id)
    {
        return $this->division->find($id);
    }
}
