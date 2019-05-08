@extends('admin.layouts.app')
@section('title', 'Vehicles Categories')
@section('breadcrumb')
      <h1>
       Vehicles Categories
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li class="active">Vehicle Categories</li>
      </ol>
@endsection
@section('content')   
 <div id="page-wrapper">
 <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <!-- /.box-header -->
               <form class="form-inline" action="{{  route('category/search') }}" method="get">
              {{ csrf_field() }}
              <div class="box-body">
                   <div class="input-group input-group-xs">
                <div class="input-group-btn">
                  <select name="p" class="form-control">
                    <option value="">All</option>
                    <option  @isset ($p) @if($p == 'type') {{ "selected" }} @endif @endisset value="type">Type</option>
                    <option  @isset ($p) @if($p == 'capacity') {{ "selected" }} @endif @endisset value="capacity">Capacity</option>
                     <option @isset ($p) @if($p == 'per_km_charge') {{ "selected" }} @endif @endisset value="per_km_charge">Per KM Charge</option>
                  </select>
                </div>
                <!-- /btn-group -->
                <input name="q" type="text" class="form-control" value="@if(isset($q)){{ $q }} @endif" id="textInput">
              </div>
                <div class="form-group">
                     <button type="submit" class="btn btn-flat margin" data-toggle="tooltip" title="Search">Search</button>
                      <a href="{{ route('category/categories') }}" type="reset" class="btn btn-default btn-flat" data-toggle="tooltip" title="Reset">Reset</a>
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
              <h3 class="box-title">Vehicle Categories</h3>
              <div class="box-tools">
                @can('create', App\Category::class)
                    <a href="{{ route('category/create') }}" type="submit" class="btn btn-primary btn-flat" data-toggle="tooltip" title="Add New"><i class="fa fa-plus"></i>&nbsp;Add</a>
                @endcan
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tr>
                  <th>Sr.</th>
                  <th>Type Name</th>
                  <th>Person Capacity</th>
                  {{-- <th>Base Fare</th> --}}
                  <th>Per Kilometer Charges</th>
                  @can('setStatus', App\Category::class)
                   <th>Status</th>
                  @endcan

                  @if ( Auth::user()->can('update' , App\Category::class) ||
                        Auth::user()->can('delete' , App\Category::class) )
                  <th>Action</th>
                  @endif
                </tr>
                  @forelse ($categories as $category)
                    <tr>
                      <td>{{  $i++  }}</td>
                      <td>{{ $category->vehicle_type }}</td>
                      <td>{{ $category->vehicle_person_capacity }}</td>
                      {{-- <td>{{ $category->vehicle_basefare }}</td> --}}
                      <td>{{ $category->per_km_charges }}</td>
                        @can('setStatus', App\Category::class)
                          <td>
                          @if($category->is_active == 1)
                               <button class="btn btn-success btn-xs btn-flat" data-id="{{ encrypt($category->id) }}"> Active&emsp; </button> 
                          @else
                              <button class="btn btn-danger btn-xs btn-flat" data-id="{{ encrypt($category->id) }}">Deactive</button>
                          @endif</td>
                        @endcan
                      
                    @if ( Auth::user()->can('update' , App\Category::class) ||
                          Auth::user()->can('delete' , App\Category::class) )
                      <td>
                        @can('update', App\Category::class)
                         <a data-toggle="tooltip" class="btn btn-flat btn-xs btn-primary" title="Edit Vehicle" href="{{ route('category/edit',['id' => encrypt($category->id)]) }}"><i class="fa fa-edit"></i></a>
                        @endcan
                        @can('delete', App\Category::class)
                         <a data-toggle="tooltip" data-toggle="modal" data-target="#delete-model" class="btn btn-flat btn-xs btn-danger" title="Delete Vehicle" href="javascript:confirmDelete('{{ route('category/destroy',['id' => encrypt($category->id)]) }}')"><i class="fa fa-trash"></i></a>
                         @endcan
                      </td>
                      @endif
                    </tr>
                     @empty
                        <tr>
                          <td colspan="6">
                             No any vehicle category record available
                          </td>
                        </tr>
                     @endforelse
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
      <div clasw="col-md-6">
          {{ $categories->appends(request()->query())->links()  }}
      </div>
      </div>
      </div>
@endsection
@section('js-script')
 <script type="text/javascript">

   $('#searchReset').click(function(){
    $('#textInput').attr('value' , '');
  });
  
    @can('delete', App\Category::class)
      function confirmDelete(delUrl) {
        console.log(delUrl);
              if (confirm("Are you sure you want to delete this category?")) {
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
               type: 'put',
               url : '{{ route('category/set-status') }}',
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


