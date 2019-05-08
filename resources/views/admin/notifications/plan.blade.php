@extends('admin.layouts.app')
@section('title', 'Plane Information')
@section('breadcrumb')
      <h1>
        Plan Information {{-- <span><a href="{{ route('plan/edit' , ['id' => encrypt($plan->id)]) }}" data-toggle="tooltip" class="btn btn-flat btn-xs btn-primary" title="Edit Plan"  }}"><i class="fa fa-edit"></i></a>
                      <a href="javascript:confirmDelete('{{ route('plan/destroy' , ['id' => encrypt($plan->id)]) }}')" data-toggle="tooltip" class="btn btn-flat btn-xs btn-danger" title="Delete Driver"><i class="fa fa-trash"></i></a>
                          </span> --}}
       {{--     <span class="dropdown">
              <i type="button" class="fa fa-gear" data-toggle="dropdown"></i>
            <div class="dropdown-menu">
              <a class="dropdown-item" href="{{ route('plan/edit' , ['id' => encrypt($plan->id)]) }}">Edit</a>
              <a class="dropdown-item" href="javascript:confirmDelete('{{ route('plan/destroy' , ['id' => encrypt($plan->id)]) }}')" >Delete</a>
            </div>
           </span> --}}
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li><a href="{{route('plan/plans') }}">Plans</a></li>
        <li class="active">Plan Information</li>
      </ol>
@endsection
@section('content')   
  
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
        <!-- Default box -->
        <div class="box">
          <div class="box-body">
                <div class="col-md-6">
  		          <ul class="list-group list-group-unbordered">
    		            <li class="list-group-item li-border-top">
    		              <b>Plane Name</b> <a class="pull-right">{{ $plan->plan_name }}</a>
    		            </li>
    		            <li class="list-group-item">
    		              <b>Plan Type</b> <a class="pull-right">
                      @if( $plan->plan_type == 1)
                         Weekly 
                      @elseif( $plan->plan_type == 2)
                         Monthly
                      @elseif( $plan->plan_type == 3)
                         Yearly
                      @endif
                      </a>
    		            </li>
                      <li class="list-group-item">
                      <b>Plan Status</b> 
                      @if( $plan->plan_status == '1')
                        <a class="pull-right" style="color:green">Active</a> 
                      @else
                        <a class="pull-right" style="color:red">Deactive</a>
                      @endif
                    </li>
    		            <li class="list-group-item">
    		              <b>Plan Cost</b> <a class="pull-right">{{ $plan->plan_cost }}</a>
    		            </li>
                    <li class="list-group-item">
                      <b>Plan Created at</b> <a class="pull-right">{{date('d-M-y', strtotime($plan->plan_created_at))}} {{date('h:i A',strtotime($plan->plan_created_at))}}</a>
                    </li>
                    <li class="list-group-item">
                      <b>Plan Updated at</b> <a class="pull-right">{{date('d-M-y', strtotime($plan->plan_updated_at))}} {{date('h:i A',strtotime($plan->plan_updated_at))}}</a>
                    </li>
                     <li class="list-group-item">
                      <b>Active User</b> <a class="pull-right"><b>15000</b></a>
                    </li>
                </ul>
                </div>
                <div class="col-md-6">
                <ul class="list-group list-group-unbordered description">
                  <li class="list-group-item li-border-top"><b>Plan Discription.</b></li>
                </ul>
                <div class="">
                  <div contenteditable="false">
                      {{ $plan->plan_description }}
                  </div>
                </div>
                </div>
         </div>
        </div>
        <!-- /.box -->
      </div>
   </div>
   </div>
@endsection
@section('css-script')
<style type="text/css">
     @media only screen and (max-width: 768px) {
    .description {
        margin-top: -18px;
     }
    }
</style>
@endsection
@section('js-script')
 <script type="text/javascript">
  function confirmDelete(delUrl) {
      console.log(delUrl);
            if (confirm("Are you sure you want to delete this plan?")) {
                document.location = delUrl;
             }
        }
        
    @if (Session::has('msg'))
          window.setTimeout(function () { 
           $(".alert-row").fadeOut('slow') }, 1500); 
    @endif
 </script>
@endsection


