@extends('layouts.main')

@section('content')
<div class="right_col" role="main">
  <div class="">
    <div class="page-title">
      <div class="title_left">
        <h3>{{ $title }}</h3>
      </div>
    </div>

    <div class="clearfix"></div>

    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>{{ $subtitle }}</h2>
            <ul class="nav navbar-right panel_toolbox">
              @can('admin')
              <a class="btn btn-primary" data-toggle="modal" data-target="#modal-import"><i class="fa fa-upload"></i>
                Import</a>
              @endcan
              <a href="{{ url()->previous() }}" class="btn btn-success"><i class="fa fa-arrow-circle-left"></i> Back</a>
              <a href="{{ url('items/create') }}" class="btn btn-warning"><i class="fa fa-plus-circle"></i> Add</a>
            </ul>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade in" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
              </button>
              {{ session('success') }}
            </div>
            @endif
            @error('item')
            <div class="alert alert-danger alert-dismissible fade in" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
              </button>
              {{ $message }}
            </div>
            @enderror
            @if (session()->has('failures'))
            <div class="alert alert-danger alert-dismissible">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              <h5><i class="icon fa fa-ban"></i> Alert!</h5>
              <table class="table table-sm">
                <tr>
                  <th>Row</th>
                  <th>Attribute</th>
                  <th>Errors</th>
                  <th>Value</th>
                </tr>
                @foreach (session()->get('failures') as $validation)
                <tr>
                  <td>{{ $validation->row() }}</td>
                  <td>{{ $validation->attribute() }}</td>
                  <td>
                    <ul>
                      @foreach ($validation->errors() as $e)
                      <li>{{ $e }}</li>
                      @endforeach
                    </ul>
                  </td>
                  <td>{{ $validation->values()[$validation->attribute()] }}</td>
                </tr>
                @endforeach
              </table>
            </div>
            @endif
            <div class="table-responsive">
              <table id="datatable-serverside" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th width="5%">No</th>
                    <th>Item Code</th>
                    <th>Description</th>
                    <th>Group</th>
                    <th width="5%">Status</th>
                    <th class="text-center" width="10%">Action</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-import">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
        </button>
        <h4 class="modal-title">Import Data</h4>
      </div>
      <form action="{{ url('items/import') }}" method="POST" enctype="multipart/form-data">
        <div class="modal-body">
          @csrf
          @method('POST')
          <div class="card-body">
            <div class="tab-content p-0">
              <div class="form-group row">
                <label class="col-sm-4 col-form-label">Import Items</label>
                <div class="col-sm-8">
                  <input type="file" name="item">
                </div>
              </div>
            </div>
          </div><!-- /.card-body -->
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
@endsection

@section('styles')
<!-- Datatables -->
<link href="{{ asset('assets/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css') }}" rel="stylesheet">

@endsection

@section('scripts')

<!-- Datatables -->
<script src="{{ asset('assets/vendors/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables.net-buttons/js/buttons.flash.min.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables.net-keytable/js/dataTables.keyTable.min.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables.net-scroller/js/dataTables.scroller.min.js') }}"></script>
<script src="{{ asset('assets/vendors/jszip/dist/jszip.min.js') }}"></script>
<script src="{{ asset('assets/vendors/pdfmake/build/pdfmake.min.js') }}"></script>
<script src="{{ asset('assets/vendors/pdfmake/build/vfs_fonts.js') }}"></script>

{{-- script datatable serverside --}}
<script>
  $(function() {
    var table = $("#datatable-serverside").DataTable({
      responsive: true
      , autoWidth: true
      , lengthChange: true
      , lengthMenu: [
        [10, 25, 50, 100, -1]
        , ['10', '25', '50', '100', 'Show all']
      ]
      , dom: 'lBfrtpi'
        // , dom: 'frtpi'
      , buttons: ["copy", "csv", "print"]
      , processing: true
      , serverSide: true
      , ajax: {
        url: "{{ route('items.data') }}"
        , data: function(d) {
          d.search = $("input[type=search][aria-controls=datatable-serverside]").val()
          // console.log(d);
        }
      }
      , columns: [{
        data: 'DT_RowIndex'
        , orderable: false
        , searchable: false
        , className: 'text-center'
      }, {
        data: "item_code"
        , name: "item_code"
        , orderable: false
      , }, {
        data: "description"
        , name: "description"
        , orderable: false
      , }, {
        data: "group_name"
        , name: "group_name"
        , orderable: false
      , }, {
        data: "item_status"
        , name: "item_status"
        , orderable: false
        , className: "text-center"
      , }, {
        data: "action"
        , name: "action"
        , orderable: false
        , searchable: false
        , className: "text-center"
      }]
      , fixedColumns: true
    , })
  });

</script>


@endsection
