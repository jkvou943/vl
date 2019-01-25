@extends('layouts.layout')
@section('label', 'Add Qa')
@section('content')
    <h1 class="page-title font-red-intense"> Add Qa
        <small>Configure your Qa.</small>
    </h1>


    <div class="row"><div class="col-md-8">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-settings font-dark"></i>
                    <span class="caption-subject font-dark sbold uppercase">Qa Form</span>
                </div>
            </div>
            <div class="portlet-body form">
                @if($errors->any())
                    <div class="alert alert-danger">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif
                <form role="form" action="{{ url('qa') }}" method="POST">
                    {{ csrf_field() }}
                    <div class="form-body">
					
						<div class="form-group">
                            <label>Product Line</label>
                            <div class="epoint_selectList form-inline">
							<select class="epoint_group form-control" name="product_line" required>
							</select>
							<select class="epoint_product form-control" name="product">							
							</select>
							<select class="epoint form-control" name="epoint">
							</select>
						</div>
						<script type="text/javascript">
						$(function(){
							$(".epoint_selectList").each(function(){
								var url = "/epoint.json";
								var epointJson;
								var temp_html;
								var oepoint_group = $(this).find(".epoint_group");
								var oepoint_product = $(this).find(".epoint_product");
								var oepoint = $(this).find(".epoint");

								var epoint_group = function(){
									temp_html = '<option value="">Group</option>';
									$.each(epointJson,function(i,epoint_group){
										temp_html+='<option value="'+i+'" '+((oepoint_group.val() == i) ?"selected":"")+'>'+i+'</option>';
									});
									oepoint_group.html(temp_html);
									epoint_product();
								};
			
								var epoint_product = function(){
									temp_html = '<option value="">Product</option>';
									
										var n = oepoint_group.val();
										if(typeof(epointJson[n]) == "undefined"){
											oepoint_product.css("display","none");
											oepoint.css("display","none");
										}else if($.isEmptyObject(epointJson[n])){
											oepoint_product.css("display","none");
											oepoint.css("display","none");
										}else{
											oepoint_product.css("display","inline");
											$.each(epointJson[n],function(i,epoint_product){
												temp_html+='<option value="'+i+'" '+((oepoint_product.val() == i) ?"selected":"")+'>'+i+'</option>';
											});
											oepoint_product.html(temp_html);
											epoint();
										}
									
								};
						
								var epoint = function(){
									temp_html = '<option value="">Problem</option>'; 
									var m = oepoint_group.val();
									var n = oepoint_product.val();
							
										if(typeof(epointJson[m][n]) == "undefined"){
											oepoint.css("display","none");
										}else if($.isEmptyObject(epointJson[m][n])){
											oepoint.css("display","none");
										}else{
											oepoint.css("display","inline");
											$.each(epointJson[m][n],function(i,epoint){
												temp_html+='<option value="'+epoint+'" '+((oepoint.val() == epoint) ?"selected":"")+'>'+epoint+'</option>';
											});
											oepoint.html(temp_html);
										};
									
								};
								oepoint_group.change(function(){
									epoint_product();
								});
								oepoint_product.change(function(){
									epoint();
								});
								$.getJSON(url,function(data){
									epointJson = data;
									epoint_group();
								});
							});
						});
						</script>
                        </div>
						
						<div class="form-group">
                            <label>Model</label>
                            <div class="input-group ">
                                <span class="input-group-addon">
                                    <i class="fa fa-envelope"></i>
                                </span>
                                <input type="text" class="form-control" placeholder="Model" name="model" id="model" value="{{old('model')}}" required />
                            </div>
                        </div>
		
						
						<div class="form-group">
                            <label>Item No.</label>
                            <div class="input-group ">
                                <span class="input-group-addon">
                                    <i class="fa fa-envelope"></i>
                                </span>
                                <input type="text" class="form-control" placeholder="Item No." name="item_no" id="item_no" value="{{old('item_no')}}" required />
                            </div>
                        </div>
						
						
						<div class="form-group">
                        <label>Question Type</label>
                        <div class="input-group ">
                        <span class="input-group-addon">
                            <i class="fa fa-bookmark"></i>
                        </span>
                            <select class="form-control" name="etype" id="etype">
                                <option value="">None</option>
                                @foreach (getEType() as $etype)
                                    <option value="{{$etype}}" <?php if($etype==old('etype')) echo 'selected';?>>{{$etype}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
					
					
                        <div class="form-group">
                            <label>Title</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-user"></i>
                                </span>
                                <input type="text" class="form-control" name="title" id="title" value="{{old('title')}}" required>
                            </div>
                        </div>
						
						<div class="form-group">
                            <label>Details</label>
                            <div class="input-group">
                                @include('UEditor::head')

                                    <!-- 加载编辑器的容器 -->
                                    <script id="qa_content" name="description" type="text/plain">
									<?php echo old('description'); ?>
									</script>
                                    <!-- 实例化编辑器 -->
                                    <script type="text/javascript">
                                        var ue = UE.getEditor('qa_content',{toolbars: [[
            'fullscreen', 'source', '|', 'undo', 'redo', '|',
            'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc', '|',
            'rowspacingtop', 'rowspacingbottom', 'lineheight', '|',
            'customstyle', 'paragraph', 'fontfamily', 'fontsize', '|',
            'directionalityltr', 'directionalityrtl', 'indent', '|',
            'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|', 'touppercase', 'tolowercase', '|',
            'link', 'unlink', 'anchor', '|', 'imagenone', 'imageleft', 'imageright', 'imagecenter', '|',
            'simpleupload', 'insertimage', 'emotion', 'scrawl', 'insertvideo', 'music', 'attachment', 'map', 'gmap', 'insertframe', 'insertcode', 'webapp', 'pagebreak', 'template', 'background', '|',
            'horizontal', 'date', 'time', 'spechars', 'snapscreen', 'wordimage', '|',
            'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', 'charts', '|',
            'print', 'preview', 'searchreplace', 'drafts', 'help'
        ]]});
                                        ue.ready(function() {
                                            ue.execCommand('serverparam', '_token', '{{ csrf_token() }}');//此处为支持laravel5 csrf ,根据实际情况修改,目的就是设置 _token 值.
                                        });
                               		 </script>
                            </div>
                        </div>
						
						
						
						<div class="form-group">
                            <label>Details （Chinese）</label>
                            <div class="input-group">
                                    <!-- 加载编辑器的容器 -->
                                    <script id="dqe_content" name="dqe_content" type="text/plain">
									<?php echo old('dqe_content'); ?>
									</script>
                                    <!-- 实例化编辑器 -->
                                    <script type="text/javascript">
                                        var ue = UE.getEditor('dqe_content',{toolbars: [[
            'fullscreen', 'source', '|', 'undo', 'redo', '|',
            'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc', '|',
            'rowspacingtop', 'rowspacingbottom', 'lineheight', '|',
            'customstyle', 'paragraph', 'fontfamily', 'fontsize', '|',
            'directionalityltr', 'directionalityrtl', 'indent', '|',
            'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|', 'touppercase', 'tolowercase', '|',
            'link', 'unlink', 'anchor', '|', 'imagenone', 'imageleft', 'imageright', 'imagecenter', '|',
            'simpleupload', 'insertimage', 'emotion', 'scrawl', 'insertvideo', 'music', 'attachment', 'map', 'gmap', 'insertframe', 'insertcode', 'webapp', 'pagebreak', 'template', 'background', '|',
            'horizontal', 'date', 'time', 'spechars', 'snapscreen', 'wordimage', '|',
            'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', 'charts', '|',
            'print', 'preview', 'searchreplace', 'drafts', 'help'
        ]]});
                                        ue.ready(function() {
                                            ue.execCommand('serverparam', '_token', '{{ csrf_token() }}');//此处为支持laravel5 csrf ,根据实际情况修改,目的就是设置 _token 值.
                                        });
                               		 </script>
                            </div>
                        </div>
						
                       
						
						<div class="form-group">
                            <label>Status</label>
                            <div class="input-group col-md-6">
                                <span class="input-group-addon">
                                    <i class="fa fa-user"></i>
                                </span>
                                <select class="form-control form-filter input-sm" name="confirm">
									<option value="0" <?php if(0==old('confirm')) echo 'selected';?>>UnConfirm</option>
									<option value="1" <?php if(1==old('confirm')) echo 'selected';?>>Confirmed</option>
									
								</select>
                            </div>
                        </div>
						
						
						





 

                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-4 col-md-8">
                                <button type="submit" class="btn blue">Submit</button>
                                <button type="reset" class="btn grey-salsa btn-outline">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


    </div>


@endsection
