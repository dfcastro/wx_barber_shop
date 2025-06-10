<?php

namespace App\Livewire\Admin\Expenses;

use App\Models\ExpenseCategory;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    // Propriedades para o formulário
    public $name = '';
    public $description = '';
    public $categoryId;

    // Regras de validação
    protected function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:expense_categories,name,' . $this->categoryId,
            'description' => 'nullable|string',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    // Salva uma categoria (criação ou atualização)
    public function saveCategory()
    {
        $this->validate();

        ExpenseCategory::updateOrCreate(['id' => $this->categoryId], [
            'name' => $this->name,
            'description' => $this->description,
        ]);

        session()->flash('message', $this->categoryId ? 'Categoria atualizada com sucesso.' : 'Categoria criada com sucesso.');

        $this->resetForm();
    }

    // Preenche o formulário para edição
    public function editCategory($id)
    {
        $category = ExpenseCategory::findOrFail($id);
        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->description = $category->description;
    }

    // Exclui uma categoria
    public function deleteCategory($id)
    {
        $category = ExpenseCategory::withCount('expenses')->findOrFail($id);

        if ($category->expenses_count > 0) {
            session()->flash('error', 'Não é possível excluir esta categoria, pois ela já está associada a despesas.');
            return;
        }

        $category->delete();
        session()->flash('message', 'Categoria excluída com sucesso.');
    }

    // Limpa o formulário
    public function resetForm()
    {
        $this->reset(['name', 'description', 'categoryId']);
    }

    public function render()
    {
        $categories = ExpenseCategory::orderBy('name')->paginate(5);
        return view('livewire.admin.expenses.category-manager', [
            'categories' => $categories,
        ]);
    }
}