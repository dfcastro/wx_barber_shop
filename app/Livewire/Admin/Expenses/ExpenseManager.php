<?php

namespace App\Livewire\Admin\Expenses;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Livewire\Component;
use Livewire\WithPagination;

class ExpenseManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    // Propriedades para o formulário
    public $expenseId;
    public $expense_category_id;
    public $description;
    public $amount;
    public $expense_date;
    public $notes;

    public $categories;

    public function mount()
    {
        $this->categories = ExpenseCategory::orderBy('name')->get();
        $this->expense_date = now()->format('Y-m-d');
    }

    protected function rules()
    {
        return [
            'expense_category_id' => 'required|exists:expense_categories,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function saveExpense()
    {
        $this->validate();

        Expense::updateOrCreate(['id' => $this->expenseId], [
            'expense_category_id' => $this->expense_category_id,
            'description' => $this->description,
            'amount' => $this->amount,
            'expense_date' => $this->expense_date,
            'notes' => $this->notes,
            'user_id' => auth()->id(), // Atribui o admin logado
        ]);

        session()->flash('message', $this->expenseId ? 'Despesa atualizada com sucesso.' : 'Despesa criada com sucesso.');

        $this->resetForm();
    }

    public function editExpense($id)
    {
        $expense = Expense::findOrFail($id);
        $this->expenseId = $expense->id;
        $this->expense_category_id = $expense->expense_category_id;
        $this->description = $expense->description;
        $this->amount = $expense->amount;
        $this->expense_date = $expense->expense_date->format('Y-m-d');
        $this->notes = $expense->notes;
    }

    public function deleteExpense($id)
    {
        Expense::destroy($id);
        session()->flash('message', 'Despesa excluída com sucesso.');
    }

    public function resetForm()
    {
        $this->reset(['expenseId', 'expense_category_id', 'description', 'amount', 'notes']);
        $this->expense_date = now()->format('Y-m-d'); // Reseta a data para hoje
    }

    public function render()
    {
        $expenses = Expense::with('category')->orderBy('expense_date', 'desc')->paginate(10);
        return view('livewire.admin.expenses.expense-manager', [
            'expenses' => $expenses,
        ]);
    }
}