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
              <a href="{{ url('transfers') }}" class="btn btn-success"><i class="fa fa-arrow-circle-left"></i> Back</a>
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
            <form id="form" data-parsley-validate class="form-horizontal form-label-left" action="{{ route('transfers.store') }}" method="POST">
              @csrf
              <div class="col-md-6 col-xs-12 left-margin">
                <div class="form-group">
                  <label>Document No. <span class="required">*</span></label>
                  <input type="text" class="form-control" name="trf_doc_num" value="{{ $sessionData ? $sessionData['trf']['trf_doc_num'] : $trf_no }}" required readonly>
                </div>
                <div class="form-group">
                  <label>Posting Date <span class="required">*</span></label>
                  <input type="date" class="form-control" name="trf_posting_date" value="{{ $sessionData ? $sessionData['trf']['trf_posting_date'] : date('Y-m-d') }}" required>
                </div>
                {{-- <div class="form-group">
                  <label>Transfer Type <span class="required">*</span></label>
                  <select id="trf_type" class="select2 form-control" name="trf_type" style="width: 100%" required>
                    <option value="">Select Type</option>
                    <option value="out">OUT</option>
                    <option value="in">IN</option>
                  </select>
                </div>
                <div id="reference" class="form-group">
                  <label>Reference No.</label>
                  <div class="input-group">
                    <input id="trf_ref_num" type="text" class="form-control" name="trf_ref_num" value="{{ $sessionData ? $sessionData['trf']['trf_ref_num'] : "" }}" placeholder="ITO Number" readonly>
                <span class="input-group-btn">
                  <button type="button" class="btn btn-primary search-reference"><i class="fa fa-ellipsis-h"></i></button>
                </span>
              </div>
          </div> --}}
        </div>
        <div class="col-md-6 col-xs-12 left-margin">
          <div class="form-group">
            <label>From Warehouse <span class="required">*</span></label>
            <select id="from_warehouse" class="select2 form-control" name="trf_from" style="width: 100%" required>
              <option value="">Select Warehouse</option>
              @foreach ($warehouses as $warehouse)
              <option value="{{ $warehouse->id }}">{{ $warehouse->warehouse_name }} ({{ $warehouse->bouwheer->bouwheer_name }})</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label>To Warehouse <span class="required">*</span></label>
            <select id="to_warehouse" class="select2 form-control" name="trf_to" style="width: 100%" required data-parsley-check-warehouses>
              <option value="">Select Warehouse</option>
              @foreach ($warehouses as $warehouse)
              <option value="{{ $warehouse->id }}">{{ $warehouse->warehouse_name }} ({{ $warehouse->bouwheer->bouwheer_name }})</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label>Remarks</label>
            <textarea id="trf_remarks" class="form-control" rows="3" name="trf_remarks" required>{{ $sessionData ? $sessionData['trf']['trf_remarks'] : "" }}</textarea>
          </div>
        </div>
        {{-- inventory detail --}}
        <div class="col-md-12 col-xs-12 left-margin">
          <div class="x_panel">
            <div class="x_title">
              <h2>Inventory Transfer Detail</h2>
              <ul class="nav navbar-right panel_toolbox"></ul>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
              <div class="table-responsive">
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

{{-- list ITO --}}
{{-- <div id="referenceModal" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">List of Inventory Transfer OUT</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="x_content">
        <div class="modal-body">
          <table id="datatable" class="table jambo_table" width="100%">
            <thead>
              <tr class="headings">
                <th class="column-title">No</th>
                <th class="column-title">Document No</th>
                <th class="column-title">Posting Date</th>
                <th class="column-title">From Whs</th>
                <th class="column-title">To Whs</th>
                <th class="column-title text-center" width="10%">Action</th>
              </tr>
            </thead>
            @foreach ($transferOuts as $out)
            <tbody>
              <tr class="lists">
                <td>{{ $loop->iteration }}</td>
<td>{{ $out->trf_doc_num }}</td>
<td>{{ $out->trf_posting_date }}</td>
<td>{{ $out->fromWarehouse->warehouse_name }}</td>
<td>{{ $out->toWarehouse->warehouse_name }}</td>
<td class="text-center">
  <button type="button" class="btn btn-primary btn-sm pick-ref" data-transferid="{{ $out->id }}"><i class="fa fa-check-square-o"></i> Pick!</button>
</td>
</tr>
</tbody>
@endforeach
</table>
</div>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>
</div>
</div>
</div> --}}

{{-- list item --}}
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
                <th class="column-title text-center" width="1%">Action</th>
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
  $(document).ready(function() {
    $(".select2").select2();

    $(document).on("select2:open", () => {
      document.querySelector(".select2-search__field").focus();
    });

    // hide id reference on load
    // $("#reference").hide();
    // if ($("#trf_type").val() === "in") {
    //   $("#reference").show();
    // }
    // $('#trf_type').on('change', function() {
    //   var trfTypeValue = $(this).val();
    //   var referenceDiv = $('#reference');
    //   var trfRefNumInput = $('#trf_ref_num');

    //   // Tampilkan atau sembunyikan elemen reference berdasarkan nilai trf_type
    //   if (trfTypeValue === 'in') {
    //     referenceDiv.show();
    //     trfRefNumInput.attr('required', true);
    //   } else {
    //     referenceDiv.hide();
    //     trfRefNumInput.removeAttr('required');
    //     $("#trf_ref_num").val("");
    //     $("#from_warehouse").val("").trigger("change");
    //     $("#to_warehouse").val("").trigger("change");
    //     resetRows(2);
    //     addItemDetail(1);
    //     var rowCount = 2;
    //   }

    //   $.ajax({
    //     type: "POST"
    //     , url: "{{ route('transfers.listwarehouses') }}"
    //     , data: {
    //       _token: '{{ csrf_token() }}'
    //       , trf_type: trfTypeValue
    //     }
    //     , success: function(data) {
    //       $("#from_warehouse").html(data.list_from_warehouses).show();
    //       $("#to_warehouse").html(data.list_to_warehouses).show();
    //     }
    //     , error: function(xhr, ajaxOptions, thrownError) { // Ketika ada error
    //       alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError); // Munculkan alert error
    //     }
    //   })
    // });

    // $(document).on("click", `.search-reference`, function() {
    //   $("#referenceModal").modal("show");
    // });

    // $("#datatable").on("click", ".pick-ref", function() {
    //   var transferId = $(this).data("transferid");
    //   getTransferReference(transferId);
    //   $("#referenceModal").modal("hide");
    // });

    // function resetRows(num) {
    //   $("#inputTable tbody tr").remove();
    //   rowCount = num; // Reset nomor baris
    // }

    // function getTransferReference(transferId) {
    //   $.ajax({
    //     url: "{{ route('transfers.getTransferReference') }}"
    //     , type: "POST"
    //     , data: {
    //       id: transferId
    //       , _token: '{{ csrf_token() }}'
    //     }
    //     , dataType: "json"
    //     , success: function(data) {
    //       resetRows(1);

    //       console.log(data.transfer);
    //       $("#trf_ref_num").val(data.transfer.trf_doc_num);
    //       $("#from_warehouse").val(data.transfer.to_warehouse.id).trigger("change");
    //       $("#trf_remarks").val(data.transfer.trf_remarks);

    //       // tambahkan item-item ke addItemDetail
    //       data.transfer.trfdetails.forEach(function(item) {
    //         addItemDetail(rowCount, item);
    //         rowCount++;
    //       })
    //     }
    //   })
    // }

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
                    <td><input type="text" class="form-control description-${rowNumber}" value="${item ? item.item.description : ''}" readonly></td>
                    <td><input type="number" class="form-control trf-qty-${rowNumber}" name="trf_qty[${rowNumber}]" value="${item ? item.trf_qty : ''}" required data-parsley-min="1" data-parsley-trigger="keyup" data-parsley-checkstock${rowNumber}></td>
                    <td><input type="text" class="form-control trf-line-remarks-${rowNumber}" name="trf_line_remarks[${rowNumber}]" value="${item ? item.trf_line_remarks : ''}" required></td>
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
            .find(`.trf-qty-${newRowNumber}`)
            .attr("name", `trf_qty[${newRowNumber}]`);
          $(this)
            .find(`.trf-line-remarks-${newRowNumber}`)
            .attr("name", `trf_line_remarks[${newRowNumber}]`);
          newRowNumber++;
        });
      }

      // Menambahkan event handler untuk tombol .search-item-${rowNumber} yang memunculkan modal #itemModal sesuai nomor urut
      $(document).on("click", `.search-item-${rowNumber}`, function() {
        $("#itemModal").modal("show");
        var warehouseId = $('#from_warehouse').val();
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

      // add parsley validation
      $('#form').parsley();

      // parsley from_warehouse and to_warehouse cannot be same
      window.Parsley.addValidator('checkWarehouses', {
        validateString: function(value) {
          return $('#from_warehouse').val() !== $('#to_warehouse').val();
        }
        , messages: {
          en: 'Cannot transfer to same warehouse'
        }
      });

      // parsley check stock validation
      window.Parsley
        .addValidator(`checkstock${rowNumber}`, {
          validateNumber: function(value, requirements, instance) {
            var warehouse_id = $('#from_warehouse').val(); // Ganti dengan ID input warehouse
            var item_id = $(`.item-id-${rowNumber}`).val(); // Ganti dengan ID input item_id

            return $.ajax({
              url: "{{ route('inventories.checkStock') }}"
              , method: "POST"
              , data: {
                trf_qty: value
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
