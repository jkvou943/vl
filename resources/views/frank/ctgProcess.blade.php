@extends('layouts.layout')
@section('crumb')
    @include('layouts.crumb', ['crumbs'=>[['CTG', '/ctg/list'], 'Process']])
@endsection
@section('content')

    <style>
        .font-dark {
            color: #5888b9 !important;
        }
    </style>

    <link rel="stylesheet" href="/js/SmartWizard/css/smart_wizard.min.css"/>
    <link rel="stylesheet" href="/js/SmartWizard/css/smart_wizard_theme_arrows.min.css"/>
    <script src="/js/SmartWizard/js/jquery.smartWizard.min.js"></script>

    @include('frank.common')
    @include('UEditor::head')

    <h1 class="page-title font-red-intense"> CTG Process
        <small></small>
    </h1>

    <div class="portlet light bordered">

        <div class="portlet-title">
            <div class="caption">
                <i class="icon-ghost font-dark"></i>
                <span class="caption-subject uppercase">Order ID <span class="font-dark">{!! $ctgRow['order_id'] !!}</span></span>
            </div>
        </div>

        <div class="portlet-body">
            <div>
                <ul class="nav nav-tabs" role="tablist" id="tabs">
                    <li role="presentation"><a href="#ctg-info" aria-controls="ctg-info" role="tab" data-toggle="tab">CTG Info</a></li>
                    <li role="presentation"><a href="#process-steps" aria-controls="process-steps" role="tab" data-toggle="tab">Process Steps</a></li>
                    <li role="presentation"><a href="#order-info" aria-controls="order-info" role="tab" data-toggle="tab">Amazon Order Info</a></li>
                    <li role="presentation"><a href="#email-history" aria-controls="email-history" role="tab" data-toggle="tab">Email History</a></li>
                </ul>

                <div class="tab-content">

                    <div role="tabpanel" class="tab-pane" id="ctg-info">
                        <form class="row">
                            <div class="col-md-8">
								<div class="font-dark">Date</div>
                                <pre>{!! $ctgRow['created_at'] !!}</pre>
                                <div class="font-dark">Gift SKU</div>
                                <pre>{!! $ctgRow['gift_sku'] !!}</pre>
                                <div class="font-dark">Order ID</div>
                                <pre>{!! $ctgRow['order_id'] !!}</pre>
                                <div class="font-dark">Customer Information</div>
                                <pre>Name: {!! $ctgRow['name'] !!}<br/>Phone: {!! $ctgRow['phone'] !!}<br/>Email: {!! $ctgRow['email'] !!}<br/>Address:<br/>{!! $ctgRow['address'] !!}</pre>
                                <div class="font-dark">Remark</div>
                                <pre>{!! $ctgRow['note'] !!}</pre>
                                <br/>
                                <div class="form-group">
                                    <label>
                                        Task Assign to
                                        <input required autocomplete="off" class="xform-autotrim form-control" placeholder="Processor" name="processor"
                                               value="{!! $ctgRow['processor']>0?"{$ctgRow['processor']} | {$users[$ctgRow['processor']]}":'' !!}" style="width:27em" list="list-users"/>
                                        <datalist id="list-users">
                                            @foreach($users as $id=>$name)
                                                <option value="{!! $id !!} | {!! $name !!}">
                                            @endforeach
                                        </datalist>
                                    </label>
                                </div>
                                <button class="btn blue" style="width:9em;" type="submit">Save</button>
                            </div>
                        </form>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="process-steps">
                        <form id="thewizard" novalidate>
                            <ul>
                                <li>
                                    <a href="#step-1">Confirm Review<br/>
                                        <small>if the customer has left a review</small>
                                    </a>
                                </li>
                                <li>
                                    <a href="#step-2">Arrange shipment<br/>
                                        <small>if the product has been shipped out</small>
                                    </a>
                                </li>
                                <li>
                                    <a href="#step-3">Delivery confirmation<br/>
                                        <small>if the customer has received the item</small>
                                    </a>
                                </li>
                                <li>
                                    <a href="#step-4">Lead to leave review<br/>
                                        <small>if the customer hasn't</small>
                                    </a>
                                </li>
                                <li>
                                    <a href="#step-5">Re-SG<br/>
                                        <small>it is a cyclic process</small>
                                    </a>
                                </li>
                            </ul>

                            <div style="min-height:250px;" class="pages">
                                <div id="step-1">
                                    <br/>
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <ul>
                                                @foreach($order['orderItems'] as $item)
                                                    <li>
                                                        <a target="_blank" rel="noreferrer" href="https://www.{!! $order['SalesChannel'] !!}/product-reviews/{!! $item['ASIN'] !!}?sortBy=recent"
                                                           title="{!! $item['SellerSKU'] !!}">{!! $item['Title'] !!}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                    <br/>
                                    <div class="row">
                                        <div class="col-xs-3" style="padding-left:3em;">
                                            <div class="form-group">
                                                <span>Had the customer left a review ?</span><br/>
                                                <label style="margin-right:5em;">
                                                    <input type="radio" name="commented" value="1" checked/>
                                                    Yes
                                                </label>
                                                <label>
                                                    <input type="radio" name="commented" value="0"/>
                                                    No
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <label>
                                                    <span>And the review ID ?</span>
                                                    <input required pattern="^\w+( +\w+)*$" autocomplete="off" class="xform-autotrim form-control" placeholder="Review ID Separated by spaces" name="review_id"
                                                           data-enable-radio="commented"/>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="step-2">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>
                                                    Replacement ID
                                                    <input required pattern=".*\S+.*" autocomplete="off" class="xform-autotrim form-control" placeholder="Shipment ID" name="shipment_id"/>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="step-3">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <pre>确认是否收货	是/否   是直接进入下一步</pre>
                                        </div>
                                    </div>
                                </div>
                                <div id="step-4">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>
                                                    Review ID Separated by spaces
                                                    <input required pattern="^\w+( +\w+)*$" autocomplete="off" class="xform-autotrim form-control" placeholder="Review ID Separated by spaces" data-assoc-name="review_id"/>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="step-5">
                                    <pre>
再营销/re-SG
	再营销为多次循环过程
	以下应该按照第一次、第二次分别记录并存档

确认推荐产品	选择推荐产品
确认意向	意向度低--表示无意向做
	意向度中--表示可以做，在咨询条件
	意向度高--明确表示可以做
	未明确意向--无回复
跟进记录	已下单
	已留评
	已退款
	完成
                                    </pre>
                                </div>
                            </div>
                        </form>
                        <br><br><br>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Tracking Note</label>
                                    <script id="bdeditor" type="text/plain"></script>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="order-info">
                        @if(!empty($order['orderItems']))
                            <div class="invoice-content-2 bordered">
                                <div class="row invoice-head">
                                    <div class="col-md-7 col-xs-6">
                                        <div class="invoice-logo">
                                            <h1 class="uppercase">{!! $order['AmazonOrderId'] !!} ( {!! $order['SellerName'] !!} )</h1>
                                            Buyer Email : {!! $order['BuyerEmail'] !!}<BR>
                                            Buyer Name : {!! $order['BuyerName'] !!}<BR>
                                            PurchaseDate : {!! $order['PurchaseDate'] !!}
                                        </div>
                                    </div>
                                    <div class="col-md-5 col-xs-6">
                                        <div class="company-address">
                                            <span class="bold ">{!! $order['Name'] !!}</span>
                                            <br> {!! $order['AddressLine1'] !!}
                                            <br> {!! $order['AddressLine2'] !!}
                                            <br> {!! $order['AddressLine3'] !!}
                                            <br> {!! $order['City'] !!} {!! $order['StateOrRegion'] !!} {!! $order['CountryCode'] !!}
                                            <br> {!! $order['PostalCode'] !!}
                                        </div>
                                    </div>
                                </div>
                                <BR><BR>
                                <div class="row invoice-cust-add">
                                    <div class="col-xs-3">
                                        <h4 class="invoice-title ">Seller ID</h4>
                                        <p class="invoice-desc">{!! $order['SellerId'] !!}   </p>
                                    </div>
                                    <div class="col-xs-3">
                                        <h4 class="invoice-title ">Site</h4>
                                        <p class="invoice-desc">{!! $order['SalesChannel'] !!}</p>
                                    </div>
                                    <div class="col-xs-2">
                                        <h4 class="invoice-title ">Fulfillment Channel</h4>
                                        <p class="invoice-desc">{!! $order['FulfillmentChannel'] !!}</p>
                                    </div>
                                    <div class="col-xs-2">
                                        <h4 class="invoice-title ">Ship Service Level</h4>
                                        <p class="invoice-desc">{!! $order['ShipServiceLevel'] !!}</p>
                                    </div>

                                    <div class="col-xs-2">
                                        <h4 class="invoice-title ">Status</h4>
                                        <p class="invoice-desc">{!! $order['OrderStatus'] !!}</p>
                                    </div>
                                </div>
                                <BR><BR>
                                <div class="row invoice-body">
                                    <div class="col-xs-12 table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                            <tr>
                                                <th class="invoice-title uppercase">Description</th>
                                                <th class="invoice-title uppercase text-center">Qty</th>
                                                <th class="invoice-title uppercase text-center">Price</th>
                                                <th class="invoice-title uppercase text-center">Shipping</th>
                                                <th class="invoice-title uppercase text-center">Promotion</th>
                                                <th class="invoice-title uppercase text-center">Tax</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($order['orderItems'] as $item)
                                                <tr>
                                                    <td>
                                                        <h4>{!! $item['ASIN'] !!} ( {!! $item['SellerSKU'] !!} )</h4>
                                                        <p> {!! $item['Title'] !!} </p>
                                                    </td>
                                                    <td class="text-center sbold">{!! $item['QuantityOrdered'] !!}</td>
                                                    <td class="text-center sbold">{{round($item['ItemPriceAmount']/$item['QuantityOrdered'],2)}}</td>
                                                    <td class="text-center sbold">{{round($item['ShippingPriceAmount'],2)}} {{($item['ShippingDiscountAmount'])?'( -'.round($item['ShippingDiscountAmount'],2).' )':''}}</td>
                                                    <td class="text-center sbold">{{($item['PromotionDiscountAmount'])?'( -'.round($item['PromotionDiscountAmount'],2).' )':''}}</td>
                                                    <td class="text-center sbold">{{round($item['ItemTaxAmount'],2)}}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row invoice-subtotal">
                                    <div class="col-xs-6">
                                        <h4 class="invoice-title uppercase">Total</h4>
                                        <p class="invoice-desc grand-total">{{round($order['Amount'],2)}} {{$order['CurrencyCode']}}</p>
                                    </div>
                                </div>

                            </div>
                        @else
                            <b>Can not match or find order</b>
                        @endif
                    </div>

                    <div role="tabpanel" class="tab-pane" id="email-history">
                        <div class="table-container">
                            <table class="table table-striped table-bordered table-hover order-column" id="email_table">
                                <thead>
                                <tr>
                                    <th>From Address</th>
                                    <th>To Address</th>
                                    <th>Subject</th>
                                    <th>Date</th>
                                    <th>User</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($emails as $data)
                                    <tr class="odd gradeX">
                                        <td>
                                            {{array_get($data,'from_address')}}
                                        </td>
                                        <td>
                                            {{array_get($data,'to_address')}}
                                        </td>
                                        <td>
                                            <a href="/send/{{array_get($data,'id')}}" target="_blank"> {{array_get($data,'subject')}}</a>
                                        </td>
                                        <td>
                                            {{array_get($data,'date')}}
                                        </td>
                                        <td>
                                            {{array_get($users,array_get($data,'user_id'))}}
                                        </td>
                                        <td>
                                            {!!array_get($data,'send_date')?'<span class="label label-sm label-success">'.array_get($data,'send_date').'</span> ':'<span class="label label-sm label-danger">'.array_get($data,'status').'</span>'!!}
                                        </td>

                                    </tr>
                                @endforeach


                                </tbody>
                            </table>
                            <script>
                                $(function () {
                                    $('#email_table').dataTable({
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
                                                "previous": "Prev",
                                                "next": "Next",
                                                "last": "Last",
                                                "first": "First"
                                            }
                                        },

                                        "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
                                        "autoWidth": false,
                                        "lengthMenu": [
                                            [10, 50, 100, -1],
                                            [10, 50, 100, "All"] // change per page values here
                                        ],
                                        // set the initial value
                                        "pageLength": 10,
                                        "order": [
                                            [3, "desc"]
                                        ] // set first column as a default sort by asc
                                    });
                                });
                            </script>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script type="text/javascript">

        // 不使用数字作为status，以备流程有增删改动
        let statusDict = {
            0: 'Confirm Review',
            1: 'Arrange Shipment',
            2: 'Delivery Confirmation',
            3: 'Lead To Leave Review',
            4: 'Re-SG'
        }

        $('#ctg-info > form').submit(function () {

            postByJson(location.href, this).then(arr => {
                toastr.success('Saved !')
            }).catch(err => {
                toastr.error(err.message)
            })

            return false
        })

        $(function ($) {

            let _steps = <?php echo empty($ctgRow['steps']) ? '{}' : $ctgRow['steps']; ?>;

            let $thewizard = $('#thewizard')

            let current_index = rows2object(Object.keys(statusDict).map(i => [i, statusDict[i]]), 1, 0)["{!! $ctgRow['status'] !!}"]

            $thewizard.smartWizard({
                selected: parseInt(current_index) || 0, // bug 传数字字符串就麻烦了
                theme: 'arrows',
                useURLhash: false,
                keyNavigation: false,
                showStepURLhash: false,
                autoAdjustHeight: false,
                hiddenSteps: (_steps.commented || 0) - 1 < 0 ? [] : [3],
                lang: {
                    next: 'Continue >',
                    previous: '< Back'
                },
                toolbarSettings: {
                    toolbarExtraButtons: [
                        $('<button class="btn blue" style="width:9em" type="submit">Save</button>'),
						$('<a href="/send/create?from_address=support@claimthegift.com&to_address={!!$ctgRow['email']!!}&subject=Claim the gift" target="_blank"><button class="btn green" style="width:9em" type="button">Compose</button></a>')
                    ]
                }
            })

            let wizardInstance = $thewizard.data('smartWizard')

            $thewizard.on('showStep', (e, anchorObject, stepNumber, stepDirection) => {
                ue.loadTrackNote()
            })


            $thewizard.on('leaveStep', (e, anchorObject, stepNumber, stepDirection) => {
                switch (stepNumber) {
                    case 0:
                        wizardInstance.stepState(3, parseInt(thewizard.commented.value) < 1 ? 'show' : 'hide')
                        break
                }
                if ('backward' === stepDirection) return true
                $pages = $thewizard.children('.pages').children('div')
                $inputs = $($pages[wizardInstance.current_index]).find('[name],[data-assoc-name]')
                for (let input of $inputs) {
                    if (!input.reportValidity()) {
                        return false
                    }
                }
            })


            $thewizard.submit(function () {

                // todo 退出自动保存、提示

                let steps = rows2object($thewizard.serializeArray(), 'name', 'value')
                steps = Object.assign(_steps, steps)
                // steps.current_index = wizardInstance.current_index
                steps.track_notes = track_notes
                let status = statusDict[wizardInstance.current_index]
                let commented = steps.commented

                // jQuery 的 urlencode 中 + 号，似乎不太靠谱
                // 使用 JSON 提交可以避免数字变字符串的问题
                postByJson(location.href, {steps, status, commented}).then(arr => {
                    toastr.success('Saved !')
                }).catch(err => {
                    toastr.error(err.message)
                })

                return false
            })


            let ue = UE.getEditor('bdeditor', {
                topOffset: 60,
                autoSyncData: false,
                enableAutoSave: false,
                initialFrameWidth: "100%",
            })

            ue.ready(function () {
                ue.execCommand('serverparam', '_token', '{!! csrf_token() !!}')
                ue.loadTrackNote()
            })

            let track_notes = _steps.track_notes
            // arr = []
            // arr.a = 333
            // JSON.stringify(arr)
            // 结果是 []
            if (!track_notes || (track_notes instanceof Array)) {
                track_notes = {}
            }

            ue.saveTrackNote = function () {
                track_notes[statusDict[wizardInstance.current_index]] = ue.getContent()
            }

            ue.loadTrackNote = function () {
                ue.setContent(track_notes[statusDict[wizardInstance.current_index]] || '')
            }

            ue.addListener('blur', ue.saveTrackNote)

            for (let input of $thewizard.find('[name]')) {
                // formElement.elements 属性包含所有输入框、选择框等等
                // 可使用 for of 循环遍历，另外有 name 属性的可通过 name 访问
                // 具有相同 name 的 input[radio] 自带分组处理功能
                // formElement.filedName.value // 此种写法兼容 radio、checkbox 等等
                thewizard[input.name].value = _steps[input.name] || ''
            }

            XFormHelper.inputEnableByRadio(thewizard)
            XFormHelper.assocFormControls(thewizard)

            let activeTab = @json($ctgRow['processor']>0)? 'process-steps' : 'ctg-info'
            $(`#tabs a[href="#${activeTab}"]`).tab('show')

        })
    </script>

@endsection
