<?php

namespace Bets\Http\Controllers\Web;

use Bets\Models\Company;
use Illuminate\Http\Request;
use Bets\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return view('web.home');
    }

    public function rules()
    {
        $company = Company::query()->first();
        $regras = $company->rules;

        return view('web.regras', compact('regras'));
    }

    public function balance()
    {
        return view('balance');
    }
}
