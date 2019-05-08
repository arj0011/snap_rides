@extends('admin.layouts.app')
@section('title', 'Drivers')
@section('breadcrumb')
      <h1>
       Drivers
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li class="active">Drivers</li>
      </ol>
@endsection
@section('content') 
<div id="loading" style="display: none;">
  <img id="loading-image" src="{{ asset('Admin/img/loading-spinner.gif') }}" alt="Loading..." />
</div>  
  <div id="page-wrapper">
     <div class="row">
            <div class="col-xs-12">
              <div class="box">
                <!-- /.box-header -->
                <form class="form-inline" action="{{  route('driver/search') }}" method="post">
                  {{ csrf_field() }}
                  <div class="box-body">
                       <div class="input-group input-group-xs">
                    <div class="input-group-btn">
                      <select name="p" class="form-control" id="selectInput">
                        <option  value="">All</option>
                        <option  @isset ($p) @if($p == 'name') {{ "selected" }} @endif @endisset value="name">Driver Name</option>
                        <option  @isset ($p) @if($p == 'mobile') {{ "selected" }} @endif @endisset value="mobile">Mobile</option>
                         <option @isset ($p) @if($p == 'email') {{ "selected" }} @endif @endisset value="email">Email</option>
                        <option @isset ($p) @if($p == 'company') {{ "selected" }} @endif @endisset value="company">Company Name</option>
                      </select>
                    </div>
                    <!-- /btn-group -->
                    <input name="q" type="text" class="form-control" value="@if(isset($q)){{ trim($q) }}@endif" id="textInput">
                  </div>
                   <div class="form-group">
                    <select class="form-control" name="is_active">
                    <option value="">Select Status</option>
                    <option value="1" @isset ($is_active) @if($is_active == '1') {{ "selected" }} @endif @endisset>Active</option>
                    <option value="0" @isset ($is_active) @if($is_active == '0') {{ "selected" }} @endif @endisset>InActive</option>
                    </select>

                   </div>
                    <div class="form-group">
                    <!--   0 = Pending, 1 = Approved , 2 = Declined -->
                    <select class="form-control" name="is_approved"><option value="">Select Aproval Status</option>
                    <option value="1" @isset ($is_approved) @if($is_approved == '1') {{ "selected" }} @endif @endisset>Approved</option>
                    <option value="0" @isset ($is_approved) @if($is_approved == '0') {{ "selected" }} @endif @endisset>Pending</option>
                    <option value="2" @isset ($is_approved) @if($is_approved == '2') {{ "selected" }} @endif @endisset>Declined</option>
                  </select>

                   </div>

                    <div class="form-group">
                         <button type="submit" class="btn btn-flat margin" data-toggle="tooltip" title="Search">Search</button>
                         <a href="{{ route('driver/drivers') }}" type="reset" class="btn btn-default btn-flat" data-toggle="tooltip" title="Reset">Reset</a>
                    </div>
                  </div>
                  <!-- /.box-body -->
                </form>
                <!-- /.box-body -->
              </div>
              <!-- /.box -->
            </div>  
     </div>


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
       <div class="col-xs-12 data-div">
          <div class="box">
          <div class="box-header">
              <h3 class="box-title"><b>Drivers</b>
              </h3>
              <div class="box-tools">
                  @can('create' , App\Driver::class)
                    <a href="{{ route('driver/create') }}" type="submit" class="btn btn-primary btn-flat" data-toggle="tooltip" title="Add New"><i class="fa fa-plus"></i>&nbsp;Add</a>
                  @endcan
              </div>
          </div>

            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <thead style="background:#F7F7F7;">
                  <tr>
                    <th>Sr.</th>
                    <th nowrap>Driver Name</th>
                    <!-- <th nowrap>Company Name</th> -->
                    <th nowrap>Mobile</th>
                    <th nowrap>Email</th>
              {{--       <th nowrap>Rating</th> --}}
                    @can('setStatus' , App\Driver::class)
                       <th nowrap>Active Status</th>
                    @endcan
                    @can('approved' , App\Driver::class)
                       <th nowrap>Approved Status</th>
                    @endcan
                    {{-- <th nowrap>Blocked Status</th> --}}
                        @if ( Auth::user()->can('update' , App\Driver::class) ||
                              Auth::user()->can('delete' , App\Driver::class) ||
                              Auth::user()->can('view' , App\Driver::class) )
                          <th nowrap>Action</th>
                        @endif
                </thead>
                <tbody>
                    @forelse ($drivers as $driver)
                      <tr><td>{{  $i++  }}</td>
                        <td>{{ $driver->driver_name }} </td>
                        <!-- <td>{{ ($driver->company_name) != '' ? $driver->company_name : 'Not available' }}</td> -->
                        <td>{{ $driver->mobile }}</td>
                        <td>{{ $driver->email }}</td>
                      {{--   <td>
                           @if ($driver->rating > '0')
                             @for ($i = '1'; $i <= $driver->rating ; $i++)
                               <span class="fa fa-star checked"></span>
                             @endfor
                             @for ($j = '1'; $j <= '6'-$i; $j++)
                               <span class="fa fa-star"></span>
                             @endfor
                           @else
                              yet to not rating 
                           @endif
                        </td> --}}
                        @can('setStatus' , App\Driver::class)
                          <td>
                          @if($driver->is_active == 1)
                                <button class="btn btn-success btn-xs btn-flat" data-id="{{ encrypt($driver->id) }}"> Active&emsp; </button> 
                          @else
                               <button class="btn btn-danger btn-xs btn-flat" data-id="{{ encrypt($driver->id) }}">Deactive</button>
                          @endif</td>
                          @endcan
                        @can('approved' , App\Driver::class)
                          <td nowrap>
                             <div class="form-group approved-form-div">
                                <select class="form-control" data-id="{{ encrypt($driver->id) }}">
                                   @if (empty($driver->is_approved))
                                     <option  value="0">Pending</option>
                                   @endif
                                   <option @if ( $driver->is_approved == 1 )
                                     {{ 'selected' }}
                                   @endif value="1">Approved</option>
                                   <option @if ( $driver->is_approved == 2 )
                                     {{ 'selected' }}
                                   @endif value="declined">Declined</option>
                                </select>
                             </div>
                          </td>
                        @endcan
                     {{--    <td class="{{ 'blocked_'.$driver->id }}">
                          <button class="btn  {{ ($driver->is_blocked == 1) ? 'btn-danger' : 'btn-success' }} btn-flat btn-block btn-xs btn-delete" data-column="is_blocked" data-status="{{ $driver->is_blocked }}" data-id="{{ $driver->id }}" data-taxt="{{ ($driver->is_active == 1) ? 'UnBlock' : 'Blocked' }}">{{ ($driver->is_blocked == 1) ? 'Blocked' : 'UnBlock' }}
                          </button>
                        </td> --}}
                      @if (   Auth::user()->can('update' , App\Driver::class) ||
                              Auth::user()->can('delete' , App\Driver::class) ||
                              Auth::user()->can('view' , App\Driver::class) )
                        <td nowrap>
                      {{--     <a href="@if (!empty($driver->vehicle_id))
                            {{ route('vehicle/edit', ['id' => encrypt($driver->vehicle_id)]) }}
                          @else
                            {{ route('vehicle/create' , ['id' => encrypt($driver->id)]) }}
                          @endif" data-toggle="tooltip" class="btn btn-flat btn-xs {{ empty($driver->vehicle_id) ? 'btn-warning' : 'btn-success' }}" title="{{ empty($driver->vehicle_id) ? 'Add vehicle' : 'Edit vehicle' }}" "><i class="fa fa-car"></i>
                          </a>

                          <a href="@if (!empty($driver->documents_id))
                            {{ route('driver/edit-documents', ['id' => encrypt($driver->documents_id)]) }}
                          @else
                            {{ route('driver/store-documents',['id' => encrypt($driver->id)]) }}
                          @endif" data-toggle="tooltip" class="btn btn-flat btn-xs {{ empty($driver->documents_id) ? 'btn-warning' : 'btn-success' }}" title="{{ empty($driver->documents_id) ? 'Add Documents' : 'Edit Documents' }}" "><i class="fa fa-file"></i>
                          </a> --}}     
                  {{--         @if ($driver->form_status == '1' )
                            <a href="{{ route('vehicle/create', ['id' => encrypt($driver->id)]) }}" data-toggle="tooltip" class="btn btn-flat btn-xs btn-info" title="Add Vehicle" "><i class="fa fa-car"></i>
                            </a>
                          @endif
                          @if($driver->form_status == '2')
                            <a href="{{ route('driver/store-documents', ['id' => encrypt($driver->id)]) }}" data-toggle="tooltip" class="btn btn-flat btn-xs btn-info" title="Upload Documents "><i class="fa fa-file"></i>
                            </a>
                          @endif --}}
                          @can('view' , App\Driver::class)
                            <a href="{{ route('driver/show', ['id' => encrypt($driver->id)]) }}" data-toggle="tooltip" class="btn btn-flat btn-xs btn-info" title="Driver Info" "><i class="fa fa-info-circle"></i>
                            </a>
                          @endcan
                          
                          @can('update' , App\Driver::class)
                            <a href="{{ route('driver/edit' , ['id' => encrypt($driver->id)]) }}" data-toggle="tooltip" class="btn btn-flat btn-xs btn-primary" title="Edit Driver"  }}"><i class="fa fa-edit"></i>
                            </a>
                          @endcan

                          @can('delete' , App\Driver::class)
                            <a href="javascript:confirmDelete('{{ route('driver/destroy' , ['id' => encrypt($driver->id)]) }}')" data-toggle="tooltip" class="btn btn-flat btn-xs btn-danger" title="Delete Driver"><i class="fa fa-trash"></i></a>
                            @endcan
                        </td>
                        @endif
                      </tr>
                      @empty
                        <tr>
                         <td colspan="6">
                           No any driver record available
                         </td>
                        </tr>
                      @endforelse
                 </tbody>
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
       </div>
     <div clasw="col-md-6">
            {{ $drivers->appends(request()->query())->links()  }}
     </div>
     </div>
     <!-- decline model -->

        @can('declined' , App\Driver::class)
   
     <div class="modal fade" id="modal-approve">
          <div class="modal-dialog">
            <form  action="{{ route('driver/decline-driver') }}" method="post">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title">Reseason</h4>
                </div>
                <div class="modal-body">
                        {{ csrf_field() }}
                          {{ method_field('PUT') }}
                        <input type="hidden" name="id" value="" id="idInput">
                        <div  class="form-group">
                           <textarea name="reason" placeholder="write a reseaon for decline to document!" class="form-control">
                            
                           </textarea>
                        </div>
                        <div class="form-group">
                          <label>Document</label><br>
                           <label class="checkbox-inline">
                                <input type="checkbox" name="decline_documents[]" value="1">ID Proof
                           </label>
                           <label class="checkbox-inline">
                                <input type="checkbox" name="decline_documents[]" value="2">Driving Licence
                           </label>
                           <label class="checkbox-inline">
                                <input type="checkbox" name="decline_documents[]" value="3">Vehicle Regitstration
                           </label>
                           <label class="checkbox-inline">
                                <input type="checkbox" name="decline_documents[]" value="4">vehicle Insurance
                           </label>
                        </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default pull-left btn-flat" data-dismiss="modal">Cancel</button>
                   <input type="submit" class="btn btn-default pull-left btn-success btn-flat" value="Submit">
                </div>
              </div>
            </form>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
        @endcan
      </div>
    </div>

@endsection
@section('css-script')
  <style type="text/css">
    .checked {
    color: orange;
    }

    .approved-form-div{
       margin-bottom: 0px !important;
    }

    .approved-form-div select{
        padding-top: 0px !important;
        padding-bottom: 0px !important;
        height: 22px;
    }


    #loading {
     width: 100%;
     height: 100%;
     top: 0;
     left: 0;
     position: fixed;
     display: block;
     opacity: 0.7;
     background-color: #fff;
     z-index: 99;
     text-align: center;
    }

  #loading-image {
    position: absolute;
    top: 258px;
    left: 728px;
    z-index: 100;
  }
  </style>
@endsection
@section('js-script')
  <script type="text/javascript">
  
  @can('approved' , App\Driver::class)
   $('table tr td select').change(function(event){
       $click = $(this);
       console.log($click);
      let value = $(this).val();
      if(value == '0' || value == '1'){
      let x = confirm("Do you want to change status?");
        if(x){
            $.ajax({
             type: "put",
             url : "{{ route('driver/approved') }}",
             data : {
                'id' :  $(this).attr('data-id'),
                'status' : $(this).val(),
                '_token': '{{ csrf_token() }}'
             },
             success: function(response)
             {
                 var data = JSON.parse(response);
                 console.log(data);
                 if(data.status){
                     alert(data.message)
                 }else{
                     alert(data.message);
                     location.reload();
                 }
             }
           });
        }
      }else{
         $('#idInput').attr('value',$(this).attr('data-id'));
         $('#modal-approve').modal('show');
      }
   });
@endcan
 @can('setStatus' , App\Driver::class)
 // set active and deactive status
  $('table tr td  button').click(function(event){
      let click = this;
      let x = confirm("Do you want to change status?");
        if(x){
            $.ajax({
             type: 'put',
             url : '{{ route('driver/set-status') }}',
             data : {
                'id' :  $(this).attr('data-id'),
                '_token': '{{ csrf_token() }}'
             },
             'beforeSend': function() {
                $('#loading').css("display","block");
              },
             success: function(response)
             {  
                let data = JSON.parse(response);
                console.log(data);
                  if(data.status != 'Failed'){
                    if(data.status){
                      alert('Successfully updated status');
                      $(click).removeClass('btn-danger');
                      $(click).addClass('btn-success');
                      $(click).html('Active&emsp; ');
                    }else{
                      alert('Successfully updated status');
                      $(click).removeClass('btn-success');
                      $(click).addClass('btn-danger');
                      $(click).text('Deactive');
                    }
                  }else{
                     alert('Failed to active status');
                  }
             },
             'error' : function(error){
                 console.log(error);
              },
              'complete': function() {
                  $('#loading').css("display","none");
              }
           });
        }
   });
   @endcan
      
    @can('delete',App\Driver::class)
      function confirmDelete(delUrl) {
        console.log(delUrl);
              if (confirm("Are you sure you want to delete this driver?")) {
                  document.location = delUrl;
               }
          }
    @endcan

    @if (Session::has('msg'))
        window.setTimeout(function () { 
         $(".alert-row").fadeOut('slow') }, 1500); 
    @endif
   </script>
@endsection
