

    <div class="row"><div class="col-md-12">
        <div class="portlet light bordered">

            <div class="portlet-body form">
                <form role="form" action="{{ url('autoprice/'.$rule['id']) }}" method="POST">
                    {{ csrf_field() }}
                    {{ method_field('PUT') }}
					<input type="hidden" name='id' value="{{$rule['id']}}" />
                    <div class="form-body">
                        <div class="form-group">
                            <label>Account</label>
                            <div class="input-group ">
                                <span class="input-group-addon">
                                    <i class="fa fa-tag"></i>
                                </span>
								
								<select name="seller_id" class="form-control form-filter" required disabled="disabled" readonly>
								   <?php 
									foreach($accounts as $k=>$v){
										$selected = ($k==$rule['seller_id'])?'selected':'';
										echo '<option value="'.$k.'" '.$selected.'>'.$v.' ( '.$k.' ) </option>';
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
                                <select name="marketplace_id" class="form-control form-filter" required disabled="disabled" readonly>
								   <?php 
									foreach(getSiteCode() as $k=>$v){ 	
										$selected = ($v==$rule['marketplace_id'])?'selected':'';
										echo '<option value="'.$v.'" '.$selected.'>'.$k.' ( '.$v.' ) </option>';
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
                                <input type="text" class="form-control" name="seller_sku" id="seller_sku" value="{{$rule['seller_sku']}}" required disabled="disabled" readonly>
                            </div>
                        </div>
						
						<div class="form-group">
                            <label>Status</label>
                            <div class="input-group ">
                                <span class="input-group-addon">
                                    <i class="fa fa-sort-amount-asc"></i>
                                </span>
                                <select name="actived" class="form-control form-filter" required>
								   	<option value="0" {{$rule['actived']==0?'selected':''}} >Disable
									<option value="1"  {{$rule['actived']==1?'selected':''}}>Enable
								
								</select>
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
				
				<table class="table table-hover">
					<thead>
						<tr>
							<th> Date </th>
							<th> Sales </th>
							<th> Stock </th>
							<th> Price </th>
							<th> Update Price </th>
						</tr>
					</thead>
					<tbody>
						@foreach ($logs as $log)
						<tr>
							<td> {{$log->date}} </td>
							<td> {{$log->sales}} </td>
							<td> {{$log->stock}} </td>
							<td> {{$log->price}} </td>
							<td>
								{{$log->update_price}}
							</td>
						</tr>
						 @endforeach
					</tbody>
				</table>
											
            </div>
        </div>
    </div>

    </div>

