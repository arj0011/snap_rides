@extends('admin.layouts.app')
@section('title', 'Roles')
@section('breadcrumb')
      <h1>
       Roles
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li class="active">Roles</li>
      </ol>
@endsection
@section('content')   
<div id="page-wrapper">
 <div class="row">
 
        <div class="col-xs-12">
          <div class="box">
            <!-- /.box-header -->
               <form class="form-inline" action="{{  route('role/search') }}" method="get">
              {{ csrf_field() }}
              <div class="box-body">
                   <div class="input-group input-group-xs">
                <!-- /btn-group -->
                <input name="q" type="text" class="form-control" value="@if(isset($q)){{ $q }} @endif" id="textInput">
              </div>
                <div class="form-group">
                     <button type="submit" class="btn btn-flat margin" data-toggle="tooltip" title="Search">Search</button>
                    <a href="{{ route('role/roles') }}" type="reset" class="btn btn-default btn-flat" data-toggle="tooltip" title="Reset">Reset</a>
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
              <h3 class="box-title">Roles</h3>
              <div class="box-tools">
                @can('create', App\Role::class)
                    <a href="{{ route('role/create') }}" type="submit" class="btn btn-primary btn-flat" data-toggle="tooltip" title="Add New"><i class="fa fa-plus"></i>&nbsp;Add</a>
                @endcan
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tr>
                  <th>Sr.</th>
                  <th>Role</th>
                @can('setStatus', App\Role::class)
                  <th>Status</th>
                @endcan
                    @if ( Auth::user()->can('update' , App\Role::class) ||
                          Auth::user()->can('delete' , App\Role::class)
                    )
                  <th>Action</th>
                @endif
                </tr>
                  @forelse ($roles as $role)
                    <tr>
                      <td>{{  $i++  }}</td>
                      <td>{{ $role->role_name }}</td>
                    @can('setStatus', App\Role::class)
                        <td class="status">
                        @if($role->role_status == 1)
                             <button class="btn btn-success btn-xs btn-flat" data-id="{{ encrypt($role->id) }}"> Active&emsp; </button> 
                        @else
                              <button class="btn btn-danger btn-xs btn-flat" data-id="{{ encrypt($role->id) }}">Deactive</button>
                        @endif</td>
                    @endcan
                    @if( Auth::user()->can('update' , App\Role::class) || 
                         Auth::user()->can('delete' , App\Role::class)
                     )
                        <td>
                        @can('update', App\Role::class)
                          <a data-toggle="tooltip" class="btn btn-flat btn-xs btn-primary" title="Edit Role" href="{{ route('role/edit',['id' => encrypt($role->id)]) }}"><i class="fa fa-edit"></i></a>
                        @endcan
                        @if ($role->id != 1 )
                          @can('delete', App\Role::class)
                            <a data-toggle="tooltip" data-toggle="modal" data-target="#delete-model" class="btn btn-flat btn-xs btn-danger" title="Delete Role" href="javascript:confirmDelete('{{ route('role/destroy',['id' => encrypt($role->id)]) }}')"><i class="fa fa-trash"></i></a>
                        @endcan
                        @endif
                        </td>
                      @endif
                    </tr>
                     @empty
                        <tr>
                          <td colspan="6">
                             No any vehicle role record available
                          </td>
                        </tr>
                     @endforelse
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
      </div>
      <div clasw="col-md-6">
          {{ $roles->appends(request()->query())->links()  }}
      </div>
      </div>
@endsection
@section('js-script')
{{-- Custome javascript sricpt --}}
<script type="text/javascript">
  
  // reset search form
   $('#searchReset').click(function(){
    $('#textInput').attr('value' , '');
  });
  
  @can('delete', App\Role::class)
  // confirmation to delete role 
  function confirmDelete(delUrl) {
    console.log(delUrl);
          if (confirm("Are you sure you want to delete this Role?")) {
              document.location = delUrl;
      }
  }
  @endcan


// hide success and failure message to response
  @if (Session::has('msg'))
      window.setTimeout(function () { 
       $(".alert-row").fadeOut('slow') }, 1500); 
  @endif
 
  @can('setStatus', App\Role::class)
 // set active and deactive status
  $('table tr td  button').click(function(event){
      let click = this;
      let x = confirm("Do you want to change status?");
        if(x){
            $.ajax({
             type: 'put',
             url : '{{ route('role/set-status') }}',
             data : {
                'id' :  $(this).attr('data-id'),
                '_token': '{{ csrf_token() }}'
             },
             success: function(response)
             {  
                let data = JSON.parse(response);
                    if(data.status){
                      $(click).removeClass('btn-danger');
                      $(click).addClass('btn-success');
                      $(click).html('Active&emsp; ');
                    }else{
                      $(click).removeClass('btn-success');
                      $(click).addClass('btn-danger');
                      $(click).text('Deactive');
                    }
             }
           });
        }
   });
  @endcan

 </script>
@endsection


