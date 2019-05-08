@extends('admin.layouts.app')
@section('title', 'Drivers')
@section('breadcrumb')
      <h1>
        Invitation Details
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li class="active">Offers</li>
      </ol>
@endsection
@section('content')   
  <div id="page-wrapper">
    
     

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
           <div class="box-body">
          <div id="page-wrapper">
               <form class="form-inline" action="<?php echo url('/') ?>/sendPromocode/<?php echo encrypt($res[0]->id); ?>" method="post" autocomplete="off">
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
                  <input type="hidden" name="offer_type" value="promocode">
                  <input name="city" id="city" type="text" class="form-control" value="@if(isset($q)){{ $q }} @endif" id="textInput" placeholder="Name or mobile">
                  </div>
                <div class="form-group">
                     <button type="submit" class="btn btn-flat margin" data-toggle="tooltip" title="Search">Search</button>
                    <a href="" type="reset" class="btn btn-default btn-flat" data-toggle="tooltip" title="Reset">Reset</a>
                </div>
            </form>
         
              </div>
             </div>  

          <div class="box-header">
            <h3 class="box-title"><b>Send <?php echo $res[0]->offer_code; ?> To Customer</b>
            </h3>
              
              <div class="box-tools">
             <img src="{{ asset('/public/Admin/loading.gif') }}" id="load-img" height="50" width="50"> <button class="btn btn-primary btn-flat send-btn"  data-toggle="tooltip" title="" data-original-title="Send Promocode">Send</button>
              </div>
          </div>                      
                   
          
          <!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                  <table class="table table-hover">
                    <thead style="background:#F7F7F7;">
                    <tr>
                      <th><input type="checkbox" class="chk_all" ></th>
                      <th nowrap>Name</th>
                      <th nowrap>Email</th>
                      <th nowrap>Mobile</th>
                      <th nowrap>City</th>
                      <th nowrap>Date</th>
                      
                    </tr>
                    </thead>
                    <tbody>
                       
                      <?php 
                      $i=1;
                      foreach ($customer as $key => $values) {  ?>
                        <tr>
                          <td><input type="checkbox" class="cust-chk" name="customer" value="{{  $values->id }}" ></td>
                          <td>{{  $values->name }}</td>
                          <td>{{  $values->email }}</td>
                          <td>{{  $values->mobile }}</td>
                          <td>{{  $values->city }}</td>
                          <td>{{  $values->created_at }}</td>
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
                     {{ $customer->appends(request()->query())->links()  }}
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
<style>
    #load-img{
        display:none;
    }
</style>
    <script src="{{ asset('Admin/js/bootstrap-datepicker.min.js') }}"></script>
    <script>
      $(document).ready(function(){
        $(".send-btn").attr("disabled",true);  
         $(".cust-chk").click(function(){
              if ($(this).prop('checked')==true){ 
                  $(".send-btn").attr("disabled",false);
              }
          });
        $(".chk_all").click(function(){
              if ($(this).prop('checked')==true){ 
                $(".cust-chk").prop("checked",true);
                $(".send-btn").attr("disabled",false); 
              }
              else{
                $(".cust-chk").prop("checked",false);  
                $(".send-btn").attr("disabled",true);   
           }
          });
          
      $('.send-btn').click(function(event){
       let click = this;
       let x = confirm("Do you want to send this promocode?");
       var cust= new Array();
        $("input:checkbox[name=customer]:checked").each(function(){
        cust.push($(this).val());
        });    
          if(x){
            $.ajax({
             type: 'put',
             url : '{{ route('send-Promocode') }}',
             data : {
                'customer' :  cust,
                'offer_id' :  '<?php echo $res[0]->id; ?>',
                '_token': '{{ csrf_token() }}'
             },
              beforeSend: function(){
              $('#load-img').show();
            },
             complete: function(){
            $('#load-img').hide();
            },        
             success: function(response)
             {  
                
                    if(response=="1"){
                      alert('Successfully send.');
                     
                    }else{
                      alert('Something went wrong.');
                    }
                 
             }
           });
        } 
           
    });   
          
      }); 
    </script>    
@endsection
