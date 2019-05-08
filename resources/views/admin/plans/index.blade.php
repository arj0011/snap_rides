@extends('admin.layouts.app')
@section('title', 'Plan')
@section('breadcrumb')
      <h1>
        Plan
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li class="active">Plans</li>
      </ol>
@endsection
@section('content')   
 <div id="page-wrapper">
 <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <!-- /.box-header -->
               <form class="form-inline" action="{{  route('plan/search') }}" method="get">
              {{ csrf_field() }}
              <div class="box-body">
                   <div class="input-group input-group-xs">
                <!-- /btn-group -->

                <input name="q" type="text" class="form-control" value="@if(isset($q)){{ $q }} @endif" placeholder="Search by name" id="textInput">
              </div>
                <div class="form-group">
                     <button type="submit" class="btn btn-flat margin" data-toggle="tooltip" title="Search">Search</button>
                     <a href="{{ route('plan/plans') }}" type="reset" class="btn btn-default btn-flat" data-toggle="tooltip" title="Reset">Reset</a>
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
              <h3 class="box-title">Vehicle plans</h3>
              <div class="box-tools">
                @can( 'create' , App\Plan::class)
                    <a href="{{ route('plan/create') }}" type="submit" class="btn btn-primary btn-flat" data-toggle="tooltip" title="Add New"><i class="fa fa-plus"></i>&nbsp;Add</a>
                @endcan
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tr>
                  <th>Sr.</th>
                  <th>Name</th>
                  <th>Description</th>
                  <th>Plan Type</th>
                  <th>Plan Cost</th>
                  @can( 'setStatus' , App\Plan::class)
                     <th>Status</th>
                  @endcan
                  @if ( Auth::user()->can('update' , App\Plan::class) ||
                        Auth::user()->can('delete' , App\Plan::class) || 
                        Auth::user()->can( 'view' , App\Plan::class) )
                  <th>Action</th>
                  @endif
                </tr>
                  @forelse ($plans as $plan)
                    <tr>
                      <td>{{  $i++  }}</td>
                      <td>{{ $plan->plan_name }}</td>
                      <td>{{ $plan->plan_description }}...</td>
                      <td>{{ $plan->plan_type }}</td>
                      <td>{{ $plan->plan_cost }}</td>
                    @can( 'setStatus' , App\Plan::class)
                      <td>
                      @if($plan->is_active == 1)
                            <button class="btn btn-success btn-xs btn-flat" data-id="{{ encrypt($plan->id) }}"> Active&emsp; </button> 
                      @else
                           <button class="btn btn-danger btn-xs btn-flat" data-id="{{ encrypt($plan->id) }}">Deactive</button>
                      @endif</td>
                    @endcan
                    @if ( Auth::user()->can('update' , App\Plan::class) ||
                        Auth::user()->can('delete' , App\Plan::class) || 
                        Auth::user()->can( 'view' , App\Plan::class) )
                      <td>
                    @can( 'view' , App\Plan::class)
                        <a href="{{ route('plan/show', ['id' => encrypt($plan->id)]) }}" data-toggle="tooltip" class="btn btn-flat btn-xs btn-info" title="Plan Info" "><i class="fa fa-info-circle"></i></a>
                    @endcan
                    @can( 'update' , App\Plan::class)
                        <a data-toggle="tooltip" class="btn btn-flat btn-xs btn-primary" title="Edit Plan" href="{{ route('plan/edit',['id' => encrypt($plan->id)]) }}"><i class="fa fa-edit"></i></a>
                    @endcan
                    @can( 'delete' , App\Plan::class)
                     <?php if($plan->id==18){  ?>
                        <a data-toggle="tooltip" data-toggle="modal" data-target="#delete-model" class="btn btn-flat btn-xs btn-danger" title="Delete Plan" href="javascript:confirmDelete('{{ route('plan/destroy',['id' => encrypt($plan->id)]) }}')"><i class="fa fa-trash"></i></a>
                      <?php } ?>
                    @endcan
                      </td>
                     @endcan

                    </tr>
                     @empty
                        <tr>
                          <td colspan="6">
                             No any  plan record available
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
          {{ $plans->appends(request()->query())->links()  }}
      </div>
      </div>
    </div>
@endsection
@section('js-script')
 <script type="text/javascript">

  $('#searchReset').click(function(){
    $('#textInput').attr('value' , '');
  });
  
   @can( 'delete' , App\Plan::class)
    function confirmDelete(delUrl) {
      console.log(delUrl);
            if (confirm("Are you sure you want to delete this plan?")) {
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
        let x = confirm("Do you realy want to change status?");
          if(x){
              $.ajax({
               type: "put",
               url : "{{ route('plan/set-status') }}",
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