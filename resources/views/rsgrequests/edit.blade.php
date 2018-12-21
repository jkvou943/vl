

    <div class="row"><div class="col-md-12">
        <div class="portlet light bordered">

            <div class="portlet-body form">
                <form role="form" action="{{ url('rsgrequests/'.$rule['id']) }}" method="POST">
                    {{ csrf_field() }}
                    {{ method_field('PUT') }}
                    <div class="form-body">
						
				<div class="mt-comments">
                                                   
					<div class="mt-comment">
						<div class="mt-comment-img">
							<img src="{{array_get($product,'product_img')}}" width="100px" height="100px"> </div>
						<div class="mt-comment-body" style="padding-left: 100px;">
							<div class="mt-comment-info">
								<span class="mt-comment-author">{{array_get($rule,'customer_email')}}</span>
								<span class="mt-comment-date">
								{{array_get($rule,'created_at')}}
								</span>
							</div>
							<div class="mt-comment-text"><a href="https://{{array_get($product,'site')}}/dp/{{array_get($product,'asin')}}?m={{array_get($product,'seller_id')}}" target="_blank">{{array_get($product,'product_name')}}</a></div> 
							<div class="mt-comment-details">
								<span class="mt-comment-status mt-comment-status-rejected">Price : {{round(array_get($product,'price'),2)}} {{array_get($product,'currency')}}<BR />
								Keyword : {{array_get($product,'keyword')}} ; Page : {{array_get($product,'page')}} ; Position : {{array_get($product,'position')}}
								<BR />
								<span class="badge badge-success">{{array_get(getStepStatus(),$rule['step'])}}</span> {{array_get($rule,'updated_at')}}</span>
							</div>
							
						</div>
					</div>
				</div>
				
				<div class="clearfix margin-bottom-20"></div>
                        <div class="form-group col-md-6">
                            <label>Customer Email</label>
                            
                             
                                <input type="text" class="form-control" name="customer_email" id="customer_email" value="{{array_get($rule,'customer_email')}}" required disabled readonly>
                            
                        </div>
						
						<div class="form-group col-md-6">
                            <label>Current Step</label>
								<select class="form-control " name="step" id="step" required>
								<?php 
								foreach(getStepStatus() as $k=>$v){ 	
									$selected = ($k==$rule['step'])?'selected':'';
									echo '<option value="'.$k.'" '.$selected.'>'.$v.'</option>';
								}?>
								
								</select>                           
                        </div>
						<div class="form-group col-md-6">
                            <label>Customer Paypal</label>
                            
                             
                                <input type="text" class="form-control" name="customer_paypal_email" id="customer_paypal_email" value="{{array_get($rule,'customer_paypal_email')}}">
                            
                        </div>
						
						<div class="form-group col-md-6">
                            <label>Fund Money</label>
                            	<div class="clearfix"></div>
                             	<div class="form-inline">
                                <input type="text" class="form-control col-md-8" name="transfer_amount" id="transfer_amount" value="{{round(array_get($rule,'transfer_amount'),2)}}">
								
								<select class="form-control col-md-4" name="transfer_currency" id="transfer_currency">
								<?php 
									foreach(getCurrency() as $v){ 
										$selected = ($v==$rule['transfer_currency'])?'selected':'';
										echo '<option value="'.$v.'" '.$selected.'>'.$v.' </option>';
									}?>
								</select>
								</div>
                            
                        </div>
						
						
						<div class="form-group col-md-6">
                            <label>Funded Paypal</label>
                                <input type="text" class="form-control" name="transfer_paypal_account" id="transfer_paypal_account" value="{{array_get($rule,'transfer_paypal_account')}}">
                           
                        </div>
						
						<div class="form-group col-md-6">
                            <label>Paypal Transaction Id</label>
                                <input type="text" class="form-control" name="transaction_id" id="transaction_id" value="{{array_get($rule,'transaction_id')}}">
                           
                        </div>

						
						
						
						<div class="form-group col-md-6">
                            <label>Amazon Order Id</label>
                                <input type="text" class="form-control" name="amazon_order_id" id="amazon_order_id" value="{{array_get($rule,'amazon_order_id')}}">
                           
                        </div>
						
						<div class="form-group col-md-6">
                            <label>Review Url</label>
                                <input type="text" class="form-control" name="review_url" id="review_url" value="{{array_get($rule,'review_url')}}">
                           
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

