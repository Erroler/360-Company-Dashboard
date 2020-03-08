<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JasminService;

class SalesController extends Controller
{

    static function getSalesKpis($inventory, $period) {

        $net_sales = 0;
        $costs_of_goods_sold = 0;

        $previous_net_sales = 0;
        $previous_costs_of_goods_sold = 0;

        $net_sales_month = array(1 => 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        $costs_of_goods_sold_month = array(1 => 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

        foreach ($inventory as $product) {

            $net_sales += $product->amountSold[$period] * $product->salePrice;
            $costs_of_goods_sold += $product->amountSold[$period] * $product->purchasePrice;

            if ($period > 1) {
                $previous_net_sales += $product->amountSold[$period - 1] * $product->salePrice;
                $previous_costs_of_goods_sold += $product->amountSold[$period - 1] * $product->purchasePrice;
            }

            foreach($product->amountSoldMonth as $index => $month) {
                $net_sales_month[$index] += $month * $product->salePrice;
                $costs_of_goods_sold_month[$index] += $month * $product->purchasePrice;
            }
        }

        //-------- Handle the selected period calculations --------//

        $gross_profit_margin = 0;
        if ($net_sales != 0) {
            $gross_profit_margin = ($net_sales - $costs_of_goods_sold) / $net_sales;
        }

        $kpis = [];
        $kpis['Net Sales']['Value'] = number_format($net_sales, 2, ',', ' ');
        $kpis['Cost of Goods Sold']['Value'] = number_format($costs_of_goods_sold, 2, ',', ' ');
        $kpis['Gross Profit Margin']['Value'] = number_format($gross_profit_margin * 100, 2, ',', ' ');
        $kpis['Net Sales ChartInfo'] = implode (", ", $net_sales_month);
        $kpis['Cost of Goods Sold ChartInfo'] = implode (", ", $costs_of_goods_sold_month);

        //--------------------------------------------------------//
        //-------- Handle the previous period calculations --------//

        $kpis['Net Sales']['Type'] = '';
        $kpis['Cost of Goods Sold']['Type'] = '';

        $previous_gross_profit_margin = 0;

        if ($previous_net_sales != 0) {
            $previous_gross_profit_margin = ($previous_net_sales - $previous_costs_of_goods_sold) / $previous_net_sales;
        }

        $kpis['Net Sales']['Type'] = OverviewController::getVariationType($previous_net_sales, $net_sales, $period);
        $kpis['Cost of Goods Sold']['Type'] = OverviewController::getVariationType($previous_costs_of_goods_sold, $costs_of_goods_sold, $period);
        $kpis['Gross Profit Margin']['Type'] = 'absolute';

        $kpis['Net Sales']['Difference'] = OverviewController::getVariation($previous_net_sales, $net_sales, $period);
        $kpis['Cost of Goods Sold']['Difference'] = OverviewController::getVariation($previous_costs_of_goods_sold, $costs_of_goods_sold, $period);
        $kpis['Gross Profit Margin']['Difference'] = number_format($net_sales - $costs_of_goods_sold, 2, ',', ' ');

        //--------------------------------------------------------//

        return $kpis;
    }

    static function getTopSellingProducts($inventory, $period) {

        $inventory = array_filter($inventory, function ($product) use ($period) {
            return $product->amountSold[$period] != 0;
        });

        usort($inventory, function($a, $b) use ($period) {return $b->amountSold[$period] - $a->amountSold[$period];});

        return array_slice($inventory, 0, 5, true);
    }

    static function getTopClients($clients, $period) {

        $clients = array_filter($clients, function ($client) use ($period) {
            return $client->totalTransactions[$period] != 0;
        });

        usort($clients, function($a, $b) use ($period) {return $b->totalTransactions[$period] - $a->totalTransactions[$period];});

        return array_slice($clients, 0, 5, true);
    }

    private function getOrderBackLog($sales, $clients, $period) {

        $sales = array_filter($sales, function ($item) use ($clients, $period) {
            
            $item->client = preg_split('/[-,]/', $clients[$item->buyerCustomerPartyTaxId]->name)[0];

            $document_period = intdiv(date('m', strtotime($item->documentDate)) - 1, 3) + 1;
            $unloading_period = intdiv(date('m', strtotime($item->unloadingDateTime)) - 1, 3) + 1;

            $item->documentDate = date('d/m/Y', strtotime($item->documentDate));
            $item->unloadingDate = date('d/m/Y', strtotime($item->unloadingDateTime));

            return ($period == 0 && time() < \strtotime($item->unloadingDateTime)
                || ($period == $document_period && $period != $unloading_period));
        });

        usort($sales, function($a, $b) {return strtotime($b->unloadingDateTime) - strtotime($a->unloadingDateTime);});

        return array_slice($sales, 0, 8, true);

    }

    // Serves the Sales index page.
    public function index(JasminService $service, $period = 0)
    {
        $cache_data = $service->getInfoInCache(['inventory', 'clients', 'assortments', 'sales']);
        $top_selling_products = SalesController::getTopSellingProducts($cache_data['inventory'], $period);
        $top_clients = SalesController::getTopClients($cache_data['clients'], $period);
        $backlog = $this->getOrderBackLog($cache_data['sales'], $cache_data['clients'], $period);

        $assortments_lbls = array_keys($cache_data['assortments']);
        $assortments_values = array_column(array_column($cache_data['assortments'], 'salesAmount'), $period);

        $kpis = SalesController::getSalesKpis($cache_data['inventory'], $period);

        return view('pages.sales', [
            'top_selling_products' => $top_selling_products,
            'top_clients' => $top_clients,
            'sales_per_product_group_lbls' => implode(", ",$assortments_lbls),
            'sales_per_product_group_values' => implode(", ",$assortments_values),
            'kpis' => $kpis,
            'backlog' => $backlog,
            'period' => $period
        ]);
    }

    public function search()
    {
        return view('pages.sales_search');
    }
}
