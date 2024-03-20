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
            @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade in" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
              </button>
              {{ session('error') }}
            </div>
            @endif
            <form id="form" data-parsley-validate class="form-horizontal form-label-left" action="{{ route('goodissues.store') }}" method="POST">
              @csrf
              <div class="col-md-6 col-xs-12 left-margin">
                <div class="form-group">
                  <label>Document No. <span class="required">*</span></label>
                  <input type="text" class="form-control" name="gi_doc_num" value="{{ $sessionData ? $sessionData['gi']['gi_doc_num'] : $gi_no }}" required readonly>
                </div>
                <div class="form-group">
                  <label>Posting Date <span class="required">*</span></label>
                  <input type="date" class="form-control" name="gi_posting_date" value="{{ $sessionData ? $sessionData['gi']['gi_posting_date'] : date('Y-m-d') }}" required>
                </div>
              </div>
              <div class="col-md-6 col-xs-12 left-margin">
                <div class="form-group">
                  <label>Warehouse <span class="required">*</span></label>
                  <select id="warehouse_id" class="select2 form-control" name="warehouse_id" style="width: 100%" required>
                    <option value="">Select Warehouse</option>
                    @foreach ($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}" {{ $sessionData ? ($sessionData['gi']['warehouse_id'] == $warehouse->id ? "selected" : "") : "" }}>{{ $warehouse->warehouse_name }} ({{ $warehouse->bouwheer->bouwheer_name }})</option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group">
                  <label>Remarks</label>
                  <textarea class="form-control" rows="3" name="gi_remarks">{{ $sessionData ? $sessionData['gi']['gi_remarks'] : ""}}</textarea>
                  <input type="hidden" class="form-control" name="gi_status" value="open" />
                </div>
              </div>
              <div class="col-md-12 col-xs-12 left-margin">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Good Issue Detail</h2>
                    <ul class="nav navbar-right panel_toolbox"></ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <table id="inputTable" class="table table-striped jambo_table">
                      <thead>
                        <tr class="headings">
                          <th class="column-title" style="vertical-align: middle" width="25%">Item Code</th>
                          <th class="column-title" style="vertical-align: middle" width="25%">Description</th>
                          <th class="column-title" style="vertical-align: middle" width="10%">Quantity</th>
                          <th class="column-title" style="vertical-align: middle">Line Remarks</th>
                          <th class="column-title" style="vertical-align: middle" width="5%"><button type="button" id="dynamic-ar" class="btn btn-primary"><i class="fa fa-plus"></i></button></th>
                        </tr>
                      </thead>
                    </table>
                  </div>
                </div>
              </div>
              <div class="col-md-12 col-xs-12 left-margin">
                <div class="form-group pull-right">
                  <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Submit</button>
                </div>
              </div>
            </form>
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
          <table id="datatable-serverside" class="table table-striped jambo_table" width="100%">
            <thead>
              <tr class="headings">
                <th class="column-title" width="1%" class="text-center">Action</th>
                <th class="column-title">Item Code</th>
                <th class="column-title">Description</th>
                <th class="column-title">Stock</th>
                {{-- <th class="column-title">Type</th> --}}
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
<script>
  // Set variabel global untuk data-type item transaction
  // window.dataType = "gi";

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
                    <td><input type="text" class="form-control description-${rowNumber}" readonly></td>
                    <td><input type="number" class="form-control gi-qty-${rowNumber}" name="gi_qty[${rowNumber}]" required data-parsley-min="1" data-parsley-trigger="keyup" data-parsley-checkstock${rowNumber}></td>
                    <td><input type="text" class="form-control gi-line-remarks-${rowNumber}" name="gi_line_remarks[${rowNumber}]" required></td>
                    <td><button type="button" class="btn btn-danger remove-input-field"><i class="fa fa-times"></i></button></td>
                </tr>`;
      $("#inputTable").append(tr);

      // Inisialisasi autocomplete pada elemen "item-code" dalam baris baru
      var newRowItemCode = $(`#inputTable tr:last .item-code-${rowNumber}`);
      initializeAutocomplete(newRowItemCode, rowNumber);

      // Menambahkan event handler untuk mengatur sumber data saat input berubah
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
              for (var i = 0; i < data.length; i++) {
                items.push({
                  id: data[i].id
                  , label: data[i].item_code
                  , desc: data[i].description
                , });
              }
              // console.log(items);

              // Setel sumber data autocomplete untuk elemen yang sesuai dalam baris yang sama
              initializeAutocomplete(inputElement, rowNumber);
              inputElement.autocomplete("option", "source", items);

              // Setel nilai item-id dan description yang sesuai dalam baris yang berbeda
              itemIDElement.val(data[0].id);
              itemCodeElement.val(data[0].label);
              descriptionElement.val(data[0].description);
            } else {
              // Jika item tidak ditemukan, kosongkan sumber data autocomplete
              inputElement.autocomplete("option", "source", []);

              // Kosongkan nilai item-id dan description dalam baris yang sama
              itemIDElement.val("");
              itemCodeElement.val("");
              descriptionElement.val("");
            }
          }
        , });
      });

      // Tambahkan event handler untuk menghapus baris
      // Tambahkan event handler untuk menghapus baris
      $(document).on("click", ".remove-input-field", function() {
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
            .find(`.gi-qty-${newRowNumber}`)
            .attr("name", `gi_qty[${newRowNumber}]`);
          $(this)
            .find(`.gi-line-remarks-${newRowNumber}`)
            .attr("name", `gi_line_remarks[${newRowNumber}]`);
          newRowNumber++;
        });
      }

      // Menambahkan event handler untuk tombol .search-item-${rowNumber} yang memunculkan modal #itemModal sesuai nomor urut
      $(document).on("click", `.search-item-${rowNumber}`, function() {
        $("#itemModal").modal("show");
        var warehouseId = $('#warehouse_id').val();
        listItem(rowNumber, warehouseId);
      });

      // Handle item selection in the modal and update the corresponding row
      $("#datatable-serverside").on(
        "click"
        , `button.pick-item-${rowNumber}`
        , function() {
          var itemID = $(this).data("item-id");
          var itemCode = $(this).data("item-code");
          var description = $(this).data("description");

          // Temukan baris yang terkait dengan tombol "Cari" yang diklik
          // console.log('rowNumber: ' + rowNumber);
          // console.log('itemID: ' + itemID);
          // console.log('itemCode: ' + itemCode);
          // console.log('description: ' + description);

          // Update nilai item-id, item-code, dan description
          $(`.item-id-${rowNumber}`).val(itemID);
          $(`.item-code-${rowNumber}`).val(itemCode);
          $(`.description-${rowNumber}`).val(description);

          // Sembunyikan modal setelah memilih item
          $("#itemModal").modal("hide");
          $(`.item-code-${rowNumber}`).focus();
        }
      );

      // $(`.gi-qty-${rowNumber}`).parsley();
      $('#form').parsley();
      window.Parsley
        .addValidator(`checkstock${rowNumber}`, {
          validateNumber: function(value, requirements, instance) {
            var warehouse_id = $('#warehouse_id').val(); // Ganti dengan ID input warehouse
            var item_id = $(`.item-id-${rowNumber}`).val(); // Ganti dengan ID input item_id

            return $.ajax({
              url: "{{ route('inventories.checkStock') }}"
              , method: "POST"
              , data: {
                gi_qty: value
                , warehouse_id: warehouse_id
                , item_id: item_id
                , _token: '{!! csrf_token() !!}'
              }
              , dataType: "json"
              , success: function(data) {
                return data.success; // Kembalikan true atau false dari respons JSON
              }
            });
          }
          , messages: {
            en: 'Quantity falls into negative' // Pesan jika validasi gagal
          }
        });
    }

    // Fungsi autocomplete yang dapat digunakan kembali
    function initializeAutocomplete(elements, rowNumber) {
      elements
        .autocomplete({
          minLength: 0
          , source: []
          , focus: function(event, ui) {
            elements.val(ui.item.label);
            return false;
          }
          , select: function(event, ui) {
            var itemIDElement = elements.siblings(
              `.item-id-${rowNumber}`
            );
            var itemCodeElement = elements.siblings(
              `.item-code-${rowNumber}`
            );
            var descriptionElement = elements.siblings(
              `.description-${rowNumber}`
            );

            // Setel nilai .description dalam <td> yang berbeda
            var descriptionElementInRow = elements
              .closest("tr")
              .find(`.description-${rowNumber}`);
            descriptionElementInRow.val(ui.item.desc);

            itemIDElement.val(ui.item.id);
            itemCodeElement.val(ui.item.label);
            descriptionElement.val(ui.item.desc);

            return false;
          }
        , })
        .autocomplete("instance")._renderItem = function(ul, item) {
          return $("<li>")
            .addClass("autocomplete-item")
            .append("<div>" + item.label + "<br>" + item.desc + "</div")
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

              return `<button class="btn btn-sm btn-info pick-item-${rowNumber}" data-item-id="${itemId}" data-item-code="${itemCode}" data-description="${description}"><i class="fa fa-check-square-o"></i> Pick!</button>`;
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
          // , {
          //   data: "type_name"
          //   , name: "type_name"
          //   , orderable: false
          // , }
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
@endsection
