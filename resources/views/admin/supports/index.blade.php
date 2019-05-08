@extends('admin.layouts.app')
@section('title', 'FAQ')
@section('breadcrumb')
      <h1>
       FAQ
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li class="active">FAQ's</li>
      </ol>
@endsection
@section('content')  
<div id="page-wrapper"> 
 <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <!-- /.box-header -->
               <form class="form-inline" action="{{  route('support/search') }}" method="get">
              {{ csrf_field() }}
              <div class="box-body">
                   <div class="input-group input-group-xs">
                <!-- /btn-group -->

                <input name="q" type="text" class="form-control" value="@if(isset($string)){{ $string }} @endif" placeholder="Search by question & answer" id="textInput">
              </div>
                <div class="form-group">
                     <button type="submit" class="btn btn-flat margin" data-toggle="tooltip" title="Search">Search</button>
                     <a href="{{ route('support/supports') }}" type="reset" class="btn btn-default btn-flat" data-toggle="tooltip" title="Reset">Reset</a>
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
             
                 <form action="{{ route('support/supports') }}" method="get" role="form" style="display: inline-block;margin-top: -5px;margin-bottom: -18px;">
                <!-- <h3 class="box-title"><b>Support Questions</b>
                  <div class="form-group">
                  <select class="form-control" name="filter" onchange='if(this.value != 0) { this.form.submit(); }'>
                    <option value="">Filter</option>
                    <option value="all">All</option>
                    @forelse ($supports_catetories as $supports_catetoy)
                       <option @if ($filter == $supports_catetoy->id )
                         {{ 'selected' }}
                       @endif value="{{ $supports_catetoy->id }}">{{ $supports_catetoy->name }}</option>
                    @empty
                       <option value="">No any category</option>
                    @endforelse
                  </select>
                </div>
                </h3>  
                -->
                   <h3 class="box-title">
              <select class="form-control" name="filter_faqfor" onchange='if(this.value != 0) { this.form.submit(); }'>
                    <option value="1">Faq For</option>
                    <option  @if ($filter_faqfor == "1" )
                         {{ 'selected' }}
                       @endif value="1">Driver</option>
                    <option @if ($filter_faqfor == "2" )
                         {{ 'selected' }}
                       @endif value="2">Customer</option>
                  </select>  
                  </h3>
                     
               <h3 class="box-title">
              <select class="form-control" name="filter_faqlang" onchange='if(this.value != 0) { this.form.submit(); }'>
                    <option value="">Faq Language</option>
                    @forelse ($language_type as $val)
                               <option  @if ($filter_faqlang == $val->id )
                         {{ 'selected' }}
                       @endif value="{{ $val->id }}"   >{{ $val->name }}</option>
                            @empty
                              <option value="">No any language</option>
                            @endforelse
                  </select>  
                  </h3>    
                   <a href="{{ route('support/supports') }}" type="reset" class="btn btn-default btn-flat" data-toggle="tooltip" title="Reset">Reset</a>
             
               </form>
              
            
              
              <div class="box-tools">
                @can( 'create' , App\Faq::class)
                    <a href="{{ route('support/create') }}" type="submit" class="btn btn-primary btn-flat" data-toggle="tooltip" title="Add New"><i class="fa fa-plus"></i>&nbsp;Add</a>
                @endcan
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tr>
                  <th>Sr.</th>
                <!--  <th>Type</th> -->
                  <th>Questions</th>
                  <th>Answeres</th>
                  <th>Language</th>
                  <th>Faq For</th>
                  @if ( Auth::user()->can('update' , App\Faq::class) ||
                        Auth::user()->can('delete' , App\Faq::class) )
                  <th nowrap>Action</th>
                  @endcan
                </tr>
                  @forelse ($supports as $support)
                    <tr>
                      <td>{{  $i++  }}</td>
                      <!-- <td>{{ $support->category }}</td> -->
                      <td>{{ $support->question }}</td>
                      <td>{{ $support->answer }}...</td>
                      <td><?php if($support->lang_id=="1"){ echo "Tamil";   } else if($support->lang_id=="2"){ echo "Sinhala"; } else{ echo "English";  }  ?></td>
                      <td><?php  if($support->faq_for=='1'){  echo "Driver"; }else{  echo "Customer";  }  ?></td>
                       @if ( Auth::user()->can('update' , App\Faq::class) ||
                        Auth::user()->can('delete' , App\Faq::class) )
                      <td nowrap>
                     {{--    <a href="{{ route('support/show', ['id' => encrypt($support->id)]) }}" data-toggle="tooltip" class="btn btn-flat btn-xs btn-info" title="support Info" "><i class="fa fa-info-circle"></i></a> --}}
                       @can( 'update' , App\Faq::class)
                        <a data-toggle="tooltip" class="btn btn-flat btn-xs btn-primary" title="Edit support" href="{{ route('support/edit',['id' => encrypt($support->id)]) }}"><i class="fa fa-edit"></i></a>
                        @endcan
                         @can( 'delete' , App\Faq::class)
                        <a data-toggle="tooltip" data-toggle="modal" data-target="#delete-model" class="btn btn-flat btn-xs btn-danger" title="Delete support" href="javascript:confirmDelete('{{ route('support/destroy',['id' => encrypt($support->id)]) }}')"><i class="fa fa-trash"></i></a>
                        @endcan
                      </td>
                      @endcan
                    </tr>
                     @empty
                        <tr>
                          <td colspan="6">
                             No any  support record available
                          </td>
                        </tr>
                     @endforelse
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
      </div>
      <div clasw="col-md-6">
          {{ $supports->appends(request()->query())->links()  }}
      </div>
    </div>
@endsection
@section('js-script')
 <script type="text/javascript">

  $('#searchReset').click(function(){
    $('#textInput').attr('value' , '');
  });
   
    @can( 'delete' , App\Faq::class)
    function confirmDelete(delUrl) {
      console.log(delUrl);
            if (confirm("Are you sure you want to delete this Question & Answere?")) {
                document.location = delUrl;
        }
    }
    @endcan

  @if (Session::has('msg'))
      window.setTimeout(function () { 
       $(".alert-row").fadeOut('slow') }, 1500); 
  @endif
  
 </script>
@endsection