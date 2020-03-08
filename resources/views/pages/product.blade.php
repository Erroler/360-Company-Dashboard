@extends('layouts.dashboard')

@section('title', 'VidalTech - Product ' . $product->itemKey)

@section('dashboard_content')
    <div class="page-header">
        <h1 class="h1 page-title">{{ $product->description }}</h1>
        <h6 class="text-muted mt-auto ml-3">[{{ $product->assortment }}]</small>
    </div>
    <div id="Info" class="row">
        <div class="col-4 pl-0">
            <img src="{{ asset($file_path) }}" class="card img-thumbnail" style="width: 100%; height: 14rem; object-fit: contain">
        </div>
        <div class="col card ml-2 p-4">
            <div class="my-auto">
                <div class="row">
                    <div class="h6 col-2 pr-0 pt-1 text-muted">Brand: </div> 
                    <span class="col pl-0">{{ $product->brand }}</span> 
                </div>
                <div class="row">
                    <div class="h6 col-2 pr-0 pt-1 text-muted">Product Key: </div> 
                    <span class="col pl-0">{{ $product->itemKey }}</span>
                </div>
                <div class="row">
                    <div class="h6 col-2 pr-0 pt-1 text-muted">Description: </div> 
                    <div class="col pl-0">{{ str_replace('"','',$product->complementaryDescription) }}</div>
                </div>
                <div class="row">
                    <div class="h6 col-2 pr-0 pt-1 text-muted">Current Stock: </div> 
                    <div class="col pl-0">{{ $product->stock }} Units</div>
                </div>
                <div class="row">
                    <div class="h6 col-2 pr-0 pt-1 text-muted">Purchase Price: </div> 
                    <div class="col pl-0">{{ $product->purchasePrice }}€</div>
                </div>
                <div class="row">
                    <div class="h6 col-2 pr-0 pt-1 text-muted">Sale Price: </div> 
                    <div class="col pl-0">{{ $product->salePrice }}€</div>
                </div>
            </div>
        </div>
    </div>  
    <div class="row">
        <div class="col-5 card mr-4">
            <div class="h4 mb-3 pt-5 text-muted text-center">Information per Quarter</div>
            <table class="table card-table table-vcenter my-auto">
                <tbody>
                    <tr>
                        <th class="text-capitalize">Quarter</th>
                        <th class="text-capitalize">Bought Units</th>
                        <th class="text-capitalize">Sold Units</th>
                    </tr>
                    <tr class="py-4">
                        <td class="text-muted text-nowrap">1st</td>
                        <td class="text-muted text-nowrap">{{$product->amountBought[1]}}</td>
                        <td class="text-muted text-nowrap">{{$product->amountSold[1]}}</td>  
                    </tr>
                    <tr class="py-4">
                        <td class="text-muted text-nowrap">2nd</td>   
                        <td class="text-muted text-nowrap">{{$product->amountBought[2]}}</td>
                        <td class="text-muted text-nowrap">{{$product->amountSold[2]}}</td>  
                    </tr>
                    <tr class="py-4">
                        <td class="text-muted text-nowrap">3rd</td>
                        <td class="text-muted text-nowrap">{{$product->amountBought[3]}}</td>
                        <td class="text-muted text-nowrap">{{$product->amountSold[3]}}</td>   
                    </tr>
                    <tr class="py-4">
                        <td class="text-muted text-nowrap">4th</td>
                        <td class="text-muted text-nowrap">{{$product->amountBought[4]}}</td>
                        <td class="text-muted text-nowrap">{{$product->amountSold[4]}}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="col card ml-1 pb-2">
            <div class="h4 mb-3 pt-5 text-muted text-center">Top Product Consumers</div>
            <table class="table card-table table-vcenter">
                <tbody>
                    <tr>
                        <th class="text-capitalize">Name</th>
                        <th class="text-capitalize">Tax ID</th>
                        <th class="text-capitalize">Bought Units</th>
                        <th class="text-capitalize">Total Product Purchases</th>
                    </tr>
                    @foreach($top_consumers as $name => $info)
                        <tr class="py-4">
                            <td class="text-muted text-nowrap"><a href={{ route('entity.consumer', ['nif' => $info['taxID']])}}>{{ preg_split('/[-,.]/', $name)[0] }}</a></td>
                            <td class="text-muted text-center text-nowrap">{{ $info['taxID'] }}</td>
                            <td class="text-muted text-center text-nowrap">{{ $info['quantity'] }}</td>
                            <td class="text-muted text-center text-nowrap">{{ number_format($info['total'], 2, ',', ' ') }}€</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if (count($top_consumers) == 0)
                <div class="h5 mt-3 mb-5 text-center">No data to display</div>
            @endif
        </div>
    </div>


@endsection