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
              <a href="{{ url('goodreceive') }}" class="btn btn-success"><i class="fa fa-arrow-circle-left"></i> Back</a>
              <a href="{{ url('goodreceive/' . $goodreceive->id . '/edit') }}" class="btn btn-warning"><i class="fa fa-pencil"></i> Edit</a>
              <a href="{{ url('goodreceive/' . $goodreceive->id . '/print') }}" class="btn btn-info" target="_blank"><i class="fa fa-print"></i> Print</a>
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
                <label>Document No. <span class="required">*</span></label>
                <input type="text" class="form-control" name="gr_doc_num" value="{{ $goodreceive->gr_doc_num }}" required readonly>
              </div>
              <div class="form-group">
                <label>Posting Date <span class="required">*</span></label>
                <input type="text" class="form-control" name="gr_posting_date" value="{{ $goodreceive->gr_posting_date }}" readonly>
              </div>
            </div>
            <div class="col-md-6 col-xs-12 left-margin">
              <div class="form-group">
                <label>Vendor <span class="required">*</span></label>
                <input type="text" class="form-control" name="vendor_id" value="{{ $goodreceive->vendor->vendor_name }}" readonly>
              </div>
              <div class="form-group">
                <label>Warehouse <span class="required">*</span></label>
                <input type="text" class="form-control" name="warehouse_id" value="{{ $goodreceive->warehouse->warehouse_name }}" readonly>
              </div>
              <div class="form-group">
                <label>Remarks</label>
                <textarea class="form-control" rows="3" name="gr_remarks" readonly> {{ $goodreceive->gr_remarks }}</textarea>
              </div>
            </div>
            <div class="col-md-12 col-xs-12 left-margin">
              <div class="row x_title">
                <div class="col-md-6">
                  <h3>Contents</h3>
                </div>
                <div class="x_content">
                  <div class="table-responsive">
                    <table id="inputTable" class="table table-striped jambo_table" width="100%">
                      <thead>
                        <tr class="headings">
                          <th class="column-title" style="vertical-align: middle" width="15%">Item Code</th>
                          <th class="column-title" style="vertical-align: middle" width="35%" colspan="3">Description</th>
                          <th class="column-title text-center" style="vertical-align: middle" width="10%">Qty</th>
                          <th class="column-title" style="vertical-align: middle" colspan="2">Line Remarks</th>
                        </tr>
                      </thead>
                      @foreach ($goodreceive->grdetails as $grdetail)
                      <tbody>
                        <tr>
                          <td>
                            <h5>{{ $grdetail->item->item_code }}</h5>
                          </td>
                          <td colspan="3">
                            <h5>{{ $grdetail->item->description }}</h5>
                          </td>
                          <td class="text-center">
                            <h5>{{ $grdetail->gr_qty }}</h5>
                          </td>
                          <td colspan="2">
                            <h5>{{ $grdetail->gr_line_remarks }}</h5>
                          </td>
                        </tr>
                        {{-- @if ($grdetail->item->is_batch == "yes")
                        <tr class="headings">
                          <th class="column-title" style="vertical-align: middle" width="15%">Batch Code</th>
                          <th class="column-title" style="vertical-align: middle" width="12%">MFG</th>
                          <th class="column-title" style="vertical-align: middle" width="12%">Expire Date</th>
                          <th class="column-title" style="vertical-align: middle" width="12%">Expire Status</th>
                          <th class="column-title text-center" style="vertical-align: middle" width="10%">Qty</th>
                          <th class="column-title" style="vertical-align: middle">Batch Remarks</th>
                          <th class="column-title" style="vertical-align: middle">Assign To</th>
                        </tr>
                        @php
                        $batches = app('App\Models\Batch')::select('batches.*', 'batch_transactions.batch_qty','batch_transactions.origin_no','batch_transactions.batch_remarks','batch_transactions.assign_to')->join('batch_transactions', 'batches.id', '=', 'batch_transactions.batch_id')->where('batch_transactions.origin_no', $goodreceive->gr_doc_num)->where('item_id', $grdetail->item_id)->get();
                        @endphp
                        @foreach ($batches as $batch)
                        <tr>
                          <td>{{ $batch->batch_no }}</td>
                        <td>{{ date('d-M-Y', strtotime($batch->mfg_date)) }}</td>
                        <td>{{ date('d-M-Y', strtotime($batch->mfg_date . "+". $grdetail->item->shelf_life." months")) }}</td>
                        <td>
                          @if (date('Y-m-d') > date('Y-m-d', strtotime($batch->mfg_date . "+". $batch->item->shelf_life." months")))
                          {{ "Expired" }}
                          @else
                          {{ "Non Expired" }}
                          @endif
                        </td>
                        <td class="text-center">{{ $batch->batch_transactions->first()->batch_qty }}</td>
                        <td>{{ $batch->batch_transactions->first()->batch_remarks }}</td>
                        <td>{{ $batch->batch_transactions->first()->assign_to }}</td>
                        </tr>
                        @endforeach
                        @endif --}}
                      </tbody>
                      @endforeach
                    </table>
                    {{-- @dd($batches) --}}
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
{{-- <script>
  // Set variabel global untuk data-type item transaction
  // window.dataType = "gr";

  $(document).ready(function() {
    $(".select2").select2();

    $(document).on("select2:open", () => {
      document.querySelector(".select2-search__field").focus();
    });

    // Variabel untuk melacak nomor baris
    addItemDetail(1); // Tambahkan baris pertama onload
    var rowCount = 2; // untuk row berikutnya saat di klik

    // Fungsi untuk menambahkan baris
    $("#dynamic-ar").on("click", function() {
      addItemDetail(rowCount);
      rowCount++; // Tingkatkan nomor baris setiap kali menambahkan baris
    });

    function addItemDetail(rowNumber) {
      var tr = `<tr>
                    <td>
                    <div class="input-group">
                        <input type="hidden" class="form-control item-id-${rowNumber}" name="item_id[${rowNumber}]" placeholder="${rowNumber}" required>
                        <input type="text" class="form-control item-code-${rowNumber}" name="item_code[${rowNumber}]" required>
                        <span class="input-group-btn">
                        <button type="button" class="btn btn-primary search-item-${rowNumber}"><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                    </td>
                    <td>
                      <input type="hidden" class="form-control is-batch-${rowNumber}" readonly>
                      <input type="text" class="form-control description-${rowNumber}" readonly>
                    </td>
                    <td><input type="number" class="form-control gr-qty-${rowNumber}" name="gr_qty[${rowNumber}]" required data-parsley-min="1"></td>
                    <td><input type="text" class="form-control gr-line-remarks-${rowNumber}" name="gr_line_remarks[${rowNumber}]" required></td>
                    <td class="text-center"><button type="button" class="btn btn-danger remove-input-field-${rowNumber}"><i class="fa fa-times"></i></button></td>
                </tr>`;
      $("#inputTable").append(tr);

      // Tambahkan event handler untuk menghapus baris
      $(document).on("click", `.remove-input-field-${rowNumber}`, function() {
        $(this).parents("tr").remove();
        updateRowNumbers();
      });

      function updateRowNumbers() {
        var newRowNumber = 1;
        $("#inputTable tr").each(function() {
          $(this)
            .find(`.item-id-${newRowNumber}`)
            .attr("name", `item_id[${newRowNumber}]`);
          $(this)
            .find(`.item-code-${newRowNumber}`)
            .attr("name", `item_code[${newRowNumber}]`);
          $(this)
            .find(`.gr-qty-${newRowNumber}`)
            .attr("name", `gr_qty[${newRowNumber}]`);
          $(this)
            .find(`.gr-line-remarks-${newRowNumber}`)
            .attr("name", `gr_line_remarks[${newRowNumber}]`);
          newRowNumber++;
        });
      }

      // Inisialisasi autocomplete pada elemen "item-code" dalam baris baru
      var newRowItemCode = $(`#inputTable tr:last .item-code-${rowNumber}`);
      initializeAutocomplete(newRowItemCode, rowNumber);

      // AUTOCOMPLETE METHOD
      $(document).on("input", `.item-code-${rowNumber}`, function() {
        var inputElement = $(this);
        var inputText = inputElement.val();

        // Temukan elemen item yang sesuai dalam baris yang sama
        var itemIDElement = inputElement.siblings(`.item-id-${rowNumber}`);
        var itemCodeElement = inputElement.siblings(
          `.item-code-${rowNumber}`
        );
        var descriptionElement = inputElement.siblings(
          `.description-${rowNumber}`
        );
        var isBatchElement = inputElement.siblings(
          `.is-batch-${rowNumber}`
        );

        // Lakukan AJAX request untuk mencari item berdasarkan inputText
        $.ajax({
          url: "{{ route('items.searchItemByCode') }}"
// url: "http://localhost/bh-inventory/items/searchItemByCode"
, type: "get"
, dataType: "json"
, data: {
item_code: inputText
, }
, success: function(data) {
if (data) {
var items = [];
for (var i = 0; i < data.length; i++) { items.push({ id: data[i].id , label: data[i].item_code , desc: data[i].description , is_batch: data[i].is_batch , }); } // Setel sumber data autocomplete untuk elemen yang sesuai dalam baris yang sama initializeAutocomplete(inputElement, rowNumber); inputElement.autocomplete("option", "source" , items); // Setel nilai item-id dan description yang sesuai dalam baris yang berbeda itemIDElement.val(data[0].id); itemCodeElement.val(data[0].label); descriptionElement.val(data[0].description); isBatchElement.val(data[0].is_batch); } else { // Jika item tidak ditemukan, kosongkan sumber data autocomplete inputElement.autocomplete("option", "source" , []); // Kosongkan nilai item-id dan description dalam baris yang sama itemIDElement.val(""); itemCodeElement.val(""); descriptionElement.val(""); isBatchElement.val(""); } } , }); }); // LIST ALL ITEM FROM MODAL AND DATATABLE METHOD $(document).on("click", `.search-item-${rowNumber}`, function() { $("#itemModal").modal("show"); var warehouseId=$('#warehouse_id').val(); listItem(rowNumber, warehouseId); }); // Handle item selection in the modal and update the corresponding row $("#datatable-serverside").on( "click" , `button.pick-item-${rowNumber}` , function() { var itemID=$(this).data("item-id"); var itemCode=$(this).data("item-code"); var description=$(this).data("description"); var is_batch=$(this).data("is-batch"); // Update nilai item-id, item-code, dan description $(`.item-id-${rowNumber}`).val(itemID); $(`.item-code-${rowNumber}`).val(itemCode); $(`.description-${rowNumber}`).val(description); $(`.is-batch-${rowNumber}`).val(is_batch); // Sembunyikan modal setelah memilih item $("#itemModal").modal("hide"); $(`.item-code-${rowNumber}`).focus(); } ); } // Fungsi autocomplete yang dapat digunakan kembali function initializeAutocomplete(elements, rowNumber) { elements.autocomplete({ minLength: 0 , source: [] , focus: function(event, ui) { elements.val(ui.item.label); return false; } , select: function(event, ui) { var itemIDElement=elements.siblings(`.item-id-${rowNumber}`); var itemCodeElement=elements.siblings(`.item-code-${rowNumber}`); var descriptionElement=elements.siblings(`.description-${rowNumber}`); var isBatchElement=elements.siblings(`.is-batch-${rowNumber}`); // Setel nilai .description dalam <td> yang berbeda
  var descriptionElementInRow = elements.closest("tr").find(`.description-${rowNumber}`);
  descriptionElementInRow.val(ui.item.desc);

  // Setel nilai .is-batch dalam <td> yang berbeda
    var isBatchElementInRow = elements.closest("tr").find(`.is-batch-${rowNumber}`);
    isBatchElementInRow.val(ui.item.is_batch);

    itemIDElement.val(ui.item.id);
    itemCodeElement.val(ui.item.label);
    descriptionElement.val(ui.item.desc);
    isBatchElement.val(ui.item.is_batch);

    return false;
    }
    , }).autocomplete("instance")._renderItem = function(ul, item) {
    return $("<li>")
      .addClass("autocomplete-item")
      .append("<div>" + item.label + "<br>" + item.desc + "</div>")
      .appendTo(ul);
      };
      }

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
      var is_batch = row.is_batch;

      return `<button class="btn btn-sm btn-info pick-item-${rowNumber}" data-item-id="${itemId}" data-item-code="${itemCode}" data-description="${description}" data-is-batch="${is_batch}"><i class="fa fa-check-square-o"></i> Pick!</button>`;
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

      </script> --}}
      @endsection
