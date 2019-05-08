@extends('admin.layouts.app')
@section('title', 'Vehicles')
@section('breadcrumb')
      <h1>
       Vehicles
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li class="active">Vehicles</li>
      </ol>
@endsection
@section('content')   
 <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <!-- /.box-header -->
               <form class="form-inline" action="{{  route('vehicle/search') }}" method="get">
              {{ csrf_field() }}
              <div class="box-body">
                   <div class="input-group input-group-xs">
                <div class="input-group-btn">
                  <select name="p" class="form-control">
                    <option value="">All</option>
                    <option  @isset ($action) @if($action == 'dri') {{ "selected" }} @endif @endisset value="dri">Driver</option>
                    <option  @isset ($action) @if($action == 'reg') {{ "selected" }} @endif @endisset value="reg">Register No.</option>
                     <option @isset ($action) @if($action == 'ins') {{ "selected" }} @endif @endisset value="ins">Insurence No.</option>
                  </select>
                </div>
                <!-- /btn-group -->
                <input name="q" type="text" class="form-control" value="@if(isset($string)){{ $string }} @endif" id="textInput">
              </div>
                <div class="form-group">
                     <button type="submit" class="btn btn-flat margin" data-toggle="tooltip" title="Search">Search</button>
                     <button type="reset" class="btn btn-default btn-flat" data-toggle="tooltip" title="Reset" id="searchReset">Reset</button>
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
        <div class="col-xs-12">
          <div class="box">
          <div class="box-header">
              <h3 class="box-title">Vehicles</h3>
              <div class="box-tools">
                    <a href="{{ route('vehicle/create') }}" type="submit" class="btn btn-primary btn-flat" data-toggle="tooltip" title="Add New"><i class="fa fa-plus"></i>&nbsp;Add</a>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tr>
                  <th>Sr.</th>
                  <th>Type</th>
                  <th>Driver</th>
                  <th>Base Fare(Par/km)</th>
                  <th>Registration No.</th>
                  <th>Insurance No.</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
                  @if (!empty($vehicles) ||  $vehicles != Null)
                  @forelse ($vehicles as $vehicle)
                    <tr>
                      <td>{{  $i++  }}</td>
                      <td>{{ $vehicle->vehicle_category }}</td>
                      <td>
                        <a href="{{ route('driver/show' , ['id' => encrypt($vehicle->driver_id)]) }}">{{ $vehicle->driver_name }}</a>
                      </td>
                      <td>{{ $vehicle->basefare }}</td>
                      <td>{{ $vehicle->registration_number }}</td>
                      <td>{{ $vehicle->insurance_number }}</td>
                      <td><span class="label label-success">@if ($vehicle->is_active == 1)
                           Active
                           @else
                           Deactive
                           @endif
                        </span></td>
                      <td>
                        <a data-toggle="tooltip" class="btn btn-flat btn-xs btn-info" title="Vehicle Info" href="{{ route('vehicle/show',['id' => encrypt($vehicle->id)]) }}"><i class="fa fa-info-circle"></i></a>
                        <a data-toggle="tooltip" class="btn btn-flat btn-xs btn-primary" title="Edit Vehicle" href="{{ route('vehicle/edit',['id' => encrypt($vehicle->id)]) }}"><i class="fa fa-edit"></i></a>
                        <a  data-toggle="tooltip" data-toggle="modal" data-target="#delete-model" class="btn btn-flat btn-xs btn-danger" title="Delete Vehicle" href="javascript:confirmDelete('{{ route('vehicle/destroy',['id' => encrypt($vehicle->id)]) }}')"><i class="fa fa-trash"></i></a>
                      </td>
                    </tr>
                     @empty
                        <tr>
                          <td colspan="6">
                             No any Vehicle record available
                          </td>
                        </tr>
                     @endforelse
                     @endif
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
      </div>
      </div>
@endsection
@section('js-script')
  <script type="text/javascript">
    function confirmDelete(delUrl) {
        console.log(delUrl);
              if (confirm("Are you sure you want to delete this vehicle?")) {
                  document.location = delUrl;
               }
          }

      @if (Session::has('msg'))
          window.setTimeout(function () { 
           $(".alert-row").fadeOut('slow') }, 1500); 
      @endif

      $('#searchReset').click(function(){
       $('#textInput').attr('value' , '');
     });
  

   </script>
@endsection


