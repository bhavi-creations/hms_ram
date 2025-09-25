<?php

namespace App\Controllers\Laboratory;

use App\Controllers\BaseController;
use App\Models\Laboratory\LabTestModel;
use App\Models\Laboratory\LabTestTypeModel;

class LabTests extends BaseController
{
    protected $labTestModel;
    protected $labTestTypeModel;

    public function __construct()
    {
        $this->labTestModel = new LabTestModel();
        $this->labTestTypeModel = new LabTestTypeModel();
    }

    public function index()
    {
        $builder = $this->labTestModel->builder();

        $builder->select('lab_tests.*, lab_test_types.name as test_type_name');
        $builder->join('lab_test_types', 'lab_test_types.id = lab_tests.test_type_id', 'left');

        $tests = $builder->get()->getResultArray();

        return view('laboratory/tests/index', ['tests' => $tests]);
    }


    public function create()
    {
        $data['types'] = $this->labTestTypeModel->findAll();
        return view('laboratory/tests/create', $data);
    }

    public function save()
    {
        $post = $this->request->getPost();

        $data = [
            'name' => $post['name'],
            'description' => $post['description'],
            'test_type_id' => $post['test_type_id'],
            'price' => $post['price']
        ];

        if ($this->labTestModel->insert($data)) {
            return redirect()->to(base_url('laboratory/tests'))->with('success', 'Test added.');
        }
        return redirect()->back()->with('error', 'Failed')->withInput();
    }

    public function edit($id)
    {
        $data['test'] = $this->labTestModel->find($id);
        $data['types'] = $this->labTestTypeModel->findAll();
        return view('laboratory/tests/edit', $data);
    }

    public function update($id)
    {
        $post = $this->request->getPost();

        $data = [
            'name' => $post['name'],
            'description' => $post['description'],
            'test_type_id' => $post['test_type_id'],
            'price' => $post['price']
        ];

        if ($this->labTestModel->update($id, $data)) {
            return redirect()->to(base_url('laboratory/tests'))->with('success', 'Test updated.');
        }
        return redirect()->back()->with('error', 'Failed')->withInput();
    }

    public function delete($id)
    {
        if ($this->labTestModel->delete($id)) {
            return redirect()->to(base_url('laboratory/tests'))->with('success', 'Test deleted.');
        }
        return redirect()->to(base_url('laboratory/tests'))->with('error', 'Failed');
    }
}
