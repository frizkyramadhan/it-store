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
            <form action="{{ url()->current() }}" method="get">
              <div class="row">
                <div class="col-md-2 col-sm-12 col-xs-12 form-group">
                  <label>From</label>
                  <input type="date" class="form-control" name="from" value="{{ request('from') }}">
                </div>
                <div class="col-md-2 col-sm-12 col-xs-12 form-group">
                  <label>To</label>
                  <input type="date" class="form-control" name="to" value="{{ request('to') }}">
                </div>

                <div class="col-md-3 col-sm-12 col-xs-12 form-group">
                  <label>Item Code</label>
                  <div class="input-group">
                    <input type="text" class="form-control item-code" name="item_code" value="{{ request('item_code') }}">
                    <span class="input-group-btn">
                      <button type="button" class="btn btn-success search-item"><i class="fa fa-ellipsis-h"></i></button>
                    </span>
                  </div>
                </div>
                <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                  <label>Warehouse</label>
                  <select class="select2 form-control" name="warehouse_ids[]" multiple="multiple">
                    @foreach ($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}" {{ in_array($warehouse->id, request('warehouse_ids', [])) ? 'selected' : '' }}>{{ $warehouse->warehouse_name }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="col-md-1 col-sm-12 col-xs-12 form-group">
                  @if (isset($results) && count($results) > 0)
                  <a href="{{ route('report.permit') }}" class="btn btn-warning form-control"><i class="fa fa-undo"></i> Reset</a>
                  <button type="submit" class="btn btn-primary form-control"><i class="fa fa-search"></i> Search</button>
                  @else
                  <label>&nbsp;</label>
                  <button type="submit" class="btn btn-primary form-control"><i class="fa fa-search"></i> Search</button>
                  @endif
                </div>
              </div>
            </form>
            <hr>
            <div class="table-responsive">
              @if (isset($results) && count($results) > 0)
              <table id="datatable-buttons" class="table table-striped table-bordered">
                @else
                <table class="table table-striped table-bordered" width="100%">
                  @endif
                  <thead>
                    <tr>
                      <th style="vertical-align: middle">No</th>
                      <th style="vertical-align: middle">Permit No</th>
                      <th style="vertical-align: middle">Permit Date</th>
                      <th style="vertical-align: middle">Valid Date</th>
                      <th style="vertical-align: middle">RMNG Days</th>
                      <th style="vertical-align: middle">Warehouse</th>
                      <th style="vertical-align: middle">Item Code</th>
                      <th style="vertical-align: middle">Description</th>
                      <th style="vertical-align: middle">SI Qty</th>
                      <th style="vertical-align: middle">Actual Qty</th>
                      <th style="vertical-align: middle">SI Line Remarks</th>
                      <th style="vertical-align: middle">Created By</th>
                    </tr>
                  </thead>
                  <tbody>
                    @if (isset($results) && count($results) > 0)
                    @foreach ($results as $result)
                    @php
                    $now = date('d-M-Y');
                    $expireDate = date('d-M-Y', strtotime($result->permit_date . "+". $result->valid_month." months"));
                    $nowTimestamp = strtotime($now);
                    $expireDateTimestamp = strtotime($expireDate);
                    $remainingDays = ($expireDateTimestamp - $nowTimestamp) / (60 * 60 * 24);
                    @endphp
                    <tr>
                      <td>{{ $loop->iteration }}</td>
                      <td>{{ $result->permit_no }}</td>
                      <td>{{ date('d-M-Y', strtotime($result->permit_date)) }}</td>
                      <td>{{ $expireDate }}</td>
                      <td class="text-right {{ $remainingDays < 0 ? 'text-danger' : '' }}"><strong>{{ $remainingDays }} Days</strong></td>
                      <td>{{ $result->warehouse_name }}</td>
                      <td>{{ $result->item_code }}</td>
                      <td>{{ $result->description }}</td>
                      <td class="text-right">{{ $result->si_qty }}</td>
                      <td class="text-right">
                        @php
                        $inventory = app('App\Models\Inventory')::select('inventories.*')->where('warehouse_id', $result->warehouse_id)->where('item_id', $result->item_id)->first();
                        if($inventory){
                        echo $inventory->stock;
                        } else {
                        echo '-';
                        }
                        @endphp
                      </td>
                      <td>{{ $result->si_line_remarks }}</td>
                      <td>{{ $result->name }}</td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                      <td colspan="12" class="text-center">No data available</td>
                    </tr>
                    @endif
                  </tbody>
                </table>
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
<!-- Select2 -->
<script src="{{ asset('assets/vendors/select2/dist/js/select2.full.min.js') }}"></script>
<script>
  $(document).ready(function() {
    $(".select2").select2();

    $(document).on("select2:open", () => {
      document.querySelector(".select2-search__field").focus();
    });
  });

</script>
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
  $(document).on("click", `.search-item`, function() {
    $("#itemModal").modal("show");
    var itemCode = $('.item-code').val();
    listItem(itemCode);
  });

  $("#datatable-serverside").on(
    "click"
    , `button.pick-item`
    , function() {
      var itemCode = $(this).data("item-code");

      // Update nilai item-id, item-code, dan description
      $(`.item-code`).val(itemCode);

      // Sembunyikan modal setelah memilih item
      $("#itemModal").modal("hide");
      $(`.item-code`).focus();
    }
  );

  function listItem(itemCode) {
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
        url: "{{ route('dashboard.searchitem') }}"
          // url: "http://localhost/bh-inventory/items/dataForTransaction"
        , data: function(d) {
          d.itemCode = itemCode;
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

            return `<button class="btn btn-sm btn-info pick-item" data-item-id="${itemId}" data-item-code="${itemCode}" data-description="${description}" data-type-name="${typeName}" data-group-name="${groupName}"><i class="fa fa-check-square-o"></i> Pick!</button>`;
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
