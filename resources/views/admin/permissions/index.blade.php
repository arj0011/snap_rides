@extends('admin.layouts.app')
@section('title', 'permissions')
@section('breadcrumb')
      <h1>
       Permissions
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li class="active">Permissions</li>
      </ol>
@endsection
@section('content')   
 <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <!-- /.box-header -->
               <form class="form-inline" action="{{  route('permission/search') }}" method="get">
              {{ csrf_field() }}
              <div class="box-body">
                   <div class="input-group input-group-xs">
                <!-- /btn-group -->
                <input name="q" type="text" class="form-control" value="@if(isset($string)){{ $string }} @endif" id="textInput">
              </div>
                <div class="form-group">
                     <button type="submit" class="btn btn-flat margin" data-toggle="tooltip" title="Search">Search</button>
                     <button type="reset" class="btn btn-default btn-flat" data-toggle="tooltip" title="Reset"  id="searchReset">Reset</button>
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
              <h3 class="box-title">Permissions
                <form active="{{ route('booking/bookings') }}" method="get" role="form" style="display: inline-block;margin-top: -5px;margin-bottom: -18px;">
                <div class="form-group">
                  <select class="form-control" name="filter" onchange='if(this.value != 0) { this.form.submit(); }'>
                    <option value="">Filter</option>
                    @forelse($modules as $module)
                      <option @isset ($filter) @if($filter == $module->id) {{ "selected" }} @endif @endisset value="{{ $module->id }}">{{ $module->name }}</option>
                    @empty
                     <option value="">No any record available</option>
                    @endforelse
                  </select>
                </div>
               </form>
              </h3>
              <div class="box-tools">
               {{--  @can('create', App\Permission::class) --}}
                    <a href="{{ route('permission/create') }}" type="submit" class="btn btn-primary btn-flat" data-toggle="tooltip" title="Add New"><i class="fa fa-plus"></i>&nbsp;Add</a>
               {{--  @endcan --}}
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tr>
                  <th>Sr.</th>
                  <th>Permission</th>
                  <th>Permission For</th>
                @can('setStatus', App\Permission::class)
                  <th>Status</th>
                @endcan
                  <th>Action</th>
                </tr>
                  @forelse ($permissions as $permission)
                    <tr>
                      <td>{{  $i++  }}</td>
                      <td>{{  ucwords($permission->permission_name) }}</td>
                      <td>{{  ucwords($permission->permission_for) }}</td>
                  {{--   @can('setStatus', App\Permission::class) --}}
                      <td class="status">
                      @if($permission->permission_status == 1)
                           <button class="btn btn-success btn-xs btn-flat" data-id="{{ encrypt($permission->id) }}"> Active&emsp; </button> 
                      @else
                            <button class="btn btn-danger btn-xs btn-flat" data-id="{{ encrypt($permission->id) }}">Deactive</button>
                      @endif</td>
                  {{--     @endcan --}}
                      <td>
                      {{-- @can('update', App\Permission::class) --}}
                        <a data-toggle="tooltip" class="btn btn-flat btn-xs btn-primary" title="Edit permission" href="{{ route('permission/edit',['id' => encrypt($permission->id)]) }}"><i class="fa fa-edit"></i></a>
                   {{--    @endcan --}}
                      @can('delete', App\Permission::class)
                        <a data-toggle="tooltip" data-toggle="modal" data-target="#delete-model" class="btn btn-flat btn-xs btn-danger" title="Delete permission" href="javascript:confirmDelete('{{ route('permission/destroy',['id' => encrypt($permission->id)]) }}')"><i class="fa fa-trash"></i></a>
                      @endcan
                      </td>
                    </tr>
                     @empty
                        <tr>
                          <td colspan="6">
                             No any permission record available
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
          {{ $permissions->appends(request()->query())->links()  }}
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
  

   @can('delete', App\Permission::class)
    // confirmation to delete permission 
    function confirmDelete(delUrl) {
      console.log(delUrl);
            if (confirm("Are you sure you want to delete this permission?")) {
                document.location = delUrl;
        }
    }
  @endcan


// hide success and failure message to response
  @if (Session::has('msg'))
      window.setTimeout(function () { 
       $(".alert-row").fadeOut('slow') }, 1500); 
  @endif
 
  @can('delete', App\Permission::class)
 // set active and deactive status
  $('table tr td  button').click(function(event){
      let click = this;
      let x = confirm("Do you realy want to change status?");
        if(x){
            $.ajax({
             type: "put",
             url : "{{ route('permission/set-status') }}",
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


