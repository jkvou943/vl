@extends('layouts.layout')
@section('crumb')
    @include('layouts.crumb', ['crumbs'=>['KMS']])
@endsection
@section('content')

    @include('frank.common')

    <style>
        .user-manual-file {
            text-overflow: ellipsis;
            max-width: 176px;
            white-space: nowrap;
            overflow: hidden;
            display: inline-block;
            vertical-align: bottom;
        }
    </style>

    <h1 class="page-title font-red-intense"> Product Guide
        <small></small>
    </h1>

    <div class="portlet light bordered">
        <div class="portlet-body">
            <div class="table-toolbar">
                <div class="row">

                    <div class="col-md-2">
                        <div class="input-group">
                            <span class="input-group-addon">Item Group</span>
                            <input type="text" class="xform-autotrim form-control" data-init-by-query="ands.item_group" placeholder="Item Group..." id="item_group" autocomplete="off"/>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="input-group">
                            <span class="input-group-addon">Brand</span>
                            <input type="text" class="xform-autotrim form-control" data-init-by-query="ands.brand" placeholder="Brand..." id="brand" autocomplete="off"/>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="input-group">
                            <span class="input-group-addon">Model</span>
                            <input type="text" class="xform-autotrim form-control" data-init-by-query="ands.item_model" placeholder="Item Model..." id="item_model" autocomplete="off"/>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="input-group">
                            <input type="text" class="xform-autotrim form-control" data-init-by-query="value" placeholder="Fuzzy search..." id="fuzzysearch" autocomplete="off"/>
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button" id="dosearch">Search!</button>
                            </span>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="btn-group " style="float:right;">
                            <button id="vl_list_export" class="btn sbold blue"> Export
                                <i class="fa fa-download"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div style="clear:both;height:50px;"></div>
            <div class="table-container" style="">
                <table class="table table-striped table-bordered" id="thetable">
                    <thead>
                    <tr>
                        <th>Item Group</th>
                        <th>Item Group Description</th>
                        <th>Brand</th>
                        <th>Model</th>
                        <th>User Manual</th>
                        <th>Video List</th>
                        <th>Q&A</th>
                        <th>Parts list</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <script>

        let $theTable = $(thetable);

        // init
        {
            XFormHelper.initByQuery('.form-control[data-init-by-query]')
            new LinkageInput([item_group, brand, item_model], @json($itemGroupBrandModels))
            bindDelayEvents([item_group, brand, item_model], 'change', () => $theTable.api().ajax.reload())
            bindDelayEvents(fuzzysearch, 'change keyup paste', () => $theTable.api().ajax.reload())
            $(dosearch).click(() => $theTable.api().ajax.reload())
        }
        // end init


        $theTable.on('preXhr.dt', (e, settings, data) => {

            Object.assign(data.search, {
                value: fuzzysearch.value,
                ands: {
                    item_group: item_group.value,
                    brand: brand.value,
                    item_model: item_model.value
                }
            })

            history.replaceState(null, null, '?' + objectToQueryString(data.search))
        })

        $theTable.dataTable({
            searching: false, // 不使用自带的搜索框
            // search: {search: queryStringToObject().value},
            serverSide: true,
            pagingType: 'bootstrap_extended',
            processing: true,
            columns: [
                {
                    data: 'item_group', name: 'item_group',
                    render(data) {
                        return `<a href="/asin?item_group=${data}" target="_blank">${data}</a>`
                    }
                },
                {data: 'brand_line', name: 'brand_line'},
                {data: 'brand', name: 'brand'},
                {data: 'item_model', name: 'item_model'},
                {
                    className: 'dt-body-right',
                    data: 'manualink', name: 'manualink',
                    // defaultContent: '<button class="btn btn-success btn-xs">View</button>',
                    render(data, type, row, meta) {
                        // let args = {'item_group': row.item_group, 'item_model': row.item_model}
                        // jQuery.param( ) 坑爹啊 jQuery uses + instead of %20 to URL-encode spaces
                        // enc_type http://php.net/manual/en/function.http-build-query.php

                        let href = `/kms/usermanual?${objectToQueryString(objectFilte(row, ['item_group', 'brand', 'item_model'], false))}`

                        if (data) {
                            let ms = data.match(/([^/]+\.\w+)$/)
                            let file = ms ? ms[1] : data
                            return `<a href="${data}" target="_blank" class="user-manual-file">${file}</a> <a href="${href}" target="_blank" class='btn btn-success btn-xs'>More</a>`
                        } else {
                            return `<a href="${href}" target="_blank" class='btn btn-default btn-xs'>No Data Available</a>`
                        }
                    }
                },
                {
                    data: 'has_video', name: 'has_video',
                    render(data, type, row, meta) {
                        return `<a href="/kms/videolist?${objectToQueryString(objectFilte(row, ['item_group', 'brand', 'item_model'], false))}" target="_blank" class='btn btn-${data > 0 ? 'success' : 'default'} btn-xs'>View</a>`
                    }
                },
                {
                    orderable: false,
                    searchable: false,
                    render() {
                        return `<a href="/question" target="_blank" class='btn btn-success btn-xs'>View</a>`
                    }
                },
                {
                    data: 'has_stock_info', name: 'has_stock_info',
                    render(data, type, row, meta) {
                        return `<a href="/kms/partslist?${objectToQueryString(objectFilte(row, ['item_group', 'brand', 'item_model'], false))}" target="_blank" class='btn btn-${data > 0 ? 'success' : 'default'} btn-xs'>View</a>`
                    }
                }
            ],
            ajax: {
                type: 'POST',
                url: '/kms/productguide/get',
                // dataSrc(json) { return json.data }
            }
        })

        // `<button type='button' class='btn btn-success btn-xs' data-search='${JSON.stringify(search)}'>View</button>`
        // $theTable.on('click', '.btn', (e) => {
        //     let search = $(e.target).data('search')
        //     if (!search) return;
        //     window.open(`/kms/${search.type}?${objectToQueryString(search.args)}`)
        // })
    </script>

@endsection