<?php

namespace App\Controllers;

use App\Models\LocationModel;
use CodeIgniter\RESTful\ResourceController;

class Locations extends ResourceController
{
    protected $modelName = LocationModel::class;
    protected $format = 'json';

    // READ: Kuhaon ang tanan locations
    public function index()
    {
        $locations = $this->model->findAll();

        return $this->respond([
            'success' => true,
            'data' => $locations
        ]);
    }
public function show($id = null)
{
    $location = $this->model->find($id);

    if (!$location) {
        return $this->failNotFound('Location not found.');
    }

    return $this->respond([
        'success' => true,
        'data' => $location
    ]);
}

    

    // CREATE: Mag-add og bag-ong location
    public function create()
    {
        $data = $this->request->getJSON(true);

        if (!$data) {
            return $this->failValidationErrors(
                'Walay data nga nadawat.'
            );
        }

        $id = $this->model->insert($data);

        if ($id === false) {
            return $this->failValidationErrors(
                $this->model->errors()
            );
        }

        return $this->respondCreated([
            'success' => true,
            'message' => 'Location successfully added.',
            'data' => $this->model->find($id)
        ]);
    }

    // UPDATE: Mag-edit og existing location
    public function update($id = null)
    {
        $location = $this->model->find($id);

        if (!$location) {
            return $this->failNotFound('Location not found.');
        }

        $data = $this->request->getJSON(true);

        if (!$data) {
            return $this->failValidationErrors(
                'Walay data nga nadawat.'
            );
        }

        $this->model->update($id, $data);

        return $this->respond([
            'success' => true,
            'message' => 'Location successfully updated.',
            'data' => $this->model->find($id)
        ]);
    }

    // DELETE: Mag-delete og location
    public function delete($id = null)
    {
        $location = $this->model->find($id);

        if (!$location) {
            return $this->failNotFound('Location not found.');
        }

        $this->model->delete($id);

        return $this->respondDeleted([
            'success' => true,
            'message' => 'Location successfully deleted.'
        ]);
    }
}