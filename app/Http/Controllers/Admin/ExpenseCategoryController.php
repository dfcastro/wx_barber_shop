<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    /**
     * Exibe a página de gerenciamento de categorias de despesas.
     */
    public function index()
    {
        return view('admin.expenses.categories.index');
    }
}