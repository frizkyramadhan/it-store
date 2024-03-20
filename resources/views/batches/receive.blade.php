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
            <form id="form" data-parsley-validate class="form-horizontal form-label-left" action="{{ route('batches.receive') }}" method="POST">
              @csrf
              <div class="col-md-12 col-xs-12 left-margin">
                <table class="table jambo_table" width="100%">
                  <thead>
                    <tr class="headings">
                      <th>Doc No.</th>
                      <th>Item Code</th>
                      <th>Description</th>
                      <th>Whse Name</th>
                      <th>Total Needed</th>
                    </tr>
                  </thead>
                  @if ($items->count() == 0)
                  <tbody>
                    <tr>
                      <td colspan="5" class="text-center">No Batch Item Selected</td>
                    </tr>
                  </tbody>
                  @else
                  @foreach ($items as $item)
                  <tbody>
                    <tr>
                      <td class="column-title" style="vertical-align: middle">{{ $sessionData['gr']['gr_doc_num'] }}</td>
                      <td class="column-title" style="vertical-align: middle">{{ $item->item_code }}</td>
                      <td class="column-title" style="vertical-align: middle">{{ $item->description }}</td>
                      <td class="column-title" style="vertical-align: middle">{{ $warehouse->warehouse_name }}</td>
                      <td class="column-title" style="vertical-align: middle">{{ $sessionData['gr']['gr_qty'][$loop->iteration] }}</td>
                    </tr>
                  </tbody>
                  @endforeach
                  @endif
                </table>
              </div>

              @if ($items->count() != 0)
              <div class="col-md-12 col-xs-12 left-margin">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Batch Created</h2>
                    <ul class="nav navbar-right panel_toolbox"></ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <div class="table-responsive">
                      <table id="inputTable" class="table table-striped jambo_table" width="100%">
                        <thead>
                          <tr class="headings">
                            <th class="column-title" style="vertical-align: middle" width="15%">Item Code</th>
                            <th class="column-title" style="vertical-align: middle" width="15%">Batch No</th>
                            <th class="column-title" style="vertical-align: middle">MFG Date</th>
                            <th class="column-title" style="vertical-align: middle" width="8%">Qty</th>
                            <th class="column-title" style="vertical-align: middle">Remarks</th>
                            <th class="column-title" style="vertical-align: middle">Assign To</th>
                            <th class="column-title text-center" style="vertical-align: middle" width="5%"><button type="button" id="dynamic-ar" class="btn btn-primary"><i class="fa fa-plus"></i></button></th>
                          </tr>
                        </thead>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
              @endif
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

@endsection

@section('styles')
<!-- iCheck -->
<link href="{{ asset('assets/vendors/iCheck/skins/flat/green.css') }}" rel="stylesheet">
<!-- Jquery UI -->
<link href="{{ asset('assets/vendors/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet">
@endsection

@section('scripts')
<!-- iCheck -->
<script src="{{ asset('assets/vendors/iCheck/icheck.min.js') }}"></script>
<!-- Parsley -->
<script src="{{ asset('assets/vendors/parsleyjs/dist/parsley.min.js') }}"></script>
<!-- Jquery UI -->
<script src="{{ asset('assets/vendors/jquery-ui/jquery-ui.js') }}"></script>
{{-- <script src="{{ asset('assets/build/js/itemTransaction.js') }}"></script> --}}
<script>
  $(document).ready(function() {
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
                      <select class="select2 form-control item-id-[${rowNumber}]" name="item_id[${rowNumber}]" style="width: 100%" required>
                        <option value="">Select Item Code</option>
                        @foreach ($items as $item)
                        <option value="{{ $item->id }}">{{ $item->item_code }}</option>
                        @endforeach
                      </select>
                    </td>
                    <td><input type="text" class="form-control" name="batch_no[${rowNumber}]" required></td>
                    <td><input type="date" class="form-control" name="mfg_date[${rowNumber}]"></td>
                    <td><input type="number" class="form-control batch-qty-[${rowNumber}]" name="batch_qty[${rowNumber}]" required data-parsley-min="1" data-parsley-trigger="keyup" data-parsley-checkqty${rowNumber} onchange="updateTotalBatchQty(${rowNumber})"></td>
                    <td><input type="text" class="form-control batch-remarks-[${rowNumber}]" name="batch_remarks[${rowNumber}]"></td>
                    <td><input type="text" class="form-control assign-to-[${rowNumber}]" name="assign_to[${rowNumber}]"></td>
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
            .find(`.batch-qty-${newRowNumber}`)
            .attr("name", `batch_qty[${newRowNumber}]`);
          $(this)
            .find(`.batch-remarks-${newRowNumber}`)
            .attr("name", `batch_remarks[${newRowNumber}]`);
          $(this)
            .find(`.assign-to-${newRowNumber}`)
            .attr("name", `assign_to[${newRowNumber}]`);
          newRowNumber++;
        });
      }

      // Fungsi untuk memperbarui total batch_qty per item_id
      function updateTotalBatchQty(rowNumber) {
        var itemId = $('select[name="item_id[' + rowNumber + ']"]').val();
        var batchQty = parseFloat($('input[name="batch_qty[' + rowNumber + ']"]').val()) || 0;

        // Inisialisasi total untuk item_id jika belum ada
        if (!totalBatchQtyPerItem[itemId]) {
          totalBatchQtyPerItem[itemId] = 0;
        }

        // Kurangi total untuk nilai sebelumnya dan tambahkan nilai baru
        totalBatchQtyPerItem[itemId] = totalBatchQtyPerItem[itemId] - batchQty;

        // Iterasi semua elemen input batch_qty untuk item_id yang sama
        $('select[name^="item_id"]').each(function() {
          if ($(this).val() === itemId) {
            batchQty = parseFloat($(this).closest('tr').find('input[name^="batch_qty"]').val()) || 0;
            totalBatchQtyPerItem[itemId] += batchQty;
          }
        });

        console.log(totalBatchQtyPerItem);
        // Tampilkan total di elemen dengan ID totalBatchQtyDisplay
        // $('#totalBatchQtyDisplay').text(JSON.stringify(totalBatchQtyPerItem));
      }

      $('#form').parsley();
      window.Parsley
        .addValidator(`checkqty${rowNumber}`, {
          validateNumber: function(value, requirements, instance) {
            var grQty = parseFloat("{{ $sessionData['gr']['gr_qty'][1] }}");

            // Mendapatkan item_id yang dipilih dari dropdown
            var itemId = instance.$element.closest('tr').find('select[name^="item_id"]').val();
            console.log(itemId);
            // Membandingkan batch_qty dengan gr_qty
            return value <= grQty;
          }
          , messages: {
            en: 'Quantity exceeds from needed' // Pesan jika validasi gagal
          }
        });

    }
  });

</script>
@endsection
