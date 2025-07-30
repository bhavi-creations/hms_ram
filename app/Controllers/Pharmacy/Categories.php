<?php namespace App\Controllers\Pharmacy;

use App\Controllers\BaseController;
use App\Models\Pharmacy\PharmacyCategoryModel;
use App\Models\Pharmacy\PharmacyMedicineModel; // To check dependencies

class Categories extends BaseController
{
    protected $categoryModel;
    protected $medicineModel;

    public function __construct()
    {
        // parent::__construct();  

        $this->categoryModel = new PharmacyCategoryModel();
        $this->medicineModel = new PharmacyMedicineModel();
    }

    /**
     * Displays a list of all medicine categories.
     */
    public function index()
    {
        $data = [
            'title'      => 'Manage Categories',
            'categories' => $this->categoryModel->findAll()
        ];
        return view('pharmacy/categories/index', $data);
    }

    /**
     * Shows the form to add a new category.
     */
    public function create()
    {
        $data = [
            'title'      => 'Add New Category',
            'validation' => service('validation')
        ];
        return view('pharmacy/categories/create', $data);
    }

    /**
     * Handles the submission of a new category form.
     */
    public function store()
    {
        $rules = [
            'name'        => 'required|min_length[3]|max_length[100]|is_unique[pharmacy_categories.name]',
            'description' => 'permit_empty|max_length[500]'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->categoryModel->save([
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description')
        ]);

        return redirect()->to(site_url('pharmacy/categories'))->with('success', 'Category added successfully.');
    }

    /**
     * Displays a form to edit an existing category.
     */
    public function edit($id = null)
    {
        $category = $this->categoryModel->find($id);

        if (empty($category)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Cannot find the category: ' . $id);
        }

        $data = [
            'title'      => 'Edit Category',
            'category'   => $category,
            'validation' => service('validation')
        ];
        return view('pharmacy/categories/edit', $data);
    }

    /**
     * Handles the update of an existing category.
     */
    public function update($id = null)
    {
        $currentCategory = $this->categoryModel->find($id);
        if (empty($currentCategory)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Cannot find the category to update: ' . $id);
        }

        $rules = [
            'name'        => 'required|min_length[3]|max_length[100]',
            'description' => 'permit_empty|max_length[500]'
        ];

        // Only add unique rule if name is changed
        if ($this->request->getPost('name') !== $currentCategory['name']) {
            $rules['name'] .= '|is_unique[pharmacy_categories.name]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->categoryModel->update($id, [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description')
        ]);

        return redirect()->to(site_url('pharmacy/categories'))->with('success', 'Category updated successfully.');
    }

    /**
     * Deletes a category. Consider restrictions if associated with medicines.
     */
    public function delete($id = null)
    {
        $category = $this->categoryModel->find($id);
        if (empty($category)) {
            return redirect()->to(site_url('pharmacy/categories'))->with('error', 'Category not found.');
        }

        // Check for associated medicines
        $associatedMedicines = $this->medicineModel->where('category_id', $id)->first();
        if ($associatedMedicines) {
            return redirect()->to(site_url('pharmacy/categories'))->with('error', 'Cannot delete category with associated medicines. Reassign medicines or consider deactivating category.');
        }

        if ($this->categoryModel->delete($id)) {
            return redirect()->to(site_url('pharmacy/categories'))->with('success', 'Category deleted successfully.');
        } else {
            return redirect()->to(site_url('pharmacy/categories'))->with('error', 'Failed to delete category.');
        }
    }
}