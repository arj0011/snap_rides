@extends('admin.layouts.app')
@section('title', 'Driver Info')
@section('breadcrumb')
      <h1>
         Driver Information 
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li><a href="{{route('driver/drivers') }}">Drivers</a></li>
        <li class="active">Driver Info</li>
      </ol>
@endsection
@section('content')   
     <div id="page-wrapper">
        
   
    @if (Session::has('msg'))
      <div class="row alert-row">
        <div class="col-md-12">
            <div  class="alert alert-{{ Session::get('color') }} alert-custome">
               <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
               <p>{{ Session::get('msg') }}</p>
            </div>
        </div>
      </div>
    @endif
    
    <div class="row">
        <div class="col-md-12">
          <!-- Custom Tabs -->
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#tab_1" data-toggle="tab">General Details</a></li>
              <li><a href="#tab_2" data-toggle="tab">Vehicle Details</a></li>
              <li><a href="#tab_3" data-toggle="tab">Documents Details</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
                    <div class="row">
		               <div class="col-md-12">
				              <div class="col-md-2">
					                @if (!empty($driver->profile_image) && file_exists('Admin/profileImage/'.$driver->profile_image))
					              	  <img class="profile-user-img img-responsive " src="{{  asset('Admin/profileImage/'.$driver->profile_image) }}" alt="User profile picture" style="width: 100%; height: 200px;" >
					                @else
					                 <img class="profile-user-img img-responsive " src="{{  asset('Admin/profileImage/unknown.png') }}" alt="User profile picture" style="width: 100%; height: 200px;" >
					                @endif
					                <ul class="list-group list-group-unbordered">
						              <li class="list-group-item">
					                  <b>Rating</b>
					                   <i class="pull-right">
				                        @if ($driver->rating > '0')
				                         @for ($i = '1'; $i <= $driver->rating ; $i++)
				                           <span class="fa fa-star checked"></span>
				                         @endfor
				                         @for ($j = '1'; $j <= '6'-$i; $j++)
				                           <span class="fa fa-star"></span>
				                         @endfor
				                        @else
				                           not rating 
				                        @endif
				                        </i>
					                </li>
					                </ul>
				              </div>

				              <div class="col-md-5">
					              <ul class="list-group list-group-unbordered">
					                <li class="list-group-item li-border-top">
					                  <b>Name</b> <a class="pull-right">{{ $driver->driver_name }}</a>
					                </li>
					                <li class="list-group-item">
					                  <b>Email</b> <a class="pull-right">{{ $driver->email }}</a>
					                </li>
					                <li class="list-group-item">
					                  <b>Mobile</b> <a class="pull-right">{{ $driver->mobile }}</a>
					                </li>
					                <li class="list-group-item">
					                  <b>Identity No</b> <a class="pull-right">{{ ($driver->identity_no != '') ? $driver->identity_no : 'Not Available'  }}</a>
					                </li>
					                <li class="list-group-item">
					                  <b>Date of Birth</b> <a class="pull-right">{{($driver->dob != '') ? date('d-M-Y', strtotime($driver->dob)) : 'Not Available'}}</a>
					                </li>
					                <li class="list-group-item">
					                  <b>Registration Date</b> <a class="pull-right">{{date('d-M-y', strtotime($driver->created_at))}} {{date('h:i A',strtotime($driver->created_at))}}</a>
					                </li>
					                <li class="list-group-item">
					                  <b>Status</b>
					                    @if ($driver->is_active == 1 )
					                      <a class="pull-right" style="color:#008000">Active</a>
					                    @else
					                      <a class="pull-right" style="color:#FF0000">Deactive</a>
					                    @endif</a>
						            </li>
					              </ul>
				              </div>

				              <div class="col-md-5">
					              <ul class="list-group list-group-unbordered">
					                <li class="list-group-item li-border-top">
					                  <b>Country</b> <a class="pull-right">{{ $driver->country_name }}</a>
					                </li>
					                <li class="list-group-item">
					                  <b>Province</b> <a class="pull-right">{{ $driver->state_name }}</a>
					                </li>
					                <li class="list-group-item">
					                  <b>City</b> <a class="pull-right">{{ $driver->city_name }}</a>
					                </li>
					                <li class="list-group-item">
					                  <b>Zip Code</b> <a class="pull-right">{{ ($driver->zip_code != Null or $driver->zip_code != '' ) ? $driver->zip_code : 'Not Available' }}</a>
					                </li>
					                <li class="list-group-item">
					                  <b>Address</b> <a class="pull-right">{{ $driver->address }}</a>
					                </li>
					                {{--  <li class="list-group-item">
					                  <b>Discount</b> <a class="pull-right">{{ $driver->discount }} </a>
					                </li>
					                 <li class="list-group-item">
					                  <b>Allow discount before 15 trip</b> <a class="pull-right">
                                           @if ($driver->allow_discount == 1)
                                             {{ 'Yes' }}
                                           @else
					                         {{ 'No' }}
                                           @endif
					                      </a>
					                </li> --}}
					              </ul>
				              </div>
		               </div>
	               </div>
                </div>
                <div class="tab-pane" id="tab_2">
                    <div class="row">
		               <div class="col-md-12">
				                <div class="col-md-2">
               @if (!empty($driver->vehicle_image && file_exists('Admin/vehicleImage/'.$driver->vehicle_image)))
                  <img class="profile-user-img img-responsive" src="{{  asset('Admin/vehicleImage/'.$driver->vehicle_image) }}" style="width: 100%; height: 200px">
              @else
                  <img class="profile-user-img img-responsive" src="{{  asset('Admin/vehicleImage/unknownVehicle.png') }}" style="width: 100%; height: 200px">
              @endif
            </div>
              <div class="col-md-5">
		          <ul class="list-group list-group-unbordered">
		            <li class="list-group-item li-border-top">
		              <b>Vehicle Type</b> <a class="pull-right">@if ($driver->vehicle_category)
		              	{{ $driver->vehicle_category }}@else {{ 'Not Available' }}@endif</a>
		            </li>
                <li class="list-group-item">
                  <b>Vehicle Model</b> <a class="pull-right">@if ($driver->vehicle_name)
		              	{{ $driver->vehicle_name }}@else {{ 'Not Available' }}@endif</a>
                </li>
		            <li class="list-group-item">
		              <b>Registration No.</b> <a class="pull-right">@if ($driver->registration_number)
		              	{{ $driver->registration_number }}@else {{ 'Not Available' }}@endif</a>
		            </li>
		            <li class="list-group-item">
		              <b>Insurance No. </b> <a class="pull-right">@if ($driver->insurance_number)
		              	{{ $driver->insurance_number }}@else {{ 'Not Available' }}@endif</a>
		            </li>
		            <li class="list-group-item">
		              <b>Year of Car </b> <a class="pull-right">@if ($driver->car_year)
		              	{{ $driver->car_year }}@else {{ 'Not Available' }}@endif</a>
		            </li>
		          </ul>
              </div>
                <div class="col-md-5">
              <ul class="list-group list-group-unbordered">
                <li class="list-group-item li-border-top">
                  <b>Color</b>@if ($driver->color)
		              	<div class="pull-right" style="background: {{ $driver->color }} ; width: 60px ; height: 20px;"></div>@else<a class="pull-right">{{ 'Not Available' }}</a>@endif 
                </li>
                {{-- <li class="list-group-item">
                  <b>Base Fare</b>@if ($driver->basefare)
		              	<a class="pull-right">{{ $driver->basefare }}  </a>@else<a class="pull-right">Not available</a>@endif
                </li> --}}
                <li class="list-group-item">
					<b>Per Kilometer Charges</b> <a class="pull-right">{{ $driver->per_km_charges }} Per/Km</a>
				</li>
              </ul>
              </div>
		               </div>
	               </div>
                </div>
                <div class="tab-pane" id="tab_3">
	                <div class="row">
		               <div class="col-md-12">
		                      <table class="table table table-hover">
		                      	 <thead>
		                      	   <tr>
			                      	 	<th>Sr.</th>
			                      	 	<th>Document</th>
			                      	 	<th>Verification Status</th>
			                      	 	<th>Download</th>
			                      	 	<th>view</th>
		                      	 	</tr>
		                      	 </thead>
		                      	 <tbody>
		                      	 	<tr>
		                      	 	    <td>1</td>
		                      	 		<td>Driver Id Proof</td>
		                      	 		@if (!empty($driver->id_proof))
		                      	 		    <td>
                                              	<select class="form-control verificationStatus" data-doc="id_proof">
                                              		<option @if ($driver->id_verification == 'unverified')
                                              			{{ 'selected' }}
                                              		@endif value="unverified">Unverified</option>
                                              		<option @if ($driver->id_verification == 'verified')
                                              			{{ 'selected' }}
                                              		@endif value="verified">Verified</option>
                                              	</select> 
		                      	 		    </td>
			                      	 		<td>
			                      	 			{{-- <a href="{{ asset('Admin/driver_documents/'.$driver->id_proof)}}" download="{{ $driver->id_proof }}">Download</a> --}}
			                      	 			
			                      	 			<a href="{{ url("downloadDoc/$driver->id_proof")  }}">Download</a>
			                      	 		</td>
			                      	 		<td>
			                      	 		   <button type="button" class="form-btn btn-default btn-flat btn-xs" data-toggle="modal" data-target="#modal-id-proof">
	                                              View
	                                           </button>
	                                        </td>
		                      	 		@else
		                      	 		   <td colspan="3"> Document not availabel yet</td>
		                      	 		@endif
		                      	 	</tr>
		                      	 	<tr>
		                      	 	    <td>2</td>
		                      	 		<td>Driving Licence</td>
		                      	 		@if (!empty($driver->driving_licence))
		                      	 		 <td>
                                              	<select class="form-control verificationStatus" data-doc="licence">
                                              		<option @if ($driver->lince_verification == 'unverified')
                                              			{{ 'selected' }}
                                              		@endif value="unverified">Unverified</option>
                                              		<option @if ($driver->lince_verification == 'verified')
                                              			{{ 'selected' }}
                                              		@endif value="verified">Verified</option>
                                              	</select> 
			                      	 		</td>
			                      	 		<td>
			                      	 			{{-- <a href="{{ asset('Admin/driver_documents/'.$driver->driving_licence)}}" download>Download</a> --}}

			                      	 			<a href="{{ url("downloadDoc/$driver->driving_licence")  }}">Download</a>

			                      	 		</td>
			                      	 		<td>
			                      	 		   <button type="button" class="btn btn-default btn-flat btn-xs" data-toggle="modal" data-target="#modal-driving-license">
	                                              View
	                                           </button>
	                                        </td>
		                      	 		@else
		                      	 		   <td colspan="3"> Document not availabel yet</td>
		                      	 		@endif
		                      	 	</tr>
		                      	 	<tr>
		                      	 	    <td>3</td>
		                      	 		<td>Vehicle Registration</td>
		                      	 		@if (!empty($driver->vehicle_registration))
			                      	 		<td>
			                      	 		    <select class="form-control verificationStatus" data-doc="registration">
                                              		<option @if ($driver->reg_verification == 'unverified')
                                              			{{ 'selected' }}
                                              		@endif value="unverified">Unverified</option>
                                              		<option @if ($driver->reg_verification == 'verified')
                                              			{{ 'selected' }}
                                              		@endif value="verified">Verified</option>
                                              	</select> 
			                      	 		</td>
			                      	 		<td>
			                      	 			{{-- <a href="{{ asset('Admin/driver_documents/'.$driver->vehicle_registration)}}" download>Download</a> --}}

			                      	 			<a href="{{ url("downloadDoc/$driver->vehicle_registration")  }}">Download</a>

			                      	 		</td>
			                      	 		<td>
			                      	 		   <button type="button" class="btn btn-default btn-flat btn-xs" data-toggle="modal" data-target="#modal-vehicle-registration">
	                                              View
	                                           </button>
	                                        </td>
		                      	 		@else
		                      	 		   <td colspan="3"> Document not availabel yet</td>
		                      	 		@endif
		                      	 	</tr>
		                      	 	<tr>
		                      	 	    <td>4</td>
		                      	 		<td>Vehicle Insurence</td>
		                      	 		@if (!empty($driver->vehicle_insurance))
			                      	 		<td>
                                               <select class="form-control verificationStatus" data-doc="insurance">
                                              		<option @if ($driver->ins_verification == 'unverified')
                                              			{{ 'selected' }}
                                              		@endif value="unverified">Unverified</option>
                                              		<option @if ($driver->ins_verification == 'verified')
                                              			{{ 'selected' }}
                                              		@endif value="verified">Verified</option>
                                              	</select> 
			                      	 		</td>
			                      	 		<td>
			                      	 			{{-- <a href="{{ asset('Admin/driver_documents/'.$driver->vehicle_insurance)}}" download>Download</a> --}}

			                      	 			<a href="{{ url("downloadDoc/$driver->vehicle_insurance")  }}">Download</a>

			                      	 		</td>
			                      	 		<td>
			                      	 		   <button type="button" class="btn btn-default btn-flat btn-xs" data-toggle="modal" data-target="#modal-vehicle-insurance">
	                                              View
	                                           </button>
	                                        </td>
	                                     @else
                                            <td colspan="3"> Document not availabel yet</td>
	                                     @endif
		                      	 	</tr>
		                      	 
		                      	 </tbody>
		                      </table>		       
		               </div>
	               </div>
                </div>
              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- nav-tabs-custom -->
        </div>
        <!-- /.col -->
    </div>
      <!-- /.row -->
      <!-- END CUSTOM TABS -->

      {{--  Model for document view --}}
      <div class="modal fade" id="modal-driving-license">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Driving Lincence</h4>
              </div>
              <div class="modal-body">
                @if (!empty($driver->driving_licence) && file_exists('Admin/driver_documents/'.$driver->driving_licence))
                        <embed src="{{ asset('Admin/driver_documents/'.$driver->driving_licence) }}" style="width: 100%; height: 530px; overflow-y: scroll;" />
                @else
                        <img src="{{ asset('Admin/driver_documents/unknown.png') }}" style="width: 100%; height: 200px; overflow-y: scroll;">
                @endif
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
    
        {{--  Model for document view --}}
      <div class="modal fade" id="modal-id-proof">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">ID Proof Documents</h4>
              </div>
              <div class="modal-body">
                @if (!empty($driver->id_proof) && file_exists('Admin/driver_documents/'.$driver->id_proof))
                    <embed src="{{ asset('Admin/driver_documents/'.$driver->id_proof) }}" style="width: 100%; height: 530px; overflow-y: scroll;" />
                @else
                   <img src="{{ asset('Admin/driver_documents/unknown.png') }}" style="width: 100%; height: 200px; overflow-y: scroll;">
                @endif
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
            {{--  Model for document view --}}
      <div class="modal fade" id="modal-vehicle-registration">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Vehicle Registration</h4>
              </div>
              <div class="modal-body">
                @if (!empty($driver->vehicle_registration) && file_exists('Admin/driver_documents/'.$driver->vehicle_registration))
                    <embed src="{{ asset('Admin/driver_documents/'.$driver->vehicle_registration) }}" style="width: 100%; height: 530px; overflow-y: scroll;" />
                @else
                   <img src="{{ asset('Admin/driver_documents/unknown.png') }}" style="width: 100%; height: 200px; overflow-y: scroll;">
                @endif
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
                  {{--  Model for document view --}}
      <div class="modal fade" id="modal-vehicle-insurance">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Vehicle Insurance</h4>
              </div>
              <div class="modal-body">
                @if (!empty($driver->vehicle_insurance) && file_exists('Admin/driver_documents/'.$driver->vehicle_insurance))
                    <embed src="{{ asset('Admin/driver_documents/'.$driver->vehicle_insurance) }}" style="width: 100%; height: 530px; overflow-y: scroll;"/>
                @else
                    <img src="{{ asset('Admin/driver_documents/unknown.png') }}"  style="width: 100%; height: 200px; overflow-y: scroll;" >
                @endif
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->

@endsection
@section('css-script')
  <style type="text/css">
  	 select{
  	 	border-top:none;
  	 	border-left :none;
  	 	border-right: none;
  	 }
  </style>
@endsection
@section('js-script')
 <script type="text/javascript">

  function confirmDelete(delUrl) {
      console.log(delUrl);
            if (confirm("Are you sure you want to delete this driver?")) {
                document.location = delUrl;
             }
        }

 @if (Session::has('msg'))
   window.setTimeout(function () { 
   $(".alert-row").fadeOut('slow') }, 2000); 
 @endif

 $(".verificationStatus").change(function(){
 	let x = confirm('Do you realy want to change status?');
    if(x){
     $.ajax({
       type: "get",
       url : "{{ route('driver/verification-status') }}",
       data : {
          'id' :  "{{ encrypt($driver->id) }}",
          'document' : $(this).attr('data-doc'),
          'status' : $(this).val()
       },
       success: function(response)
       { 
        var data = JSON.parse(response); 
       	   if(data.status){
             alert(data.message);       	    
       	   }else{
       	   	 alert(data.message);
       	   }
       },
       failure:function(response){
            var data = JSON.parse(response); 
       	   if(data.false){
       	   	 alert(data.message);
       	   }
       }
     });
}
 });

 </script>
@endsection

