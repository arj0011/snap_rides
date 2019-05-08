@extends('admin.layouts.app')
@section('title', 'Setting')
@section('breadcrumb')
      <h1>
        Settings
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li class="active">Setting</li>
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
    <div class="col-md-12">
  <!-- Default box -->
      <div class="box">
        <div class="box-body">
          <form data-toggle="validator" role="form" action="{{ route('setting/update') }}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            {{ method_field('PUT') }}
            <div class="col-md-6">
              
              @foreach($settings as $setting)
                @if ( $setting->key == 'base_km' )
                  <div class="form-group">
                    <label>Base kilometer</label>
                    <input name='setting_base_fare_km' onkeypress="return isNumberKey(event);"   class="form-control" value="{{ $setting->value }}" placeholder="Base fare kilometer">
                     @if ($errors->has('setting_base_fare_km'))
                        <span class="star help-block">
                            <strong>{{ $errors->first('setting_base_fare_km') }}</strong>
                        </span>
                     @endif
                     <div class="help-block with-errors"></div>
                  </div>
                @endif
                
               {{--  @if ($setting->key == 'wait_charge_permin')
                  <div class="form-group">
                    <label>Waiting Charge (Per Min)</label>
                    <input name='wait_charge_permin' class="form-control" value="{{ $setting->value }}" placeholder="Waiting Charge Per Minute" onkeypress="return isNumberKey(event);" data-required-error="please enter waiting charge">
                     @if ($errors->has('wait_charge_permin'))
                        <span class="star help-block">
                            <strong>{{ $errors->first('setting_nearest_km') }}</strong>
                        </span>
                     @endif
                     <div class="help-block with-errors"></div>
                  </div>
                @endif --}}
                
                @if ($setting->key == 'driver_request_timeout')
                  <div class="form-group">
                    <label>Booking Request Timeout</label><small>&nbsp;In (Sec)</small>
                    <input type="text"   onkeypress="return isNumberKey(event);"   maxlength="3" name='setting_time_out' class="form-control" value="{{ $setting->value }}" placeholder="Request time out" data-required-error="please enter base fare">
                     @if ($errors->has('setting_time_out'))
                        <span class="star help-block">
                            <strong>{{ $errors->first('setting_time_out') }}</strong>
                        </span>
                     @endif
                     <div class="help-block with-errors"></div>
                  </div>
                @endif
             
                @if ($setting->key == 'sedan_commission')
                <div class="form-group">
                  <label>Commission on Sedan</label><small data-slider-tooltip="show">&nbsp;In (%)</small>
                  <input type="number" name='sedan_commission' class="form-control" value="{{ $setting->value }}" step="any" max="100" min="0" placeholder="Commission on Sedan">
                     @if ($errors->has('sedan_commission'))
                        <span class="star help-block">
                            <strong>{{ $errors->first('sedan_commission') }}</strong>
                        </span>
                     @endif
                     <div class="help-block with-errors"></div>
                </div>
                @endif

                @if ($setting->key == 'seater7_commission')
                <div class="form-group">
                  <label>Commission on 7 Seater</label><small data-slider-tooltip="show">&nbsp;In (%)</small>
                  <input type="number" name='seater7_commission' class="form-control" value="{{ $setting->value }}" step="any" max="100" min="0" placeholder="Commission on 7 Seater">
                     @if ($errors->has('seater7_commission'))
                        <span class="star help-block">
                            <strong>{{ $errors->first('seater7_commission') }}</strong>
                        </span>
                     @endif
                     <div class="help-block with-errors"></div>
                </div>
                @endif

                @if ($setting->key == 'delux_commission')
                <div class="form-group">
                  <label>Commission on Delux</label><small data-slider-tooltip="show">&nbsp;In (%)</small>
                  <input type="number" name='delux_commission' class="form-control" value="{{ $setting->value }}" step="any" max="100" min="0" placeholder="Commission on Delux">
                     @if ($errors->has('delux_commission'))
                        <span class="star help-block">
                            <strong>{{ $errors->first('delux_commission') }}</strong>
                        </span>
                     @endif
                     <div class="help-block with-errors"></div>
                </div>
                @endif

                @if ($setting->key == 'sedan_driver_payouts')
                <div class="form-group">
                  <label>Sedan Driver Payouts</label><small data-slider-tooltip="show">&nbsp;In (R)</small>
                  <input type="number" name='sedan_driver_payouts' class="form-control" value="{{ $setting->value }}" step="any" max="100" min="0" placeholder="Sedan Driver Payouts">
                     @if ($errors->has('sedan_driver_payouts'))
                        <span class="star help-block">
                            <strong>{{ $errors->first('sedan_driver_payouts') }}</strong>
                        </span>
                     @endif
                     <div class="help-block with-errors"></div>
                </div>
                @endif

                @if ($setting->key == 'seater7_driver_payouts')
                <div class="form-group">
                  <label>7 Seater Driver Payouts</label><small data-slider-tooltip="show">&nbsp;In (R)</small>
                  <input type="number" name='seater7_driver_payouts' class="form-control" value="{{ $setting->value }}" step="any" max="100" min="0" placeholder="7 Seater Driver Payouts">
                     @if ($errors->has('seater7_driver_payouts'))
                        <span class="star help-block">
                            <strong>{{ $errors->first('seater7_driver_payouts') }}</strong>
                        </span>
                     @endif
                     <div class="help-block with-errors"></div>
                </div>
                @endif

                @if ($setting->key == 'delux_driver_payouts')
                <div class="form-group">
                  <label>Delux Driver Payouts</label><small data-slider-tooltip="show">&nbsp;In (R)</small>
                  <input type="number" name='delux_driver_payouts' class="form-control" value="{{ $setting->value }}" step="any" max="100" min="0" placeholder="Delux Driver Payouts">
                     @if ($errors->has('delux_driver_payouts'))
                        <span class="star help-block">
                            <strong>{{ $errors->first('delux_driver_payouts') }}</strong>
                        </span>
                     @endif
                     <div class="help-block with-errors"></div>
                </div>
                @endif

              @endforeach

                  <div class="form-group"> 
                     <button type="reset" class="btn btn-default btn-flat" data-toggle="tooltip" title="Reset Form">Reset</button>
                      <button type="submit" class="btn  btn-flat btn-success">Update</button>
                  </div>

            </div>
          </form>
       </div>
      </div>
      <!-- /.box -->
    </div>
  </div> 
  </div>
@endsection
@section('css-script')
 <!--  bootstrap  validatoin css file -->
 <link rel="stylesheet" type="text/css" href="{{ asset('Admin/css/bootstrapValidator.min.css') }}">
    <!--  bootstrap  slider css file -->
 <link rel="stylesheet" type="text/css" href="{{ asset('Admin/css/slider.css') }}">

@endsection
@section('js-script')
   <!-- bootstrap validation script -->
   <script type="text/javascript" src="{{ asset('Admin/js/bootstrapValidator.min.js') }}"></script>
      <!-- bootstrap slider js script -->
   <script type="text/javascript" src="{{ asset('Admin/js/bootstrap-slider.js') }}"></script>
   <script type="text/javascript">
     @if (Session::has('msg'))
      window.setTimeout(function () { 
       $(".alert-row").fadeOut('slow') }, 2000); 
      @endif

      $(function () {
        /* BOOTSTRAP SLIDER */
        $('.slider').slider()
      })

 function isNumberKey(evt){
        var charCode = (evt.which) ? evt.which : evt.keyCode
        return !(charCode > 31 && (charCode < 48 || charCode > 57));
      }
   </script>
@endsection