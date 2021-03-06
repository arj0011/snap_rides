@extends('admin.layouts.app')
@section('title', 'Dispatchers')
@section('breadcrumb')
      <h1>
       Dispatcher Users
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li class="active">Dispatcher Users</li>
      </ol>
@endsection
@section('content')   
 <div id="page-wrapper">
 <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <!-- /.box-header -->
               <form class="form-inline" action="{{  route('user/search') }}" method="post">
              {{ csrf_field() }}
              <div class="box-body">
                   <div class="input-group input-group-xs">
                <div class="input-group-btn">
                  <select name="p" class="form-control">
                    <option value="">All</option>
                    <option  @isset ($p) @if($p == 'name') {{ "selected" }} @endif @endisset value="name">Name</option>
                    <option  @isset ($p) @if($p == 'mobile') {{ "selected" }} @endif @endisset value="mobile">Mobile</option>
                     <option @isset ($p) @if($p == 'email') {{ "selected" }} @endif @endisset value="email">Email</option>
                  </select>
                </div>
                <!-- /btn-group -->
                <input name="q" type="text" class="form-control" value="@if(isset($q)){{ $q }} @endif" id="textInput">
              </div>
                <div class="form-group">
                     <button type="submit" class="btn btn-flat margin" data-toggle="tooltip" title="Search">Search</button>
                     <a href="{{ route('user/users') }}" type="reset" class="btn btn-default btn-flat" data-toggle="tooltip" title="Reset">Reset</a>
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
              <h3 class="box-title">Dispatcher Users</h3>
              <div class="box-tools">

               @can( 'create' , App\User::class)
                  <a href="{{ route('user/create') }}" type="submit" class="btn btn-primary btn-flat" data-toggle="tooltip" title="Add New"><i class="fa fa-plus"></i>&nbsp;Add</a>
                @endcan
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tr>
                  <th>Sr.</th>
                  <th>Role</th>
                  <th>Username</th>
                  <th>Name</th>
                  <th>Mobile</th>
                  <th>Email</th>
                  @can( 'setStatus' , App\User::class)
                   <th>Status</th>
                  @endcan
                  @if( Auth::user()->can('view' , App\Role::class) || 
                       Auth::user()->can('delete' , App\Role::class)  ||
                       Auth::user()->can('update' , App\Role::class) )
                  <th>Action</th>
                  @endif
                </tr>
                  @forelse ($users as $user)
                    <tr>
                      <td>{{  $i++  }}</td>
                      <td>@if ($user->role_name)
                        {{ $user->role_name }}@else
                        {{ 'Role not Assign' }}
                      @endif</td>
                      <td>{{ $user->username }}</td>
                      <td>{{ $user->name }}</td>
                      <td>{{ $user->mobile }}</td>
                      <td>{{ $user->email }}</td>
                      @can( 'setStatus' , App\User::class)
                        <td class="status">
                          @if($user->user_status == 1)
                               <button class="btn btn-success btn-xs btn-flat" data-id="{{ encrypt($user->id) }}"> Active&emsp; </button> 
                          @else
                                <button class="btn btn-danger btn-xs btn-flat" data-id="{{ encrypt($user->id) }}">Deactive</button>
                          @endif
                          </td>
                        @endcan
                      @if( Auth::user()->can('view' , App\Role::class) || 
                       Auth::user()->can('delete' , App\Role::class)  ||
                       Auth::user()->can('update' , App\Role::class) )
                        <td>
                        @can( 'view' , App\User::class)
                          <a href="{{ route('user/show', ['id' => encrypt($user->id)]) }}" data-toggle="tooltip" class="btn btn-flat btn-xs btn-info" title="Dispatcher Info" "><i class="fa fa-info-circle"></i></a>
                        @endcan
                        @can( 'update' , App\User::class)
                          <a data-toggle="tooltip" class="btn btn-flat btn-xs btn-primary" title="Edit Dispatcher" href="{{ route('user/edit',['id' => encrypt($user->id)]) }}"><i class="fa fa-edit"></i></a>
                          @endcan
                        @can( 'delete' , App\User::class)
                          <a data-toggle="tooltip" data-toggle="modal" data-target="#delete-model" class="btn btn-flat btn-xs btn-danger" title="Delete Dispatcher" href="javascript:confirmDelete('{{ route('user/destroy',['id' => encrypt($user->id)]) }}')"><i class="fa fa-trash"></i></a>
                          @endcan
                        </td>
                      @endif
                    </tr>
                     @empty
                        <tr>
                          <td colspan="6">
                             No any user record available
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
          {{ $users->appends(request()->query())->links()  }}
      </div>
      </div>
@endsection
@section('js-script')
 <script type="text/javascript">

   $('#searchReset').click(function(){
    $('#textInput').attr('value' , '');
  });

  @can( 'delete' , App\User::class)
    function confirmDelete(delUrl) {
      console.log(delUrl);
            if (confirm("Are you sure you want to delete this dispatcher?")) {
                document.location = delUrl;
        }
    }
  @endcan

  @if (Session::has('msg'))
      window.setTimeout(function () { 
       $(".alert-row").fadeOut('slow') }, 1500); 
  @endif

   // set active and deactive status
   @can( 'setStatus' , App\User::class)
    $('table tr td  button').click(function(event){
        let click = this;
        let x = confirm("Do you want to change status?");
          if(x){
              $.ajax({
               type: "put",
               url : "{{ route('user/set-status') }}",
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


