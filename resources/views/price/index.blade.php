@extends('layouts.layout')
@section('label', 'Price Model')
@section('content')
<style>
.mt-repeater-c .mt-repeater-item {
    border-bottom: 1px solid #ddd;
    padding-bottom: 5px;
    margin-bottom: 5px;
}
#result-tab-content .tab-pane table td{
	width:4%;
	font-size:12px;
	text-align:center;
	line-height:25px;
	border-bottom:1px solid #666666;
}
.price_model{font-size:12px;}
.price_model .form-control{
height: 20px;
    padding: 1px;
    font-size: 12px;
	}
.price_model_list .col-md-1{
width:14%; padding:5px;}

.border-right{
border-right:1px solid #333;
}

.border-left{
border-left:1px solid #333;
}

.border-bottom{
border-bottom:1px solid #333;
}

.border-topbottom{
border-bottom:1px solid #333;
border-top:1px solid #333;
    height: 40px;
}
</style>
    <div class="col-lg-12 price_model" style="font-size:12px;">
        <div class="col-md-12">
<div class="portlet light portlet-fit bordered ">



    <div class="portlet-title">
        <div class="caption">
            <i class="icon-microphone font-green"></i>
            <span class="caption-subject bold font-green">Price Model</span>
            <span class="caption-helper"><br />本模型用于计算同一SKU不同销售定价下不同销量所带来的经济效益差别, 作为销售定价的参考作为销售定价的参考<br />建议每周测算一次建议每周测算一次,	最少每月重新测算一次并定价, 当定价与销量发生明显变化时亦须重新测算</span>
        </div>

    </div>
	<form id="price_form">
    <div class="portlet-body" id="cus_price_form">
<p id="dynamic_pager_content1" class="well" style="margin:0px; padding: 10px"> 	一、销售定价-经济效益测算：</p>
		
		
		<div style="clear:both;height:30px;"></div>
		<div class="col-md-3" style="border-right: 1px solid #ccc;">
			
			 <div class="col-md-6" style="line-height: 20px;
    margin-bottom: 22px;">
				<label class="control-label">SAP 物料号</label>
				<input type="text" class="form-control"  name="I_MATNR" >
			
			 </div>
			 <div class="col-md-6" style="line-height: 20px;
    margin-bottom: 22px;">
				 <label class="control-label">当前月份</label>
				 <BR/>{{date('Y-m')}}
			 </div>
			 
			 <div class="col-md-6" style="line-height: 20px;
    margin-bottom: 22px;">
				 <label class="control-label">销售站点</label>
				 <select class="form-control" name="I_VKBUR">
							@foreach (getSapSiteCode() as $k=>$v)
								<option value="{{$k}}">{{$v}}</option>
							@endforeach
				 </select>	
			 </div>
			 
			 <div class="col-md-6" style="line-height: 20px;
    margin-bottom: 22px;">
				 <label class="control-label">产品尺寸</label>
				 <select class="form-control" name="I_ZCC">
					<option value="1">标准尺寸</option>
					<option value="2">超大尺寸</option>
				 </select>		
			 </div>
		</div>								
		<div class="form-group mt-repeater-c col-md-9 price_model_list">
			 <div class="col-md-1 border-topbottom border-left">
				 <label class="control-label head">价格类型</label>
			 </div>
			 <div class="col-md-1 border-topbottom border-left">
				 <label class="control-label head">页面竞品定价(站点币种)</label>
			 </div>
			 <div class="col-md-1 border-topbottom">
				 <label class="control-label head">该价格下销量</label>
			 </div>
			 <div class="col-md-1 border-topbottom border-left">
				 <label class="control-label head">你的定价(站点币种)</label>
			 </div>
			 <div class="col-md-1 border-topbottom">
				 <label class="control-label head">预计日销量</label>
			 </div>
			 <div class="col-md-1 border-topbottom">
				 <label class="control-label head">促销价(站点币种)</label>
			 </div>
			 <div class="col-md-1 border-topbottom border-right">
				 <label class="control-label head">月促销量</label>
			 </div>

								 
							<div data-repeater-list="price-group">
								<div data-repeater-item="" class="mt-repeater-item" style="border-bottom:none;" >
									<div class=" mt-repeater-row">
									<div class="col-md-1 border-left">
										 <input class="form-control my_price" readonly="" name="price-group[0][ZLX]" type="text" value="Price1">
									 </div>
									 <div class="col-md-1 border-left ">
										 <input class="form-control" name="price-group[0][ZJPJG]" type="text" value="0">
									 </div>
									 <div class="col-md-1">
										 <input class="form-control" name="price-group[0][ZJPXL]" type="text" value="0">
									 </div>
									 <div class="col-md-1 border-left">
										 <input class="form-control" name="price-group[0][PRICE]" type="text" value="0">
									 </div>
									 <div class="col-md-1">
										<input class="form-control" name="price-group[0][ZXL]" type="text" value="0">
									 </div>
									 <div class="col-md-1">
										<input class="form-control" name="price-group[0][CPRICE]" type="text" value="0">
									 </div>
									 <div class="col-md-1 border-right">
										<input class="form-control" name="price-group[0][ZCXL]" type="text" value="0">
									 </div>
									
									 
									</div>
								</div>
							<div data-repeater-item="" class="mt-repeater-item" style="border-bottom:none;">
									<div class=" mt-repeater-row">
									<div class="col-md-1 border-left">
										 <input class="form-control my_price" readonly="" name="price-group[1][ZLX]" type="text" value="Price2">
									 </div>
									 <div class="col-md-1 border-left">
										 <input class="form-control" name="price-group[1][ZJPJG]" type="text" value="0">
									 </div>
									 <div class="col-md-1">
										 <input class="form-control" name="price-group[1][ZJPXL]" type="text" value="0">
									 </div>
									 <div class="col-md-1 border-left">
										 <input class="form-control" name="price-group[1][PRICE]" type="text" value="0">
									 </div>
									 <div class="col-md-1">
										<input class="form-control" name="price-group[1][ZXL]" type="text" value="0">
									 </div>
									 <div class="col-md-1">
										<input class="form-control" name="price-group[1][CPRICE]" type="text" value="0">
									 </div>
									 <div class="col-md-1 border-right">
										<input class="form-control" name="price-group[1][ZCXL]" type="text" value="0">
									 </div>
									 
									 
									</div>
								</div><div data-repeater-item="" class="mt-repeater-item" style="border-bottom:none;">
									<div class=" mt-repeater-row">
									<div class="col-md-1 border-left">
										 <input class="form-control my_price" readonly="" name="price-group[2][ZLX]" type="text" value="Price3">
									 </div>
									 <div class="col-md-1 border-left">
										 <input class="form-control" name="price-group[2][ZJPJG]" type="text" value="0">
									 </div>
									 <div class="col-md-1">
										 <input class="form-control" name="price-group[2][ZJPXL]" type="text" value="0">
									 </div>
									 <div class="col-md-1 border-left">
										 <input class="form-control" name="price-group[2][PRICE]" type="text" value="0">
									 </div>
									 <div class="col-md-1">
										<input class="form-control" name="price-group[2][ZXL]" type="text" value="0">
									 </div>
									 <div class="col-md-1">
										<input class="form-control" name="price-group[2][CPRICE]" type="text" value="0">
									 </div>
									 <div class="col-md-1 border-right">
										<input class="form-control" name="price-group[2][ZCXL]" type="text" value="0">
									 </div>
								
									 
									</div>
								</div><div data-repeater-item="" class="mt-repeater-item" style="border-bottom:none;">
									<div class=" mt-repeater-row">
									<div class="col-md-1 border-left">
										 <input class="form-control my_price" readonly="" name="price-group[3][ZLX]" type="text" value="Price4">
									 </div>
									 <div class="col-md-1 border-left">
										 <input class="form-control" name="price-group[3][ZJPJG]" type="text" value="0">
									 </div>
									 <div class="col-md-1">
										 <input class="form-control" name="price-group[3][ZJPXL]" type="text" value="0">
									 </div>
									 <div class="col-md-1 border-left">
										 <input class="form-control" name="price-group[3][PRICE]" type="text" value="0">
									 </div>
									 <div class="col-md-1">
										<input class="form-control" name="price-group[3][ZXL]" type="text" value="0">
									 </div>
									 <div class="col-md-1">
										<input class="form-control" name="price-group[3][CPRICE]" type="text" value="0">
									 </div>
									 <div class="col-md-1 border-right">
										<input class="form-control" name="price-group[3][ZCXL]" type="text" value="0">
									 </div>
								
									
									</div>
								</div><div data-repeater-item="" class="mt-repeater-item" style="border-bottom:none;">
									<div class=" mt-repeater-row">
									<div class="col-md-1 border-left">
										 <input class="form-control my_price" readonly="" name="price-group[4][ZLX]" type="text" value="Price5">
									 </div>
									 <div class="col-md-1 border-left">
										 <input class="form-control" name="price-group[4][ZJPJG]" type="text" value="0">
									 </div>
									 <div class="col-md-1">
										 <input class="form-control" name="price-group[4][ZJPXL]" type="text" value="0">
									 </div>
									 <div class="col-md-1 border-left">
										 <input class="form-control" name="price-group[4][PRICE]" type="text" value="0">
									 </div>
									 <div class="col-md-1">
										<input class="form-control" name="price-group[4][ZXL]" type="text" value="0">
									 </div>
									 <div class="col-md-1">
										<input class="form-control" name="price-group[4][CPRICE]" type="text" value="0">
									 </div>
									 <div class="col-md-1 border-right">
										<input class="form-control" name="price-group[4][ZCXL]" type="text" value="0">
									 </div>
						
									
									</div>
								</div><div data-repeater-item="" class="mt-repeater-item" style="border-bottom:none;">
									<div class=" mt-repeater-row">
									<div class="col-md-1 border-left border-bottom">
										 <input class="form-control my_price" readonly="" name="price-group[5][ZLX]" type="text" value="Price6">
									 </div>
									 <div class="col-md-1 border-left border-bottom">
										 <input class="form-control" name="price-group[5][ZJPJG]" type="text" value="0">
									 </div>
									 <div class="col-md-1 border-bottom">
										 <input class="form-control" name="price-group[5][ZJPXL]" type="text" value="0">
									 </div>
									 <div class="col-md-1 border-left border-bottom">
										 <input class="form-control" name="price-group[5][PRICE]" type="text" value="0">
									 </div>
									 <div class="col-md-1 border-bottom">
										<input class="form-control" name="price-group[5][ZXL]" type="text" value="0">
									 </div>
									 <div class="col-md-1 border-bottom">
										<input class="form-control" name="price-group[5][CPRICE]" type="text" value="0">
									 </div>
									 <div class="col-md-1 border-right border-bottom">
										<input class="form-control" name="price-group[5][ZCXL]" type="text" value="0">
									 </div>
				
									 
									</div>
								</div></div>
							
				
							
						</div>
		<div style="clear:both"></div>
		<p id="dynamic_pager_content1" class="well" style="margin:0px; padding: 10px"> 二、销量影响因子</p>
		
		<div style="clear:both"></div>
		<div class="col-md-2" >
			<div class="mt-repeater-c">
			<div class="mt-repeater-item" style="text-align:center;">
				<div class="row mt-repeater-row">系数</div>
			</div>
			<div class="mt-repeater-item" style="line-height:20px;text-align:center;">
				<div class="row mt-repeater-row">品线市场流量趋势(i1)</div>
			</div>
			<div class="mt-repeater-item" style="line-height:20px;text-align:center;">
				<div class="row mt-repeater-row">推广费流量趋势(i2)</div>
			</div>

			</div>
		</div>




		<div class="col-md-10">
		<div class="mt-repeater-c">
			<div class="mt-repeater-item">
				<div class="row mt-repeater-row">
				<div class="col-md-1">
					 1月
				 </div>
				 <div class="col-md-1">2月
				 </div>
				 <div class="col-md-1">
					 3月
				 </div>
				 <div class="col-md-1">
					 4月
				 </div>
				 <div class="col-md-1">
					5月
				 </div>
				 <div class="col-md-1">
					6月
				 </div>
				 <div class="col-md-1">
					7月
				 </div>
				 <div class="col-md-1">
					8月
				 </div>
				 <div class="col-md-1">
					9月
				 </div>
				 <div class="col-md-1">
					10月
				 </div>
				 <div class="col-md-1">
					11月
				 </div>
				 <div class="col-md-1">
					12月
				 </div>
				</div>
			</div>
			
			<div class="mt-repeater-item">
				<div class="row mt-repeater-row">
				<div class="col-md-1">
					 <input class="form-control" name="month-ZPXSCQS-1" type="text" value="1">
				 </div>
				 <div class="col-md-1">
					 <input class="form-control" name="month-ZPXSCQS-2" type="text" value="0.95">
				 </div>
				 <div class="col-md-1">
					 <input class="form-control" name="month-ZPXSCQS-3" type="text" value="0.98">
				 </div>
				 <div class="col-md-1">
					 <input class="form-control" name="month-ZPXSCQS-4" type="text" value="1">
				 </div>
				 <div class="col-md-1">
					<input class="form-control" name="month-ZPXSCQS-5" type="text" value="1.05">
				 </div>
				 <div class="col-md-1">
					<input class="form-control" name="month-ZPXSCQS-6" type="text" value="1.1">
				 </div>


				 <div class="col-md-1">
					<input class="form-control" name="month-ZPXSCQS-7" type="text" value="1.5">
				 </div>
				 <div class="col-md-1">
					<input class="form-control" name="month-ZPXSCQS-8" type="text" value="1.4">
				 </div>
				 <div class="col-md-1">
					<input class="form-control" name="month-ZPXSCQS-9" type="text" value="1.5">
				 </div>
				 <div class="col-md-1">
					<input class="form-control" name="month-ZPXSCQS-10" type="text" value="1.6">
				 </div>
				 <div class="col-md-1">
					<input class="form-control" name="month-ZPXSCQS-11" type="text" value="3">
				 </div>
				 <div class="col-md-1">
					<input class="form-control" name="month-ZPXSCQS-12" type="text" value="4">
				 </div>
				</div>
			</div>
			
			<div class="mt-repeater-item">
				<div class="row mt-repeater-row">
				<div class="col-md-1">
					 <input class="form-control" name="month-ZTGFQS-1" type="text" value="1.1">
				 </div>
				 <div class="col-md-1">
					 <input class="form-control" name="month-ZTGFQS-2" type="text" value="1.1">
				 </div>
				 <div class="col-md-1">
					 <input class="form-control" name="month-ZTGFQS-3" type="text" value="1">
				 </div>
				 <div class="col-md-1">
					 <input class="form-control" name="month-ZTGFQS-4" type="text" value="1">
				 </div>
				 <div class="col-md-1">
					<input class="form-control" name="month-ZTGFQS-5" type="text" value="1">
				 </div>
				 <div class="col-md-1">
					<input class="form-control" name="month-ZTGFQS-6" type="text" value="1">
				 </div>
				 <div class="col-md-1">
					<input class="form-control" name="month-ZTGFQS-7" type="text" value="1">
				 </div>
				 <div class="col-md-1">
					<input class="form-control" name="month-ZTGFQS-8" type="text" value="1">
				 </div>
				 <div class="col-md-1">
					<input class="form-control" name="month-ZTGFQS-9" type="text" value="1.1">
				 </div>
				 <div class="col-md-1">
					<input class="form-control" name="month-ZTGFQS-10" type="text" value="1">
				 </div>
				 <div class="col-md-1">
					<input class="form-control" name="month-ZTGFQS-11" type="text" value="1">
				 </div>
				 <div class="col-md-1">
					<input class="form-control" name="month-ZTGFQS-12" type="text" value="1">
				 </div>
				</div>
			</div>
			</div>
		</div><div style="clear:both"></div>
	

<p id="dynamic_pager_content1" class="well" style="margin:0px; padding: 10px"> 三、参数表 <button id="genStockAge" class="btn btn-success" type="button" style="padding: 0px 5px;
    font-size: 12px;">
						自动获取</button></p>
		<div style="clear:both"></div>
		<div class="col-md-6" >
			<div class="mt-repeater-c">
			<div class="mt-repeater-item">
				<div class="row mt-repeater-row"  style="line-height:20px;">
				<div class="col-md-4" >
					 指标
				 </div>
				 <div class="col-md-4">
					 单位	
				 </div>
				 <div class="col-md-4">
					 值
				 </div>
		
				</div>






			</div><div class="mt-repeater-item">
				<div class="row mt-repeater-row">
				 <div class="col-md-4">
					单位产品成本
				 </div>
				 <div class="col-md-4">
					CNY
				 </div>
				 <div class="col-md-4">
					 <input class="form-control" name="I_ZDWCB" type="text" value="0">
				 </div>
		
				</div>
				</div><div class="mt-repeater-item">
				<div class="row mt-repeater-row">
				 <div class="col-md-4">
					单位拣配费
				 </div>
				 <div class="col-md-4">
					CNY
				 </div>
				 <div class="col-md-4">
					 <input class="form-control" name="I_ZDWJPF" type="text" value="0">
				 </div>
		
				</div>
				</div><div class="mt-repeater-item">
				<div class="row mt-repeater-row">
				 <div class="col-md-4">
					1-9月单位仓储费
				 </div>
				 <div class="col-md-4">
					CNY
				 </div>
				 <div class="col-md-4">
					 <input class="form-control" name="I_ZDWCCF19" type="text" value="0">
				 </div>
		
				</div>
				</div><div class="mt-repeater-item">
				<div class="row mt-repeater-row">
				 <div class="col-md-4">
					10-12月单位仓储费
				 </div>
				 <div class="col-md-4">
					CNY
				 </div>
				 <div class="col-md-4">
					 <input class="form-control" name="I_ZDWCCF1012" type="text" value="0">
				 </div>
		
				</div>
				</div>
				
				<div class="mt-repeater-item">
				<div class="row mt-repeater-row">
				 <div class="col-md-4">
					汇率
				 </div>
				 <div class="col-md-4">
					/
				 </div>
				 <div class="col-md-4">
					 <input class="form-control" name="I_ZHL" type="text" value="0">
				 </div>
		
				</div>
				</div></div></div>
				<div class="col-md-6" >
			<div class="mt-repeater-c">
			<div class="mt-repeater-item">
				<div class="row mt-repeater-row">
				 <div class="col-md-4">
					平台佣金比例
				 </div>
				 <div class="col-md-4">
					%
				 </div>
				 <div class="col-md-4">
					 <input class="form-control" name="I_ZPTYJBL" type="text" value="0">
				 </div>
		
				</div>
				</div><div class="mt-repeater-item">
				<div class="row mt-repeater-row">
				 <div class="col-md-4">
					营销费用率
				 </div>
				 <div class="col-md-4">
					%
				 </div>
				 <div class="col-md-4">
					 <input class="form-control" name="I_ZYXFYL" type="text" value="0">
				 </div>
		
				</div>
				</div><div class="mt-repeater-item">
				<div class="row mt-repeater-row">
				 <div class="col-md-4">
					异常率
				 </div>
				 <div class="col-md-4">
					%
				 </div>
				 <div class="col-md-4">
					 <input class="form-control" name="I_ZYCL" type="text" value="0">
				 </div>
		
				</div>
				</div><div class="mt-repeater-item">
				<div class="row mt-repeater-row">
				 <div class="col-md-4">
					产品类型
				 </div>
				 <div class="col-md-4">
					A B C
				 </div>
				 <div class="col-md-4">
				 	<select class="form-control" name="I_ZCPLX">
						<option value="A">A.正常（非B非C）
						<option value="B">B.快速更新换代
						<option value="C">C.带锂电池
					</select>
				 </div>
		
				</div>
				</div><div class="mt-repeater-item">
				<div class="row mt-repeater-row">
				 <div class="col-md-4">
					平均库龄
				 </div>
				 <div class="col-md-4">
					月
				 </div>
				 <div class="col-md-4">

					 <input class="form-control" name="I_ZPJCL" type="text" value="0">
	
				 </div>
		
				</div>
				</div><div class="mt-repeater-item">
				<div class="row mt-repeater-row">
				 <div class="col-md-4">
					当前库存数量
				 </div>
				 <div class="col-md-4">
					PCS
				 </div>
				 <div class="col-md-4">
					 <input class="form-control" name="I_ZKC" type="text" value="0">
				 </div>
		
				</div>
			</div>
			</div>
		</div></div>
		
		<div style="clear:both"></div>
<div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-4 col-md-8">
                                <button type="button" class="btn blue" id='cus_price_submit'>Submit</button>
                                
                            </div>
                        </div>
                    </div>
					<div style="clear:both;height:30px;"></div>
        </div>
		

		<div class="portlet light bordered" id="showData" style="display:none;">
			<div class="portlet-body">
				<ul class="nav nav-tabs" id='result-tab'>
					<li class="active">
						<a href="#price_result" data-toggle="tab" aria-expanded="true"> 经济效益测算
</a>                                            </li>
				   
				</ul>
				<div class="tab-content" id='result-tab-content'>
					<div class="tab-pane fade active in" id="price_result">
						<div class="col-md-12" id="lineChart" style="height:300px;width:100%;"></div>
					</div>
				</div>
				<div class="clearfix margin-bottom-20"> </div>
			</div>
		</div>
		</form><div style="clear:both"></div>
		
		
      </div><div style="clear:both"></div>
</div>
		
		<div style="clear:both"></div>
		 </div><div style="clear:both"></div>
<script>
$(function() {
$('.mt-repeater-c').repeater({
	defaultValues: {
		'ZJPJG': '0',
		'ZJPXL': '0',
		'PRICE': '0',
		'ZXL': '0',
		'CPRICE': '0',
		'ZCXL': '0',
	},
	show: function () {
		$(this).slideDown();
		reidx_cus();
	},
	hide: function (deleteElement) {
		$(this).slideUp("normal",function(){
			$(this).remove();
			reidx_cus();
		});
	},
	isFirstItemUndeletable: true
});
$('#genStockAge').on('click',function(){

	var sku=$('input[name="I_MATNR"]').val();
	var site=$('select[name="I_VKBUR"]').val();
	if(!sku){
		alert('请输入SAP物料号');
		$('input[name="I_MATNR"]').focus();
	}
	if(!site){
		alert('请选择销售站点');
		$('select[name="I_VKBUR"]').focus();
	}
	$.ajax({
		url:'/price/getStockAge',
		data:{sku:sku,site:site},
		type:'post',
		dataType: "json",
		success:function(data){
			if(data){
				$('input[name="I_ZPJCL"]').val(Math.round(data.SAGE/30));
				$('input[name="I_ZDWCB"]').val(data.VERPR);
				$('select[name="I_ZCC"]').val(data.ZCC);
				$('input[name="I_ZDWCCF19"]').val(data.ZDWCCF19);
				$('input[name="I_ZDWCCF1012"]').val(data.ZDWCCF1012);
				$('input[name="I_ZPTYJBL"]').val(data.ZCRATIO);
				$('input[name="I_ZDWJPF"]').val(data.FBAPRICE);
				var YCL = data.YCL;
				if((YCL.substr(YCL.length-1,1))=='-'){
					YCL = Number(YCL.substr(0,YCL.length-1));
				}
				$('input[name="I_ZYCL"]').val(YCL);
				$('input[name="I_ZHL"]').val(data.RATE);
			}
			
		},
	});
});
$('#cus_price_submit').on('click',function(){

	$(this).addClass('disabled');
	$('#showData').slideDown();
	$('#showData').html('<h1>Loading......</h1>');
	
			
	$.ajax({
		url:'/price/get',
		data:{data:$("#price_form").serializeArray()},
		dataType: "json",
		type:'post',
		success:function(redata){
			//console.log(redata);
			if(redata.result==1){
				$('#showData').html('<div class="portlet-body"><ul class="nav nav-tabs"id="result-tab"><li class="active"><a href="#price_result"data-toggle="tab"aria-expanded="true">经济效益测算</a></li></ul><div class="tab-content"id="result-tab-content"><div class="tab-pane fade active in"id="price_result"><div class="col-md-12"id="lineChart"style="height:300px;width:100%;"></div></div></div><div class="clearfix margin-bottom-20"></div></div>');
				var O_TAB2=redata.data.O_TAB2;
				var O_TAB=redata.data.O_TAB;
				console.log(O_TAB);
				var chartX=[]; var chartY=[]; var priceDetails ={};
				var getPriceStr='';
				for( var child_i in O_TAB2 )
			　　{
					var pricetag = O_TAB2[child_i].ZLX;
					var priceres = sap_number_format(O_TAB2[child_i].ZJJXY);
					$('[value="'+pricetag+'"]').parent().siblings('.ZJJXY').html(priceres);
					$('[value="'+pricetag+'"]').parent().siblings('.ZFLAG').html(O_TAB2[child_i].ZFLAG);
					chartX.push(pricetag);
					chartY.push(priceres);
					if(!getPriceStr) getPriceStr='<tr><td>价格类型</td><td>页面竞品定价</td><td>该价格下产品销量</td><td>你的定价</td><td>预计日销量</td><td>促销价</td><td>月促销量</td><td>经济效益</td><td>最优标记</td><tr>';
					getPriceStr+='<tr><td>'+pricetag+'</td><td>'+sap_number_format(O_TAB2[child_i].ZJPJG)+'</td><td>'+sap_number_format(O_TAB2[child_i].ZJPXL)+'</td><td>'+sap_number_format(O_TAB2[child_i].PRICE)+'</td><td>'+sap_number_format(O_TAB2[child_i].ZXL)+'</td><td>'+sap_number_format(O_TAB2[child_i].CPRICE)+'</td><td>'+sap_number_format(O_TAB2[child_i].ZCXL)+'</td><td>'+sap_number_format(O_TAB2[child_i].ZJJXY)+'</td><td>'+O_TAB2[child_i].ZFLAG+'</td></tr>';
			　　}
				if(getPriceStr) $('#price_result').append('<table>'+getPriceStr+'</table>');
	

			
				for( var child_i2 in O_TAB)
			　　{
					var pricetag = O_TAB[child_i2].ZLX;
					if(!priceDetails[pricetag]){
						priceDetails[pricetag]='<tr><td>指标/月份</td><td>正常价</td><td>正常价销量</td><td>促销价</td><td>促销价销量</td><td>收入合计</td><td>总月销量</td><td>当月入库</td><td>存货期初数量</td><td>存货期末数量</td><td>产品成本</td><td>平台佣金</td><td>拣配费</td><td>仓储费</td><td>营销费</td><td>销毁费</td><td>成本合计</td><td>异常费用</td><td>业务净利润</td><td>资产减值损失</td><td>营运费用</td><td>利润总额</td><td>所得税</td><td>净利润</td><td>折现净利润</td><tr>';
						$('#result-tab').append('<li class=""><a href="#result_price_'+pricetag+'" data-toggle="tab" aria-expanded="false"> '+pricetag+' </a></li>');
						$('#result-tab-content').append('<div class="tab-pane fade" id="result_price_'+pricetag+'"></div>');

					}
					
					priceDetails[pricetag]+='<tr><td>'+O_TAB[child_i2].MONTH+'</td><td>'+sap_number_format(O_TAB[child_i2].PRICE)+'</td><td>'+sap_number_format(O_TAB[child_i2].QUAN)+'</td><td>'+sap_number_format(O_TAB[child_i2].CPRICE)+'</td><td>'+sap_number_format(O_TAB[child_i2].CQUAN)+'</td><td>'+sap_number_format(O_TAB[child_i2].INCOME)+'</td><td>'+sap_number_format(O_TAB[child_i2].ZQUAN)+'</td><td>'+sap_number_format(O_TAB[child_i2].RQUAN)+'</td><td>'+sap_number_format(O_TAB[child_i2].QCQUAN)+'</td><td>'+sap_number_format(O_TAB[child_i2].QMQUAN)+'</td><td>'+sap_number_format(O_TAB[child_i2].CPCB)+'</td><td>'+sap_number_format(O_TAB[child_i2].PTYJ)+'</td><td>'+sap_number_format(O_TAB[child_i2].JPF)+'</td><td>'+sap_number_format(O_TAB[child_i2].CCF)+'</td><td>'+sap_number_format(O_TAB[child_i2].YXF)+'</td><td>'+sap_number_format(O_TAB[child_i2].XHF)+'</td><td>'+sap_number_format(O_TAB[child_i2].CBHJ)+'</td><td>'+sap_number_format(O_TAB[child_i2].YCFY)+'</td><td>'+sap_number_format(O_TAB[child_i2].YWJLR)+'</td><td>'+sap_number_format(O_TAB[child_i2].ZCJZSS)+'</td><td>'+sap_number_format(O_TAB[child_i2].YYFY)+'</td><td>'+sap_number_format(O_TAB[child_i2].LRZE)+'</td><td>'+sap_number_format(O_TAB[child_i2].SDS)+'</td><td>'+sap_number_format(O_TAB[child_i2].JLR)+'</td><td>'+sap_number_format(O_TAB[child_i2].ZXJLR)+'</td></tr>';
			　　}
			
				$.each(priceDetails, function (k, v) {
					$('#result_price_'+k).append('<table>'+v+'</table>');
				});
				
				var lineChart = echarts.init(document.getElementById('lineChart'));

				var option = {
					tooltip : {
						trigger: 'axis'
					},
					legend: {
						data:['经济效益']
					},

					toolbox: {
						show : true,
						feature : {
							mark : {show: true},
							dataView : {show: true, readOnly: false},
							magicType : {show: true, type: ['line', 'bar', 'stack', 'tiled']},
							restore : {show: true},
							saveAsImage : {show: true}
						}
					},
					calculable : true,
					xAxis : [
						{
							type : 'category',
							boundaryGap : false,
							data : chartX
						}
					],
					yAxis : [
						{
							type : 'value',
							axisLine : {  
								show: true,
								onZero:false,
								lineStyle: {
									color: 'red',
									type: 'dashed',
									width: 2
								}
							},
						}
					],
					series : [
						{
							name:'经济效益',
							type:'line',
							data:chartY
						}
					]
				};
				lineChart.setOption(option);
						
			}else{
				toastr.error(redata.message);
			}
		}
	});
	$(this).removeClass('disabled');
});
});
function reidx_cus(){
	var i=0;
	$('.mt-repeater-c .my_price').each(function(){
		i++;
		$(this).val('Price'+i);
		$(this).attr("value",'Price'+i);

	});
}

function sap_number_format(str){
	if((str.substr(str.length-1,1))=='-'){
		str = '-'+Number(str.substr(0,str.length-1));
	}
	str.replace('/,/g',"");
	return Number(str);
}
</script>
<div style="clear:both;"></div>

@endsection