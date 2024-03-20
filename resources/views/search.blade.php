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
              <a href="{{ url('/') }}" class="btn btn-success"><i class="fa fa-arrow-circle-left"></i> Back</a>
              @can('admin')
              <a href="{{ url('items/' . $item->id . '/edit') }}" class="btn btn-warning"><i class="fa fa-pencil"></i> Edit</a>
              @endcan
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

            <div class="col-md-6 col-xs-12 center-margin">
              <div class="form-group">
                <label>Item Code <span class="required">*</span></label>
                <input type="text" class="form-control" value="{{ $item->item_code }}" readonly>
              </div>
              <div class="form-group">
                <label>Description <span class="required">*</span></label>
                <input type="text" class="form-control" value="{{ $item->description }}" readonly>
              </div>
              <div class="form-group">
                <label>Group <span class="required">*</span></label>
                <input type="text" class="form-control" value="{{ $item->group->group_name }}" readonly>
              </div>
              <div class="form-group">
                <label>Status</label>
                <div data-toggle="buttons">
                  @if ($item->item_status == 'active')
                  <label class="btn btn-default {{ $item->item_status == 'active' ? 'active' : '' }}">
                    <input type="radio" name="item_status" value="active" {{ $item->item_status == 'active' ? 'checked' : '' }}> Active
                  </label>
                  @else
                  <label class="btn btn-default {{ $item->item_status == 'inactive' ? 'active' : '' }}">
                    <input type="radio" name="item_status" value="inactive" {{ $item->item_status == 'inactive' ? 'checked' : '' }}> Inactive
                  </label>
                  @endif
                </div>
              </div>
            </div>

            <div class="col-lg-12 col-xs-12 left-margin">
              <h3>Inventory Data: </h3>
              <div class="x-content">
                <table class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Bouwheer</th>
                      <th>Warehouse</th>
                      <th class="text-right">Stock</th>
                    </tr>
                  </thead>
                  @foreach($inventories as $inventory)
                  <tbody>
                    <tr>
                      <th scope="row">{{ $loop->iteration }}</th>
                      <td style="overflow:hidden; height: 45px;">{{ $inventory->bouwheer_name }}</td>
                      <td>{{ $inventory->warehouse_name }}</td>
                      <td class="text-right">{{ $inventory->stock != 0 ? $inventory->stock : "" }}</td>
                    </tr>
                  </tbody>
                  @endforeach
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
{{--
<div id="itemModal" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">List of Batch Item</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="x_content">
        <div class="modal-body">
          <table id="datatable-serverside" class="table table-striped jambo_table" width="100%">
            <thead>
              <tr class="headings">
                <th class="column-title">Batch No.</th>
                <th class="column-title">Mfg Date</th>
                <th class="column-title">Expire Date</th>
                <th class="column-title">Expire Status</th>
                <th class="column-title">Stock</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div> --}}
@endsection

@section('styles')
<!-- iCheck -->
<link href="{{ asset('assets/vendors/iCheck/skins/flat/green.css') }}" rel="stylesheet">
<!-- Select2 -->
<link href="{{ asset('assets/vendors/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<!-- Datatables -->
<link href="{{ asset('assets/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css') }}" rel="stylesheet">
@endsection

@section('scripts')
<!-- iCheck -->
<script src="{{ asset('assets/vendors/iCheck/icheck.min.js') }}"></script>
<!-- Parsley -->
<script src="{{ asset('assets/vendors/parsleyjs/dist/parsley.min.js') }}"></script>
<!-- Select2 -->
<script src="{{ asset('assets/vendors/select2/dist/js/select2.full.min.js') }}"></script>
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

<script>
  $(document).ready(function() {
    $('.select2').select2();

    $(document).on('select2:open', () => {
      document.querySelector('.select2-search__field').focus();
    })

    // @foreach($inventories as $inventory)
    // $(document).on("click", `.get-batch-{{ $inventory->id }}`, function() {
    //   $("#itemModal").modal("show");
    //   var id = '{{ $inventory->id }}';
    //   var item_id = '{{ $item->id }}';
    //   listItem(id, item_id);
    // })
    // @endforeach

    // function listItem(id, item_id) {
    //   var table = $("#datatable-serverside").DataTable({
    //     responsive: true
    //     , autoWidth: true
    //     , lengthChange: true
    //     , lengthMenu: [
    //       [10, 25, 50, 100, -1]
    //       , ["10", "25", "50", "100", "Show all"]
    //     , ]
    //     , dom: "lfrtpi"
    //     , processing: true
    //     , serverSide: true
    //     , ajax: {
    //       url: "{{ route('items.getbatchbyitem') }}"
    //         // url: "http://localhost/bh-inventory/items/dataForTransaction"
    //       , data: function(d) {
    //         d.id = id;
    //         d.item_id = item_id;
    //         d.search = $(
    //           "input[type=search][aria-controls=datatable-serverside]"
    //         ).val();
    //         // console.log(d);
    //       }
    //     , }
    //     , columns: [{
    //         data: "batch_no"
    //         , name: "batch_no"
    //         , orderable: false
    //       , }
    //       , {
    //         data: "mfg_date"
    //         , name: "mfg_date"
    //         , orderable: false
    //       , }
    //       , {
    //         data: "expire_date"
    //         , name: "expire_date"
    //         , orderable: false
    //       , }
    //       , {
    //         data: "expire_status"
    //         , name: "expire_status"
    //         , orderable: false
    //       , }
    //       , {
    //         data: "batch_stock"
    //         , name: "batch_stock"
    //         , orderable: false
    //         , className: "text-center"
    //       , }
    //     , ]
    //     , fixedColumns: true
    //     , destroy: true, // agar tidak reinitialize setiap kali listItem dipanggil
    //   });
    // }
  });

</script>


@endsection
