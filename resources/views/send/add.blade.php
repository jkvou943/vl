@extends('layouts.layout')
@section('label', 'Compose')
@section('content')
<style>
  .ui-autocomplete {
    max-height: 300px;
	z-index:9999;
    overflow-y: auto;
    /* 防止水平滚动条 */
    overflow-x: hidden;
  }
</style>
<script>
  $(function() {
    $( "#tags" ).autocomplete({
      source: "/template/ajax/get",
      minLength: 1,
      select: function( event, ui ) {
	  	if(ui.item){
		
		   var title = ui.item.title;
		   var desc = ui.item.desc;
		   rename = new RegExp("{CUSTOMER_NAME}","g"); 
		   remail = new RegExp("{CUSTOMER_EMAIL}","g"); 
		   retitle = new RegExp("{EMAIL_TITLE}","g"); 
		   title = title.replace(rename, "");
		   title = title.replace(remail, "");	
		   title = title.replace(retitle, "");	
		   desc = desc.replace(rename, "");
		   desc = desc.replace(remail, "");
		   desc = desc.replace(retitle, "");
		   $( "#subject" ).val(title);
           var ue = UE.getEditor('container');
		   ue.ready(function() {
				ue.setContent(desc);
		   });
		   
		}	
      }
    });
	$("#fileupload").submit(function(e){

	  var mycars = new Object();
	
	  @foreach ($accounts_type as $account_email=>$type)
		 mycars["{{$account_email}}"] = '{{$type}}';
	  @endforeach

	  if(mycars[$('#from_address').val()]!='Amazon') return true;
	  
	  var ue = UE.getEditor('container');
	  var str = ue.getContent();
	  
	  var forbidwords = {!!getForbidWords()!!};
	  var haveforbidwords = '';
	  for(var j = 0,len = forbidwords.length; j < len; j++){
		 var reg = eval("/"+forbidwords[j]+"/ig");
   		 if(reg.test(str)){
			haveforbidwords = haveforbidwords + forbidwords[j] + ' ; ' ;
		 }
	  }
	  
	  if(haveforbidwords){
	  	
	  	 bootbox.dialog({
			message: "Your submission contains sensitive words : ( "+haveforbidwords+" ) , Please resubmit it after revision",
			title: "Error",
				buttons: {
					main: {
						label: "Return To Edit",
						className: "blue"
					}
				}
		});
		return false;
	  }
	  
	  if($('#warn').val()!=1){
	  	var havewarnwords = '';	
		  var warnwords = {!!getWarnWords()!!};
		  for(var j = 0,len = warnwords.length; j < len; j++){
			 var reg = eval("/"+warnwords[j]+"/ig");
			 if(reg.test(str)){
				havewarnwords = havewarnwords + warnwords[j] + ' ; ' ;
			 }
		  }
		  if(havewarnwords){
		   bootbox.dialog({
				message: "Your submission contains sensitive words : ( "+havewarnwords+" ) , Please resubmit it after revision",
				title: "Warning",
				buttons: {
					danger: {
						label: "Continue Submit",
						className: "red",
						callback: function() {
							$('#warn').val(1);
							$('#fileupload').submit();
						}
					},
					main: {
						label: "Return To Edit",
						className: "blue"
					}
				}
			});
			return false;
		}
	  }
	  return true;		  
	});
  });
  </script>
    <h1 class="page-title font-red-intense"> Compose
        <small>Create a new email.</small>
    </h1>


    <div class="row"><div class="col-md-12">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-settings font-dark"></i>
                    <span class="caption-subject font-dark sbold uppercase">Compose Email</span>
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
                <div class="col-xs-10">
                    <form id="fileupload" action="{{ url('send') }}" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}
						<input type="hidden" name="warn" id="warn" value="0">
                        <input type="hidden" name="inbox_id" id="inbox_id" value="0">
                        <input type="hidden" name="user_id" id="user_id" value="{{Auth::user()->id}}">

                        <div class="form-group">
                            <label>From</label>
                            <div class="input-group ">
                                <span class="input-group-addon">
                                    <i class="fa fa-bookmark"></i>
                                </span>
                                <select name="from_address" id="from_address" class="btn btn-default">
                                    @foreach ($accounts as $account_id=>$account_email)
                                        <option value="{{$account_email}}" <?php if($request->get('from_address')==$account_email) echo 'selected';?>>{{$account_email}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Send To ( Multiple please use " <font color="#FF0000">;</font> " to separate ) </label>
                            <div class="input-group ">
                                <span class="input-group-addon">
                                    <i class="fa fa-bookmark"></i>
                                </span>
                                <input type="mail" class="form-control" name="to_address" id="to_address" value="<?php echo $request->get('to_address');?>" >
                            </div>
                        </div>
						<div class="form-group">
                            <label>Templates</label>
                            <div class="input-group ">
                                <span class="input-group-addon">
                                    <i class="fa fa-bookmark"></i>
                                </span>
                                <input type="text" class="form-control" id="tags" >
                            </div>
                        </div>
						
                        <div class="form-group">
                            <label>Subject</label>
                            <div class="input-group ">
                                <span class="input-group-addon">
                                    <i class="fa fa-bookmark"></i>
                                </span>
                                <input type="text" class="form-control" name="subject" id="subject" value="<?php echo $request->get('subject');?>" >
                            </div>
                        </div>
						
						<div class="form-group">
							<label class="control-label">Specify time to send ( UTC Time )</label>
							<div class="input-group">
								<div class="input-group  form_datetime date">
									<input type="text" size="16" name="plan_date" readonly class="form-control">
									<span class="input-group-btn">
										<button class="btn default date-clear" type="button">
											<i class="fa fa-times"></i>
										</button>
										<button class="btn default date-set" type="button">
											<i class="fa fa-calendar"></i>
										</button>
									</span>
								</div>
							</div>
							
						</div>


                        <div class="form-group" >
                        @include('UEditor::head')

                        <!-- 加载编辑器的容器 -->
                            <script id="container" name="content" type="text/plain"></script>
                            <!-- 实例化编辑器 -->
                            <script type="text/javascript">
                                var ue = UE.getEditor('container');
                                ue.ready(function() {
                                    ue.execCommand('serverparam', '_token', '{{ csrf_token() }}');//此处为支持laravel5 csrf ,根据实际情况修改,目的就是设置 _token 值.
                                });
                            </script>
                            <div style="clear:both;"></div>
                        </div>
						
						
												
                        <div class="form-group">
                            <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
                            <div class="row fileupload-buttonbar">
                                <div class="col-lg-7">
                                    <!-- The fileinput-button span is used to style the file input field as button -->
                                    <span class="btn green fileinput-button">
                                                <i class="fa fa-plus"></i>
                                                <span> Add files... </span>
                                                <input type="file" name="files[]" multiple=""> </span>
                                    <button type="submit" class="btn blue start">
                                        <i class="fa fa-upload"></i>
                                        <span> Start upload </span>
                                    </button>
                                    <button type="reset" class="btn warning cancel">
                                        <i class="fa fa-ban-circle"></i>
                                        <span> Cancel upload </span>
                                    </button>

                                    <button type="button" class="btn red delete">
                                        <i class="fa fa-trash"></i>
                                        <span> Delete </span>
                                    </button>
                                    <input type="checkbox" class="toggle">
                                    <!-- The global file processing state -->
                                    <span class="fileupload-process"> </span>
                                </div>
                                <!-- The global progress information -->
                                <div class="col-lg-5 fileupload-progress fade">
                                    <!-- The global progress bar -->
                                    <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar progress-bar-success" style="width:0%;"> </div>
                                    </div>
                                    <!-- The extended global progress information -->
                                    <div class="progress-extended"> &nbsp; </div>
                                </div>
                            </div>
                            <!-- The table listing the files available for upload/download -->
                            <table role="presentation" class="table table-striped clearfix">
                                <tbody class="files"> </tbody>
                            </table>
                            <div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" data-filter=":even">
                                <div class="slides"> </div>
                                <h3 class="title"></h3>
                                <a class="prev"> ‹ </a>
                                <a class="next"> › </a>
                                <a class="close white"> </a>
                                <a class="play-pause"> </a>
                                <ol class="indicator"> </ol>
                            </div>
                            <!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
                            <script id="template-upload" type="text/x-tmpl"> {% for (var i=0, file; file=o.files[i]; i++) { %}
                            <tr class="template-upload fade">
                                <td>
                                    <p class="name">{%=file.name%}</p>
                                    <strong class="error text-danger label label-danger"></strong>
                                </td>
                                <td>
                                    <p class="size">Processing...</p>
                                    <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                                        <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                                    </div>
                                </td>
                                <td> {% if (!i && !o.options.autoUpload) { %}
                                    <button class="btn blue start" disabled>
                                        <i class="fa fa-upload"></i>
                                        <span>Start</span>
                                    </button> {% } %} {% if (!i) { %}
                                    <button class="btn red cancel">
                                        <i class="fa fa-ban"></i>
                                        <span>Cancel</span>
                                    </button> {% } %} </td>
                            </tr> {% } %} </script>
                            <!-- The template to display files available for download -->
                            <script id="template-download" type="text/x-tmpl"> {% for (var i=0, file; file=o.files[i]; i++) { %}
                            <tr class="template-download fade">
                                <td>
                                    <p class="name"> {% if (file.url) { %}
                                        <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl? 'data-gallery': ''%}>{%=file.name%}</a> {% } else { %}
                                        <span>{%=file.name%}</span> {% } %}
                                        {% if (file.name) { %}
                                            <input type="hidden" name="fileid[]" value="{%=file.url%}">
                                        {% } %}

                                        </p> {% if (file.error) { %}
                                    <div>
                                        <span class="label label-danger">Error</span> {%=file.error%}</div> {% } %} </td>
                                <td>
                                    <span class="size">{%=o.formatFileSize(file.size)%}</span>
                                </td>
                                <td> {% if (file.deleteUrl) { %}
                                    <button class="btn red delete btn-sm" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}" {% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}' {% } %}>
                                        <i class="fa fa-trash-o"></i>
                                        <span>Delete</span>
                                    </button>
                                    <input type="checkbox" name="delete" value="1" class="toggle"> {% } else { %}
                                    <button class="btn yellow cancel btn-sm">
                                        <i class="fa fa-ban"></i>
                                        <span>Cancel</span>
                                    </button> {% } %} </td>
                            </tr> {% } %} </script>
                            <div style="clear:both;"></div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-offset-4 col-md-8">
                                    <button type="submit" class="btn blue">Submit</button>
                                    <button type="reset" class="btn grey-salsa btn-outline">Cancel</button>
									<button type="submit" class="btn yellow" name='asDraft' value="1">Save as Draft</button>
                                </div>
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                    </form>
                    <div style="clear:both;"></div>
                </div>
                <div style="clear:both;"></div>
            </div>
        </div>
    </div>

    </div>


@endsection
