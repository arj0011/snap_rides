@extends('admin.layouts.app')
@section('title', 'Riders')
@section('breadcrumb')
      <h1>
       Riders
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li class="active">Riders</li>
      </ol>
@endsection
@section('content')   
 <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <!-- /.box-header -->
            <form class="form-inline" action="{{  route('rider/search') }}" method="get">
              {{ csrf_field() }}
              <div class="box-body">
                   <div class="input-group input-group-xs">
                <div class="input-group-btn">
                   <select name="p" class="form-control">
                    <option value="">All</option>
                    <option  @isset ($p) @if($p == 'name') {{ "selected" }} @endif @endisset value="name">Name</option>
                    <option  @isset ($p) @if($p == 'mobile') {{ "selected" }} @endif @endisset value="mobile">Mobile</option>
                    {{-- <option  @isset ($p) @if($p == 'email') {{ "selected" }} @endif @endisset value="email">Email</option>
                    <option  @isset ($p) @if($p == 'city') {{ "selected" }} @endif @endisset value="city">City</option> --}}
                  </select>
                </div>
                <!-- /btn-group -->
                <input name="q" type="text" class="form-control" value="@if(isset($q)){{ $q }} @endif" id="textInput">
              </div>
                <div class="form-group">
                     <button type="submit" class="btn btn-flat margin" data-toggle="tooltip" title="Search">Search</button>
                  <a href="{{ route('rider/riders') }}" type="reset" class="btn btn-default btn-flat" data-toggle="tooltip" title="Reset">Reset</a>
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
              <h3 class="box-title"><b>Riders</b></h3>
              <div class="box-tools">
                
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <thead style="background:#F7F7F7;">
                <tr>
                  <th>Sr.</th>
                  <th>Name</th>
                  <th>Mobile</th>
                  {{-- <th>Email</th> --}}
                  {{-- <th>City</th> --}}
                 @can('setStatus', App\Customer::class)
                   <th>Status</th>
                 @endcan
                 
                @if ( Auth::user()->can('view' , App\Customer::class) || Auth::user()->can('destroy' , App\Customer::class))
                  <th>Action</th>
                @endif
                </tr>
                </thead>
                <tbody>
                  @forelse ($riders as $rider)
                    <tr>
                      <td>{{  $i++  }}</td>
                      <td>{{ $rider->name }}</td>
                      <td>{{ $rider->mobile }}</td>
                      {{-- <td>{{ $rider->email }}</td> --}}
                      {{-- <td>{{ $rider->city_name }}</td> --}}
                    @can( 'setStatus' , App\Customer::class)
                      <td>
                      @if($rider->is_active == 1)
                            <button class="btn btn-success btn-xs btn-flat" data-id="{{ encrypt($rider->id) }}"> Active&emsp; </button> 
                      @else
                           <button class="btn btn-danger btn-xs btn-flat" data-id="{{ encrypt($rider->id) }}">Deactive</button>
                      @endif</td>
                    @endcan
                      
                    @if(Auth::user()->can('view' , App\Customer::class) || Auth::user()->can('destroy' , App\Customer::class))
                    <td>
                    @can('view', App\Customer::class)
                      <a href="{{  route('rider/show',['id' => encrypt($rider->id) ]) }}" class="btn  btn-xs btn-info btn-flat"><i class="fa fa-info-circle"></i></a>
                    @endcan
                    @can('destroy', App\Customer::class)
                      <a href="javascript:confirmDelete('{{ route('rider/destroy',['id' => encrypt($rider->id) ]) }}')" class="btn  btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></a>
                    @endcan
                    </td>
                    @endif
                    </tr>
                    @empty
                    <tr>
                      <td colspan="6">
                         No any trip record available
                      </td>
                    </tr>
                 @endforelse
                </tbody>
              </table>
                <div clasw="col-md-6">
            {{ $riders->appends(request()->query())->links()  }}
     </div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
      </div>
@endsection
@section('js-script')
  <script type="text/javascript">
     $('#searchReset').click(function(){
      $('#textInput').attr('value' , '');
    });


    // set active and deactive status
   @can( 'setStatus' , App\Customer::class)
    $('table tr td  button').click(function(event){
        let click = this;
        let x = confirm("Do you want to change status?");
          if(x){
              $.ajax({
               type: "put",
               url : "{{ route('rider/set-status') }}",
               data : {
                  'id' :  $(this).attr('data-id'),
                  '_token': '{{ csrf_token() }}'
               },
               success: function(response)
               {  
                  let data = JSON.parse(response);
                      if(data.status){
                         alert('successfully changed status');
                        $(click).removeClass('btn-danger');
                        $(click).addClass('btn-success');
                        $(click).html('Active&emsp; ');
                      }else{
                         alert('successfully changed status');
                        $(click).removeClass('btn-success');
                        $(click).addClass('btn-danger');
                        $(click).text('Deactive');
                      }
               }
             });
          }
     });
  @endcan

  @can('destroy',App\Customer::class)
      function confirmDelete(delUrl) {
        console.log(delUrl);
              if (confirm("Are you sure you want to delete this rider?")) {
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


