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
              <a href="{{ url()->previous() }}" class="btn btn-success"><i class="fa fa-arrow-circle-left"></i> Back</a>
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
            <form action="{{ url('batches/'.$batch->id) }}" method="post" autocomplete="on">
              @csrf
              @method('PUT')
              <div class="col-md-6 col-xs-12 left-margin">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Item Detail</h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <div class="form-group">
                      <label>Item Code</label>
                      <div class="input-group">
                        <input type="hidden" class="form-control item-id" name="item_id" value="{{ $batch->item->id }}">
                        <input type="text" class="form-control item-code" value="{{ $batch->item->item_code }}" readonly>
                        <span class="input-group-btn">
                          <button type="button" class="btn btn-primary search-item"><i class="fa fa-search"></i></button>
                        </span>
                      </div>
                    </div>
                    <div class="form-group">
                      <label>Description</label>
                      <input type="text" class="form-control description" value="{{ $batch->item->description }}" readonly>
                    </div>
                    <div class="form-group">
                      <label>Type</label>
                      <input type="text" class="form-control type-name" value="{{ $batch->item->type->type_name }}" readonly>
                    </div>
                    <div class="form-group">
                      <label>Group</label>
                      <input type="text" class="form-control group-name" value="{{ $batch->item->group->group_name }}" readonly>
                    </div>
                    <div class="form-group">
                      <label>Shelf Life (month)</label>
                      <input type="text" class="form-control shelf-life" value="{{ $batch->item->shelf_life }}" readonly>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6 col-xs-12 left-margin">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Batch Detail</h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <div class="form-group">
                      <label>Batch No.</label>
                      <input type="text" class="form-control" name="batch_no" value="{{ $batch->batch_no }}" required>
                    </div>
                    <div class="form-group">
                      <label>Mfg Date</label>
                      <input type="date" class="form-control" name="mfg_date" value="{{ $batch->mfg_date }}" required>
                    </div>
                    <div class="form-group">
                      <label>Batch Status</label>
                      <select id="batch_status" class="select2 form-control" name="batch_status" style="width: 100%" required>
                        <option value="active" {{ $batch->batch_status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $batch->batch_status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              <div class="form-group pull-right">
                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Submit</button>
              </div>
              <input type="hidden" name="url" value="{{ url()->previous() }}">
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>

<div id="itemModal" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">List of Items</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="x_content">
        <div class="modal-body">
          <table id="datatable-serverside" class="table table-striped jambo_table" cellspacing="0" width="100%">
            <thead>
              <tr class="headings">
                <th class="column-title" width="1%" class="text-center">Action</th>
                <th class="column-title">Item Code</th>
                <th class="column-title">Description</th>
                <th class="column-title">Type</th>
                <th class="column-title">Group</th>
                <th class="column-title">Status</th>
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
</div>
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
  });

  $(document).on("click", `.search-item`, function() {
    $("#itemModal").modal("show");
    var warehouseId = $('#warehouse_id').val();
    listItem(warehouseId);
  });

  $("#datatable-serverside").on(
    "click"
    , `button.pick-item`
    , function() {
      var itemID = $(this).data("item-id");
      var itemCode = $(this).data("item-code");
      var description = $(this).data("description");
      var typeName = $(this).data("type-name");
      var groupName = $(this).data("group-name");
      var shelfLife = $(this).data("shelf-life");

      // Update nilai item-id, item-code, dan description
      $(`.item-id`).val(itemID);
      $(`.item-code`).val(itemCode);
      $(`.description`).val(description);
      $(`.type-name`).val(typeName);
      $(`.group-name`).val(groupName);
      $(`.shelf-life`).val(shelfLife);

      // Sembunyikan modal setelah memilih item
      $("#itemModal").modal("hide");
      $(`.item-code`).focus();
    }
  );

  function listItem(warehouseId) {
    var table = $("#datatable-serverside").DataTable({
      responsive: true
      , autoWidth: true
      , lengthChange: true
      , lengthMenu: [
        [10, 25, 50, 100, -1]
        , ["10", "25", "50", "100", "Show all"]
      , ]
      , dom: "lfrtpi"
      , processing: true
      , serverSide: true
      , ajax: {
        url: "{{ route('items.dataForTransaction') }}"
          // url: "http://localhost/bh-inventory/items/dataForTransaction"
        , data: function(d) {
          d.warehouseId = warehouseId;
          d.search = $(
            "input[type=search][aria-controls=datatable-serverside]"
          ).val();
          // console.log(d);
        }
      , }
      , columns: [{
          data: "action"
          , name: "action"
          , orderable: false
          , searchable: false
          , className: "text-center"
          , render: function(data, type, row, meta) {
            var itemId = row.id;
            var itemCode = row.item_code;
            var description = row.description;
            var typeName = row.type_name;
            var groupName = row.group_name;
            var shelfLife = row.shelf_life;

            return `<button class="btn btn-sm btn-info pick-item" data-item-id="${itemId}" data-item-code="${itemCode}" data-description="${description}" data-type-name="${typeName}" data-group-name="${groupName}" data-shelf-life="${shelfLife}"><i class="fa fa-check-square-o"></i> Pick!</button>`;
          }
        , }
        , {
          data: "item_code"
          , name: "item_code"
          , orderable: false
        , }
        , {
          data: "description"
          , name: "description"
          , orderable: false
        , }
        , {
          data: "type_name"
          , name: "type_name"
          , orderable: false
        , }
        , {
          data: "group_name"
          , name: "group_name"
          , orderable: false
        , }
        , {
          data: "item_status"
          , name: "item_status"
          , orderable: false
          , className: "text-center"
        , }
      , ]
      , fixedColumns: true
      , destroy: true, // agar tidak reinitialize setiap kali listItem dipanggil
    });
  }

</script>


@endsection
