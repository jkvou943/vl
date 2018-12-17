@extends('layouts.layout')
@section('label', 'Automatic Price Adjustment')
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
                        <span class="caption-subject bold uppercase">Automatic Price Adjustment</span>
                    </div>
                </div>
                <div class="table-toolbar">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="btn-group">
                                <a data-target="#ajax" data-toggle="modal" href="{{ url('autoprice/create')}}"><button id="sample_editable_1_2_new" class="btn sbold blue"> Add New
                                    <i class="fa fa-plus"></i>
                                </button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="portlet-body">

                    <table class="table table-striped table-bordered table-hover order-column" id="manage_account">
                        <thead>
                        <tr>
                            <th > Account </th>
                            <th> Site </th>
                            <th> SellerSKU </th>
                            <th> Status </th> 
							<th> Asin </th>                      
							<th> Stock </th>
							<th> Sales </th>
							<th> Price </th>
							<th> Update Price </th>
							<th> Update Date </th>
							<th> User </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($rules as $rule)
                            <tr class="odd gradeX">
                                <td>
                                    <a data-target="#ajax" data-toggle="modal" href="{{ url('autoprice/'.$rule['id'].'/edit') }}">{{array_get($accounts,$rule['seller_id'])}}</a>
                                </td>
                                <td>
                                    <a data-target="#ajax" data-toggle="modal" href="{{ url('autoprice/'.$rule['id'].'/edit') }}">{{array_get(getSiteUrl(),$rule['marketplace_id'])}}</a>
                                </td>
                                <td>
                                    <a data-target="#ajax" data-toggle="modal" href="{{ url('autoprice/'.$rule['id'].'/edit') }}">{{$rule['seller_sku']}}</a>
                                </td>
                                <td>
                                    {!!array_get($actived,$rule['actived'])!!}
                                </td>
								<td>
                                    <a target="_blank" href="https://www.{{array_get(getSiteUrl(),$rule['marketplace_id'])}}/dp/{{$rule['asin']}}?m={{$rule['seller_id']}}">{{$rule['asin']}}</a>
                                </td>

								<td>
                                    {{($rule['fba_stock']+$rule['fbm_stock'])}}
                                </td>
								<td>
                                    {{$rule['sales']}}
                                </td>
								<td>
                                    {{$rule['price']}}
                                </td>
								<td>
                                    {{$rule['update_price']}}
                                </td>
								<td>
                                    {{$rule['submitted_date']}}
                                </td>
								 <td>
                                    {{array_get($users,$rule['user_id'],$rule['user_id'])}}
                                </td>
                            </tr>
                        @endforeach



                        </tbody>
                    </table>
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
        $(function() {
        var TableDatatablesManaged = function () {

            var initTable = function () {

                var table = $('#manage_account');

                // begin first table
                table.dataTable({

                    // Internationalisation. For more info refer to http://datatables.net/manual/i18n
                    "language": {
                        "aria": {
                            "sortAscending": ": activate to sort column ascending",
                            "sortDescending": ": activate to sort column descending"
                        },
                        "emptyTable": "No data available in table",
                        "info": "Showing _START_ to _END_ of _TOTAL_ records",
                        "infoEmpty": "No records found",
                        "infoFiltered": "(filtered1 from _MAX_ total records)",
                        "lengthMenu": "Show _MENU_",
                        "search": "Search:",
                        "zeroRecords": "No matching records found",
                        "paginate": {
                            "previous":"Prev",
                            "next": "Next",
                            "last": "Last",
                            "first": "First"
                        }
                    },

                    "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.

                    "lengthMenu": [
                        [5, 15, 20, -1],
                        [5, 15, 20, "All"] // change per page values here
                    ],
                    // set the initial value
                    "pageLength": 20,
                    "pagingType": "bootstrap_full_number",
                    "columnDefs": [

                        {
                            "className": "dt-right",
                            //"targets": [2]
                        }
                    ],
                    "order": [
                        [1, "asc"]
                    ] // set first column as a default sort by asc
                });

            }


            return {

                //main function to initiate the module
                init: function () {
                    if (!jQuery().dataTable) {
                        return;
                    }

                    initTable();
                }

            };

        }();



            TableDatatablesManaged.init();
			$("#ajax").on("hidden.bs.modal",function(){
				$(this).find('.modal-content').html('<div class="modal-body"><img src="../assets/global/img/loading-spinner-grey.gif" alt="" class="loading"><span>Loading... </span></div>');
			});
        });


</script>


@endsection
