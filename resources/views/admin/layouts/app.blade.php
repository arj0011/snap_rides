<!DOCTYPE html>
<html>
<head>
    @include('admin.layouts.head')
        @section('css-script')@show
</head>
<body class="hold-transition skin-blue sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">
  <header class="main-header">
    @include('admin.layouts.header')
  </header>

  <!-- Left side column. contains the sidebar -->
  <aside class="main-sidebar">
     @include('admin.layouts.aside')
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      @section('breadcrumb')@show
    </section>

    <!-- Main content -->
    <section class="content">
      @section('content')@show
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <footer class="main-footer">
    @include('admin.layouts.footer')
  </footer>

</div>
<!-- ./wrapper -->
  @include('admin.layouts.foot')
  @section('js-script')@show
</body>
</html>

