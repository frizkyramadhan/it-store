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
              <a href="{{ url('permits') }}" class="btn btn-success"><i class="fa fa-arrow-circle-left"></i> Back</a>
              @if ($permit->permit_status == 'valid')
              <a href="{{ url('permits/extend/'. $permit->id) }}" class="btn btn-danger"><i class="fa fa-history"></i> Extends</a>
              @endif
              <a href="{{ url('permits/'. $permit->id.'/edit') }}" class="btn btn-warning"><i class="fa fa-pencil"></i> Edit</a>
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
            @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade in" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
              </button>
              {{ session('error') }}
            </div>
            @endif
            <div class="col-md-6 col-xs-12 left-margin">
              <div class="form-group">
                <label>Permit No. <span class="required">*</span></label>
                <input type="text" class="form-control" name="permit_no" value="{{ $permit->permit_no }}" readonly>
              </div>
              <div class="form-group">
                <label>Permit Date <span class="required">*</span></label>
                <input type="text" class="form-control" name="permit_date" value="{{ date('d-M-Y', strtotime($permit->permit_date)) }}" readonly>
              </div>
              <div class="form-group">
                <label>Valid Month <span class="required">*</span></label>
                <input type="number" class="form-control" name="valid_month" value="{{ $permit->valid_month }}" readonly>
              </div>
            </div>
            <div class="col-md-6 col-xs-12 left-margin">
              <div class="form-group">
                <label>Permit Type <span class="required">*</span></label>
                <input type="text" class="form-control" name="permit_type" value="{{ $permit->permit_type }}" readonly>
              </div>
              <div class="form-group">
                <label>Warehouse <span class="required">*</span></label>
                <input type="hidden" class="form-control" id="warehouse_id" value="{{ $permit->warehouse->id }}" readonly>
                <input type="text" class="form-control" name="warehouse_id" value="{{ $permit->warehouse->warehouse_name }}" readonly>
              </div>
              <div class="form-group">
                <label>Status</label><br>
                @if ($permit->permit_status == 'valid')
                <span class="label label-primary">Valid</span>
                @else
                <span class="label label-default">Extended</span>
                @endif
              </div>
            </div>
            <div class="col-md-12 col-xs-12 left-margin">
              <div class="x_panel">
                <div class="x_title">
                  <h2>Permit Detail</h2>
                  <ul class="nav navbar-right panel_toolbox"></ul>
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">
                  <div class="table-responsive">
                    <table id="inputTable" class="table table-striped jambo_table" width="100%">
                      <thead>
                        <tr class="headings">
                          <th class="column-title" style="vertical-align: middle">Item Code</th>
                          <th class="column-title" style="vertical-align: middle">Description</th>
                          <th class="column-title text-right" style="vertical-align: middle">SI Qty</th>
                          <th class="column-title text-right" style="vertical-align: middle">Actual Qty</th>
                          <th class="column-title" style="vertical-align: middle">Line Remarks</th>
                          <th class="column-title text-center" style="vertical-align: middle">Edit</th>
                        </tr>
                      </thead>
                      @foreach ($permit->permitdetails as $permitdetail)
                      <tbody>
                        <tr>
                          <td>{{ $permitdetail->item->item_code }}</td>
                          <td>{{ $permitdetail->item->description }}</td>
                          <td class="text-right">{{ $permitdetail->si_qty }}</td>
                          <td class="text-right">
                            @php
                            $inventory = app('App\Models\Inventory')::select('inventories.*')->where('warehouse_id', $permit->warehouse->id)->where('item_id', $permitdetail->item->id)->first();
                            if($inventory){
                            echo $inventory->stock;
                            } else {
                            echo '-';
                            }
                            @endphp
                          </td>
                          <td>{{ $permitdetail->si_line_remarks }}</td>
                          <td class="text-right">
                            <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editItem-{{ $permitdetail->id }}"><i class="fa fa-pencil"></i></button>
                          </td>
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
  </div>
</div>

@foreach ($permit->permitdetails as $permitdetail)
<div id="editItem-{{ $permitdetail->id }}" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Edit Permit Item</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form data-parsley-validate class="form-horizontal form-label-left" action="{{ route('permits.updateItem', ['permit_id' => $permit->id, 'id' => $permitdetail->id]) }}" method="POST">
        @method('PATCH')
        @csrf
        <div class="x_content">
          <div class="modal-body">
            <label>Item Code <span class="required">*</span></label>
            <div class="input-group">
              <input type="hidden" class="form-control item-id-{{ $permitdetail->id }}" name="item_id" value="{{ $permitdetail->item->id }}" required>
              <input type="text" class="form-control item-code-{{ $permitdetail->id }}" name="item_code" value="{{ $permitdetail->item->item_code }}" required>
              <span class="input-group-btn">
                <button type="button" class="btn btn-primary search-item-{{ $permitdetail->id }}"><i class="fa fa-search"></i></button>
              </span>
            </div>
            <div class="form-group">
              <label>Description <span class="required">*</span></label>
              <input type="text" class="form-control description-{{ $permitdetail->id }}" value="{{ $permitdetail->item->description }}" readonly>
            </div>
            <div class="form-group">
              <label>SI Quantity <span class="required">*</span></label>
              <input type="number" class="form-control" name="si_qty" value="{{ $permitdetail->si_qty }}" required data-parsley-min="1">
            </div>
            <div class="form-group">
              <label>Remarks <span class="required">*</span></label>
              <input type="text" class="form-control" name="si_line_remarks" value="{{ $permitdetail->si_line_remarks }}" required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach

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
                <th class="column-title">Stock</th>
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
<!-- Jquery UI -->
<link href="{{ asset('assets/vendors/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet">
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
<!-- Jquery UI -->
<script src="{{ asset('assets/vendors/jquery-ui/jquery-ui.js') }}"></script>
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
{{-- <script src="{{ asset('assets/build/js/itemTransaction.js') }}"></script> --}}

@foreach ($permit->permitdetails as $permitdetail)

<script>
  $(document).ready(function() {
    // LIST ALL ITEM FROM MODAL AND DATATABLE METHOD
    $(document).on("click", `.search-item-{{ $permitdetail->id }}`, function() {
      $("#itemModal").modal("show");
      // get rowNumber from {{ $permitdetail->id }}
      var rowNumber = "{{ $permitdetail->id }}";
      var warehouseId = $('#warehouse_id').val();
      listItem(rowNumber, warehouseId);
    });

    // Handle item selection in the modal and update the corresponding row
    $("#datatable-serverside").on(
      "click"
      , `button.pick-item-{{ $permitdetail->id }}`
      , function() {
        var itemID = $(this).data("item-id");
        var itemCode = $(this).data("item-code");
        var description = $(this).data("description");

        // Update nilai item-id, item-code, dan description
        $(`.item-id-{{ $permitdetail->id }}`).val(itemID);
        $(`.item-code-{{ $permitdetail->id }}`).val(itemCode);
        $(`.description-{{ $permitdetail->id }}`).val(description);

        // Sembunyikan modal setelah memilih item
        $("#itemModal").modal("hide");
        $(`.item-code-{{ $permitdetail->id }}`).focus();
      }
    );

    // datatable serverside list item
    function listItem(rowNumber, warehouseId) {
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

              return `<button class="btn btn-sm btn-info pick-item-{{ $permitdetail->id }}" data-item-id="${itemId}" data-item-code="${itemCode}" data-description="${description}"><i class="fa fa-check-square-o"></i> Pick!</button>`;
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
            data: "stock"
            , name: "stock"
            , orderable: false
            , className: "text-right"
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
  });

</script>
@endforeach
@endsection
