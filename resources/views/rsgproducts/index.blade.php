@extends('layouts.layout')
@section('label', 'RSG Products')
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
                        <span class="caption-subject bold uppercase">RSG Products</span>
                    </div>
                </div>
                <div class="table-toolbar">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="btn-group">
                                <a data-target="#ajax" data-toggle="modal" href="{{ url('rsgproducts/create')}}"><button id="sample_editable_1_2_new" class="btn sbold blue"> Add New
                                    <i class="fa fa-plus"></i>
                                </button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="portlet-body">

                    <div class="table-container">
							<div class="table-actions-wrapper">
								<select id="customstatus" class="table-group-action-input form-control input-inline input-small input-sm">
									<option value="">Select Status</option>
									<option value="0">Disabled</option>
									<option value="1">Enabled</option>
									<option value="-1">Reject</option>
								</select>
								
												
								<button class="btn btn-sm green table-group-action-submit">
									<i class="fa fa-check"></i> Update</button>
							</div>
							<table class="table table-striped table-bordered table-hover table-checkable" id="datatable_ajax_rsg_products">
								<thead>
								<tr role="row" class="heading">
									<th width="2%">
										<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
											<input type="checkbox" class="group-checkable" data-set="#datatable_ajax_rsg_products .checkboxes" />
											<span></span>
										</label>
									</th>
									<th width="30%"> Product </th>
									<th width="10%"> ActiveDate </th>
									<th width="10%"> Daily Gift </th>
									<th width="10%"> Daily Remain </th>
									<th width="10%"> Date </th>
									<th width="10%"> User </th>
									<th width="10%"> Status</th>
								</tr>
								<tr role="row" class="filter">
									<td> </td>
									<td>
										<div class="input-group">
										<select name="seller_id" class="form-control form-filter input-sm">
											<option value="">Select Account</option>
										   <?php 
											foreach($accounts as $k=>$v){ 	
												echo '<option value="'.$k.'">'.$v.'</option>';
											}?>
										</select>
										</div>
										<div class="input-group">
										<input type="text" class="form-control form-filter input-sm" placeholder='ASIN' name="asin">
										</div>
									</td>
									
									<td>
										<div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
											<input type="text" class="form-control form-filter input-sm" readonly name="date_from" placeholder="From" value="">
											<span class="input-group-btn">
												<button class="btn btn-sm default" type="button">
													<i class="fa fa-calendar"></i>
												</button>
											</span>
										</div>
										<div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
											<input type="text" class="form-control form-filter input-sm" readonly name="date_to" placeholder="To" value="">
											<span class="input-group-btn">
												<button class="btn btn-sm default" type="button">
													<i class="fa fa-calendar"></i>
												</button>
											</span>
										</div>
									</td>
									
									<td></td>
									<td></td>
									<td></td>
									<!--
									<td>									
										<select name="bgbu" class="form-control form-filter input-sm">
										<option value="">BG && BU</option>
										<option value="-">[Empty]</option>
										<?php 
										$bg='';
										foreach($teams as $team){ 	
											$bg=$team->bg;
											if($team->bg && $team->bu) echo '<option value="'.$team->bg.'_'.$team->bu.'">'.$team->bg.' - '.$team->bu.'</option>';
										}?>
									</select>
									</td>
									-->
									 <td>
									
										<select name="user_id" class="form-control form-filter input-sm">
										<option value="">Users</option>
										@foreach ($users as $user_id=>$user_name)
												<option value="{{$user_id}}">{{$user_name}}</option>
										@endforeach
										</select>
									
									</td>
									<td>
									<select id="status"  name="status" class="form-control form-filter input-sm">
										<option value="">Select Status</option>
										<option value="0">Disabled</option>
										<option value="1">Enabled</option>
										<option value="-1">Reject</option>
									</select>
									</td>
	
									<td>
										<div class="margin-bottom-5">
											<button class="btn btn-sm green btn-outline filter-submit margin-bottom">
												<i class="fa fa-search"></i> Search</button>
										</div>
										<button class="btn btn-sm red btn-outline filter-cancel">
											<i class="fa fa-times"></i> Reset</button>
									</td>
								</tr>
								</thead>
								<tbody> </tbody>
							</table>
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
										src: $("#datatable_ajax_rsg_products"),
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
						
											"bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
											"aoColumnDefs": [ { "bSortable": false, "aTargets": [ 0 ,1,6,7,8 ] }],
											"lengthMenu": [
												[-1,10, 20, 50],
												['All',10, 20, 50] // change per page values here
											],
											"pageLength": 10, // default record count per page
											"ajax": {
												"url": "{{ url('rsgproducts/get')}}", // ajax source
											},
											 "createdRow": function( row, data, dataIndex ) {
												$(row).children('td').eq(1).attr('style', 'text-align: left; ');
											},
											"order": [
												[5, "desc"]
											],// set first column as a default sort by asc
										}
									});
						
									// handle group actionsubmit button click
									grid.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
										e.preventDefault();
										var customstatus = $("#customstatus", grid.getTableWrapper());
										if ((customstatus.val() != "") && grid.getSelectedRowsCount() > 0) {
											grid.setAjaxParam("customActionType", "group_action");
											grid.setAjaxParam("customstatus", customstatus.val());
											grid.setAjaxParam("id", grid.getSelectedRows());
											grid.getDataTable().draw(false);
											//grid.clearAjaxParams();
										} else if (customstatus.val() == "") {
											App.alert({
												type: 'danger',
												icon: 'warning',
												message: 'Please select an action',
												container: grid.getTableWrapper(),
												place: 'prepend'
											});
										} else if (grid.getSelectedRowsCount() === 0) {
											App.alert({
												type: 'danger',
												icon: 'warning',
												message: 'No record selected',
												container: grid.getTableWrapper(),
												place: 'prepend'
											});
										}
						
									});
						
									grid.setAjaxParam("date_from", $("input[name='date_from']").val());
									grid.setAjaxParam("date_to", $("input[name='date_to']").val());
									grid.setAjaxParam("asin", $("input[name='asin']").val());
									grid.setAjaxParam("seller_id", $("select[name='seller_id']").val());
									//grid.setAjaxParam("bgbu", $("select[name='bgbu']").val());
									grid.setAjaxParam("user_id", $("select[name='user_id']").val());
									grid.setAjaxParam("status", $("select[name='status']").val());
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
	
						</div>
                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
        </div>
    </div>


<div class="modal fade bs-modal-lg" id="ajax" role="basic" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content" >
			<div class="modal-body" >
				<img src="../assets/global/img/loading-spinner-grey.gif" alt="" class="loading">
				<span>Loading... </span>
			</div>
		</div>
	</div>
</div>



@endsection
