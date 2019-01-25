@extends('layouts.layout')
@section('label', 'Asins List')
@section('content')
<style type="text/css">
.dataTables_extended_wrapper .table.dataTable {
  margin: 0px !important;
}

table.dataTable thead th, table.dataTable thead td {
    padding: 10px 2px !important;}
table.dataTable tbody th, table.dataTable tbody td {
    padding: 10px 0px;
}
th,td,td>span {
    font-size:12px !important;
	font-family:Arial, Helvetica, sans-serif;}

</style>
    <h1 class="page-title font-red-intense"> Asins List
        <small>Configure your Asin.</small>
    </h1>
    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-body">
					<div class="table-toolbar">
                    <div class="row">
                       
                        <div class="col-md-8">
                            <div class="table-actions-wrapper" id="table-actions-wrapper">
                            <span> </span>


                            <select id="giveUser" class="table-group-action-input form-control input-inline input-small input-sm">
                                <option value="">Change Group</option>
                                @foreach ($groups as $user_id=>$user_name)
                                    <option value="{{$user_id}}">{{$user_name}}</option>
                                @endforeach
                            </select>
							
							<select id="giveReviewUser" class="table-group-action-input form-control input-inline input-small input-sm">
                                <option value="">Change Review User</option>
                                @foreach ($users as $user_id=>$user_name)
                                    <option value="{{$user_id}}">{{$user_name}}</option>
                                @endforeach
                            </select>

							
							<input id='giveStar' placeholder='Limit Star' class="table-group-action-input form-control input-inline input-small input-sm">
                            <button class="btn btn-sm green table-group-action-submit">
                                <i class="fa fa-check"></i> Change</button>
                        </div>
						
						
                        </div>
                        <div class="col-md-4" >
                            <div class="btn-group " style="float:right;">
                                <button id="vl_list_export" class="btn sbold blue"> Export
                                    <i class="fa fa-download"></i>
                                </button>
                               
                            </div>
                        </div>
                    </div>
                </div>
				<div style="clear:both;height:50px;"></div>
                    <div class="table-container">
                        
                        <table class="table table-striped table-bordered table-hover table-checkable" id="datatable_ajax_asin">
                            <thead>
                            <tr role="row" class="heading">
                                <th width="2%">
                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="checkbox" class="group-checkable" data-set="#datatable_ajax_asin .checkboxes" />
                                        <span></span>
                                    </label>
                                </th>
                                <th width="8%"> Site </th>
                                <th width="5%"> Asin </th>
								<th width="5%"> SellerSku </th>
                                <th width="5%"> ItemNo. </th>
								<th width="5%"> Model </th>
								<th width="5%"> Status </th>
                                <th width="5%"> Item Group </th>
                                <th width="5%"> Item Group Description </th>
								<th width="5%"> Seller </th>
								<th width="2%"> BG </th>
								<th width="2%"> BU </th>
                                <th width="5%"> Group </th>
								<th width="5%"> Review User </th>
                                <th width="5%"> Action </th>
                            </tr>
                            <tr role="row" class="filter">
                                <td> </td>
                                <td>
                                    <input type="text" class="form-control form-filter input-sm" name="site">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-filter input-sm" name="asin">
                                </td>
								<td>
                                    <input type="text" class="form-control form-filter input-sm" name="sellersku">
                                </td>
                                <td>

                                    <input type="text" class="form-control form-filter input-sm" name="item_no">
		
                                </td>
								<td>

                                    <input type="text" class="form-control form-filter input-sm" name="item_model">
		
                                </td>
								<td>

                                    <select class="form-control form-filter input-sm" name="status">
                                        <option value="">Select...</option>
                                        @foreach (getAsinStatus() as $key=>$val)
                                            <option value="{{$key}}">{{$val}}</option>
                                        @endforeach
                                    </select>
		
                                </td>
                                <td>

                                    <input type="text" class="form-control form-filter input-sm" name="item_group">
		
                                </td>
                                <td>

                                    <input type="text" class="form-control form-filter input-sm" name="brand_line">
		
                                </td>
								<td>

                                    <input type="text" class="form-control form-filter input-sm" name="seller">
		
                                </td>
								<td>

                                    <input type="text" class="form-control form-filter input-sm" name="bg">
		
                                </td>
								<td>

                                    <input type="text" class="form-control form-filter input-sm" name="bu">
		
                                </td>
								
                                <td>
   
                                    <select class="form-control form-filter input-sm" name="group_id">
                                        <option value="">Select...</option>
										<option value="empty">[Empty]</option>
                                        @foreach ($groups as $user_id=>$user_name)
                                            <option value="{{$user_id}}">{{$user_name}}</option>
                                        @endforeach
                                    </select>
                                </td>
								<td>
   
                                    <select class="form-control form-filter input-sm" name="review_user_id">
                                        <option value="">Select...</option>
										<option value="empty">[Empty]</option>
                                        @foreach ($users as $user_id=>$user_name)
                                            <option value="{{$user_id}}">{{$user_name}}</option>
                                        @endforeach
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
                    </div>
                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
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
                src: $("#datatable_ajax_asin"),
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
                    "dom": "<'row' <'col-md-12'>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable

                    "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
                    "aoColumnDefs": [ { "bSortable": false, "aTargets": [ 0 , 14 ] }],
                    "lengthMenu": [
                        [10, 20, 50, -1],
                        [10, 20, 50, 'All'] // change per page values here
                    ],
                    "pageLength": 10, // default record count per page

                    "ajax": {
                        "url": "{{ url('asin/get')}}", // ajax source
                    },
                    "order": [
                        [1, "desc"]
                    ],// set first column as a default sort by asc
                    "createdRow": function( row, data, dataIndex ) {
                        $(row).children('td').eq(3).attr('style', 'text-align: left;')
                    },
                }
            });

            // handle group actionsubmit button click
			//$("#table-actions-wrapper") grid.getTableWrapper()
            $("#table-actions-wrapper").on('click', '.table-group-action-submit', function (e) {
                e.preventDefault();
                var giveUser = $("#giveUser", $("#table-actions-wrapper"));
				var giveReviewUser = $("#giveReviewUser", $("#table-actions-wrapper"));
				var giveStar = $("#giveStar", $("#table-actions-wrapper"));
				
                if ((giveUser.val() != "" || giveReviewUser.val() != "" || giveStar.val() != "") && grid.getSelectedRowsCount() > 0) {
                    grid.setAjaxParam("customActionType", "group_action");

                    grid.setAjaxParam("giveUser", giveUser.val());
					grid.setAjaxParam("giveReviewUser", giveReviewUser.val());
					grid.setAjaxParam("giveStar", giveStar.val());
                    grid.setAjaxParam("id", grid.getSelectedRows());
                    grid.getDataTable().draw(false);
                    //grid.clearAjaxParams();
                } else if (giveUser.val() == "" && giveReviewUser.val() == "" && giveStar.val() == "") {
                    App.alert({
                        type: 'danger',
                        icon: 'warning',
                        message: 'Please select an action',
                        container: $("#table-actions-wrapper"),
                        place: 'prepend'
                    });
                } else if (grid.getSelectedRowsCount() === 0) {
                    App.alert({
                        type: 'danger',
                        icon: 'warning',
                        message: 'No record selected',
                        container: $("#table-actions-wrapper"),
                        place: 'prepend'
                    });
                }
            });

            //grid.setAjaxParam("customActionType", "group_action");
            grid.setAjaxParam("site", $("input[name='site']").val());
            grid.setAjaxParam("asin", $("input[name='asin']").val());
			grid.setAjaxParam("sellersku", $("input[name='sellersku']").val());
            grid.setAjaxParam("item_no", $("input[name='item_no']").val());
			grid.setAjaxParam("item_model", $("input[name='item_model']").val());
			grid.setAjaxParam("status", $("select[name='status']").val());
            grid.setAjaxParam("item_group", $("input[name='item_group']").val());
            grid.setAjaxParam("brand_line", $("input[name='brand_line']").val());
            grid.setAjaxParam("seller", $("input[name='seller']").val());
			grid.setAjaxParam("bg", $("input[name='bg']").val());
			grid.setAjaxParam("bu", $("input[name='bu']").val());
            grid.setAjaxParam("group_id", $("select[name='group_id']").val());
			grid.setAjaxParam("review_user_id", $("select[name='review_user_id']").val());
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
	$("#vl_list_export").click(function(){
		location.href='/asinexport?item_model='+$("input[name='item_model']").val()+'&site='+$("input[name='site']").val()+'&asin='+$("input[name='asin']").val()+'&sellersku='+$("input[name='sellersku']").val()+'&item_no='+$("input[name='item_no']").val()+'&item_group='+$("input[name='item_group']").val()+'&bg='+$("input[name='bg']").val()+'&seller='+$("input[name='seller']").val()+'&brand_line='+$("input[name='brand_line']").val()+'&bu='+$("input[name='bu']").val()+'&status='+$("select[name='status']").val()+'&user_id='+$("select[name='review_user_id']").val()+'&group_id='+$("select[name='group_id']").val();
	});
});


</script>


@endsection
