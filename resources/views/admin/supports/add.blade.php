@extends('admin.layouts.app')
@section('title', 'Add  FAQ')
@section('breadcrumb')
      <h1>
        Add New FAQ
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li><a href="{{route('support/supports') }}">Supports</a></li>
        <li class="active">Add FAQ</li>
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
          <form data-toggle="validator" role="form" action="{{ route('support/store') }}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="col-md-6">
                
                <div class="form-group">
                        <label>Language Type</label><span class="star">&nbsp;*</span>
                        <select name = 'lang_type' class="form-control"  data-required-error="please select FAQ language."  required>
                           <option value="">--select language--</option>
                           @forelse ($language_type as $val)
                               <option @if (old('lang_type') == $val->id )
                                 {{  'selected' }}
                               @endif value="{{ $val->id }}">{{ $val->name }}</option>
                            @empty
                              <option value="">No any language</option>
                            @endforelse
                        </select>
                         @if ($errors->has('lang_type'))
                            <span class="star  help-block">
                                <strong>{{ $errors->first('lang_type') }}</strong>
                            </span>
                         @endif
                         <div class="help-block with-errors"></div>
                </div>
                
                <div class="form-group">
                        <label>Faq For</label><span class="star">&nbsp;*</span>
                        <select name = 'faq_for' class="form-control"  data-required-error="please select FAQ for."  required>
                           <option value="">--select Faq for--</option>
                               <option value="1">Driver</option> 
                               <option value="2">Customer</option>
                        </select>
                         @if ($errors->has('faq_for'))
                            <span class="star  help-block">
                                <strong>{{ $errors->first('faq_for') }}</strong>
                            </span>
                         @endif
                         <div class="help-block with-errors"></div>
                </div>

                <!--<div class="form-group">
                        <label> FAQ Type</label><span class="star">&nbsp;*</span>
                        <select name = 'type' class="form-control"  data-required-error="please select FAQ type"  required>
                               <option value="">--select type--</option>
                           @forelse ($supports_catetories as $supports_catetoy)
                               <option @if (old('type') == $supports_catetoy->id )
                                 {{  'selected' }}
                               @endif value="{{ $supports_catetoy->id }}">{{ $supports_catetoy->name }}</option>
                            @empty
                              <option value="">No any category</option>
                            @endforelse
                        </select>
                         @if ($errors->has('type'))
                            <span class="star  help-block">
                                <strong>{{ $errors->first('type') }}</strong>
                            </span>
                         @endif
                         <div class="help-block with-errors"></div>
                </div>
                -->

               <div class="form-group">
                <label>Question</label><span class="star">&nbsp;*</span>
                <input name='question' id="type" class="form-control" value="{{ old('question') }}" placeholder="question" data-required-error="please enter question"  required>
                 @if ($errors->has('question'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('question') }}</strong>
                    </span>
                 @endif
                 <div class="help-block with-errors"></div>
              </div>

               <div class="form-group">
                <label>Answer</label><span class="star">&nbsp;*</span>
                <textarea class="form-control textarea" name='answer' id="supportTextarea" class="form-control"  placeholder="Please enter answer"  data-required-error="please enter answer"  required>
                      {{ old('answer') }}
                </textarea>
                 @if ($errors->has('answer'))
                    <span class="star help-block">
                        {{ old('answer') }}
                    </span>
                 @endif
                 <div class="help-block with-errors"></div>
              </div>

              <div class="form-group"> 
                  <button type="reset" class="btn btn-default btn-flat" data-toggle="tooltip" title="Reset Form">Reset</button>
                  <button type="submit" class="btn btn-md btn-flat btn-primary">Add</button>
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
 <!-- validatoin css file -->
 <link rel="stylesheet" type="text/css" href="{{ asset('Admin/css/bootstrapValidator.min.css') }}">
 <!-- toggle css file -->
 <link rel="stylesheet" type="text/css" href="{{  asset('Admin/css/bootstrap-toggle.css') }}">
 <!-- edit css file -->
 <style type="text/css">
   label{
          color: #222d32 !important;
   }
   textarea#supportTextarea {
    width:100%;
    box-sizing:border-box;
    display:block;
    max-width:100%;
    line-height:1.5;
    padding:15px 15px 30px;
}
 </style>
@endsection
@section('js-script')
   <!-- bootstrap toggle script -->
   <script type="text/javascript"  src="{{ asset('Admin/js/bootstrap-toggle.js') }}"></script>
   <!-- bootstrap validation script -->
   <script type="text/javascript" src="{{ asset('Admin/js/bootstrapValidator.min.js') }}"></script>
   <!-- texarea auto sizer -->
   <script type="text/javascript" src="{{ asset('Admin/js/texarea-autosizer.js') }}"></script>
 
   <script type="text/javascript">
    $(function() {
      $('#toggle').bootstrapToggle();
    });
     @if (Session::has('msg'))
      window.setTimeout(function () { 
       $(".alert-row").fadeOut('slow') }, 2000); 
   @endif
      autosize($("#supportTextarea"));
      </script>
@endsection