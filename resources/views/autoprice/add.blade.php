

    <div class="row"><div class="col-md-12">
        <div class="portlet light bordered">

            <div class="portlet-body form">
                <form role="form" action="{{ url('autoprice') }}" method="POST">
                    {{ csrf_field() }}
                    <div class="form-body">
                        <div class="form-group">
                            <label>Account</label>
                            <div class="input-group ">
                                <span class="input-group-addon">
                                    <i class="fa fa-tag"></i>
                                </span>
								
								<select name="seller_id" class="form-control form-filter" required>
								   <?php 
									foreach($accounts as $k=>$v){ 	
										echo '<option value="'.$k.'">'.$v.' ( '.$k.' ) </option>';
									}?>
								</select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Site</label>
                            <div class="input-group ">
                                <span class="input-group-addon">
                                    <i class="fa fa-sort-amount-asc"></i>
                                </span>
                                <select name="marketplace_id" class="form-control form-filter" required>
								   <?php 
									foreach(getSiteCode() as $k=>$v){ 	
										echo '<option value="'.$v.'">'.$k.' ( '.$v.' ) </option>';
									}?>
								</select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>SellerSKU</label>
                            <div class="input-group ">
                                <span class="input-group-addon">
                                    <i class="fa fa-heart"></i>
                                </span>
                                <input type="text" class="form-control" name="seller_sku" id="seller_sku" value="" required>
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

