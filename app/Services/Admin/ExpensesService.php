<?php

namespace Bets\Services\Admin;


use Bets\Models\Expense;
use Bets\Services\BaseService;

/**
* Expenses Service for Admin
*/
class ExpensesService extends BaseService
{
    protected $modelClass = Expense::class;
}
