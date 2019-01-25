<!DOCTYPE html>
<!-- 
Template Name: Metronic - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.7
Version: 4.7
Author: KeenThemes
Website: http://www.keenthemes.com/
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Dribbble: www.dribbble.com/keenthemes
Like: www.facebook.com/keenthemes
Purchase: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
Renew Support: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->

    <head>
        <meta charset="utf-8" />
        <title>Questions And Solutions</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="#1 selling multi-purpose bootstrap admin theme sold in themeforest marketplace packed with angularjs, material design, rtl support with over thausands of templates and ui elements and plugins to power any type of web applications including saas and admin dashboards. Preview page of Theme #1 for blog listing page"
            name="description" />
        <meta content="" name="author" />
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="/assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="/assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="/assets/global/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="/assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN PAGE LEVEL STYLES -->
        <link href="/assets/pages/css/blog.min.css" rel="stylesheet" type="text/css" />
		<link href="/assets/pages/css/search.min.css" rel="stylesheet" type="text/css">
        <!-- END PAGE LEVEL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
        <link href="/assets/layouts/layout/css/layout.min.css" rel="stylesheet" type="text/css" />
        <link href="/assets/layouts/layout/css/themes/darkblue.min.css" rel="stylesheet" type="text/css" id="style_color" />
        <link href="/assets/layouts/layout/css/custom.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME LAYOUT STYLES -->
        <link rel="shortcut icon" href="favicon.ico" />
		<style>
		.search-page .search-bar input,.search-page .search-bar select{
    border: 1px !important;
    background-color: #f3f6f9;
    color: #a0a9b4;
    height: 55px;
}
		</style>
		 </head>
    <!-- END HEAD -->

    <body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white" style="background:#eef1f5;">

	<div class="blog-page">

	<div class="search-page">
	<div class="search-bar" style="  position: fixed; z-index:999;background: #eee;">
                                <div class="row">
								<form method="get" action="/question">
									
                                    <div class="col-lg-7">
						<div class="col-md-3">
                            <select class="form-control" name="etype" id="etype">
                                <option value="">Question Type</option>
                                @foreach (getEType() as $etype)
                                    <option value="{{$etype}}">{{$etype}}</option>
                                @endforeach
                            </select>
                        </div>	
                                        <div class="col-md-9 input-group">
                                            <input type="text" class="form-control" name="keywords" placeholder="Search for..." >
                                            <span class="input-group-btn">
                                                <button class="btn blue uppercase bold" type="submit">Search</button>
                                            </span>
                                        </div>
                                    </div>
									</form>
                                    <div class="col-lg-5">
                                        <p class="search-desc clearfix" style="color: #333;
  font-size: 35px;
  text-align: center;">Questions And Solutions</p>
                                    </div>
                                </div>
                            </div>
	</div>

	 <div class="row" style="  padding-top: 120px;z-index:998;">
	 
	  <div class="col-lg-10 col-lg-offset-1">
                                    <div class="blog-single-content blog-container" style="padding: 30px;
  background-color: #fff;">
                                        <div class="blog-single-head">
                                            <h1 class="blog-single-head-title">{{$qa->title}}</h1>
                                            <div class="blog-single-head-date">
                                                <i class="icon-calendar font-blue"></i>
                                                <a href="javascript:;">{{$qa->created_at}} By {{array_get($users,$qa->user_id)}}</a>
                                            </div>
                                        </div>
                                        <div class="blog-single-desc">
											<h4 style="line-height: 30px;font-weight: 400;">
											Product Line: {!!$qa->product_line!!} <br>
											Product : {!!$qa->product!!} <br>
											Model :  {!!$qa->model!!} <br>
											Item No. : {!!$qa->item_no!!}<br>
											Question Type : {!!$qa->etype!!}<br>
											Problem Point. : {!!$qa->epoint!!}</h4>
											 
                                            {!!$qa->description!!}
                                        </div>
                                        
										
										<div class="blog-comments" style="margin-top:50px;">
											<h3 class="sbold blog-comments-title">Details （Chinese）:</h3>
                                            {!!$qa->dqe_content!!}
                                        </div>
										<?php if($qa->confirm!=1){?>
                                        <div class="blog-comments">
                                            <h3 class="sbold blog-comments-title">Update Details:</h3>
											<form role="form" action="{{ url('question/'.$qa['id']) }}" method="POST">
                    {{ csrf_field() }}
					{{ method_field('PUT') }}
											<div class="input-group ">
                  
								
								@include('UEditor::head')

                                    <!-- 加载编辑器的容器 -->
                                    <script id="valuelink_dqe_content" name="dqe_content" type="text/plain">
											</script>
											<!-- 实例化编辑器 -->
											<script type="text/javascript">
												var ue = UE.getEditor('valuelink_dqe_content',{toolbars: [[
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
								
                        <div class="row" style="text-align:right; margin-top:30px;margin-right:30px;">
                          
                                <button type="submit" class="btn blue">Submit</button>
                                <button type="reset" class="btn grey-salsa btn-outline">Cancel</button>
                       
                        </div>
                 
            </form>                                
                                        </div><?php } ?>
                                    </div>
                                </div>
		
								</div>
								

		</div>

        <!-- END QUICK NAV -->
        <!--[if lt IE 9]>
<script src="/assets/global/plugins/respond.min.js"></script>
<script src="/assets/global/plugins/excanvas.min.js"></script> 
<script src="/assets/global/plugins/ie8.fix.min.js"></script> 
<![endif]-->
        <!-- BEGIN CORE PLUGINS -->
        <script src="/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
        <script src="/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="/assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>
        <script src="/assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
        <script src="/assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
        <script src="/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
        <!-- END CORE PLUGINS -->
        <!-- BEGIN THEME GLOBAL SCRIPTS -->
        <script src="/assets/global/scripts/app.min.js" type="text/javascript"></script>
        <!-- END THEME GLOBAL SCRIPTS -->
        <!-- BEGIN THEME LAYOUT SCRIPTS -->
        <script src="/assets/layouts/layout/scripts/layout.min.js" type="text/javascript"></script>
        <script src="/assets/layouts/layout/scripts/demo.min.js" type="text/javascript"></script>
        <script src="/assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
        <script src="/assets/layouts/global/scripts/quick-nav.min.js" type="text/javascript"></script>
        <!-- END THEME LAYOUT SCRIPTS -->
    </body>

</html>