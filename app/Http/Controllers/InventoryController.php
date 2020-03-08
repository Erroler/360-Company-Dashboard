<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JasminService;
use App\Services\SaftService;

class InventoryController extends Controller
{
    static function getInventoryKpis ($inventory, $period) {

        $inventory_value = 0;
        $inventory_value_begin = 0;
        $sales = 0;

        foreach ($inventory as $product) {

            // inventory value for the whole year or for the last trimester is the same
            if ($period == 0) {
                $inventory_value += $product->stock * $product->purchasePrice;
                $sales = $product->amountSold[0] * $product->salePrice;
            }
            else {
                // inventory value takes in account not only the products bought and sold in the trimester but in the previous trimester too
                $index = 1;
                while ($period >= $index && $index <= 4) {
                    $inventory_value += 
                        ($product->amountBought[$index] - $product->amountSold[$index]) * $product->purchasePrice;

                    $sales += $product->amountSold[$index] * $product->salePrice;

                    // in the first trimester, inventory value in the begining is 0
                    if ($period != 1) {
                        $inventory_value_begin += 
                            ($product->amountBought[$index - 1] - $product->amountSold[$index - 1]) * $product->purchasePrice;
                    }

                    $index++;
                }
            }
        }

        $avg_inventory = ($inventory_value_begin + $inventory_value) / 2;
        $inventory_turnover = 0;

        if ($avg_inventory != 0) {
            $inventory_turnover = $sales / $avg_inventory;
        }

        $kpis = [];
        $kpis['Inventory Turnover'] = number_format($inventory_turnover, 2, ',', ' ');
        $kpis['Average Inventory Period'] = number_format(($period == 0 ? 365 : 91) / $inventory_turnover, 2, ',', ' ');;
        $kpis['Inventory Value'] = number_format($inventory_value, 2, ',', ' ');

        return $kpis;
    }

    private function getMostProfitableProducts($inventory, $period) { 

        $inventory = array_filter($inventory, function ($product) use ($period) {
            return $product->amountSold[$period] != 0;
        });

        usort($inventory, function($a, $b) use ($period) {
            return $b->amountSold[$period] * ($b->salePrice - $b->purchasePrice) - $a->amountSold[$period] * ($a->salePrice - $a->purchasePrice);
        });

        return array_slice($inventory, 0, 5, true);
    }

    // Serves the Inventory index page.
    public function index(JasminService $service,SaftService $saftService, $period = 0)
    {
        $cache_data = $service->getInfoInCache(['inventory', 'assortments']);
        $top_selling_products = SalesController::getTopSellingProducts($cache_data['inventory'], $period);
        $most_profitable_products = $this->getMostProfitableProducts($cache_data['inventory'], $period);

        $assortments_lbls = array_keys($cache_data['assortments']);
        $assortments_values = array_column(array_column($cache_data['assortments'], 'inventoryAmount'), $period);

        $kpis = InventoryController::getInventoryKpis($cache_data['inventory'], $period);
        
        return view('pages.inventory', [
            'top_selling_products' => $top_selling_products, 
            'most_profitable_products' => $most_profitable_products,
            'inventory_per_category_lbls' => implode(", ", $assortments_lbls),
            'inventory_per_category_values' => implode(", ", $assortments_values),
            'kpis' => $kpis,
            'period' => $period
        ]);
    }

    private function topProductConsumers($transactions, $product_itemKey) {

        $top_consumers = [];

        foreach ($transactions as $transaction) {
            foreach ($transaction->documentLines as $transaction_info) {
                if ($transaction_info->salesItem === $product_itemKey) {
                    if (array_key_exists($transaction->buyerCustomerPartyName, $top_consumers)) {
                        $top_consumers[$transaction->buyerCustomerPartyName]['quantity'] += $transaction_info->quantity;
                        $top_consumers[$transaction->buyerCustomerPartyName]['total'] += $transaction_info->lineExtensionAmountAmount;
                    }
                    else {
                        $top_consumers[$transaction->buyerCustomerPartyName] = [
                            'taxID' => $transaction->buyerCustomerPartyTaxId,
                            'quantity' => $transaction_info->quantity,
                            'total' => $transaction_info->lineExtensionAmountAmount
                        ];
                    }
                }
            }
        }

        uasort($top_consumers, function($a, $b) {return $b['total'] - $a['total'];});
        return array_slice($top_consumers, 0, 5, true);
    }

    public function product(JasminService $service, $itemKey)
    {
        $inventory = $service->getInfoInCache(['inventory'])['inventory'];

        // Abort if product does not exist
        if (!array_key_exists($itemKey, $inventory)) abort(404);

        // Get Consumers who most bought this product
        $top_consumers = $this->topProductConsumers($service->getInfoInCache(['sales'])['sales'], $itemKey);

        // Get product info
        $productInfo = $inventory[$itemKey];

        // Get product image
        $file_path = $service->get_image($productInfo->image);
        return view('pages.product', ['product' => $productInfo, 'file_path' => $file_path, 'top_consumers' => $top_consumers]);
    }

    public function search()
    {
        return view('pages.inventory_search');
    }

    public function getKPIs(SaftService $service, $period) {
        $profit_loss_statement = $service->get('profit_loss_statement');
        $balance_sheet = $service->get('balance_sheet');

        switch ($period) {
            case 0://All Year
                $pls = $profit_loss_statement['all_year'];
                $bs = $balance_sheet['all_year'];
                $beginningInventory = $balance_sheet['month']['1']['Ativo']['Ativo corrente']['Inventários'];
                $endingInventory = $balance_sheet['month']['12']['Ativo']['Ativo corrente']['Inventários'];
                break;

            case 1: case 2: case 3: case 4: //Trimester $period
                $pls = $profit_loss_statement['trimester'][$period];
                $bs = $balance_sheet['trimester'][$period];
                $beginningInventory = $balance_sheet['month'][$period*3-2]['Ativo']['Ativo corrente']['Inventários'];
                $endingInventory = $balance_sheet['month'][$period*3]['Ativo']['Ativo corrente']['Inventários'];
                break;
        }
        
        $sales = $pls['revenues']['Vendas e serviços prestados'];

        //KPIS:

        //inventory Value
        $inventoryValue = $bs['Ativo']['Ativo corrente']['Inventários'];
        $inventoryKPIs['Inventory Value'] = number_format(abs($inventoryValue), 2, ',', ' ');
        
        //Inventory Turnover
        $averageInventory = ($beginningInventory + $endingInventory) / 2;
        $inventoryTurnover = ($averageInventory != 0) ? $sales / $averageInventory : 0;
        $inventoryKPIs['Inventory Turnover'] = number_format(abs($inventoryTurnover), 2, ',', ' ');
        
        //Average Inventory Period
        $averageInventoryPeriod = ($period == 0 ? 365 : 92) / $inventoryTurnover;
        $inventoryKPIs['Average Inventory Period'] = number_format(abs($averageInventoryPeriod), 2, ',', ' ');

        //Period
        $inventoryKPIs['period'] = $period;

        return $inventoryKPIs;
    }
}
