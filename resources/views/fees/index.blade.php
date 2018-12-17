@extends('layouts.layout')
@section('label', 'Fees List')
@section('content')
    <h1 class="page-title font-red-intense"> Fees List
    </h1>
    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-body">
					<div class="tabbable-line">
					<ul class="nav nav-tabs ">
						<li class="active">
							<a href="#tab_ads" data-toggle="tab" aria-expanded="true" > Ads Fee</a>
						</li>
						<li >
							<a href="#tab_deal" data-toggle="tab" aria-expanded="true" > Deal Fee</a>
						</li>
						<li >
							<a href="#tab_coupon" data-toggle="tab" aria-expanded="true" > Coupon Fee</a>
						</li>
						<li >
							<a href="#tab_servicefee" data-toggle="tab" aria-expanded="true" > Service Fee</a>
						</li>
						<li >
							<a href="#tab_cpcfee" data-toggle="tab" aria-expanded="true" > Cpc Detail Fee</a>
						</li>
					</ul>
            		<div class="tab-content">
						<div class="tab-pane active" id="tab_ads">
						<div class="table-container">
							<div class="table-actions-wrapper">
								<select id="bgbu" class="table-group-action-input form-control input-inline input-small input-sm">
									<option value="">Select BG && BU</option>
									<?php 
									$bg='';
									foreach($teams as $team){ 	
										$bg=$team->bg;
										if($team->bg && $team->bu) echo '<option value="'.$team->bg.'_'.$team->bu.'">'.$team->bg.' - '.$team->bu.'</option>';
									}?>
								</select>
								<input type="text" placeholder="SAPSKU" id="sku" class="table-group-action-input form-control input-inline input-small input-sm" />
								
																	
								<button class="btn btn-sm green table-group-action-submit">
									<i class="fa fa-check"></i> Change</button>
							</div>
							<table class="table table-striped table-bordered table-hover table-checkable" id="datatable_ajax_ads">
								<thead>
								<tr role="row" class="heading">
									<th width="2%">
										<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
											<input type="checkbox" class="group-checkable" data-set="#datatable_ajax_ads .checkboxes" />
											<span></span>
										</label>
									</th>
									<th width="15%"> Date </th>
									<th width="15%"> Account </th>
									<th width="15%"> Invoice Id </th>
									<th width="15%"> BGBU </th>
									<th width="15%"> User </th>
									<th width="15%"> SKU </th>
									<th width="15%"> Amount</th>
								</tr>
								<tr role="row" class="filter">
									<td> </td>
									<td>
										<div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
											<input type="text" class="form-control form-filter input-sm" readonly name="date_from" placeholder="From" value="{{$date_from}}">
											<span class="input-group-btn">
																		<button class="btn btn-sm default" type="button">
																			<i class="fa fa-calendar"></i>
																		</button>
																	</span>
										</div>
										<div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
											<input type="text" class="form-control form-filter input-sm" readonly name="date_to" placeholder="To" value="{{$date_to}}">
											<span class="input-group-btn">
																		<button class="btn btn-sm default" type="button">
																			<i class="fa fa-calendar"></i>
																		</button>
																	</span>
										</div>
									</td>
									<td>
										
										<select name="sellerid" class="form-control form-filter input-sm">
											<option value="">Select Account</option>
										   <?php 
											foreach($accounts as $k=>$v){ 	
												echo '<option value="'.$k.'">'.$v.'</option>';
											}?>
										</select>
										
									</td>
									<td>
									
										<input type="text" class="form-control form-filter input-sm" name="invoiceid">
										
										
										
									</td>
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
									 <td>
									
										<select name="user_id" class="form-control form-filter input-sm">
										<option value="">Users</option>
										@foreach ($users as $user_id=>$user_name)
												<option value="{{$user_id}}">{{$user_name}}</option>
											@endforeach
									</select>
									
									</td>
									<td><input type="text" class="form-control form-filter input-sm" name="sku"></td>
	
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
										src: $("#datatable_ajax_ads"),
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
											"aoColumnDefs": [ { "bSortable": false, "aTargets": [ 0 ,2,3,4,5 ] }],
											"lengthMenu": [
												[-1,10, 20, 50],
												['All',10, 20, 50] // change per page values here
											],
											"pageLength": 10, // default record count per page
											"ajax": {
												"url": "{{ url('fees/getads')}}", // ajax source
											},
											"order": [
												[1, "desc"]
											],// set first column as a default sort by asc
										}
									});
						
									// handle group actionsubmit button click
									grid.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
										e.preventDefault();
										var bgbu = $("#bgbu", grid.getTableWrapper());
										var sku = $("#sku", grid.getTableWrapper());
										if ((bgbu.val() != "" && sku.val() != "") && grid.getSelectedRowsCount() > 0) {
											grid.setAjaxParam("customActionType", "group_action");
											grid.setAjaxParam("custombgbu", bgbu.val());
											grid.setAjaxParam("customsku", sku.val());
											grid.setAjaxParam("id", grid.getSelectedRows());
											grid.getDataTable().draw(false);
											//grid.clearAjaxParams();
										} else if (bgbu.val() == "" || sku.val() == "") {
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
									grid.setAjaxParam("invoiceid", $("input[name='invoiceid']").val());
									grid.setAjaxParam("sellerid", $("select[name='sellerid']").val());
									grid.setAjaxParam("bgbu", $("select[name='bgbu']").val());
									grid.setAjaxParam("user_id", $("select[name='user_id']").val());
									grid.setAjaxParam("sku", $("input[name='sku']").val());
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
						});
						</script>
	
						</div>
						</div>
						
						<div class="tab-pane" id="tab_deal">
						<div class="table-container">
							<div class="table-actions-wrapper">
								<select id="bgbu" class="table-group-action-input form-control input-inline input-small input-sm">
									<option value="">Select BG && BU</option>
									<?php 
									$bg='';
									foreach($teams as $team){ 	
										$bg=$team->bg;
										if($team->bg && $team->bu) echo '<option value="'.$team->bg.'_'.$team->bu.'">'.$team->bg.' - '.$team->bu.'</option>';
									}?>
								</select>
								<input type="text" placeholder="SAPSKU" id="sku" class="table-group-action-input form-control input-inline input-small input-sm" />
																	
								<button class="btn btn-sm green table-group-action-submit">
									<i class="fa fa-check"></i> Change</button>
							</div>
							<table class="table table-striped table-bordered table-hover table-checkable" id="datatable_ajax_deal">
								<thead>
								<tr role="row" class="heading">
									<th width="2%">
										<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
											<input type="checkbox" class="group-checkable" data-set="#datatable_ajax_deal .checkboxes" />
											<span></span>
										</label>
									</th>
									<th width="15%"> Date </th>
									<th width="15%"> Account </th>
									<th width="30%"> Fee Description </th>
									<th width="10%"> BGBU </th>
									<th width="10%"> User </th>
									<th width="10%"> SKU </th>
									<th width="10%"> Amount</th>
								</tr>
								<tr role="row" class="filter">
									<td> </td>
									<td>
										<div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
											<input type="text" class="form-control form-filter input-sm" readonly name="date_from" placeholder="From" value="{{$date_from}}">
											<span class="input-group-btn">
																		<button class="btn btn-sm default" type="button">
																			<i class="fa fa-calendar"></i>
																		</button>
																	</span>
										</div>
										<div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
											<input type="text" class="form-control form-filter input-sm" readonly name="date_to" placeholder="To" value="{{$date_to}}">
											<span class="input-group-btn">
																		<button class="btn btn-sm default" type="button">
																			<i class="fa fa-calendar"></i>
																		</button>
																	</span>
										</div>
									</td>
									<td>
										
										<select name="sellerid" class="form-control form-filter input-sm">
											<option value="">Select Account</option>
										   <?php 
											foreach($accounts as $k=>$v){ 	
												echo '<option value="'.$k.'">'.$v.'</option>';
											}?>
										</select>
										
									</td>
									<td>
									
										<input type="text" class="form-control form-filter input-sm" name="feedes">
										
										
										
									</td>
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
									 <td>
									
										<select name="user_id" class="form-control form-filter input-sm">
										<option value="">Users</option>
										@foreach ($users as $user_id=>$user_name)
												<option value="{{$user_id}}">{{$user_name}}</option>
											@endforeach
									</select>
									
									</td>
									<td><input type="text" class="form-control form-filter input-sm" name="sku"></td>
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
							var TableDatatablesAjaxDeal = function () {
						
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
										src: $("#datatable_ajax_deal"),
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
											"aoColumnDefs": [ { "bSortable": false, "aTargets": [ 0 ,2,3,4,5 ] }],
											"lengthMenu": [
												[-1,10, 20, 50],
												['All',10, 20, 50] // change per page values here
											],
											"pageLength": 10, // default record count per page
											"ajax": {
												"url": "{{ url('fees/getdeal')}}", // ajax source
											},
											"order": [
												[1, "desc"]
											],// set first column as a default sort by asc
										}
									});
						
									// handle group actionsubmit button click
									grid.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
										e.preventDefault();
										var bgbu = $("#bgbu", grid.getTableWrapper());
										var sku = $("#sku", grid.getTableWrapper());
										if ((bgbu.val() != "" && sku.val() != "") && grid.getSelectedRowsCount() > 0) {
											grid.setAjaxParam("customActionType", "group_action");
											grid.setAjaxParam("custombgbu", bgbu.val());
											grid.setAjaxParam("customsku", sku.val());
											grid.setAjaxParam("id", grid.getSelectedRows());
											grid.getDataTable().draw(false);
											//grid.clearAjaxParams();
										} else if (bgbu.val() == "" || sku.val() == "") {
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
									grid.setAjaxParam("feedes", $("input[name='feedes']").val());
									grid.setAjaxParam("sellerid", $("select[name='sellerid']").val());
									grid.setAjaxParam("bgbu", $("select[name='bgbu']").val());
									grid.setAjaxParam("user_id", $("select[name='user_id']").val());
									grid.setAjaxParam("sku", $("input[name='sku']").val());
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
							TableDatatablesAjaxDeal.init();
						});
						</script>
	
						</div>
						</div>
						
						
						<div class="tab-pane" id="tab_coupon">
						<div class="table-container">
							<div class="table-actions-wrapper">
								<select id="bgbu" class="table-group-action-input form-control input-inline input-small input-sm">
									<option value="">Select BG && BU</option>
									<?php 
									$bg='';
									foreach($teams as $team){ 	
										$bg=$team->bg;
										if($team->bg && $team->bu) echo '<option value="'.$team->bg.'_'.$team->bu.'">'.$team->bg.' - '.$team->bu.'</option>';
									}?>
								</select>
								<input type="text" placeholder="SAPSKU" id="sku" class="table-group-action-input form-control input-inline input-small input-sm" />
								
																	
								<button class="btn btn-sm green table-group-action-submit">
									<i class="fa fa-check"></i> Change</button>
							</div>
							<table class="table table-striped table-bordered table-hover table-checkable" id="datatable_ajax_coupon">
								<thead>
								<tr role="row" class="heading">
									<th width="2%">
										<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
											<input type="checkbox" class="group-checkable" data-set="#datatable_ajax_coupon .checkboxes" />
											<span></span>
										</label>
									</th>
									<th width="15%"> Date </th>
									<th width="15%"> Account </th>
									<th width="30%"> Fee Description </th>
									<th width="10%"> BGBU </th>
									<th width="10%"> User </th>
									<th width="15%"> SKU </th>
									<th width="10%"> Amount</th>
								</tr>
								<tr role="row" class="filter">
									<td> </td>
									<td>
										<div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
											<input type="text" class="form-control form-filter input-sm" readonly name="date_from" placeholder="From" value="{{$date_from}}">
											<span class="input-group-btn">
																		<button class="btn btn-sm default" type="button">
																			<i class="fa fa-calendar"></i>
																		</button>
																	</span>
										</div>
										<div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
											<input type="text" class="form-control form-filter input-sm" readonly name="date_to" placeholder="To" value="{{$date_to}}">
											<span class="input-group-btn">
																		<button class="btn btn-sm default" type="button">
																			<i class="fa fa-calendar"></i>
																		</button>
																	</span>
										</div>
									</td>
									<td>
										
										<select name="sellerid" class="form-control form-filter input-sm">
											<option value="">Select Account</option>
										   <?php 
											foreach($accounts as $k=>$v){ 	
												echo '<option value="'.$k.'">'.$v.'</option>';
											}?>
										</select>
										
									</td>
									<td>
									
										<input type="text" class="form-control form-filter input-sm" name="feedes">
										
										
										
									</td>
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
									 <td>
									
										<select name="user_id" class="form-control form-filter input-sm">
										<option value="">Users</option>
										@foreach ($users as $user_id=>$user_name)
												<option value="{{$user_id}}">{{$user_name}}</option>
											@endforeach
									</select>
									
									</td>
									<td><input type="text" class="form-control form-filter input-sm" name="sku"></td>
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
							var TableDatatablesAjaxCoupon = function () {
						
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
										src: $("#datatable_ajax_coupon"),
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
											"aoColumnDefs": [ { "bSortable": false, "aTargets": [ 0 ,2,3,4,5 ] }],
											"lengthMenu": [
												[-1,10, 20, 50],
												['All',10, 20, 50] // change per page values here
											],
											"pageLength": 10, // default record count per page
											"ajax": {
												"url": "{{ url('fees/getcoupon')}}", // ajax source
											},
											"order": [
												[1, "desc"]
											],// set first column as a default sort by asc
										}
									});
						
									// handle group actionsubmit button click
									grid.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
										e.preventDefault();
										var bgbu = $("#bgbu", grid.getTableWrapper());
										var sku = $("#sku", grid.getTableWrapper());
										if ((bgbu.val() != "" && sku.val() != "") && grid.getSelectedRowsCount() > 0) {
											grid.setAjaxParam("customActionType", "group_action");
											grid.setAjaxParam("custombgbu", bgbu.val());
											grid.setAjaxParam("customsku", sku.val());
											grid.setAjaxParam("id", grid.getSelectedRows());
											grid.getDataTable().draw(false);
											//grid.clearAjaxParams();
										} else if (bgbu.val() == "" || sku.val() == "") {
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
									grid.setAjaxParam("feedes", $("input[name='feedes']").val());
									grid.setAjaxParam("sellerid", $("select[name='sellerid']").val());
									grid.setAjaxParam("bgbu", $("select[name='bgbu']").val());
									grid.setAjaxParam("user_id", $("select[name='user_id']").val());
									grid.setAjaxParam("sku", $("input[name='sku']").val());
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
							TableDatatablesAjaxCoupon.init();
						});
						</script>
	
						</div>
						</div>
						
						
						<div class="tab-pane" id="tab_servicefee">
						<div class="table-container">
							<div class="table-actions-wrapper">
								<select id="bgbu" class="table-group-action-input form-control input-inline input-small input-sm">
									<option value="">Select BG && BU</option>
									<?php 
									$bg='';
									foreach($teams as $team){ 	
										$bg=$team->bg;
										if($team->bg && $team->bu) echo '<option value="'.$team->bg.'_'.$team->bu.'">'.$team->bg.' - '.$team->bu.'</option>';
									}?>
								</select>
								<input type="text" placeholder="SAPSKU" id="sku" class="table-group-action-input form-control input-inline input-small input-sm" />
								
																	
								<button class="btn btn-sm green table-group-action-submit">
									<i class="fa fa-check"></i> Change</button>
							</div>
							<table class="table table-striped table-bordered table-hover table-checkable" id="datatable_ajax_servicefee">
								<thead>
								<tr role="row" class="heading">
									<th width="2%">
										<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
											<input type="checkbox" class="group-checkable" data-set="#datatable_ajax_servicefee .checkboxes" />
											<span></span>
										</label>
									</th>
									<th width="15%"> Date </th>
									<th width="15%"> Account </th>
									<th width="30%"> Fee Description </th>
									<th width="10%"> BGBU </th>
									<th width="10%"> User </th>
									<th width="15%"> SKU </th>
									<th width="10%"> Amount</th>
								</tr>
								<tr role="row" class="filter">
									<td> </td>
									<td>
										<div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
											<input type="text" class="form-control form-filter input-sm" readonly name="date_from" placeholder="From" value="{{$date_from}}">
											<span class="input-group-btn">
																		<button class="btn btn-sm default" type="button">
																			<i class="fa fa-calendar"></i>
																		</button>
																	</span>
										</div>
										<div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
											<input type="text" class="form-control form-filter input-sm" readonly name="date_to" placeholder="To" value="{{$date_to}}">
											<span class="input-group-btn">
																		<button class="btn btn-sm default" type="button">
																			<i class="fa fa-calendar"></i>
																		</button>
																	</span>
										</div>
									</td>
									<td>
										
										<select name="sellerid" class="form-control form-filter input-sm">
											<option value="">Select Account</option>
										   <?php 
											foreach($accounts as $k=>$v){ 	
												echo '<option value="'.$k.'">'.$v.'</option>';
											}?>
										</select>
										
									</td>
									<td>
									
										<input type="text" class="form-control form-filter input-sm" name="feedes">
										
										
										
									</td>
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
									 <td>
									
										<select name="user_id" class="form-control form-filter input-sm">
										<option value="">Users</option>
										@foreach ($users as $user_id=>$user_name)
												<option value="{{$user_id}}">{{$user_name}}</option>
											@endforeach
									</select>
									
									</td>
									<td><input type="text" class="form-control form-filter input-sm" name="sku"></td>
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
							var TableDatatablesAjaxServicefee = function () {
						
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
										src: $("#datatable_ajax_servicefee"),
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
											"aoColumnDefs": [ { "bSortable": false, "aTargets": [ 0 ,2,3,4,5 ] }],
											"lengthMenu": [
												[-1,10, 20, 50],
												['All',10, 20, 50] // change per page values here
											],
											"pageLength": 10, // default record count per page
											"ajax": {
												"url": "{{ url('fees/getservice')}}", // ajax source
											},
											"order": [
												[1, "desc"]
											],// set first column as a default sort by asc
										}
									});
						
									// handle group actionsubmit button click
									grid.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
										e.preventDefault();
										var bgbu = $("#bgbu", grid.getTableWrapper());
										var sku = $("#sku", grid.getTableWrapper());
										if ((bgbu.val() != "" && sku.val() != "") && grid.getSelectedRowsCount() > 0) {
											grid.setAjaxParam("customActionType", "group_action");
											grid.setAjaxParam("custombgbu", bgbu.val());
											grid.setAjaxParam("customsku", sku.val());
											grid.setAjaxParam("id", grid.getSelectedRows());
											grid.getDataTable().draw(false);
											//grid.clearAjaxParams();
										} else if (bgbu.val() == "" || sku.val() == "") {
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
									grid.setAjaxParam("feedes", $("input[name='feedes']").val());
									grid.setAjaxParam("sellerid", $("select[name='sellerid']").val());
									grid.setAjaxParam("bgbu", $("select[name='bgbu']").val());
									grid.setAjaxParam("user_id", $("select[name='user_id']").val());
									grid.setAjaxParam("sku", $("input[name='sku']").val());
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
							TableDatatablesAjaxServicefee.init();
						});
						</script>
	
						</div>
						</div>
						
						
						<div class="tab-pane" id="tab_cpcfee">
						<div class="table-container">
							<div class="table-actions-wrapper">
								<select id="bgbu" class="table-group-action-input form-control input-inline input-small input-sm">
									<option value="">Select BG && BU</option>
									<?php 
									$bg='';
									foreach($teams as $team){ 	
										$bg=$team->bg;
										if($team->bg && $team->bu) echo '<option value="'.$team->bg.'_'.$team->bu.'">'.$team->bg.' - '.$team->bu.'</option>';
									}?>
								</select>
								<input type="text" placeholder="SAPSKU" id="sku" class="table-group-action-input form-control input-inline input-small input-sm" />
								
																	
								<button class="btn btn-sm green table-group-action-submit">
									<i class="fa fa-check"></i> Change</button>
							</div>
							<table class="table table-striped table-bordered table-hover table-checkable" id="datatable_ajax_cpcfee">
								<thead>
								<tr role="row" class="heading">
									<th width="2%">
										<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
											<input type="checkbox" class="group-checkable" data-set="#datatable_ajax_cpcfee .checkboxes" />
											<span></span>
										</label>
									</th>
									<th width="10%"> Date </th>
									<th width="7%"> Account </th>
									<th width="10%"> Campaign </th>
									<th width="10%"> Ad Group </th>
									<th width="5%"> Sales </th>
									<th width="5%"> Profit </th>
									<th width="5%"> Orders </th>
									<th width="5%"> Status </th>
									<th width="7%"> BGBU </th>
									<th width="7%"> User </th>
									<th width="7%"> SKU </th>
									<th width="7%"> Cost </th>
								</tr>
								<tr role="row" class="filter">
									<td> </td>
									<td>
										<div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
											<input type="text" class="form-control form-filter input-sm" readonly name="date_from" placeholder="From" value="{{$date_from}}">
											<span class="input-group-btn">
																		<button class="btn btn-sm default" type="button">
																			<i class="fa fa-calendar"></i>
																		</button>
																	</span>
										</div>
										<div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
											<input type="text" class="form-control form-filter input-sm" readonly name="date_to" placeholder="To" value="{{$date_to}}">
											<span class="input-group-btn">
																		<button class="btn btn-sm default" type="button">
																			<i class="fa fa-calendar"></i>
																		</button>
																	</span>
										</div>
									</td>
									<td>
										
										<select name="sellerid" class="form-control form-filter input-sm">
											<option value="">Select Account</option>
										   <?php 
											foreach($accounts as $k=>$v){ 	
												echo '<option value="'.$k.'">'.$v.'</option>';
											}?>
										</select>
										
									</td>
									<td >
									
										<input type="text" class="form-control form-filter input-sm" name="feedes1">
										
										
										
									</td>
									<td >
									
										<input type="text" class="form-control form-filter input-sm" name="feedes2">
										
										
										
									</td>
									
									<td colspan="3"></td>
									<td >
									<select name="status" class="form-control form-filter input-sm">
										<option value="">Status</option>
										<option value="enabled">Enabled</option>
										<option value="paused">Paused</option>
									</select>
									</td>
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
									 <td>
									
										<select name="user_id" class="form-control form-filter input-sm">
										<option value="">Users</option>
										@foreach ($users as $user_id=>$user_name)
												<option value="{{$user_id}}">{{$user_name}}</option>
											@endforeach
									</select>
									
									</td>
									<td><input type="text" class="form-control form-filter input-sm" name="sku"></td>
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
							var TableDatatablesAjaxcpcfee = function () {
						
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
										src: $("#datatable_ajax_cpcfee"),
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
											"aoColumnDefs": [ { "bSortable": false, "aTargets": [ 0 ,2,3,4,5,6,7,8 ] }],
											"lengthMenu": [
												[-1,10, 20, 50],
												['All',10, 20, 50] // change per page values here
											],
											"pageLength": 10, // default record count per page
											"ajax": {
												"url": "{{ url('fees/getcpc')}}", // ajax source
											},
											"order": [
												[1, "desc"]
											],// set first column as a default sort by asc
										}
									});
						
									// handle group actionsubmit button click
									grid.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
										e.preventDefault();
										var bgbu = $("#bgbu", grid.getTableWrapper());
										var sku = $("#sku", grid.getTableWrapper());
										if ((bgbu.val() != "" && sku.val() != "") && grid.getSelectedRowsCount() > 0) {
											grid.setAjaxParam("customActionType", "group_action");
											grid.setAjaxParam("custombgbu", bgbu.val());
											grid.setAjaxParam("customsku", sku.val());
											grid.setAjaxParam("id", grid.getSelectedRows());
											grid.getDataTable().draw(false);
											//grid.clearAjaxParams();
										} else if (bgbu.val() == "" || sku.val() == "") {
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
									grid.setAjaxParam("feedes1", $("input[name='feedes1']").val());
									grid.setAjaxParam("feedes2", $("input[name='feedes2']").val());
									grid.setAjaxParam("sellerid", $("select[name='sellerid']").val());
									grid.setAjaxParam("bgbu", $("select[name='bgbu']").val());
									grid.setAjaxParam("user_id", $("select[name='user_id']").val());
									grid.setAjaxParam("status", $("select[name='status']").val());
									grid.setAjaxParam("sku", $("input[name='sku']").val());
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
							TableDatatablesAjaxcpcfee.init();
						});
						</script>
	
						</div>
						</div>
						
					

						

					</div>
                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
        </div>
    </div>






@endsection
