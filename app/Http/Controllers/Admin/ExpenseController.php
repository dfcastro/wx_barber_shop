<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Exibe a página de gerenciamento de despesas.
     */
    public function index()
    {
        return view('admin.expenses.index');
    }
}