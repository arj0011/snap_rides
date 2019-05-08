@extends('admin.layouts.app')
@section('title', 'Offers')
@section('breadcrumb')
      <h1>
       Offers
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li class="active">Offers</li>
      </ol>
@endsection
@section('content')   
  <div id="page-wrapper">
     <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <!-- /.box-header -->
              <div class="box-body">
            <form class="form-inline" action="<?php echo url('/') ?>/offers/promo" method="post" autocomplete="off">
              {{ csrf_field() }}
              <div class="input-group input-group-xs">
                
                  <input type="hidden" name="offer_type" value="promocode">
                  <input name="code" id="code" type="text" class="form-control" value="@if(isset($q)){{ $q }} @endif" id="textInput" placeholder="Offer Code">
                  </div>
                <div class="form-group">
                     <button type="submit" class="btn btn-flat margin" data-toggle="tooltip" title="Search">Search</button>
                    <a href="" type="reset" class="btn btn-default btn-flat" data-toggle="tooltip" title="Reset">Reset</a>
                </div>
            </form>
         
              </div>
              <!-- /.box-body -->
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
            <h3 class="box-title"><b>Offers</b>
            </h3>
            <div class="box-tools">
            <a href="<?php echo url('/') ?>/addOffer" type="submit" class="btn btn-primary btn-flat" data-toggle="tooltip" title="" data-original-title="Add New"><i class="fa fa-plus"></i>&nbsp;Add New Offer</a> 
            <!--<a href="<?php //echo url('/') ?>/driverInvitation" type="submit" class="btn btn-primary btn-flat" data-toggle="tooltip" title="" data-original-title="Add New"><i class="fa fa-plus"></i>&nbsp;Driver Invitation</a>--> 
            </div>
          </div>                      
                   
          
          <!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                  <table class="table table-hover">
                    <thead style="background:#F7F7F7;">
                    <tr>
                      <th>Sr.</th>
                      <th nowrap>Code</th>
                      <th nowrap>Offer Type</th>
                      <th nowrap>Percent(%)</th>
                      <th nowrap>Amount(R)</th>
                      <th nowrap>Start Date</th>
                      <th nowrap>End Date</th>
                      {{-- <th nowrap>Offer Days</th> --}}
                      <th nowrap>Actions</th>
                  
                    </tr>
                    </thead>
                    <tbody>
                       
                      <?php 
                      $i=1;
                      foreach ($data as $key => $value) {  ?>
                        <tr>
                          <td><?php echo $i; ?></td>
                          <td>{{  $value->offer_code }}</td>
                          <td>{{  $value->offerType }}</td>
                          <td>{{  $value->percent }}</td>
                          <td>{{  $value->amount }}</td>
                          <td>{{  date('Y-m-d',strtotime($value->start_date)) }}</td>
                          <td>{{  date('Y-m-d',strtotime($value->end_date)) }}</td>  
                          {{-- <td>{{  $value->plan_extends_for_days }} days</td>   --}}
                          <td><?php if($value->status=='1'){  ?> <button class="btn btn-success btn-xs btn-flat" data-status="0" data-id="{{ $value->id }}"> Active</button> <a href="<?php echo url('/'); ?>/sendPromocode/<?php echo encrypt($value->id); ?>" class="btn btn-warning btn-xs btn-flat">Send</a> <?php }else{?><button class="btn btn-danger btn-xs btn-flat" data-status="1" data-id="{{ $value->id }}">Deactive</button><?php } ?> 
                          <a ><a href="{{ route('editOffers',['id' => encrypt($value->id)]) }}"><i class="fa fa-edit"></i> </a></td>
                        </tr>
                       
                      <?php 
                          $i++;
                          } 
                      ?>
                    </tbody>
                  </table>
                </div>
                 <div clasw="col-md-6">
                     {{ $data->appends(request()->query())->links()  }}
                    </div> 
          <!-- /.box-body -->
        </div>
          
        <!-- /.box -->
      </div><!-- col-xs-12 -->
       <div clasw="col-md-6">
            
     </div>
   </div>
 </div>
@endsection
@section('css-script')
   <link href="{{ asset('Admin/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
@endsection
@section('js-script')
    <script src="{{ asset('Admin/js/bootstrap-datepicker.min.js') }}"></script>
    <script>
      $('table tr td  button').click(function(event){
      let click = this;
      let x = confirm("Do you want to change status?");
        if(x){
            $.ajax({
             type: 'put',
             url : '{{ route('set-Offerstatus') }}',
             data : {
                'id' :  $(this).attr('data-id'),
                'status' :  $(this).attr('data-status'),
                '_token': '{{ csrf_token() }}'
             },
             success: function(response)
             {  
                
                    if(response=="Active"){
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
                 location.reload();   
             }
           });
        }
   }); 
    </script>    
@endsection
