

    <div class="row"><div class="col-md-12">
        <div class="portlet light bordered">

            <div class="portlet-body form">
                <form role="form" action="{{ url('couponkunnr/'.$rule['id']) }}" method="POST">
                    {{ csrf_field() }}
                    {{ method_field('PUT') }}
					<input type="hidden" name='id' value="{{$rule['id']}}" />
                    <div class="form-body">
                        <div class="form-group">
                            <label>Sold-to party （售达方）</label>
                            <div class="input-group ">
                                <span class="input-group-addon">
                                    <i class="fa fa-tag"></i>
                                </span>
								
								<select name="kunnr" class="form-control form-filter" required >
								   <?php 
									foreach($accounts as $k=>$v){
										$selected = ($k==$rule['kunnr'])?'selected':'';
										echo '<option value="'.$k.'" '.$selected.'>'.$v.'</option>';
									}?>
								</select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Sap Seller ID （销售组）</label>
                            <div class="input-group ">
                                <span class="input-group-addon">
                                    <i class="fa fa-sort-amount-asc"></i>
                                </span>
                                <select name="sap_seller_id" class="form-control form-filter" required>
								   <?php 
									foreach($users as $k=>$v){ 	
										$selected = ($k==$rule['sap_seller_id'])?'selected':'';
										echo '<option value="'.$k.'" '.$selected.'>'.$k.' ( '.$v.' ) </option>';
									}?>
								</select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>SAP SKU （物料号）</label>
                            <div class="input-group ">
                                <span class="input-group-addon">
                                    <i class="fa fa-heart"></i>
                                </span>
                                <input type="text" class="form-control" name="sku" id="sku" value="{{$rule['sku']}}" required>
                            </div>
                        </div>
						
						<div class="form-group">
                            <label>Coupon Code （Coupon描述）</label>
                            <div class="input-group ">
                                <span class="input-group-addon">
                                    <i class="fa fa-heart"></i>
                                </span>
                                <input type="text" class="form-control" name="coupon_description" id="coupon_description" value="{{$rule['coupon_description']}}" required>
                            </div>
                        </div>

                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-4 col-md-8">
                                <button type="submit" class="btn blue">Submit</button>
                                <button type="button"  class="btn grey-salsa btn-outline"  data-dismiss="modal" aria-hidden="true">Close</button>
                            </div>
                        </div>
                    </div>
                </form>
				
											
            </div>
        </div>
    </div>

    </div>

