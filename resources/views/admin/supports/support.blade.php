@extends('admin.layouts.app')
@section('title', ' FAQ  Information')
@section('breadcrumb')
      <h1>
        FAQ Information 
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li><a href="{{route('support/supports') }}">FAQ list</a></li>
        <li class="active">FAQ Information</li>
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
                  {{--  <label>FAQ Type</label><br/>
                   <b>{{ $support->category_name }}</b>
                    <label>FAQ Question</label><br/>
                   <b>{{ $support->question }}</b>
                    <label>FAQ Answer</label><br/>
                   <b>{{ $support->answer }}</b> --}}
                        <ul class="list-group list-group-unbordered">
                    <li class="list-group-item li-border-top">
                      <b>FAQ Type</b><a class="pull-right">{{ $support->category_name }}</a>
                    </li>
                    <li class="list-group-item">
                      <b>Question</b> <a class="pull-right">{{ $support->question }}</a>
                    </li>
                    <li class="list-group-item" >
                      <b>Answer</b><div style="height: 200px;overflow-y: scroll;"><a class="pull-right">{{ $support->answer }}</a></div>
                    </li>
                    <li class="list-group-item">
                      <b>Create Date</b> <a class="pull-right">{{date('d-M-y', strtotime($support->created_at))}} {{date('h:i A',strtotime($support->created_at))}}</a>
                    </li>
                      <li class="list-group-item">
                      <b>Update Date</b> <a class="pull-right">{{date('d-M-y', strtotime($support->created_at))}} {{date('h:i A',strtotime($support->created_at))}}</a>
                    </li>
                </ul>
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
    @if (Session::has('msg'))
          window.setTimeout(function () { 
           $(".alert-row").fadeOut('slow') }, 1500); 
    @endif
 </script>
@endsection


