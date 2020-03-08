<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Services\JasminService;
use App\Services\SaftService;

class OverviewController extends Controller
{
    static function getVariationType ($previous, $current, $period) {

        if ($period <= 1)
            return '';

        if (round($previous, 2) > round($current, 2))
            return 'decrease';
        else if (round($previous, 2) < round($current, 2))
            return 'increase';
        else
            return 'maintain';
    }

    static function getVariation ($previous, $current, $period) {
        if ($period <= 1)
            return '';
        else if ($previous == 0)
            return '100';
        else
            return number_format(($current - $previous) / $previous * 100, 0, ',', ' ');
    }

    /**
     * Serves the overview page.
     */
    public function index(JasminService $service, SaftService $saftService, $period = 0)
    {  
        $cache_data = $service->getInfoInCache(['inventory', 'clients']);
        $top_selling_products = SalesController::getTopSellingProducts($cache_data['inventory'], $period);
        $top_clients = SalesController::getTopClients($cache_data['clients'], $period);

        $saft_kpis = OverviewController::getKPIs($saftService, $period);
        $sales_kpis = SalesController::getSalesKpis($cache_data['inventory'], $period);
        $inventory_kpis = InventoryController::getInventoryKpis($cache_data['inventory'], $period);
        
        return view('pages.overview', [
            'top_selling_products' => $top_selling_products,
            'top_clients' => $top_clients, 
            'saft_kpis' => $saft_kpis,
            'sales_kpis' => $sales_kpis,
            'inventory_kpis' => $inventory_kpis,
            'period' => $period
        ]);
    }

    protected function getKPIs(SaftService $service, $period) {
        $profit_loss_statement = $service->get('profit_loss_statement');
        $balance_sheet = $service->get('balance_sheet');

        switch ($period) {
            case 0://All Year
                $pls = $profit_loss_statement['all_year'];
                $bs = $balance_sheet['all_year'];
                $beginningInventory = $balance_sheet['month']['1']['Ativo']['Ativo corrente']['Inventários'];
                $endingInventory = $balance_sheet['month']['12']['Ativo']['Ativo corrente']['Inventários'];
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
        
        //Financial Autonomy = Capitais próprios/Ativo líquido
        $shareholderEquity = $bs['Capital Próprio e Passivo']['Total do Capital Próprio'];
        $totalLiabilities = $bs['Capital Próprio e Passivo']['Total do Passivo'];  // O montante total do activo líquido da sociedade tem de ser sempre igual ao montante total do seu passivo.
        $financialAutonomy = $shareholderEquity/$totalLiabilities;

        $previous_financialAutonomy = 0;

        if ($period > 1) {
            $previous_shareholderEquity = $previous_bs['Capital Próprio e Passivo']['Total do Capital Próprio'];
            $previous_totalLiabilities = $previous_bs['Capital Próprio e Passivo']['Total do Passivo']; //activo líquido = total do passivo.
            $previous_financialAutonomy = $previous_shareholderEquity/$previous_totalLiabilities;
        }

        $overviewKPIs['Financial Autonomy']['Value'] = number_format($financialAutonomy, 2, ',', ' ');
        $overviewKPIs['Financial Autonomy']['Type'] = OverviewController::getVariationType($previous_financialAutonomy, $financialAutonomy, $period);
        $overviewKPIs['Financial Autonomy']['Difference'] = OverviewController::getVariation($previous_financialAutonomy, $financialAutonomy, $period);

        
        //Total Assets
        $totalAssets = $bs['Ativo']['Total do Ativo'];
        $previous_totalAssets = 0;

        if ($period > 1) 
            $previous_totalAssets = $previous_bs['Ativo']['Total do Ativo'];

        $overviewKPIs['Total Assets']['Value'] = number_format($totalAssets, 2, ',', ' ');
        $overviewKPIs['Total Assets']['Type'] = OverviewController::getVariationType($previous_totalAssets, $totalAssets, $period);
        $overviewKPIs['Total Assets']['Difference'] = OverviewController::getVariation($previous_totalAssets, $totalAssets, $period);

        //Period
        $overviewKPIs['period'] = $period;

        return $overviewKPIs;
    }
}
