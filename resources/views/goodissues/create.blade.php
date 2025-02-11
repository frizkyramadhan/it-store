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
                <div class="form-group">
                  <label>MR No Reference</label>
                  <div class="input-group">
                    <input type="text" class="form-control" id="mr_no" value="{{ $mr ? $mr->mr_doc_num : "" }}" readonly />
                    <input type="hidden" class="form-control" id="mr_id" name="mr_id" value="{{ $mr ? $mr->id : "" }}" />
                    <span class="input-group-btn">
                      <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#mrModal"><i class="fa fa-search"></i></button>
                    </span>
                  </div>
                </div>
                <div class="form-group">
                  <label>Warehouse <span class="required">*</span></label>
                  <select id="warehouse_id" class="select2 form-control" name="warehouse_id" style="width: 100%" required>
                    <option value="">Select Warehouse</option>
                    @foreach ($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}">{{ $warehouse->warehouse_name }} ({{ $warehouse->bouwheer->bouwheer_name }})</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-md-6 col-xs-12 left-margin">
                <div class="form-group">
                  <label>Project <span class="required">*</span></label>
                  <select id="project_id" class="select2 form-control" name="project_id" style="width: 100%" required>
                    <option value="">Select Project</option>
                    @foreach ($projects as $project)
                    <option value="{{ $project->id }}">{{ $project->project_code }} - {{ $project->project_name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group">
                  <label>Issue Purpose <span class="required">*</span></label>
                  <select id="issue_purpose_id" class="select2 form-control" name="issue_purpose_id" style="width: 100%" required>
                    <option value="">Select Issue Purpose</option>
                    @foreach ($issuepurposes as $issuepurpose)
                    <option value="{{ $issuepurpose->id }}">{{ $issuepurpose->purpose_name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group">
                  <label>IT WO Reference</label>
                  <div class="input-group">
                    <input type="text" class="form-control" id="it_wo_no" value="" readonly />
                    <input type="hidden" class="form-control" id="it_wo_id" name="it_wo_id" value="" />
                    <span class="input-group-btn">
                      <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#itwoModal"><i class="fa fa-search"></i></button>
                    </span>
                  </div>
                </div>

                <div class="form-group">
                  <label>Remarks</label>
                  <textarea class="form-control" rows="2" name="gi_remarks" required></textarea>
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
                            <th class="column-title" style="vertical-align: middle" width="25%">Item Code</th>
                            <th class="column-title" style="vertical-align: middle" width="25%">Description</th>
                            <th class="column-title" style="vertical-align: middle" width="10%">Quantity</th>
                            <th class="column-title" style="vertical-align: middle" width="10%">Price (IDR)</th>
                            <th class="column-title" style="vertical-align: middle" width="10%">Total</th>
                            <th class="column-title" style="vertical-align: middle">Line Remarks</th>
                            <th class="column-title text-center" style="vertical-align: middle" width="5%"><button type="button" id="dynamic-ar" class="btn btn-primary"><i class="fa fa-plus"></i></button></th>
                          </tr>
                        </thead>
                      </table>
                      <div class="form-group text-right">
                        <label>Total Cost (IDR)</label>
                        <input id="total_cost" type="text" class="form-control" name="total_cost" readonly />
                        <input type="hidden" class="form-control" name="gi_status" value="open" />
                      </div>
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

<div id="mrModal" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
        </button>
        <h4 class="modal-title">List of Material Request</h4>
      </div>
      <div class="modal-body">
        <table id="mr_table" class="table table-striped jambo_table" width="100%">
          <thead>
            <tr class="headings">
              <th class="column-title" width="1%" class="text-center">Action</th>
              <th class="column-title">MR No</th>
              <th class="column-title">Date</th>
              <th class="column-title">Project</th>
              <th class="column-title">Issue Purpose</th>
              <th class="column-title">IT WO No.</th>
              <th class="column-title">Remarks</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($materialRequests as $materialRequest)
            <tr>
              <td>
                <button class="btn btn-sm btn-info pick-mr" data-mr-id="{{ $materialRequest->id }}" data-mr-no="{{ $materialRequest->mr_doc_num }}">
                  <i class="fa fa-check-square-o"></i> Pick!
                </button>
              </td>
              <td>{{ $materialRequest->mr_doc_num }}</td>
              <td>{{ $materialRequest->mr_posting_date }}</td>
              <td>{{ $materialRequest->project->project_code }}</td>
              <td>{{ $materialRequest->issuepurpose->purpose_name }}</td>
              <td>{{ $materialRequest->it_wo_no }}</td>
              <td>{{ $materialRequest->mr_remarks }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div id="itwoModal" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
        </button>
        <h4 class="modal-title">Search IT WO</h4>
      </div>
      <div class="modal-body">
        <div class="col-md-3 col-sm-12 col-xs-12 form-group">
          <input id="date" type="date" name="date" placeholder="Date" class="form-control" value="{{ @$post['date'] }}">
        </div>
        <div class="col-md-3 col-sm-12 col-xs-12 form-group">
          {{-- <input id="kode_project" type="text" name="kode_project" placeholder="Project" class="form-control" value="{{ @$post['kode_project'] }}"> --}}
          <select id="kode_project" class="form-control" name="kode_project" style="width: 100%">
            <option value="">Select Project</option>
            @foreach ($projects as $project)
            <option value="{{ $project->project_code }}">{{ $project->project_code }} - {{ $project->project_name }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-md-3 col-sm-12 col-xs-12 form-group">
          <input id="nik" type="text" name="nik" placeholder="NIK" class="form-control" value="{{ @$post['nik'] }}">
        </div>

        <div class="col-md-3 col-sm-12 col-xs-12 form-group">
          <input id="name" type="text" name="name" placeholder="Name" class="form-control" value="{{ @$post['name'] }}">
        </div>

        <div class="col-md-3 col-sm-12 col-xs-12 form-group">
          <input id="no_wo" type="text" name="no_wo" placeholder="IT WO No." class="form-control" value="{{ @$post['no_wo'] }}">
        </div>

        <div class="col-md-3 col-sm-12 col-xs-12 form-group">
          <input id="issue" type="text" name="issue" placeholder="Issue" class="form-control" value="{{ @$post['issue'] }}">
        </div>

        <div class="col-md-3 col-sm-12 col-xs-12 form-group">
          {{-- <input id="status" type="text" name="status" placeholder="Status" class="form-control" value="{{ @$post['status'] }}"> --}}
          <select id="status" name="status" class="form-control">
            <option value="">Select Status</option>
            <option value="waiting" {{ @$post['status'] == 'waiting' ? 'selected' : '' }}>Waiting</option>
            <option value="process" {{ @$post['status'] == 'process' ? 'selected' : '' }}>Process</option>
            <option value="finished" {{ @$post['status'] == 'finished' ? 'selected' : '' }}>Finished</option>
            <option value="canceled" {{ @$post['status'] == 'canceled' ? 'selected' : '' }}>Canceled</option>
          </select>
        </div>

        <div class="col-md-3 col-sm-12 col-xs-12 form-group">
          <div class="text-center">
            <button id="search_button" type="submit" class="btn btn-success">Search</button>
            <button id="reset_button" class="btn btn-primary" type="reset">Reset</button>
          </div>
        </div>

        <div id="search_result">
        </div>
        <div id="error" class="col-md-12 col-sm-12 col-xs-12 form-group">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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

    // Apply right alignment to numeric inputs
    function applyNumericInputStyles() {
      $("[class*='gi-qty-']").css('text-align', 'right');
      $("[class*='price-']").css('text-align', 'right');
      $("[class*='gi-line-total-']").css('text-align', 'right');
      $("#total_cost").css('text-align', 'right');
    }

    // Apply styles initially
    applyNumericInputStyles();

    $(document).on('click', '.pick-mr', function() {
      const mrId = $(this).data('mr-id');
      const mrNo = $(this).data('mr-no');

      // Set values to the input fields
      $('#mr_id').val(mrId);
      $('#mr_no').val(mrNo);

      // Get MR reference data
      getMrReference(mrId);

      // Close the modal
      $('#mrModal').modal('hide');
    });

    // Function to format number with thousand separator
    function formatRupiah(angka) {
      let number_string = angka.toString().replace(/[^,\d]/g, '')
        , split = number_string.split(',')
        , sisa = split[0].length % 3
        , rupiah = split[0].substr(0, sisa)
        , ribuan = split[0].substr(sisa).match(/\d{3}/gi);

      if (ribuan) {
        separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
      }

      rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
      return rupiah;
    }

    // Function to parse formatted number back to float
    function parseRupiah(rupiah) {
      return parseFloat(rupiah.replace(/[^\d]/g, ''));
    }

    // Variabel untuk melacak nomor baris
    addItemDetail(1); // Tambahkan baris pertama onload
    var rowCount = 2; // untuk row berikutnya saat di klik

    // Fungsi untuk menambahkan baris
    $("#dynamic-ar").on("click", function() {
      addItemDetail(rowCount);
      rowCount++; // Tingkatkan nomor baris setiap kali menambahkan baris
    });

    // Modify calculateLineTotal function
    function calculateLineTotal(rowNumber) {
      var qty = parseFloat($(`.gi-qty-${rowNumber}`).val()) || 0;
      var price = parseRupiah($(`.price-${rowNumber}`).val()) || 0;
      var total = qty * price;

      // Update price with formatted value
      $(`.price-${rowNumber}`).val(formatRupiah(price));

      // Update gi_line_total with formatted value
      $(`.gi-line-total-${rowNumber}`).val(formatRupiah(total));

      // Calculate total cost
      calculateTotalCost();
    }

    // Modify calculateTotalCost function
    function calculateTotalCost() {
      var totalCost = 0;

      $("[class*='gi-line-total-']").each(function() {
        var lineTotal = parseRupiah($(this).val()) || 0;
        totalCost += lineTotal;
      });

      $("#total_cost").val(formatRupiah(totalCost));
    }


    // Attach event listeners untuk gi_qty dan price pada setiap baris yang baru ditambahkan
    function attachCalculationEvents(rowNumber) {
      // Saat input gi_qty diubah
      $(`.gi-qty-${rowNumber}`).on('keyup change', function() {
        $(this).val(formatRupiah($(this).val()));
        calculateLineTotal(rowNumber);
      });

      // Saat input price diubah
      $(`.price-${rowNumber}`).on('keyup change', function() {
        $(this).val(formatRupiah($(this).val()));
        calculateLineTotal(rowNumber);
      });
    }

    // Fungsi untuk menambahkan baris baru
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
                    <td><input type="text" class="form-control gi-qty-${rowNumber}" name="gi_qty[${rowNumber}]" required data-parsley-min="1" data-parsley-trigger="keyup" data-parsley-checkstock${rowNumber}autocomplete="off"></td>
                    <td><input type="text" class="form-control price-${rowNumber}" name="price[${rowNumber}]" required autocomplete="off"></td>
                    <td><input type="text" class="form-control gi-line-total-${rowNumber}" name="gi_line_total[${rowNumber}]" readonly></td>
                    <td><input type="text" class="form-control gi-line-remarks-${rowNumber}" name="gi_line_remarks[${rowNumber}]"></td>
                    <td><button type="button" class="btn btn-danger remove-input-field"><i class="fa fa-times"></i></button></td>
                </tr>`;
      $("#inputTable").append(tr);

      // Attach event untuk kalkulasi otomatis pada gi_qty dan price
      attachCalculationEvents(rowNumber);

      // Apply right alignment to new numeric inputs
      applyNumericInputStyles();

      // Hitung ulang total setelah baris baru ditambahkan
      calculateTotalCost();

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
        calculateTotalCost();
      });

      function updateRowNumbers() {
        var newRowNumber = 1;
        $("#inputTable tr").each(function() {
          $(this)
            .find('.item-id-' + newRowNumber)
            .attr("name", `item_id[${newRowNumber}]`);
          $(this)
            .find('.item-code-' + newRowNumber)
            .attr("name", `item_code[${newRowNumber}]`);
          $(this)
            .find('.gi-qty-' + newRowNumber)
            .attr("name", `gi_qty[${newRowNumber}]`);
          $(this)
            .find('.price-' + newRowNumber)
            .attr("name", `price[${newRowNumber}]`);
          $(this)
            .find('.gi-line-total-' + newRowNumber)
            .attr("name", `gi_line_total[${newRowNumber}]`);
          $(this)
            .find('.gi-line-remarks-' + newRowNumber)
            .attr("name", `gi_line_remarks[${newRowNumber}]`);
          newRowNumber++;
        });

        // Setelah update, hitung ulang total biaya
        calculateTotalCost();
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

      // Add validator first, outside the form submit handler
      window.Parsley.addValidator(`checkstock${rowNumber}`, {
        validateNumber: function(value, requirements, instance) {
          var warehouse_id = $('#warehouse_id').val();
          var item_id = $(`.item-id-${rowNumber}`).val();

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
              return data.success;
            }
          });
        }
        , messages: {
          en: 'Quantity falls into negative'
        }
      });

      // Then handle the form submission
      $('#form').parsley().on('form:submit', function() {
        // Remove formatting from all price inputs
        $("[class*='price-']").each(function() {
          $(this).val(parseRupiah($(this).val()));
        });

        // Remove formatting from all line total inputs
        $("[class*='gi-line-total-']").each(function() {
          $(this).val(parseRupiah($(this).val()));
        });

        // Remove formatting from total cost
        $("#total_cost").val(parseRupiah($("#total_cost").val()));

        return true; // Allow form submission to continue
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

    $('#search_button').on('click', function() {
      searchItwo();
    });

    // Pilih item ketika tombol "Pick!" diklik
    $(document).on('click', '.pick-item', function() {
      const woId = $(this).data('wo-id'); // Mengambil data ID IT WO
      const woNo = $(this).data('wo-no'); // Mengambil data Nomor IT WO

      // Set nilai input it_wo_id dan it_wo_no dengan item yang dipilih
      $('#it_wo_id').val(woId);
      $('#it_wo_no').val(woNo);

      // Tutup modal setelah item dipilih
      $('#itwoModal').modal('hide');
    });

    function searchItwo() {
      $('#search_result').html(''); // Kosongkan hasil pencarian sebelumnya

      $.ajax({
        url: 'http://192.168.32.37/arka-rest-server/api/it_wo_store'
        , type: 'GET'
        , datatype: 'json'
        , data: {
          'arka-key': 'arka123'
          , 'date': $('#date').val()
          , 'kode_project': $('#kode_project').val()
          , 'nik': $('#nik').val()
          , 'name': $('#name').val()
          , 'no_wo': $('#no_wo').val()
          , 'issue': $('#issue').val()
          , 'status': $('#status').val()
        }
        , success: function(result) {
          if (result.status) {
            const itwoList = result.data;
            let html = '';

            html += `
          <div class="col-md-12 col-sm-12 col-xs-12 form-group">
            <table class="table table-striped jambo_table" width="100%">
            <thead>
              <tr class="headings">
                <th class="column-title" width="1%" class="text-center">Action</th>
                <th class="column-title">Date</th>
                <th class="column-title">Project</th>
                <th class="column-title">NIK</th>
                <th class="column-title">Name</th>
                <th class="column-title">IT WO</th>
                <th class="column-title">Issue</th>
                <th class="column-title">Status</th>
              </tr>
            </thead>
            <tbody>`;

            // Looping untuk menambahkan baris data
            $.each(itwoList, (i, data) => {
              html += `
            <tr>
              <td class="text-center"><button class="btn btn-sm btn-info pick-item" data-wo-id="${data.id_wo}" data-wo-no="${data.no_wo}"><i class="fa fa-check-square-o"></i> Pick!</button></td>
              <td>${data.date}</td>
              <td>${data.kode_project}</td>
              <td>${data.nik}</td>
              <td>${data.name}</td>
              <td>${data.no_wo}</td>
              <td>${data.issue}</td>
              <td class="text-center">${data.status}</td>
            </tr>`;
            });

            html += `</tbody>
          </table>
          </div>`;

            // Tampilkan hasil pencarian
            $('#search_result').append(html);
          } else {
            $('#error').html(`
            <div class="alert alert-warning alert-dismissible fade in" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
              </button>
              IT WO Not Found, Please Try Another Keyword!
            </div>
            `);
          }
        }
      });
    }

    // function untuk reset
    $('#reset_button').on('click', function() {
      resetSearch();
    });

    function resetSearch() {
      // Kosongkan semua input
      $('#date').val('');
      $('#kode_project').val('');
      $('#nik').val('');
      $('#name').val('');
      $('#no_wo').val('');
      $('#issue').val('');
      $('#status').val('');

      // Hapus hasil pencarian
      $('#search_result').html('');
      $('#error').html('');
    }

    // Agar semua inputan di dalam modal bisa disubmit dengan menekan enter
    $('#itwoModal').find('input').each(function() {
      $(this).on('keypress', function(e) {
        if (e.which === 13) {
          $('#search_button').click();
        }
      });
    });

    @if(isset($mr))
    getMrReference(`{{ $mr->id}}`);
    @endif

    function getMrReference(mrId) {
      $.ajax({
        url: "{{ url('goodsissues/get-mr-reference') }}/" + mrId
        , type: "GET"
        , dataType: "json"
        , success: function(data) {
          if (data.success) {
            // Reset existing table rows
            $("#inputTable tr:not(:first)").remove();

            // Set header/main form values
            $('#warehouse_id').val(data.data.warehouse_id).trigger('change');
            $('#project_id').val(data.data.project_id).trigger('change');
            $('#issue_purpose_id').val(data.data.issue_purpose_id).trigger('change');
            $('#it_wo_id').val(data.data.it_wo_id);
            $('#it_wo_no').val(data.data.it_wo_no);
            $('textarea[name="gi_remarks"]').val(data.data.mr_remarks);

            // Add MR items to table
            data.data.mrdetails.forEach(function(item, index) {
              // Change to use index + 1 for rowNumber
              const rowNumber = index + 1;

              // Add new row
              const tr = `<tr>
              <td>
                <div class="input-group">
                  <input type="hidden" class="form-control item-id-${rowNumber}" name="item_id[${rowNumber}]" value="${item.item_id}" required>
                  <input type="text" class="form-control item-code-${rowNumber}" name="item_code[${rowNumber}]" value="${item.item.item_code}" required readonly>
                  <span class="input-group-btn">
                    <button type="button" class="btn btn-primary search-item-${rowNumber}"><i class="fa fa-search"></i></button>
                  </span>
                </div>
              </td>
              <td><input type="text" class="form-control description-${rowNumber}" value="${item.item.description}" readonly></td>
              <td><input type="text" class="form-control gi-qty-${rowNumber}" name="gi_qty[${rowNumber}]" value="${item.mr_qty}" required data-parsley-min="1" data-parsley-trigger="keyup" data-parsley-checkstock${rowNumber} autocomplete="off"></td>
              <td><input type="text" class="form-control price-${rowNumber}" name="price[${rowNumber}]" required autocomplete="off"></td>
              <td><input type="text" class="form-control gi-line-total-${rowNumber}" name="gi_line_total[${rowNumber}]" readonly></td>
              <td><input type="text" class="form-control gi-line-remarks-${rowNumber}" name="gi_line_remarks[${rowNumber}]" value="${item.mr_line_remarks || ''}"></td>
              <td><button type="button" class="btn btn-danger remove-input-field"><i class="fa fa-times"></i></button></td>
            </tr>`;

              $("#inputTable").append(tr);

              // Attach event untuk kalkulasi otomatis pada gi_qty dan price
              attachCalculationEvents(rowNumber);

              // Apply right alignment to new numeric inputs
              applyNumericInputStyles();

              // Hitung ulang total setelah baris baru ditambahkan
              calculateTotalCost();

              // Inisialisasi autocomplete pada elemen "item-code" dalam baris baru
              initializeAutocomplete($(`.item-code-${rowNumber}`), rowNumber);

              // Add Parsley validation for stock check
              window.Parsley
                .addValidator(`checkstock${rowNumber}`, {
                  validateNumber: function(value, requirements, instance) {
                    var warehouse_id = $('#warehouse_id').val();
                    var item_id = $(`.item-id-${rowNumber}`).val();

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
                    });
                  }
                  , messages: {
                    en: 'Quantity falls into negative'
                  }
                });
            });

            // Update global rowCount to match the number of items
            rowCount = data.data.mrdetails.length + 1;

            // Calculate initial totals
            calculateTotalCost();
          } else {
            console.error("Error loading MR reference data");
          }
        }
        , error: function(xhr, status, error) {
          console.error("AJAX Error:", error);
        }
      });
    }
  });

</script>
@endsection
