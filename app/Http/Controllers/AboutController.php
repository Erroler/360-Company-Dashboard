<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\SaftService;

class AboutController extends Controller
{
    // Serves the About page.
    public function index(SaftService $service)
    {
        $about = $service->get('company_information');
        return view('pages.about', compact('about'));
    }

    // Serves the View Trial Balance Sheet page.
    public function trialBalanceSheet(SaftService $service)
    {
        $trial_balance_sheet = $service->get('trial_balance_sheet')['all_year'];
        
        return view('pages.trial_balance_sheet', compact('trial_balance_sheet'));
    }

    // Serves the View Profit&Loss Statement page.
    public function profitLossStatement(SaftService $service)
    {
        $profit_loss_statement = $service->get('profit_loss_statement')['all_year'];

        return view('pages.profit_loss_statement', compact('profit_loss_statement'));
    }

    // Serves the View Balance Sheet page.
    public function balanceSheet(SaftService $service)
    {
        $balance_sheet = $service->get('balance_sheet')['all_year'];
        
        return view('pages.balance_sheet', compact('balance_sheet'));
    }
}
