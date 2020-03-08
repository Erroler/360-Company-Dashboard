window.addEventListener('DOMContentLoaded', () => {
    setupTables();
});

let createDataTable = (id, columns, createdRow = null, initComplete = null, url = null, orderByCol = [0, 'asc']) => {

	if(!url)
		url = window.location.pathname + '/api';
	if ($(id).length !== 0) {
		let index = 0;
		let columnDefs = [];
		for (let column of columns) {
			if (column['searchable'] === undefined) column['searchable'] = false;
			if (column['orderable'] === undefined) column['orderable'] = false;
			if (column['className'] === undefined) column['className'] = null;
			if (column['render'] === undefined)
				column['render'] = function (data, type, row) {
					return row[column['name']];
				};
            columnDefs.push({
                targets: index,
                name: column['name'],
                searchable: column['searchable'],
                orderable: column['orderable'],
                render: column['render'],
                className: column['className']
            });
            index++;
        }
		
		$(id).DataTable({
			processing: true,
			serverSide: false,
			searchDelay: 500,
			ajax: { url: url },
			order: [orderByCol],
			columnDefs: columnDefs,
			createdRow: createdRow,
			initComplete: initComplete
        });
        
		return true;
    }
    
	return false;
}


let setupTables = () => {
    
    let formatPrice = (number) => {
        return "€ " + number.toFixed(2);
    }

    let renderPrice = (name) => {
        return function (data, type, row) {
            return formatPrice(row[name]);
        }
    }

    let formatUnit = (quantity) => {
        return quantity + ((quantity  == 1 || quantity == -1) ? " Unit" : " Units");
    }

    let renderUnit = (name) => {
        return function (data, type, row) {
            if(type == "display")
                return formatUnit(row[name]);
            return row[name];
        }
    }

    let formatDate = (date) => {
        var options = {year: "numeric", month: "2-digit", day: "2-digit"};
        return (new Date(date)).toLocaleDateString('en-GB', options);
    }

    let renderDate = (name) => {
        return function (data, type, row) {
            if(type == "display")
                return formatDate(row[name]);
            return row[name];
        }
    }

    let formatName = (name) => {
        return name.split(/[-,.]/)[0];
    }

    let renderName = (name) => {
        return function (data, type, row) {
            return formatName(row[name]);
        }
    }

    let formatMoreInfo = (name, entity_type, taxID) => {
        return '<p class="float-left more-info pointer mb-0 ml-1 mr-3"><i class="fe fe-chevron-right fe-lg"></i></p>' + 
           '<a ' + ((taxID != null) ? 'href="/entity/' + entity_type + '/' + taxID : '') + '">' + formatName(name) + '</a>';
    }

    let renderMoreInfo = (name, entity_type, taxID, product_syntax) => {
        return function (data, type, row) {
            if(type == "display")
                return formatMoreInfo(row[name], entity_type, row[taxID]);
            let p = '';
            row['documentLines'].forEach(product => {
                p += ' ' + product[product_syntax];
            });
            console.log(row[name] + p);
            return row[name] + p;
        }
    }

    let formatProductLink = (name) => {
        return '<a href="/inventory/product/' + name + '">' + formatName(name) + '</a>';
    }

    let renderProductLink = (name) => {
        return function (data, type, row) {
            if(type == "display")
                return formatProductLink(row[name]);
            return row[name];
        }
    }

    let showMoreInfo = function (row, fc, ua) {
        content = '<div class="extra-info ml-5 mr-4 my-1">' + 
        '<div class="row">' +
        '<div class="col-2 font-weight-bold">Product</div>' +
        '<div class="col-2 font-weight-bold">Description</div>' +
        '<div class="col-1 font-weight-bold pr-0">' + ((ua === 'unitPriceAmount') ? 'Unit Price' : 'Unit Cost') + '</div>' +
        '<div class="col-1 font-weight-bold">Quantity</div>' +
        '<div class="col font-weight-bold">Payable Amt</div>' +
        '<div class="col font-weight-bold">Gross Val</div>' +
        '<div class="col font-weight-bold">Tax Amt</div>' +
        '<div class="col font-weight-bold">Delivery Date</div>' +
        '</div>';

        row.data()['documentLines'].forEach((item) => {
            content += '<div class="row">'
            content += '<div class="col-2 text-truncate">' + formatProductLink(item[fc]) + '</div>'
            content += '<div class="col-2 text-truncate">' + item['description'] + '</div>'
            content += '<div class="col-1">' + formatPrice(item[ua]) + '</div>'
            content += '<div class="col-1">' + formatUnit(item['quantity']) + '</div>'
            content += '<div class="col">' + formatPrice(item['lineExtensionAmountAmount']) + '</div>'
            content += '<div class="col">' + formatPrice(item['grossValueAmount']) + '</div>'
            content += '<div class="col">' + formatPrice(item['taxTotalAmount']) + '</div>'
            content += '<div class="col">' + formatDate(item['deliveryDate']) + '</div>'
            content += '</div>'
        });
        content += '</div>';
        return content;
    }

    createDataTable(
        '#inventorySearch',                                         /** id */
        [
            { name: 'itemKey', searchable: true, orderable: true, render: renderProductLink('itemKey') }, /** columns */
            { name: 'description', searchable: true },
            { name: 'brand', searchable: true, orderable: true },
            { name: 'price', orderable: true, render: renderPrice('price') },
            { name: 'stock', orderable: true, render: renderUnit('stock') }
        ]
    );

    createDataTable(
        '#purchasesSearch',
        [   
            { name: 'accountingPartyName', searchable: true, orderable: true, render: renderMoreInfo('accountingPartyName', 'supplier', 'accountingPartyTaxId', 'purchasesItem') },
            { name: 'accountingPartyTaxId', searchable: true },
            { name: 'payableAmountAmount', orderable: true, render: renderPrice('payableAmountAmount') },
            { name: 'grossValueAmount', orderable: true, render: renderPrice('grossValueAmount') },
            { name: 'taxTotalAmount', orderable: true, render: renderPrice('taxTotalAmount') },
            { name: 'documentDate', orderable: true, render: renderDate('documentDate') },
            { name: 'dueDate', orderable: true, render: renderDate('dueDate') },
        ],
        null, null, '/purchases/search/api', [5, 'desc']
    );

    createDataTable(
        '#salesSearch',
        [
            { name: 'buyerCustomerPartyName', searchable: true, orderable: true, render: renderMoreInfo('buyerCustomerPartyName', 'consumer', 'buyerCustomerPartyTaxId', 'salesItem') },
            { name: 'buyerCustomerPartyTaxId', searchable: true },
            { name: 'payableAmountAmount', orderable: true, render: renderPrice('payableAmountAmount') },
            { name: 'grossValueAmount', orderable: true, render: renderPrice('grossValueAmount') },
            { name: 'taxTotalAmount', orderable: true, render: renderPrice('taxTotalAmount') },
            { name: 'documentDate', orderable: true, render: renderDate('documentDate') },
            { name: 'dueDate', orderable: true, render: renderDate('dueDate') },
        ],
        null, null, null, [5, 'desc']
    );

    $('#purchasesSearch tbody').on('click', 'tr[role]', function (e) {
        if (e['target'].nodeName === 'A')
            return;

        var tr = $(this).closest('tr');
        var row = $('#purchasesSearch').DataTable().row(tr);
 
        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            $(tr).find('i').addClass('fe-chevron-right');
            $(tr).find('i').removeClass('fe-chevron-down');
            tr.removeClass('shown');
        }
        else {
            // Open this row
            content = showMoreInfo(row, 'purchasesItem', 'unitCostAmount');
            row.child(content).show();
            tr.addClass('shown');
            $(tr).find('i').removeClass('fe-chevron-right');
            $(tr).find('i').addClass('fe-chevron-down');
        }
    });

    $('#salesSearch tbody').on('click', 'tr[role]', function (e) {
        if (e['target'].nodeName === 'A')
            return;

        var tr = $(this).closest('tr');
        var row = $('#salesSearch').DataTable().row(tr);
 
        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            $(tr).find('i').addClass('fe-chevron-right');
            $(tr).find('i').removeClass('fe-chevron-down');
            tr.removeClass('shown');
        }
        else {
            // Open this row
            content = showMoreInfo(row, 'salesItem', 'unitPriceAmount');
            row.child(content).show();
            tr.addClass('shown');
            $(tr).find('i').removeClass('fe-chevron-right');
            $(tr).find('i').addClass('fe-chevron-down');
        }
    });

}