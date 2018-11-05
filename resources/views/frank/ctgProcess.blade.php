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
                </ul>

                <div class="tab-content">

                    <div role="tabpanel" class="tab-pane" id="ctg-info">
                        <form class="row">
                            <div class="col-md-8">
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
                                    <a href="#step-1">留评确认<br/>
                                        <small>确认客户是否留评</small>
                                    </a>
                                </li>
                                <li>
                                    <a href="#step-2">发货<br/>
                                        <small>确认是否已发货</small>
                                    </a>
                                </li>
                                <li>
                                    <a href="#step-3">收货确认<br/>
                                        <small>确认客户是否已收货</small>
                                    </a>
                                </li>
                                <li>
                                    <a href="#step-4">引导留评<br/>
                                        <small>跟进客户留评</small>
                                    </a>
                                </li>
                                <li>
                                    <a href="#step-5">再营销<br/>
                                        <small>再营销为多次循环过程</small>
                                    </a>
                                </li>
                            </ul>

                            <div style="min-height:250px;" class="pages">
                                <div id="step-1">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>
                                                    Review ID
                                                    <input required pattern="^\w+$" autocomplete="off" class="xform-autotrim form-control" placeholder="Review ID" name="review_id"/>
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
                                                    Shipment ID
                                                    <input required pattern="^\w+$" autocomplete="off" class="xform-autotrim form-control" placeholder="Shipment ID" name="shipment_id"/>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="step-3">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="step-4">
                                    <ul>
                                        <li>跟进客户留评</li>
                                    </ul>
                                </div>
                                <div id="step-5">
                                    <ul>
                                        <li>再营销</li>
                                    </ul>
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
                </div>

            </div>
        </div>
    </div>

    <script>
        $('#ctg-info > form').submit(function () {
            $.ajax({
                method: 'POST',
                url: location.href,
                data: $(this).serialize(),
                dataType: 'json',
                success(arr) {
                    if (false === arr[0]) return toastr.error(arr[1])
                    toastr.success('Saved !')
                }
            })
            return false
        })
    </script>

    <script type="text/javascript">
        $(function ($) {

            let steps = <?php echo empty($ctgRow['steps']) ? '{}' : $ctgRow['steps']; ?>;

            let $thewizard = $('#thewizard')

            $thewizard.smartWizard({
                selected: parseInt(steps.current_index) || 0, // bug 传数字字符串就麻烦了
                theme: 'arrows',
                useURLhash: false,
                keyNavigation: false,
                showStepURLhash: false,
                autoAdjustHeight: false,
                lang: {
                    next: 'Continue >',
                    previous: '< Back'
                },
                toolbarSettings: {
                    toolbarExtraButtons: [
                        $('<button class="btn blue" style="width:9em" type="submit">Save</button>')
                    ]
                }
            })

            let wizardInstance = $thewizard.data('smartWizard')

            $thewizard.on('showStep', (e, anchorObject, stepNumber, stepDirection) => {
                ue.loadTrackNote()
                // console.log(wizardInstance.current_index, stepNumber)
            })


            $thewizard.on('leaveStep', (e, anchorObject, stepNumber, stepDirection) => {
                // localStorage
                // console.log(e, anchorObject, stepNumber, stepDirection)
                if ('backward' === stepDirection) return true
                $pages = $thewizard.children('.pages').children('div')
                $inputs = $($pages[wizardInstance.current_index]).find('[name]')
                for (let input of $inputs) {
                    if (!input.reportValidity()) {
                        return false
                    }
                }
            })


            let statusDict = {
                0: 'check review',
                1: 'do delivery',
                2: 'check delivery',
                3: 'ask for review',
                4: 're sg'
            }

            $thewizard.submit(function () {

                // todo 退出自动保存、提示
                // todo 禁用 UE 自动缓存

                let steps = rows2object($thewizard.serializeArray(), 'name', 'value')
                steps.current_index = wizardInstance.current_index
                steps.track_notes = track_notes
                let status = statusDict[wizardInstance.current_index]
                let commented = steps.review_id ? 1 : 0

                // jQuery 的 urlencode 中 + 号，似乎不太靠谱
                // 使用 JSON 提交可以避免数字变字符串的问题
                $.ajax({
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    url: location.href,
                    data: JSON.stringify({steps, status, commented}),
                    dataType: 'json',
                    success(arr) {
                        if (false === arr[0]) return toastr.error(arr[1])
                        toastr.success('Saved !')
                    }
                })

                return false
            })


            let ue = UE.getEditor('bdeditor', {
                topOffset: 60,
                initialFrameWidth: "100%",
            })

            ue.ready(function () {
                ue.execCommand('serverparam', '_token', '{!! csrf_token() !!}')
                ue.loadTrackNote()
            })

            let track_notes = steps.track_notes || {}

            ue.saveTrackNote = function () {
                track_notes[statusDict[wizardInstance.current_index]] = ue.getContent()
            }

            ue.loadTrackNote = function () {
                ue.setContent(track_notes[statusDict[wizardInstance.current_index]] || '')
            }

            ue.addListener('blur', ue.saveTrackNote)

            for (let input of $thewizard.find('[name]')) {
                input.value = steps[input.name] || ''
            }

            let activeTab = @json($ctgRow['processor']>0)? 'process-steps' : 'ctg-info'
            $(`#tabs a[href="#${activeTab}"]`).tab('show')

        })
    </script>

@endsection