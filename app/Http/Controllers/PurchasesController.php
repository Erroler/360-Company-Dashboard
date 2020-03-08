<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JasminService;

class PurchasesController extends Controller
{
    static function getPurchasesKpis($inventory, $period) {

        $purchases = 0;
        $previous_purchases = 0;
        $purchases_month = array(1 => 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

        foreach ($inventory as $product) {

            $purchases += $product->amountBought[$period] * $product->purchasePrice;

            if ($period > 1) {
                $purchases += $product->amountBought[$period - 1] * $product->purchasePrice;
            }

            foreach($product->amountBoughtMonth as $index => $month) {
                $purchases_month[$index] += $month * $product->purchasePrice;
            }
        }

        $kpis = [];
        $kpis['Total Purchases']['Value'] = number_format($purchases, 2, ',', ' ');
        $kpis['Purchases ChartInfo'] = implode (", ", $purchases_month);

        $kpis['Total Purchases']['Type'] = OverviewController::getVariationType($previous_purchases, $purchases, $period);
        $kpis['Total Purchases']['Difference'] = OverviewController::getVariation($previous_purchases, $purchases, $period);


        return $kpis;
    }

    static function getTopSuppliers($suppliers, $period) {

        $suppliers = array_filter($suppliers, function ($supplier) use ($period) {
            return $supplier->totalTransactions[$period] != 0;
        });

        usort($suppliers, function($a, $b) use ($period) {return $b->totalTransactions[$period] - $a->totalTransactions[$period];});

        return array_slice($suppliers, 0, 9, true);
    }


    // Serves the Purchases index page.
    public function index(JasminService $service, $period = 0)
    {
        $cache_data = $service->getInfoInCache(['inventory', 'suppliers', 'assortments']);
        $top_suppliers = PurchasesController::getTopSuppliers($cache_data['suppliers'], $period);

        $assortments_lbls = array_keys($cache_data['assortments']);
        $assortments_values = array_column(array_column($cache_data['assortments'], 'purchasesAmount'), $period);
        $kpis = PurchasesController::getPurchasesKpis($cache_data['inventory'], $period);

        return view('pages.purchases',  [
            'top_suppliers' => $top_suppliers,
            'purchases_per_product_group_lbls' => implode(", ", $assortments_lbls),
            'purchases_per_product_group_values' => implode(", ", $assortments_values),
            'kpis' => $kpis,
            'period' => $period
        ]);
    }

    public function search()
    {
        return view('pages.purchases_search');
    }
}
