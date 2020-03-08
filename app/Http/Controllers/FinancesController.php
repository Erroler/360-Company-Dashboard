<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SaftService;
use App\Services\JasminService;

class FinancesController extends Controller
{
    // Serves the Finances index page.
    public function index(SaftService $saftService, JasminService $jasminService, $period = 0)
    {
        $saft_kpis = $this->getKPIs($saftService, $period);
        $cache_data = $jasminService->getInfoInCache(['inventory']);
        $jasmin_kpis = SalesController::getSalesKpis($cache_data['inventory'], $period);

        return view('pages.finances', [
            'saft_kpis' => $saft_kpis,
            'jasmin_kpis' => $jasmin_kpis,
        ]);
    }

    public function getKPIs(SaftService $service, $period) {
        $profit_loss_statement = $service->get('profit_loss_statement');
        $balance_sheet = $service->get('balance_sheet');

        switch ($period) {
            case 0://All Year
                $pls = $profit_loss_statement['all_year'];
                $bs = $balance_sheet['all_year'];
                break;

            case 1: 
                $pls = $profit_loss_statement['trimester'][$period];
                $bs = $balance_sheet['trimester'][$period];
                break;

            case 2: case 3: case 4: //Trimester $period
                $pls = $profit_loss_statement['trimester'][$period];
                $bs = $balance_sheet['trimester'][$period];
                $previous_pls = $profit_loss_statement['trimester'][$period - 1];
                $previous_bs = $balance_sheet['trimester'][$period - 1];
                break;
        }
        
        $totalAssets = $bs['Ativo']['Total do Ativo'];

        if ($period > 1) {
            $previous_totalAssets = $previous_bs['Ativo']['Total do Ativo'];
        }

        //KPIS:

        //EBIT
        $ebit = $pls['ebit'];
        $previous_ebit = 0;

        if ($period > 1) 
            $previous_ebit = $previous_pls['ebit'];

        $financesKPIs['EBIT']['Value'] = number_format($ebit, 2, ',', ' ');
        $financesKPIs['EBIT']['Type'] = OverviewController::getVariationType($previous_ebit, $ebit, $period);
        $financesKPIs['EBIT']['Difference'] = OverviewController::getVariation($previous_ebit, $ebit, $period);

        //EBITDA
        $ebitda = $pls['ebitda'];
        $previous_ebitda = 0;

        if ($period > 1) 
            $previous_ebitda = $previous_pls['ebitda'];

        $financesKPIs['EBITDA']['Value'] = number_format($ebitda, 2, ',', ' ');
        $financesKPIs['EBITDA']['Type'] = OverviewController::getVariationType($previous_ebitda, $ebitda, $period);
        $financesKPIs['EBITDA']['Difference'] = OverviewController::getVariation($previous_ebitda, $ebitda, $period);

        //Net Profit = Net Income
        $netIncome = $pls['net_income'];
        $previous_netIncome = 0;

        if ($period > 1) 
            $previous_netIncome = $previous_pls['net_income'];

        $financesKPIs['Net Profit']['Value'] = number_format($netIncome, 2, ',', ' ');
        $financesKPIs['Net Profit']['Type'] = OverviewController::getVariationType($previous_netIncome, $netIncome, $period);
        $financesKPIs['Net Profit']['Difference'] = OverviewController::getVariation($previous_netIncome, $netIncome, $period);
        
        
        //Financial Autonomy = Capitais próprios/Ativo líquido
        $shareholderEquity = $bs['Capital Próprio e Passivo']['Total do Capital Próprio'];
        $totalLiabilities = $bs['Capital Próprio e Passivo']['Total do Passivo']; //activo líquido = total do passivo.
        $financialAutonomy = $shareholderEquity/$totalLiabilities;

        $previous_financialAutonomy = 0;

        if ($period > 1) {
            $previous_shareholderEquity = $previous_bs['Capital Próprio e Passivo']['Total do Capital Próprio'];
            $previous_totalLiabilities = $previous_bs['Capital Próprio e Passivo']['Total do Passivo']; //activo líquido = total do passivo.
            $previous_financialAutonomy = $previous_shareholderEquity/$previous_totalLiabilities;
        }

        $financesKPIs['Financial Autonomy']['Value'] = number_format($financialAutonomy, 2, ',', ' ');
        $financesKPIs['Financial Autonomy']['Type'] = OverviewController::getVariationType($previous_financialAutonomy, $financialAutonomy, $period);
        $financesKPIs['Financial Autonomy']['Difference'] = OverviewController::getVariation($previous_financialAutonomy, $financialAutonomy, $period);

        //Account Receivable
        $accountsReceivable = $bs['Ativo']['Ativo corrente']['Clientes'] 
                                + $bs['Ativo']['Ativo corrente']['Outros créditos a receber'] 
                                + $bs['Ativo']['Ativo não corrente']['Créditos a receber'];
        
        $previous_accountsReceivable = 0;

        if ($period > 1) {
            $previous_accountsReceivable = $previous_bs['Ativo']['Ativo corrente']['Clientes'] 
                                            + $previous_bs['Ativo']['Ativo corrente']['Outros créditos a receber'] 
                                            + $previous_bs['Ativo']['Ativo não corrente']['Créditos a receber'];
        }

        $financesKPIs['Accounts Receivable']['Value'] = number_format($accountsReceivable, 2, ',', ' ');
        $financesKPIs['Accounts Receivable']['Type'] = OverviewController::getVariationType($previous_accountsReceivable, $accountsReceivable, $period);
        $financesKPIs['Accounts Receivable']['Difference'] = OverviewController::getVariation($previous_accountsReceivable, $accountsReceivable, $period);

        //Account Payable
        $accountsPayable = $bs['Capital Próprio e Passivo']['Passivo']['Passivo corrente']['Fornecedores']
                            + $bs['Capital Próprio e Passivo']['Passivo']['Passivo corrente']['Outras dívidas a pagar']
                            + $bs['Capital Próprio e Passivo']['Passivo']['Passivo não corrente']['Outras dívidas a pagar'];

        $previous_accountsPayable = 0;

        if ($period > 1) {
            $previous_accountsPayable = $previous_bs['Capital Próprio e Passivo']['Passivo']['Passivo corrente']['Fornecedores']
                                + $previous_bs['Capital Próprio e Passivo']['Passivo']['Passivo corrente']['Outras dívidas a pagar']
                                + $previous_bs['Capital Próprio e Passivo']['Passivo']['Passivo não corrente']['Outras dívidas a pagar'];
        }

        $financesKPIs['Accounts Payable']['Value'] = number_format($accountsPayable, 2, ',', ' ');
        $financesKPIs['Accounts Payable']['Type'] = OverviewController::getVariationType($previous_accountsPayable, $accountsPayable, $period);
        $financesKPIs['Accounts Payable']['Difference'] = OverviewController::getVariation($previous_accountsPayable, $accountsPayable, $period);


        //Current Ratio = Current assets / Current liabilities
        $currentLiabilities = $bs['Capital Próprio e Passivo']['Total do Passivo corrente'];
        $currentAssets = $bs['Ativo']['Total do Ativo corrente'];
        $currentRatio = $currentAssets / $currentLiabilities;

        $previous_currentRatio = 0;

        if ($period > 1) {
            $previous_currentLiabilities = $previous_bs['Capital Próprio e Passivo']['Total do Passivo corrente'];
            $previous_currentAssets = $previous_bs['Ativo']['Total do Ativo corrente'];
            $previous_currentRatio = $previous_currentAssets / $previous_currentLiabilities;
        }

        $financesKPIs['Current Ratio']['Value'] = number_format($currentRatio, 2, ',', ' ');
        $financesKPIs['Current Ratio']['Type'] = OverviewController::getVariationType($previous_currentRatio, $currentRatio, $period);
        $financesKPIs['Current Ratio']['Difference'] = OverviewController::getVariation($previous_currentRatio, $currentRatio, $period);


        //Working Capital = Current assets - Current liabilities
        $workingCapital = $currentAssets - $currentLiabilities;

        $previous_workingCapital = 0;

        if ($period > 1) {
            $previous_workingCapital = $previous_currentAssets - $previous_currentLiabilities;
        }

        $financesKPIs['Working Capital']['Value'] = number_format($workingCapital, 2, ',', ' ');
        $financesKPIs['Working Capital']['Type'] = OverviewController::getVariationType($previous_workingCapital, $workingCapital, $period);
        $financesKPIs['Working Capital']['Difference'] = OverviewController::getVariation($previous_workingCapital, $workingCapital, $period);

        //Basic Earning Power = Earnings Before Interest and Taxes (EBIT)/Total Assets
        $earningPower = ($ebit / $totalAssets) * 100;

        $previous_earningPower = 0;

        if ($period > 1) {
            $previous_earningPower = ($previous_ebit / $previous_totalAssets) * 100;
        }

        $financesKPIs['Earning Power']['Value'] = number_format($earningPower, 2, ',', ' ');
        $financesKPIs['Earning Power']['Type'] = OverviewController::getVariationType($previous_earningPower, $earningPower, $period);
        $financesKPIs['Earning Power']['Difference'] = OverviewController::getVariation($previous_earningPower, $earningPower, $period);

        //Net Profit 12 Months
        $netIncome_12M = array_map(function ($month) { return $month['net_income']; }, $profit_loss_statement['month']);
        $financesKPIs['Net Profit ChartInfo'] = implode (", ", $netIncome_12M);

        //Return on Assets 12Months = Net Income / Total Assets
        $assets_12M = array_map(function ($month) { return $month['Ativo']['Total do Ativo']; }, $balance_sheet['month']);
        $returnOnAssets = array_map(function ($monthAsset, $monthNetIncome) { return $monthNetIncome / $monthAsset; }, $assets_12M, $netIncome_12M);
        $financesKPIs['Return on Assets ChartInfo'] = implode (", ", $returnOnAssets);
        $financesKPIs['Profit and Loss Statement'] = $pls;

        //Period
        $financesKPIs['period'] = $period;

        return $financesKPIs;
    }
}
