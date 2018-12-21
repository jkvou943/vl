

    <div class="row"><div class="col-md-12">
        <div class="portlet light bordered">

            <div class="portlet-body form">
                <form role="form" action="{{ url('rsgproducts/'.$rule['id']) }}" method="POST">
                    {{ csrf_field() }}
                    {{ method_field('PUT') }}
                    <div class="form-body">
						<div class="form-group col-md-6">
                            <label>Start Date</label>
                            <div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
								<input type="text" class="form-control" readonly name="start_date" placeholder="From" value="{{array_get($rule,'start_date')}}" required>
								<span class="input-group-btn">
									<button class="btn default" type="button">
										<i class="fa fa-calendar"></i>
									</button>
								</span>
							</div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>End Date</label>
                            <div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
								<input type="text" class="form-control" readonly name="end_date" placeholder="To" value="{{array_get($rule,'end_date')}}" required>
								<span class="input-group-btn">
									<button class="btn   default" type="button">
										<i class="fa fa-calendar"></i>
									</button>
								</span>
							</div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Account</label>
                            
                                
								<select name="seller_id" class="form-control" required disabled readonly>
								   <?php 
									foreach($accounts as $k=>$v){ 
										$selected = ($k==$rule['seller_id'])?'selected':'';
										echo '<option value="'.$k.'" '.$selected.'>'.$v.' ( '.$k.' ) </option>';
									}?>
								</select>
                            
                        </div>
                        <div class="form-group col-md-6">
                            <label>Site</label>
                           
                                
                                <select name="site" class="form-control " required disabled readonly>
								   <?php 
									foreach(getAsinSites() as $v){ 	
										$selected = ($v==$rule['site'])?'selected':'';
										echo '<option value="'.$v.'" '.$selected.'>'.$v.' </option>';
									}?>
								</select>
                            
                        </div>
                        <div class="form-group col-md-6">
                            <label>Asin</label>
                            
                             
                                <input type="text" class="form-control" name="asin" id="asin" value="{{array_get($rule,'asin')}}" required disabled readonly>
                            
                        </div>
						
						<div class="form-group col-md-6">
                            <label>Daily Gift Quantity</label>
                            
                      
                                <input type="text" class="form-control" name="daily_stock" id="daily_stock" value="{{array_get($rule,'daily_stock')}}" required>
                           
                        </div>
						<div class="form-group col-md-6">
                            <label>Product Name</label>
                            
                             
                                <input type="text" class="form-control" name="product_name" id="product_name" value="{{array_get($rule,'product_name')}}" required>
                            
                        </div>
						
						<div class="form-group col-md-6">
                            <label>Product Image</label>
                            
                      
                                <input type="text" class="form-control" name="product_img" id="product_img" value="{{array_get($rule,'product_img')}}">
                           
                        </div>
						
						<div class="form-group col-md-6">
                            <label>Price</label>
                            	<div class="clearfix"></div>
                             	<div class="form-inline">
                                <input type="text" class="form-control col-md-8" name="price" id="price" value="{{round(array_get($rule,'price'),2)}}" required>
								
								<select class="form-control col-md-4" name="currency" id="currency" required>
								<?php 
									foreach(getCurrency() as $v){ 
										$selected = ($v==$rule['currency'])?'selected':'';
										echo '<option value="'.$v.'" '.$selected.'>'.$v.' </option>';
									}?>
								</select>
								</div>
                            
                        </div>
						
						<div class="form-group col-md-6">
                            <label>Purchase Search Keywords</label>
                            
                      
                                <input type="text" class="form-control" name="keyword" id="keyword" value="{{array_get($rule,'keyword')}}">
                           
                        </div>
						
						<div class="form-group col-md-6">
                            <label>Purchase Search Page No.</label>
                            
                      
                                <input type="text" class="form-control" name="page" id="page" value="{{intval(array_get($rule,'page'))}}" >
                           
                        </div>
						
						
						<div class="form-group col-md-6">
                            <label>Purchase Search Position</label>
                            
                      
                                <input type="text" class="form-control" name="position" id="position" value="{{intval(array_get($rule,'position'))}}" >
                           
                        </div>
						
						<div class="form-group col-md-6">
                            <label>Positive Target</label>
                            
                      
                                <input type="text" class="form-control" name="positive_target" id="positive_target" value="{{intval(array_get($rule,'positive_target'))}}">
                           
                        </div>
						
						
						<div class="form-group col-md-6">
                            <label>Positive Daily Limit</label>
                            
                      
                                <input type="text" class="form-control" name="positive_daily_limit" id="positive_daily_limit" value="{{intval(array_get($rule,'positive_daily_limit'))}}">
                           
                        </div>
						
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-4 col-md-8">
								<button type="button"  class="btn grey-salsa btn-outline pull-right"  data-dismiss="modal" aria-hidden="true">Close</button>
                                <button type="submit" class="btn blue pull-right">Submit</button>
                                
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    </div>
<script>
$(function() {
$('.date-picker').datepicker({
	rtl: App.isRTL(),
	autoclose: true
});
});
</script>
