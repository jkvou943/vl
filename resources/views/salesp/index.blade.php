@extends('layouts.layout')
@section('label', 'Sales Prediction')
@section('content')
<style>
        .form-control {
            height: 29px;
        }
		.dataTables_extended_wrapper .table.dataTable {
  margin: 0px !important;
}


th,td,td>span {
    font-size:12px !important;
	font-family:Arial, Helvetica, sans-serif;}
    </style>
    <h1 class="page-title font-red-intense"> Sales Prediction
    </h1>
    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-body">
							
							
					<div class="table-toolbar">
                    <form role="form" action="{{url('salesp')}}" method="GET">
                        {{ csrf_field() }}
                        <div class="row">
						<div class="col-md-2">
                            <select name="bgbu" class="form-control form-filter input-sm">
										<option value="">All BG && BU</option>
										<option value="-">[Empty]</option>
										<?php 
										$bg='';
										foreach($teams as $team){ 	
											$bg=$team->bg;
											if($team->bg && $team->bu) echo '<option value="'.$team->bg.'_'.$team->bu.'">'.$team->bg.' - '.$team->bu.'</option>';
										}?>
									</select>
                        </div>
                        <div class="col-md-2">
                            <select name="sap_seller_id" class="form-control form-filter input-sm">
										<option value="">All Sellers</option>
										@foreach ($users as $sap_seller_id=>$user_name)
											<option value="{{$sap_seller_id}}">{{$user_name}}</option>
										@endforeach
									</select>
                        </div>
                        <div class="col-md-2">
                            
                                <select name="sap_site_id" class="form-control form-filter input-sm">
										<option value="">All Site</option>
										@foreach (matchSapSiteCode() as $k=>$v)
											<option value="{{$v}}">{{$k}}</option>
										@endforeach
									</select>

                           
                        </div>
                        <div class="col-md-2">
                            
                                <input type="text" class="form-control form-filter input-sm" name="sku" placeholder='SKU'>

                        </div>

						
						
						<div class="col-md-2">
						<button type="button" class="btn blue" id="data_search">Search</button>
                                       
						</div>	
						</div>

                    </form>
					
                </div>
                    <div class="table-container">

                        <table class="table table-striped table-bordered table-hover" id="datatable_ajax_sp">
                            <thead>
                            <tr role="row" >
								<td colspan="5" style="background:#c6e7ff">Sku Base Info</td>
								<td colspan="4" style="background:#e2efda">Last 4 weeks sales</td>
								<td colspan="22" style="background:#b4c6e7;text-align:left">Expected sales in the next 22 weeks</td>
							</tr>
							 <tr role="row" class="heading">
							 	<th style="background-color:#c6e7ff"> Group </th>
								<th style="background-color:#c6e7ff" > Seller </th>
								<th  style="background-color:#c6e7ff"> SKU </th>
								<th  style="background-color:#c6e7ff"> Factory </th>
								<th  style="background-color:#c6e7ff"> Description </th>
								<th  style="background-color:#e2efda">22-28</th>
								<th  style="background-color:#e2efda">15-21</th>
								<th  style="background-color:#e2efda">8-14</th>
								<th  style="background-color:#e2efda">1-7</th>
								<?php foreach($addcolspans as $k=>$v){ ?>
									<th style="background:#b4c6e7"> {{$k}} </th>
								<?php } ?>
                            </tr>
                            
                            </thead>
                            <tbody> </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
        </div>
    </div>




<script>
    var TableDatatablesAjax = function () {

        var initTable = function () {
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN' : '{{ csrf_token() }}' }
            });
            var grid = new Datatable();

            grid.init({
                src: $("#datatable_ajax_sp"),
                onSuccess: function (grid, response) {
                    // grid:        grid object
                    // response:    json object of server side ajax response
                    // execute some code after table records loaded
                },
                onError: function (grid) {
                    // execute some code on network or other general error
                },
                onDataLoad: function(grid) {
                    // execute some code on ajax data load
                    //alert('123');
                    //alert($("#subject").val());
                    //grid.setAjaxParam("subject", $("#subject").val());

                },
                loadingMessage: 'Loading...',
                dataTable: { // here you can define a typical datatable settings from http://datatables.net/usage/options

                    // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
                    // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/scripts/datatable.js).
                    // So when dropdowns used the scrollable div should be removed.
                    //"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
					

                    "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
                    "aoColumnDefs": [ { "bSortable": false, "aTargets": [4,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30] }],
                    "lengthMenu": [
                        [10, 50, 100, -1],
                        [10, 50, 100, 'All'] // change per page values here
                    ],
                    "pageLength": 10, // default record count per page
                    "ajax": {
                        "url": "{{ url('salesp/get')}}", // ajax source
                    },
                    "order": [
                        [1, "desc"]
                    ],// set first column as a default sort by asc
					
					buttons: [
                        { extend: 'csv', className: 'btn purple btn-outline ',filename:'salesp' }
                    ],
					 "createdRow": function( row, data, dataIndex ) {
                        $(row).children('td').eq(4).attr('style', 'max-width: 200px;overflow:hidden;white-space:nowrap;text-align: left; ');
						$(row).children('td').eq(4).attr('title', $(row).children('td').eq(4).text());
						$(row).children('td').eq(5).attr('style', 'min-width:60px;white-space:nowrap;');
						$(row).children('td').eq(6).attr('style', 'min-width:60px;white-space:nowrap;');
						$(row).children('td').eq(7).attr('style', 'min-width:60px;white-space:nowrap;');
						$(row).children('td').eq(8).attr('style', 'min-width:60px;white-space:nowrap;');
						
                    },
					scrollY:        380,
                    scrollX:        true,
					

					fixedColumns:   {
						leftColumns:9,
						rightColumns: 0
					},
					"dom": "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
                }
            });
            grid.setAjaxParam("sku", $("input[name='sku']").val());
            grid.setAjaxParam("bgbu", $("select[name='bgbu']").val());
			grid.setAjaxParam("sap_seller_id", $("select[name='sap_seller_id']").val());
			grid.setAjaxParam("sap_site_id", $("select[name='sap_site_id']").val());
            grid.getDataTable().ajax.reload(null,false);
            //grid.clearAjaxParams();
        }


        return {

            //main function to initiate the module
            init: function () {
                initTable();
            }

        };

    }();

$(function() {
    TableDatatablesAjax.init();
	$('#data_search').on('click',function(){
		var dttable = $('#datatable_ajax_sp').dataTable();
	    dttable.fnClearTable(); //清空一下table
	    dttable.fnDestroy(); //还原初始化了的datatable
		TableDatatablesAjax.init();
	});
});


</script>


@endsection
