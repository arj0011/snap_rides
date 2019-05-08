@extends('admin.layouts.app')
@section('title', 'Drivers')
@section('breadcrumb')
      <h1>
       Offers
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li class="active">Invitations</li>
      </ol>
@endsection
@section('content')   
  <div id="page-wrapper">
     <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <!-- /.box-header -->
              <div class="box-body">
            <form class="form-inline" action="<?php echo url('/') ?>/driverInvitation" method="post" autocomplete="off">
              {{ csrf_field() }}
              <div class="input-group input-group-xs">
                <!-- <div class="input-group input-group-xs">
                  <div class="input-group-btn">
                    <select name="offer_type" id="offer_type" class="form-control" id="selectInput">
                      <option value="">Offer Types</option>
                      <option  @isset ($p) @if($p == 'id') {{ "selected" }} @endif @endisset value="id">Promo Code</option>
                      <option  @isset ($p) @if($p == 'driver') {{ "selected" }} @endif @endisset value="driver">Invite Code</option>
                       
                    </select>
                  </div>
                -->
                <!-- /btn-group -->
                  
                   <input name="driver_name" id="code" type="text" class="form-control"  id="textInput" placeholder="Driver Name">
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
            <h3 class="box-title"><b>Driver Invitation</b>
            </h3>
           
          </div>                      
                   
          
          <!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                  <table class="table table-hover">
                    <thead style="background:#F7F7F7;">
                    <tr>
                      <th>Sr.</th>
                      <th nowrap>Name</th>
                      <th nowrap>Email</th>
                      <th nowrap>count</th>
                      <th nowrap>Invite Code</th>
                      <th nowrap>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                       
                      <?php 
                      $i=1;
                      foreach ($drivers as $key => $values) {  
                      
                       ?>
                        <tr>
                          <td><?php echo $i; ?> </td>
                          <td>{{  $values->driver_name }}</td>
                          <td>{{  $values->email }}</td>
                           <td>{{  $values->driver_count }}</td>
                          <td>{{  $values->invite_code }}</td>
                          <td><a href="<?php echo url('/') ?>/driver_invitationDetail/<?php echo encrypt($values->id); ?>" type="submit" class="btn btn-primary btn-flat" data-toggle="tooltip" title="" data-original-title="Add New">Driver Invitation Detail</a>  
                          </td>
                        </tr>
                       
                      <?php 
                         $i++;
                      } 
                      ?>
                    </tbody>
                  </table>
                </div>
                     <div clasw="col-md-6">
                     {{ $drivers->appends(request()->query())->links()  }}
                    </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->i
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
      let x = confirm("Do you realy want to change status?");
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
                 
             }
           });
        }
   }); 
    </script>    
@endsection
