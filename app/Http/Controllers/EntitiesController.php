<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JasminService;

class EntitiesController extends Controller
{

    private function topProducts ($transactions, $nif, $all_inventory, $type) {

        $top_inventory = [];

        foreach ($transactions as $transaction) {
            
            if ($type == 'sale' && $transaction->buyerCustomerPartyTaxId == $nif) {

                foreach ($transaction->documentLines as $transaction_info) {
                    if (array_key_exists($transaction_info->salesItem, $top_inventory)){
                        $top_inventory[$transaction_info->salesItem]->amountSold[0] += $transaction_info->quantity;
                    }
                    else {
                        $top_inventory[$transaction_info->salesItem] = $all_inventory[$transaction_info->salesItem];
                        $top_inventory[$transaction_info->salesItem]->amountSold[0] = $transaction_info->quantity;
                    }
                }
            }

            else if ($type == 'purchase' && $transaction->accountingPartyTaxId == $nif) {

                foreach ($transaction->documentLines as $transaction_info) {                        
                    if (array_key_exists($transaction_info->purchasesItem, $top_inventory)){
                        $top_inventory[$transaction_info->purchasesItem]->amountBought[0] += $transaction_info->quantity;
                    }
                    else {
                        $top_inventory[$transaction_info->purchasesItem] = $all_inventory[$transaction_info->purchasesItem];
                        $top_inventory[$transaction_info->purchasesItem]->amountBought[0] = $transaction_info->quantity;
                    }
                }
            }
        }

        if ($type == 'sale') {
            usort($top_inventory, function($a, $b) {return $b->amountSold[0] - $a->amountSold[0];});
        }
        else if ($type == 'purchase') {
            usort($top_inventory, function($a, $b) {return $b->amountBought[0] - $a->amountBought[0];});
        }        

        return array_slice($top_inventory, 0, 5, true);

    }
    
    public function supplier($nif, JasminService $service)
    {
        $cache_data = $service->getInfoInCache(['purchases', 'suppliers', 'inventory']);
        $top_inventory = $this->topProducts($cache_data['purchases'], $nif, $cache_data['inventory'], 'purchase');
        $name = preg_split('/[-,]/', $cache_data['suppliers'][$nif]->name)[0];

        return view('pages.entity', [
            'transactions' => $cache_data['purchases'],
            'info' => $cache_data['suppliers'][$nif],
            'name' => strtolower($name),
            'type' => 'Supplier',
            'top_inventory' => $top_inventory
        ]);
    }

    public function consumer($nif, JasminService $service)
    {
        $cache_data = $service->getInfoInCache(['sales', 'clients', 'inventory']);
        $top_inventory = $this->topProducts($cache_data['sales'], $nif, $cache_data['inventory'], 'sale');
        $name = preg_split('/[-,]/', $cache_data['clients'][$nif]->name)[0];

        return view('pages.entity', [
            'transactions' => $cache_data['sales'],
            'info' => $cache_data['clients'][$nif],
            'name' => strtolower($name),
            'type' => 'Consumer',
            'top_inventory' => $top_inventory
        ]);
    }
}
