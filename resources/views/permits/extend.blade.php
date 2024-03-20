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
            <form data-parsley-validate class="form-horizontal form-label-left" action="{{ route('permits.extendSubmit') }}" method="POST">
              @csrf
              <div class="col-md-6 col-xs-12 left-margin">
                <div class="form-group">
                  <label>Permit No. <span class="required">*</span></label>
                  <input type="text" class="form-control" name="permit_no" value="{{ $permit->permit_no }}" required>
                </div>
                <div class="form-group">
                  <label>Permit Date <span class="required">*</span></label>
                  <input type="date" class="form-control" name="permit_date" value="{{ $permit->permit_date }}" required>
                </div>
                <div class="form-group">
                  <label>Valid Month <span class="required">*</span></label>
                  <input type="number" class="form-control" name="valid_month" value="{{ $permit->valid_month }}" required="required" data-validate-minmax="1,100" min="1" data-parsley-min="1">
                </div>
              </div>
              <div class="col-md-6 col-xs-12 left-margin">
                <div class="form-group">
                  <label>Permit Type <span class="required">*</span></label>
                  <select id="vendor" class="select2 form-control" name="permit_type" style="width: 100%" required>
                    <option value="">Select Permit Type</option>
                    <option value="P1" {{ $permit->permit_type == "P1" ? "selected" : "" }}>P1</option>
                    <option value="P2" {{ $permit->permit_type == "P2" ? "selected" : "" }}>P2</option>
                    <option value="P3" {{ $permit->permit_type == "P3" ? "selected" : "" }}>P3</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Warehouse <span class="required">*</span></label>
                  <select id="warehouse_id" class="select2 form-control" name="warehouse_id" style="width: 100%" required>
                    <option value="">Select Warehouse</option>
                    @foreach ($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}" {{ $permit->warehouse_id == $warehouse->id ? "selected" : "" }}>
                      {{ $warehouse->warehouse_name }} ({{ $warehouse->bouwheer->bouwheer_name }})
                    </option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group">
                  <label>Previous No. <span class="required">*</span></label>
                  <input id="permit_id" type="hidden" name="permit_id" class="form-control" value="{{ $permit->id }}">
                  <input type="text" class="form-control" value="{{ $permit->permit_no }}" readonly>
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
                            <th class="column-title" style="vertical-align: middle" width="25%">Item Code</th>
                            <th class="column-title" style="vertical-align: middle" width="25%">Description</th>
                            <th class="column-title" style="vertical-align: middle" width="10%">Quantity</th>
                            <th class="column-title" style="vertical-align: middle">Line Remarks</th>
                            <th class="column-title text-right" style="vertical-align: middle" width="10%"><button type="button" id="dynamic-ar" class="btn btn-primary"><i class="fa fa-plus"></i></button></th>
                          </tr>
                        </thead>
                      </table>
                    </div>
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
<script>
  // Set variabel global untuk data-type item transaction
  // window.dataType = "gr";

  $(document).ready(function() {
    $(".select2").select2();

    $(document).on("select2:open", () => {
      document.querySelector(".select2-search__field").focus();
    });

    getExtendReference("#permit_id");

    function resetRows(num) {
      $("#inputTable tbody tr").remove();
      rowCount = num; // Reset nomor baris
    }

    function getExtendReference(permitId) {
      $.ajax({
        url: "{{ route('permits.getExtendReference', ['id' => $permit->id]) }}"
        , type: "GET"
        , dataType: "json"
        , success: function(data) {
          resetRows(1);

          console.log(data.permit);

          // tambahkan item-item ke addItemDetail
          data.permit.permitdetails.forEach(function(item) {
            addItemDetail(rowCount, item);
            rowCount++;
          })
        }
      })
    }

    // Variabel untuk melacak nomor baris
    addItemDetail(1); // Tambahkan baris pertama onload
    var rowCount = 2; // untuk row berikutnya saat di klik

    // Fungsi untuk menambahkan baris
    $("#dynamic-ar").on("click", function() {
      addItemDetail(rowCount);
      rowCount++; // Tingkatkan nomor baris setiap kali menambahkan baris
    });

    function addItemDetail(rowNumber, item) {
      var tr = `<tr>
                    <td>
                    <div class="input-group">
                        <input type="hidden" class="form-control item-id-${rowNumber}" name="item_id[${rowNumber}]" placeholder="${rowNumber}" value="${item ? item.item_id : ''}" required>
                        <input type="text" class="form-control item-code-${rowNumber}" name="item_code[${rowNumber}]" value="${item ? item.item.item_code : ''}" required>
                        <span class="input-group-btn">
                        <button type="button" class="btn btn-primary search-item-${rowNumber}"><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                    </td>
                    <td>
                      <input type="text" class="form-control description-${rowNumber}" value="${item ? item.item.description : ''}" readonly>
                    </td>
                    <td><input type="number" class="form-control si-qty-${rowNumber}" name="si_qty[${rowNumber}]" value="${item ? item.si_qty : ''}" required data-parsley-min="1"></td>
                    <td><input type="text" class="form-control si-line-remarks-${rowNumber}" name="si_line_remarks[${rowNumber}]" value="${item ? item.si_line_remarks : ''}" required></td>
                    <td class="text-right"><button type="button" class="btn btn-danger remove-input-field-${rowNumber}"><i class="fa fa-times"></i></button></td>
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
            .find(`.si-qty-${newRowNumber}`)
            .attr("name", `si_qty[${newRowNumber}]`);
          $(this)
            .find(`.si-line-remarks-${newRowNumber}`)
            .attr("name", `si_line_remarks[${newRowNumber}]`);
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
              isBatchElement.val("");
            }
          }
        , });
      });

      // LIST ALL ITEM FROM MODAL AND DATATABLE METHOD
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

          // Update nilai item-id, item-code, dan description
          $(`.item-id-${rowNumber}`).val(itemID);
          $(`.item-code-${rowNumber}`).val(itemCode);
          $(`.description-${rowNumber}`).val(description);

          // Sembunyikan modal setelah memilih item
          $("#itemModal").modal("hide");
          $(`.item-code-${rowNumber}`).focus();
        }
      );
    }

    // Fungsi autocomplete yang dapat digunakan kembali
    function initializeAutocomplete(elements, rowNumber) {
      elements.autocomplete({
        minLength: 0
        , source: []
        , focus: function(event, ui) {
          elements.val(ui.item.label);
          return false;
        }
        , select: function(event, ui) {
          var itemIDElement = elements.siblings(`.item-id-${rowNumber}`);
          var itemCodeElement = elements.siblings(`.item-code-${rowNumber}`);
          var descriptionElement = elements.siblings(`.description-${rowNumber}`);

          // Setel nilai .description dalam <td> yang berbeda
          var descriptionElementInRow = elements.closest("tr").find(`.description-${rowNumber}`);
          descriptionElementInRow.val(ui.item.desc);

          itemIDElement.val(ui.item.id);
          itemCodeElement.val(ui.item.label);
          descriptionElement.val(ui.item.desc);

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
@endsection
