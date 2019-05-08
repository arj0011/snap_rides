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
 

          <div class="box-header">
            <h3 class="box-title"><b><?php echo $get_code[0]->name; ?> Invitations</b>
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
                      <th nowrap>Mobile</th>
                      <th nowrap>Date</th>
                      
                    </tr>
                    </thead>
                    <tbody>
                       
                      <?php 
                      $i=1;
                      foreach ($drivers as $key => $values) {  ?>
                        <tr>
                          <td><?php echo $i; ?> </td>
                          <td>{{  $values->name }}</td>
                          <td>{{  $values->email }}</td>
                          <td>{{  $values->mobile }}</td>
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
                     {{ $drivers->appends(request()->query())->links()  }}
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
