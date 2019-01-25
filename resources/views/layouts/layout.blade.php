<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->

<head>
    <meta charset="utf-8" />
    <title>@yield('title')</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta content="@yield('description')" name="description" />
    <meta content="" name="author" />
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL PLUGINS -->
	<link href="/assets/global/plugins/bootstrap-colorpicker/css/colorpicker.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/jquery-minicolors/jquery.minicolors.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/bootstrap-toastr/toastr.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/pages/css/pricing.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/bootstrap-multiselect/css/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL STYLES -->
    <link href="/assets/global/css/components.css" rel="stylesheet" id="style_components" type="text/css" />
    <link href="/assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
    <!-- END THEME GLOBAL STYLES -->
    <!-- BEGIN THEME LAYOUT STYLES -->
    <link href="/assets/layouts/layout/css/layout.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/layouts/layout/css/themes/darkblue.min.css" rel="stylesheet" type="text/css" id="style_color" />
    <link href="/assets/layouts/layout/css/custom.min.css" rel="stylesheet" type="text/css" />
    <!-- END THEME LAYOUT STYLES -->
    <link href="/assets/global/plugins/fancybox/source/jquery.fancybox.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/jquery-file-upload/blueimp-gallery/blueimp-gallery.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/jquery-file-upload/css/jquery.fileupload.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/jquery-file-upload/css/jquery.fileupload-ui.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />
	<link href="/assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="/assets/global/plugins/jquery-ui/jquery-ui.min.css">
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
	<script src="/assets/global/plugins/jquery-repeater/jquery.repeater.js" type="text/javascript"></script>
    <!-- END CORE PLUGINS -->


    <script src="/assets/global/plugins/jquery-ui/jquery-ui.min.js"></script>
    <script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
	<script src="/assets/pages/scripts/form-repeater.js?v=laji" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-multiselect/js/bootstrap-multiselect.js" type="text/javascript"></script>
    <script src="/assets/pages/scripts/components-bootstrap-multiselect.js" type="text/javascript"></script>
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="/assets/global/plugins/bootstrap-toastr/toastr.min.js" type="text/javascript"></script>
	<script src="/assets/global/plugins/bootbox/bootbox.min.js" type="text/javascript"></script>
	<script src="/assets/pages/scripts/ui-modals.min.js" type="text/javascript"></script>

    <!-- END PAGE LEVEL SCRIPTS -->
    <script src="/assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
	<script src="/assets/global/plugins/bootstrap-wizard/jquery.bootstrap.wizard.js" type="text/javascript"></script>

    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="/assets/global/scripts/app.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js" type="text/javascript"></script>
	<script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/jquery-minicolors/jquery.minicolors.min.js" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="/assets/layouts/layout/scripts/layout.js" type="text/javascript"></script>
    <script src="/assets/layouts/layout/scripts/demo.min.js" type="text/javascript"></script>
    <script src="/assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
    <script src="/assets/layouts/global/scripts/quick-nav.min.js" type="text/javascript"></script>
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="/assets/global/plugins/fancybox/source/jquery.fancybox.pack.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/jquery-file-upload/js/vendor/jquery.ui.widget.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/jquery-file-upload/js/vendor/tmpl.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/jquery-file-upload/js/vendor/load-image.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/jquery-file-upload/js/vendor/canvas-to-blob.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/jquery-file-upload/blueimp-gallery/jquery.blueimp-gallery.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/jquery-file-upload/js/jquery.iframe-transport.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/jquery-file-upload/js/jquery.fileupload.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/jquery-file-upload/js/jquery.fileupload-process.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/jquery-file-upload/js/jquery.fileupload-image.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/jquery-file-upload/js/jquery.fileupload-audio.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/jquery-file-upload/js/jquery.fileupload-video.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/jquery-file-upload/js/jquery.fileupload-validate.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/jquery-file-upload/js/jquery.fileupload-ui.js" type="text/javascript"></script>

    <script src="/assets/pages/scripts/form-fileupload.js" type="text/javascript"></script>
	<script src="/assets/layouts/global/scripts/echarts.min.js" type="text/javascript"></script>

    <!-- END PAGE LEVEL SCRIPTS -->
    <link rel="shortcut icon" href="/favicon.ico" />
    <link href="/css/common.css?v=9" rel="stylesheet" type="text/css"/>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-sidebar-closed-hide-logo page-container-bg-solid page-content-white page-sidebar-closed">
<div class="page-wrapper">
    <!-- BEGIN HEADER -->
    <div class="page-header navbar navbar-fixed-top">
        <!-- BEGIN HEADER INNER -->
        <div class="page-header-inner ">
            <!-- BEGIN LOGO -->
            <div class="page-logo">
                <a href="/">
                    <img src="/assets/layouts/layout/img/logo.png" alt="logo" class="logo-default" /> </a>
                <div class="menu-toggler sidebar-toggler">
                    <span></span>
                </div>
            </div>
            <!-- END LOGO -->
            <!-- BEGIN RESPONSIVE MENU TOGGLER -->
            <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
                <span></span>
            </a>
            <!-- END RESPONSIVE MENU TOGGLER -->
            <!-- BEGIN TOP NAVIGATION MENU -->
            <div class="top-menu">
                <ul class="nav navbar-nav pull-right">

                    <li class="dropdown dropdown-user">
                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                            <i class="fa fa-book" style="font-size:20px"></i>
                            <span class="username"> KMS </span>
                            <i class="fa fa-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-default">
                            <li>
                                <a href="/kms/productguide">
                                    <i class="fa fa-file"></i> Product Guide
                                </a>
                            </li>
                            <li>
                                <a href="/asin">
                                    <i class="fa fa-table"></i> Asin Table
                                </a>
                            </li>
                            <li>
                                <a href="/qa">
                                    <i class="fa fa-files-o"></i> Q&A Manage
                                </a>
                            </li>
                            <li>
                                <a href="/question">
                                    <i class="fa fa-question"></i> Q&A Center
                                </a>
                            </li>
                            <li>
                                <a href="/kms/learn">
                                    <i class="fa fa-book"></i> Learning Center
                                </a>
                            </li>
                            <li>
                                <a href="/kms/notice">
                                    <i class="fa fa-inbox"></i> Notice Center
                                </a>
                            </li>
                            <li>
                                <a href="/template">
                                    <i class="fa fa-inbox"></i> Templates
                                </a>
                            </li>
                            <li>
                                <a href="/kms/usermanual">
                                    <i class="fa fa-book"></i> User Manuals
                                </a>
                            </li>
                            <li>
                                <a href="/kms/videolist">
                                    <i class="fa fa-play-circle-o"></i> Video List
                                </a>
                            </li>
                            <li>
                                <a href="/kms/partslist">
                                    <i class="fa fa-list"></i> Parts List
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="dropdown dropdown-user">
                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                            <img alt="" class="img-circle" src="/assets/layouts/layout/img/avatar.png" />
                            <span class="username username-hide-on-mobile"> {{Auth::user()->email}} </span>
                            <i class="fa fa-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-default">
                            <li>
                                <a href="{{ url('profile') }}">
                                    <i class="icon-user"></i> My Profile </a>
                            </li>
                            <!--<li>
                                <a href="{{ url('account') }}">
                                    <i class="icon-lock"></i> Account Setting </a>
                            </li>-->
                            <li>
                                <a href="{{ route('logout') }}" onClick="event.preventDefault();document.getElementById('logout-form').submit();">
                                    <i class="icon-key"></i> Log Out </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        </ul>
                    </li>
                    <!-- END USER LOGIN DROPDOWN -->
                    <!-- BEGIN QUICK SIDEBAR TOGGLER -->
                    <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                    <li class="dropdown dropdown-quick-sidebar-toggler">
                        <a href="javascript:;" class="dropdown-toggle">
                            <i class="icon-logout"></i>
                        </a>
                    </li>
                    <!-- END QUICK SIDEBAR TOGGLER -->
                </ul>
            </div>
            <!-- END TOP NAVIGATION MENU -->
        </div>
        <!-- END HEADER INNER -->
    </div>
    <!-- END HEADER -->
    <!-- BEGIN HEADER & CONTENT DIVIDER -->
    <div class="clearfix"> </div>
    <!-- END HEADER & CONTENT DIVIDER -->
    <!-- BEGIN CONTAINER -->
    <div class="page-container">
        <!-- BEGIN SIDEBAR -->
        <div class="page-sidebar-wrapper">
            <!-- BEGIN SIDEBAR -->
            <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
            <!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
            <div class="page-sidebar navbar-collapse collapse">
                <!-- BEGIN SIDEBAR MENU -->
                <!-- DOC: Apply "page-sidebar-menu-light" class right after "page-sidebar-menu" to enable light sidebar menu style(without borders) -->
                <!-- DOC: Apply "page-sidebar-menu-hover-submenu" class right after "page-sidebar-menu" to enable hoverable(hover vs accordion) sub menu mode -->
                <!-- DOC: Apply "page-sidebar-menu-closed" class right after "page-sidebar-menu" to collapse("page-sidebar-closed" class must be applied to the body element) the sidebar sub menu mode -->
                <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
                <!-- DOC: Set data-keep-expand="true" to keep the submenues expanded -->
                <!-- DOC: Set data-auto-speed="200" to adjust the sub menu slide up/down speed -->
                <ul class="page-sidebar-menu  page-header-fixed page-sidebar-menu-closed" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" style="padding-top: 20px">
                    <!-- DOC: To remove the sidebar toggler from the sidebar you just need to completely remove the below "sidebar-toggler-wrapper" LI element -->
                    <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
                    <li class="sidebar-toggler-wrapper hide">
                        <div class="sidebar-toggler">
                            <span></span>
                        </div>
                    </li>
                    <?php
                    $action_method = explode('.', request()->route()->getAction()['as'] ?? '');

                    $action = $action_method[0];
                    $method = isset($action_method[1])?$action_method[1]:'';


                    ?>
                    <!-- END SIDEBAR TOGGLER BUTTON -->
                    <!-- DOC: To remove the search box from the sidebar you just need to completely remove the below "sidebar-search-wrapper" LI element -->
                    <li class="nav-item <?php if($action=='send' && $method=='create') echo 'active';?> ">
                        <a href="{{ url('send/create') }}" class="nav-link nav-toggle">
                            <i class="fa fa-edit"></i>
                            <span class="title">Compose</span>
                            <!--<span class="arrow"></span>-->
                            <?php if($action=='send') echo '<span class="selected"></span>';?>

                        </a>
                    </li>

                    <li class="nav-item <?php if($action=='inbox') echo 'active';?>">
                        <a href="{{url('inbox')}}" class="nav-link nav-toggle">
                            <i class="fa fa-envelope-o"></i>
                            <span class="title">InBox</span>
                            <?php if($action=='inbox') echo '<span class="selected"></span>';?>

                        </a>

                    </li>
					<?php $filtertype=isset($type)?$type:''; ?>
					@foreach (getAccountTypes() as $type)

					<li class="nav-item <?php if($action=='filterInbox' && $filtertype==$type) echo 'active';?>">
                        <a href="{{url('inbox/filter/'.$type)}}" class="nav-link nav-toggle">
                            <i class="fa fa-envelope-o"></i>
                            <span class="title">{{$type}} InBox</span>
                            <?php if($action=='filterInbox' && $filtertype==$type) echo '<span class="selected"></span>';?>
                        </a>
                    </li>
					@endforeach


					<li class="nav-item <?php if($action=='phone') echo 'active';?>">
                        <a href="{{url('phone')}}" class="nav-link nav-toggle">
                            <i class="fa fa-phone"></i>
                            <span class="title">Call Message</span>
                            <?php if($action=='phone') echo '<span class="selected"></span>';?>

                        </a>

                    </li>



                    <li class="nav-item <?php if($action=='send' && $method!='create') echo 'active';?>">
                        <a href="{{url('send')}}" class="nav-link nav-toggle">
                            <i class="fa fa-share-square-o"></i>
                            <span class="title">SendBox</span>
                            <?php if($action=='send') echo '<span class="selected"></span>';?>

                        </a>

                    </li>

                    <li class="nav-item <?php if($action=='review') echo 'active';?>">
                        <a href="{{ url('review') }}" class="nav-link nav-toggle">
                            <i class="fa fa-table"></i>
                            <span class="title">Reviews Table</span>
                            <?php if($action=='review') echo '<span class="selected"></span>';?>

                        </a>

                    </li>

                    <li class="nav-item <?php if($action=='star') echo 'active';?>">
                        <a href="{{ url('star') }}" class="nav-link nav-toggle">
                            <i class="fa fa-star"></i>
                            <span class="title">Asin Rating Table</span>
                            <?php if($action=='star') echo '<span class="selected"></span>';?>

                        </a>

                    </li>

					<li class="nav-item <?php if($action=='exception') echo 'active';?>">
                        <a href="{{ url('exception') }}" class="nav-link nav-toggle">
                            <i class="fa fa-ticket"></i>
                            <span class="title">Refund & Replacement</span>
                            <?php if($action=='exception') echo '<span class="selected"></span>';?>

                        </a>

                    </li>

                    @include('layouts.menu', ['text'=>'C T G', 'uri'=>'/ctg/list', 'fa'=>'google'])

                    <?php if(Auth::user()->admin){?>
                    <li class="nav-item <?php if($action=='auto') echo 'active';?>">
                        <a href="{{url('auto')}}" class="nav-link nav-toggle">
                            <i class="fa fa-reply-all"></i>
                            <span class="title">Auto Reply</span>
                            <?php if($action=='auto') echo '<span class="selected"></span>';?>
                        </a>
                    </li>


                    <li class="nav-item <?php if($action=='rule') echo 'active';?>">
                        <a href="{{ url('rule') }}" class="nav-link nav-toggle">
                            <i class="fa fa-filter"></i>
                            <span class="title">Match Rules</span>
                            <?php if($action=='rule') echo '<span class="selected"></span>';?>

                        </a>

                    </li>


					 <li class="nav-item <?php if($action=='group') echo 'active';?>">
                        <a href="{{ url('group') }}" class="nav-link nav-toggle">
                            <i class="fa fa-group"></i>
                            <span class="title">Group Manage</span>
                            <?php if($action=='group') echo '<span class="selected"></span>';?>

                        </a>

                    </li>


                    <li class="nav-item <?php if($action=='user') echo 'active';?>">
                        <a href="{{ url('user') }}" class="nav-link nav-toggle">
                            <i class="fa fa-user"></i>
                            <span class="title">User Manage</span>
                            <?php if($action=='user') echo '<span class="selected"></span>';?>

                        </a>

                    </li>

                    <li class="nav-item <?php if($action=='account') echo 'active';?>">
                        <a href="{{ url('account') }}" class="nav-link nav-toggle">
                            <i class="icon-diamond"></i>
                            <span class="title">Seller Account Manage</span>
                            <?php if($action=='account') echo '<span class="selected"></span>';?>

                        </a>

                    </li>
					

					<li class="nav-item <?php if($action=='etotal') echo 'active';?>">
                        <a href="{{ url('etotal') }}" class="nav-link nav-toggle">
                            <i class="fa fa-question-circle"></i>
                            <span class="title">Product Problem Statistics</span>
                            <?php if($action=='etotal') echo '<span class="selected"></span>';?>

                        </a>

                    </li>
					<li class="nav-item <?php if($action=='sellertab') echo 'active';?>">
                        <a href="{{ url('sellertab') }}" class="nav-link nav-toggle">
                            <i class="fa fa-wrench"></i>
                            <span class="title">Seller Tab Config</span>
                            <?php if($action=='sellertab') echo '<span class="selected"></span>';?>

                        </a>

                    </li>
					<li class="nav-item <?php if($action=='rs') echo 'active';?>">
                        <a href="{{ url('rs') }}" class="nav-link nav-toggle">
                            <i class="fa fa-wrench"></i>
                            <span class="title">Review Step Config</span>
                            <?php if($action=='rs') echo '<span class="selected"></span>';?>

                        </a>

                    </li>
					<?php } ?>
					<li class="nav-item <?php if($action=='total') echo 'active';?>">
                        <a href="{{ url('total') }}" class="nav-link nav-toggle">
                            <i class="fa fa-download"></i>
                            <span class="title">Data Statistics</span>
                            <?php if($action=='total') echo '<span class="selected"></span>';?>

                        </a>
                    </li>

					<li class="nav-item <?php if($action=='seller') echo 'active';?>">
                        <a href="{{ url('seller') }}" class="nav-link nav-toggle">
                            <i class="fa fa-tasks"></i>
                            <span class="title">Sales Management</span>
                            <?php if($action=='seller') echo '<span class="selected"></span>';?>
                        </a>
                    </li>


					<li class="nav-item <?php if($action=='tran') echo 'active';?>">
                        <a href="{{ url('tran') }}" class="nav-link nav-toggle">
                            <i class="fa fa-truck"></i>
                            <span class="title">Distribution analysis</span>
                            <?php if($action=='tran') echo '<span class="selected"></span>';?>
                        </a>
                    </li>
					
					<li class="nav-item <?php if($action=='fees') echo 'active';?>">
                        <a href="{{ url('fees') }}" class="nav-link nav-toggle">
                            <i class="fa fa-dollar"></i>
                            <span class="title">Fees Split</span>
                            <?php if($action=='fees') echo '<span class="selected"></span>';?>
                        </a>
                    </li>
					
					<li class="nav-item <?php if($action=='mcforder') echo 'active';?>">
                        <a href="{{ url('mcforder') }}" class="nav-link nav-toggle">
                            <i class="fa fa-sticky-note-o"></i>
                            <span class="title">Mcf Order</span>
                            <?php if($action=='mcforder') echo '<span class="selected"></span>';?>
                        </a>
                    </li>
					
					
					<li class="nav-item <?php if($action=='price') echo 'active';?>">
                        <a href="{{ url('price') }}" class="nav-link nav-toggle">
                            <i class="fa fa-line-chart"></i>
                            <span class="title">Price Model</span>
                            <?php if($action=='price') echo '<span class="selected"></span>';?>
                        </a>
                    </li>
					
					<li class="nav-item <?php if($action=='autoprice') echo 'active';?>">
                        <a href="{{ url('autoprice') }}" class="nav-link nav-toggle">
                            <i class="fa fa-area-chart"></i>
                            <span class="title">Automatic Price Adjustment</span>
                            <?php if($action=='autoprice') echo '<span class="selected"></span>';?>
                        </a>
                    </li>
					<li class="nav-item <?php if($action=='salesp') echo 'active';?>">
                        <a href="{{ url('salesp') }}" class="nav-link nav-toggle">
                            <i class="fa fa-shopping-cart"></i>
                            <span class="title">Sales Prediction</span>
                            <?php if($action=='salesp') echo '<span class="selected"></span>';?>
                        </a>
                    </li>
					
					<li class="nav-item <?php if($action=='rsgproducts') echo 'active';?>">
                        <a href="{{ url('rsgproducts') }}" class="nav-link nav-toggle">
                            <i class="fa fa-gift"></i>
                            <span class="title">Rsg Products</span>
                            <?php if($action=='rsgproducts') echo '<span class="selected"></span>';?>
                        </a>
                    </li>
					
					<li class="nav-item <?php if($action=='rsgrequests') echo 'active';?>">
                        <a href="{{ url('rsgrequests') }}" class="nav-link nav-toggle">
                            <i class="fa fa-gift"></i>
                            <span class="title">Rsg Requests</span>
                            <?php if($action=='rsgrequests') echo '<span class="selected"></span>';?>
                        </a>
                    </li>



                </ul>
                <!-- END SIDEBAR MENU -->
                <!-- END SIDEBAR MENU -->
            </div>
            <!-- END SIDEBAR -->
        </div>
        <!-- END SIDEBAR -->
        <!-- BEGIN CONTENT -->
        <div class="page-content-wrapper">
            <!-- BEGIN CONTENT BODY -->
            <div class="page-content">


                <div class="page-bar">
                    <ul class="page-breadcrumb">
                        <li>
                            <a href="{{url('home')}}">Home</a>
                            <i class="fa fa-circle"></i>
                        </li>
                        <li>
                            <span>@yield('label')</span>
                        </li>
                        @yield('crumb')
                    </ul>
                </div>


                @yield('content')
            </div>
            <!-- END CONTENT BODY -->
        </div>
        <!-- END CONTENT -->
    </div>
    <!-- END CONTAINER -->
    <!-- BEGIN FOOTER -->
    <div class="page-footer">
        <div class="page-footer-inner"> 2018 © Valuelink Ltd.
        </div>
        <div class="scroll-to-top">
            <i class="icon-arrow-up"></i>
        </div>
    </div>
    <!-- END FOOTER -->
</div>

<!-- END QUICK NAV -->

<script>
    $(function() {
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "positionClass": "toast-bottom-right",
            "onclick": null,
            "showDuration": "1000",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
        @if(Session::has('success_message'))
            toastr.success("{{Session::get('success_message')}}");
            {{Session::forget('success_message')}}
        @endif

        @if(Session::has('error_message'))
            toastr.error("{{Session::get('error_message')}}");
            {{Session::forget('error_message')}}
        @endif
    });
</script>



<!-- END THEME LAYOUT SCRIPTS -->
</body>

</html>