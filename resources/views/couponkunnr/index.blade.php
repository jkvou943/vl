@extends('layouts.layout')
@section('label', 'Coupon Match Rules')
@section('content')
<style type="text/css">
.dataTables_extended_wrapper .table.dataTable {
  margin: 0px !important;
}

table.dataTable thead th, table.dataTable thead td {
    padding: 10px 2px !important;}
table.dataTable tbody th, table.dataTable tbody td {
    padding: 10px 2px;
}
th,td,td>span {
    font-size:12px !important;
	font-family:Arial, Helvetica, sans-serif;}

</style>
    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption font-dark">
                        <i class="icon-settings font-dark"></i>
                        <span class="caption-subject bold uppercase">Coupon Match Rules</span>
                    </div>
                </div>
                <div class="table-toolbar">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="btn-group">
                                <a data-target="#ajax" data-toggle="modal" href="{{ url('couponkunnr/create')}}"><button id="sample_editable_1_2_new" class="btn sbold blue"> Add New
                                    <i class="fa fa-plus"></i>
                                </button>
                                </a>
                            </div>
                        </div>
						
						<div class="col-md-6">
						<form action="{{url('couponkunnr/upload')}}" method="post" enctype="multipart/form-data">
						<div class="col-md-4" style="text-align:right;" >

							<a href="{{ url('/uploads/coupon/coupon.csv')}}" >Import Template
                                </a>	
						</div>
						<div class="col-md-4">
							{{ csrf_field() }}
								 <input type="file" name="importFile"  />
						</div>
						<div class="col-md-4">
							<button type="submit" class="btn blue" id="data_search">Upload</button>

						</div>
						
						</form>
						</div>
						
                    </div>
                </div>
				
                <div class="portlet-body">
<div class="table-container">
                    <table class="table table-striped table-bordered table-hover" id="manage_coupon_rules">
                        <thead>
                        <tr>
							 <th width="30%"> Coupon Code （Coupon描述）</th>
                            <th width="15%"> Sold-to party （售达方） </th>
                             <th width="15%"> Sku （物料号）</th>
                            <th width="15%"> Sap Seller ID （销售组）</th> 
							<th width="15%"> Action </th>                   			
                        </tr>
						<tr role="row" class="filter">
									<td><input type="text" class="form-control form-filter input-sm" name="coupon_description"> </td>
									
									<td>
										<select name="kunnr" class="form-control form-filter input-sm" >
										<option value="">Please Select
								   <?php 
									foreach($accounts as $k=>$v){
										echo '<option value="'.$k.'">'.$v.'</option>';
									}?>
								</select>
									</td>
									<td><input type="text" class="form-control form-filter input-sm" name="sku"> </td>
									
									<td>
										<select name="sap_seller_id" class="form-control form-filter input-sm" >
										<option value="">Please Select
								   <?php 
									foreach($users as $k=>$v){ 	
										echo '<option value="'.$k.'" >'.$k.' ( '.$v.' ) </option>';
									}?>
								</select>
									</td>
									<td>
										
											<button class="btn btn-sm green btn-outline filter-submit margin-bottom">
												<i class="fa fa-search"></i> Search</button>
										
										<button class="btn btn-sm red btn-outline filter-cancel">
											<i class="fa fa-times"></i> Reset</button>
									</td>
									
						</tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
					</option>
                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
        </div>
    </div>


 <div class="modal fade bs-modal" id="ajax" role="basic" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" >
                <div class="modal-body" >
                    <img src="../assets/global/img/loading-spinner-grey.gif" alt="" class="loading">
                    <span>Loading... </span>
                </div>
            </div>
        </div>
    </div>

    <script>
        var TableDatatablesAjax = function () {
						
								var initPickers = function () {
									//init date pickers
									$('.date-picker').datepicker({
										rtl: App.isRTL(),
										autoclose: true
									});
								}
						
								var initTable = function () {
									$.ajaxSetup({
										headers: { 'X-CSRF-TOKEN' : '{{ csrf_token() }}' }
									});
									var grid = new Datatable();
						
									grid.init({
										src: $("#manage_coupon_rules"),
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
											"dom": "<'row'<'col-md-6 col-sm-12'pli><'col-md-6 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-6 col-sm-12'pli><'col-md-6 col-sm-12'>>",
											"bSortable": false,
											"bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
											"lengthMenu": [
												[-1,10, 20, 50],
												['All',10, 20, 50] // change per page values here
											],
											"pageLength": 10, // default record count per page
											"ajax": {
												"url": "{{ url('couponkunnr/get')}}", // ajax source
											},
										}
									});
						
						
									grid.setAjaxParam("coupon_description", $("input[name='coupon_description']").val());
									grid.setAjaxParam("sku", $("input[name='sku']").val());
									grid.setAjaxParam("kunnr", $("select[name='kunnr']").val());
									grid.setAjaxParam("sap_seller_id", $("select[name='sap_seller_id']").val());
									grid.getDataTable().ajax.reload(null,false);
									//grid.clearAjaxParams();
								}
						
						
								return {
						
									//main function to initiate the module
									init: function () {
										initPickers();
										initTable();
									}
						
								};
						
							}();
						
						$(function() {
							
							TableDatatablesAjax.init();
							
							$("#ajax").on("hidden.bs.modal",function(){
								$(this).find('.modal-content').html('<div class="modal-body"><img src="../assets/global/img/loading-spinner-grey.gif" alt="" class="loading"><span>Loading... </span></div>');
							});
						});


</script>


@endsection
