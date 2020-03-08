<?php

namespace App\Services;

use Storage;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class JasminService
{

    // OAuth bearer token used to authenticate as a client in API requests to Jasmin.
    protected $bearer_token;

    // Time (minutes) the bearer token is cached for.
    protected $bearer_token_cache_time = 600;

    // API Base Url
    protected $api_url = "https://my.jasminsoftware.com/api/224837/224837-0001/";

    // Minutes that information taken from jasmin is cached for
    protected $time_cache = 14400;


    // Authenticates with the Jasmin API if needed.
    function __construct() {
        \Cache::forget('jasminToken');
        // \Cache::forget('inventory');
        // \Cache::forget('clients');
        // \Cache::forget('suppliers');
        // \Cache::forget('sales');
        // \Cache::forget('purchases');
        // \Cache::forget('assortments');


        $this->bearer_token = \Cache::remember('jasminToken', $this->bearer_token_cache_time * 60, function () {
            $client = new Client([
                'timeout'  => 10.0,
            ]);
    
            $response = $client->request('POST', 'https://identity.primaverabss.com/core/connect/token', [
                'auth' => [ env('JASMIN_APPLICATION_ID'), env('JASMIN_APPLICATION_SECRET') ],
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'scope' => 'application',
                ]
            ]);
            return json_decode($response->getBody())->access_token;
        });
    }

    // Builds the Guzzle\Client object that will be used to make requests.
    private function build_guzzle_client() {
        return new Client([
            'base_uri' => $this->api_url,
            'timeout'  => 10.0,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->bearer_token,        
                'Accept'        => 'application/json',
            ]
        ]);
    }

    /*  gets inventory from jasmin and saves it in cache
    [
        'itemKey' => 'A001',
        'description' => 'ASUS VG248QE',
        'complementaryDescription' => string,
        'brand' => 'ASUS',
        'purchase' => 0,00,
        'assortment' => string,
        'sale' => 0,00,
        'amount' => 0
    ]
    */
    private function storeProductsCache($assortments) {
        
        $inventory_rspnse = $this->query_jasmin('GET', 
            'salesCore/salesItems/odata?' .
            '&$select=ItemKey,PriceListLines/PriceAmountAmount,PriceListLines/PriceList' .
            '&$expand=PriceListLines');

        $inventory_price = [];

        foreach($inventory_rspnse->items as $item) {
            try {
                $inventory_price[$item->itemKey] = $item->priceListLines[0]->priceAmountAmount;
            }
            catch (\Exception $e){

            }
        }

        $inventory_rspnse = $this->query_jasmin('GET', 
            'materialsCore/materialsItems/odata?' . 
            '&$select=ItemKey,Description,ComplementaryDescription,Brand,ComplementaryDescription,Assortment,Brand,Image,' .
            'MaterialsItemWarehouses/StockBalance,MaterialsItemWarehouses/LastUnitCostAmount' . 
            '&$expand=MaterialsItemWarehouses');

        $inventory = [];

        // Format
        foreach ($inventory_rspnse->items as $item) {
            $item->purchasePrice = $item->materialsItemWarehouses[0]->lastUnitCostAmount;
            $item->stock = $item->materialsItemWarehouses[0]->stockBalance;
            unset($item->materialsItemWarehouses);
            $item->amountSold = array(0, 0, 0, 0, 0);
            $item->amountSoldMonth = array(1 => 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            $item->amountBought = array(0, 0, 0, 0, 0);
            $item->amountBoughtMonth = array(1 => 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            $item->salePrice = $inventory_price[$item->itemKey];
            $inventory[$item->itemKey] = $item;

            if ($item->assortment == null) {
                $item->assortment = "OTHERS";
            }
            
            $assortments[$item->assortment]->inventoryAmount[0] += $item->stock;

        }

        return $inventory;
    }

     /* 
        nif => [
            'name' => 'FNAC',
            'companyTaxID' => '503952230',
            'streetName' => 'R. Carlos Alberto da Mota Pinto',
            'buildingNumber' => string,
            'postalZone' => string,
            'cityName' => string,
            'country' => string,
            'telephone' => '219404700',
            'email' => string,
            'website' => string,
            'totalTransactions' => 0
        ]
    */
    private function storeClientsCache() {

        $clients_rspnse =  $this->query_jasmin('GET', 
            'salesCore/customerParties'
        );

        $clients = [];

        // Format
        foreach ($clients_rspnse as $item) {

            $client = new \stdClass();
            $client->name = $item->name;
            $client->companyTaxID = $item->companyTaxID;
            $client->electronicMail = $item->electronicMail;
            $client->telephone = $item->telephone;
            $client->websiteUrl = $item->websiteUrl;
            $client->streetName = $item->streetName;
            $client->buildingNumber = $item->buildingNumber;
            $client->country = $item->countryDescription;
            $client->postalZone = $item->postalZone;
            $client->cityName = $item->cityName;
            $client->currency = $item->currencyDescription;
            $client->totalTransactions = array(0, 0, 0, 0, 0);
            $clients[$item->companyTaxID] = $client;
        }

        return $clients;
    }

    /* gets suppliers from jasmin and saves them in cache
        nif => [
            'name' => 'FNAC',
            'companyTaxID' => '503952230',
            'address' => 'R. Carlos Alberto da Mota Pinto',
            'telephone' => '219404700',
            'email' => string,
            'website' => string,
            'totalTransactions' => 0
        ]
    */
    private function storeSuppliersCache() {

        $suppliers_rspnse =  $this->query_jasmin('GET', 
            'purchasesCore/supplierParties'
        );

        $suppliers = [];

        // Format
        foreach ($suppliers_rspnse as $item) {

            if ($item->companyTaxID == "") {
                continue;
            }

            $supplier = new \stdClass();
            $supplier->name = $item->name;
            $supplier->companyTaxID = $item->companyTaxID;
            $supplier->electronicMail = $item->electronicMail;
            $supplier->telephone = $item->telephone;
            $supplier->websiteUrl = $item->websiteUrl;
            $supplier->streetName = $item->streetName;
            $supplier->buildingNumber = $item->buildingNumber;
            $supplier->postalZone = $item->postalZone;
            $supplier->cityName = $item->cityName;
            $supplier->country = $item->countryDescription;
            $supplier->currency = $item->currency;
            $supplier->totalTransactions = array(0, 0, 0, 0, 0);
            $suppliers[$item->companyTaxID] = $supplier;
        }

        return $suppliers;
    }

    /* gets sales from jasmin and saves them in cache ordered by date
        [
            'buyerCustomerPartyTaxId' => client's nif,
            'documentDate' => timestamp,
            'unloadingDateTime' => timestamp
            'payableAmountAmount' => total value of sale,
            'documentLines' => [
                'salesItem' => itemKey,
                'quantity' => number of items sold
            ]
        ]
    */
    private function storeSalesCache() {
        return Cache::remember('sales', $this->time_cache, function () {
            $t = null;
            $skip = 0;
            while(True) {
                $tmp = $this->query_jasmin('GET', 
                    'billing/invoices/odata?$skip=' . $skip .
                    '&$select=BuyerCustomerPartyName,BuyerCustomerPartyTaxId,DocumentDate,PayableAmountAmount,' .
                    'GrossValueAmount,TaxTotalAmount,DueDate,UnloadingDateTime,LoadingPointAddress,UnloadingStreetName,'.
                    'DocumentLines/SalesItem,DocumentLines/Description,DocumentLines/Quantity,' .
                    'DocumentLines/TaxTotalAmount,DocumentLines/UnitPriceAmount,DocumentLines/LineExtensionAmountAmount,' .
                    'DocumentLines/GrossValueAmount,DocumentLines/DeliveryDate' .
                    '&$expand=DocumentLines' .
                    '&$orderby=DocumentDate'
                );

                if (count($tmp->items) > 0) {
                    if ($t === null)
                        $t = $tmp;
                    else
                        $t->items = array_merge($t->items, $tmp->items);

                    $skip += count($tmp->items);
                }

                if ($tmp->nextPageLink == null)
                    return $t->items;
            }
        });
    }

    /* gets purchases from jasmin and saves them in cache ordered by date
        [
            'accountingPartyTaxId' => supplier's nif,
            'documentDate' => timestamp,
            'payableAmountAmount' => total value of purchase,
            'documentLines' => [
                'purchasesItem' => itemKey,
                'quantity' => number of items purchased
            ]
        ]
    */
    private function storePurchasesCache() {
        return Cache::remember('purchases', $this->time_cache, function () {
            $t = null;
            $skip = 0;
            while(True) {
                $tmp = $this->query_jasmin('GET', 
                    'invoiceReceipt/invoices/odata?$skip=' . $skip .
                    '&$select=AccountingPartyName,AccountingPartyTaxId,DocumentDate,GrossValueAmount,' .
                    'TaxTotalAmount,PayableAmountAmount,DueDate,PaymentTermDescription,' .
                    'DocumentLines/PurchasesItem,DocumentLines/Description,DocumentLines/Quantity,' .
                    'DocumentLines/TaxTotalAmount,DocumentLines/UnitCostAmount,DocumentLines/LineExtensionAmountAmount,' .
                    'DocumentLines/GrossValueAmount,DocumentLines/DeliveryDate' .
                    '&$expand=DocumentLines' .
                    '&$orderby=DocumentDate'
                );
                if (count($tmp->items) > 0) {
                    if ($t === null)
                        $t = $tmp;
                    else
                        $t->items = array_merge($t->items, $tmp->items);

                    $skip += count($tmp->items);
                }

                if ($tmp->nextPageLink == null)
                    return $t->items;
            }
        });
    }

    /*  gets assortments/products' groups from jasmin and saves it in cache
        'name' => [
            'purchasesAmount' => 0,
            'salesAmount' => 0,
            'inventoryAmount' => 0,
        ]
    */
    private function storeAssortmentsCache() {



        $assortments_rspnse = $this->query_jasmin('GET', 
            'businessCore/assortments/odata?' .
            '$select=AssortmentKey'
        );

        $assortments = [];

        foreach($assortments_rspnse->items as $assortment) {
            $assortment->purchasesAmount = array(0, 0, 0, 0, 0);
            $assortment->salesAmount = array(0, 0, 0, 0, 0);
            $assortment->inventoryAmount = array(0, 0, 0, 0, 0);
            $assortments[$assortment->assortmentKey] = $assortment;
            unset($assortment->assortmentKey);
        }

        $assortments["OTHERS"] = new \stdClass();
        $assortments["OTHERS"]->purchasesAmount = array(0, 0, 0, 0, 0);
        $assortments["OTHERS"]->salesAmount = array(0, 0, 0, 0, 0);
        $assortments["OTHERS"]->inventoryAmount = array(0, 0, 0, 0, 0);

        return $assortments;
    }

    public function getInfoInCache($cache_keys) {   
        
        // sales - inventory and clients
        // purchases - inventory and suppliers

        $cache_data = [];

        $get_sales = in_array('sales', $cache_keys);
        $get_inventory = in_array('inventory', $cache_keys) && !Cache::has('inventory');
        $get_clients = in_array('clients', $cache_keys) && !Cache::has('clients');
        $get_purchases = in_array('purchases', $cache_keys);
        $get_suppliers = in_array('suppliers', $cache_keys) && !Cache::has('suppliers');
        $get_assortments = in_array('assortments', $cache_keys) && !Cache::has('assortments');

        $inventory = null;
        $sales = null;
        $clients = null;
        $suppliers = null;
        $purchases = null;
        $assortments = null;

        if ($get_inventory || $get_assortments) {
            $assortments = $this->storeAssortmentsCache();
            $inventory = $this->storeProductsCache($assortments);
        }

        if ($get_clients) {
            $clients = $this->storeClientsCache();
        }

        if ($get_suppliers)
            $suppliers = $this->storeSuppliersCache();

        if ($get_sales)
            $sales = $this->storeSalesCache();

        if ($get_purchases)
            $purchases = $this->storePurchasesCache();
       
        if ($get_inventory || $get_clients || $get_assortments) {

            if (!$get_sales)
                $sales = $this->storeSalesCache();

            foreach($sales as $sale) {

                $month = date('m', strtotime($sale->documentDate)) + 0;
                $period = intdiv($month - 1, 3) + 1;

                if ($get_inventory || $get_assortments) {

                    foreach($sale->documentLines as $product) {
                        $inventory[$product->salesItem]->amountSold[$period] += $product->quantity;
                        $inventory[$product->salesItem]->amountSold[0] += $product->quantity;
                        $inventory[$product->salesItem]->amountSoldMonth[$month] += $product->quantity;

                        $assortments[$inventory[$product->salesItem]->assortment]->salesAmount[0] += $product->quantity;
                        $assortments[$inventory[$product->salesItem]->assortment]->salesAmount[$period] += $product->quantity;
                    }   
                }
    
                if ($get_clients) {
                    $clients[$sale->buyerCustomerPartyTaxId]->totalTransactions[$period] += $sale->payableAmountAmount;
                    $clients[$sale->buyerCustomerPartyTaxId]->totalTransactions[0] += $sale->payableAmountAmount;
                }
            }
        }

        if ($get_inventory || $get_suppliers || $get_assortments) {

            if (!$get_purchases)
                $purchases = $this->storePurchasesCache();
        
            foreach($purchases as $purchase) {

                if ($purchase->accountingPartyTaxId == "") {
                    continue;
                }

                $month = date('m', strtotime($purchase->documentDate)) + 0;
                $period = intdiv($month - 1, 3) + 1;

                if ($get_inventory || $get_assortments) {

                    foreach($purchase->documentLines as $product) {
                        $inventory[$product->purchasesItem]->amountBought[$period] += $product->quantity;
                        $inventory[$product->purchasesItem]->amountBought[0] += $product->quantity;
                        $inventory[$product->purchasesItem]->amountBoughtMonth[$month] += $product->quantity;

                        $assortments[$inventory[$product->purchasesItem]->assortment]->purchasesAmount[$period] += $product->quantity;
                        $assortments[$inventory[$product->purchasesItem]->assortment]->purchasesAmount[0] += $product->quantity;
                    }
                }

                if ($get_suppliers) {
                    $suppliers[$purchase->accountingPartyTaxId]->totalTransactions[0] += $purchase->payableAmountAmount;
                    $suppliers[$purchase->accountingPartyTaxId]->totalTransactions[$period] += $purchase->payableAmountAmount;
                }
            }
        }

        if ($get_inventory || $get_assortments) {

            if (!Cache::has('inventory')) 
                Cache::put('inventory', $inventory, $this->time_cache);

            if (!Cache::has('assortments')) 
                Cache::put('assortments', $assortments, $this->time_cache);
        }
        
        if (in_array('inventory', $cache_keys)) {
            $cache_data['inventory'] = Cache::get('inventory');
        }

        if (in_array('assortments', $cache_keys)) {
            $cache_data['assortments'] = Cache::get('assortments');
        }
        
        if ($get_clients) {
            $cache_data['clients'] = $clients;
            Cache::put('clients', $clients, $this->time_cache);
        }
        else if (in_array('clients', $cache_keys)) {
            $cache_data['clients'] = Cache::get('clients');
        }

        if ($get_sales) {
            $cache_data['sales'] = $sales;
        }

        if ($get_purchases) {
            $cache_data['purchases'] = $purchases;
        }

        if ($get_suppliers) {
            $cache_data['suppliers'] = $suppliers;
            Cache::put('suppliers', $suppliers, $this->time_cache);
        }
        else if (in_array('suppliers', $cache_keys)) {
            $cache_data['suppliers'] = Cache::get('suppliers');
        }

        return $cache_data;
    }

    public function inventory($param) {
        $products = Cache::remember('dt_inventory', $this->time_cache, function() {
            $products = $this->query_jasmin('GET',
                'materialsCore/materialsItems/odata?' . 
                '&$select=ItemKey,Description,Brand,MaterialsItemWarehouses/StockBalance,MaterialsItemWarehouses/LastUnitCostAmount' .
                '&$expand=MaterialsItemWarehouses');

            // Format
            foreach ($products->items as $item) {
                $item->price = $item->materialsItemWarehouses[0]->lastUnitCostAmount;
                $item->stock = $item->materialsItemWarehouses[0]->stockBalance;
                unset($item->materialsItemWarehouses);
            }

            return $products->items;
        });

        return $this->getTable($param, $products);
    }

    public function purchases($param) {
        return $this->getTable($param, $this->storePurchasesCache());
    }

    public function sales($param) {
        return $this->getTable($param, $this->storeSalesCache());
    }

    public function query_jasmin($operation, $query) {
        $client = $this->build_guzzle_client();
        $response = $client->request($operation, $query);
        return json_decode($response->getBody());
    }

    public function get_image($path) {
        $file_path = 'images/' . preg_split("/\//", $path, 7)[5] . '.jpeg';
        if (!file_exists(public_path($file_path))) {
            $client = $this->build_guzzle_client();
            $response = $client->request('GET', preg_split("/\//", $path, 4)[3]);
            file_put_contents(public_path($file_path), $response->getBody()->getContents());
        }
        return $file_path;
    }

    public function getTable($param, $records) {
        return [
            'data' => $records,
            '_' => $param['_']
        ];
    }
}
