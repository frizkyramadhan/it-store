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
            <h2>{{ $subtitle }}<small class="text-danger"><b>*{{ $goodissue->is_cancelled == 'yes' ? 'Canceled' : '' }}</b></small></h2>
            <ul class="nav navbar-right panel_toolbox">
              <form action="{{ route('goodissues.cancel', $goodissue->id) }}" method="POST">
                <a href="{{ route('goodissues.index') }}" class="btn btn-success"><i class="fa fa-arrow-circle-left"></i> Back</a>
                @if ($goodissue->is_cancelled != 'yes')
                <a href="{{ route('goodissues.edit', $goodissue->id) }}" class="btn btn-warning"><i class="fa fa-pencil"></i> Edit</a>
                <a href="{{ route('goodissues.print', $goodissue->id) }}" class="btn btn-info" target="_blank"><i class="fa fa-print"></i> Print</a>
                @csrf
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure want to cancel this data?')"><i class="fa fa-times"></i> Cancel</button>
                @endif
              </form>
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
                <input type="text" class="form-control" name="gi_doc_num" value="{{ $goodissue->gi_doc_num }}" required readonly>
              </div>
              <div class="form-group">
                <label>Posting Date <span class="required">*</span></label>
                <input type="text" class="form-control" name="gi_posting_date" value="{{ $goodissue->gi_posting_date }}" readonly>
              </div>
              <div class="form-group">
                <label>Warehouse <span class="required">*</span></label>
                <input type="text" class="form-control" name="warehouse_id" value="{{ $goodissue->warehouse->warehouse_name }}" readonly>
              </div>
              <div class="form-group">
                <label>Project <span class="required">*</span></label>
                <input type="text" class="form-control" name="project_id" value="{{ $goodissue->project->project_code ?? "" }} - {{ $goodissue->project->project_name ?? "" }}" readonly>
              </div>
            </div>
            <div class="col-md-6 col-xs-12 left-margin">
              <div class="form-group">
                <label>Issue Purpose <span class="required">*</span></label>
                <input type="text" class="form-control" name="issue_purpose_id" value="{{ $goodissue->issuepurpose->purpose_name ?? "" }}" readonly>
              </div>
              <div class="form-group">
                <label>IT WO Reference</label>
                <div class="input-group">
                  <input @if($goodissue->it_wo_id) id="it_wo_id" @endif type="text" class="form-control" value="{{ $goodissue->it_wo_id }}" readonly />
                  <span class="input-group-btn">
                    <button id="itwoDetail" class="btn btn-primary" type="button" data-wo-id="{{ $goodissue->it_wo_id }}" data-toggle="modal" data-target="#itwoModal">Detail</button>
                  </span>
                </div>
              </div>
              <div class="form-group">
                <label>Remarks</label>
                <textarea class="form-control" rows="4" name="gi_remarks" readonly> {{ $goodissue->gi_remarks }}</textarea>
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
                          <th class="column-title text-right" style="vertical-align: middle" width="10%">Qty</th>
                          <th class="column-title text-right" style="vertical-align: middle" width="10%">Price (IDR)</th>
                          <th class="column-title text-right" style="vertical-align: middle" width="10%">Total</th>
                          <th class="column-title" style="vertical-align: middle" colspan="2">Line Remarks</th>
                        </tr>
                      </thead>
                      @foreach ($goodissue->gidetails as $gidetail)
                      <tbody>
                        <tr>
                          <td>
                            <h5>{{ $gidetail->item->item_code }}</h5>
                          </td>
                          <td colspan="3">
                            <h5>{{ $gidetail->item->description }}</h5>
                          </td>
                          <td class="text-right">
                            <h5>{{ $gidetail->gi_qty }}</h5>
                          </td>
                          <td class="text-right">
                            <h5>{{ number_format($gidetail->price, 2, '.', ',') }}</h5>
                          </td>
                          <td class="text-right">
                            <h5>{{ number_format($gidetail->gi_line_total, 2, '.', ',') }}</h5>
                          </td>
                          <td colspan="2">
                            <h5>{{ $gidetail->gi_line_remarks }}</h5>
                          </td>
                        </tr>
                        {{-- @if ($gidetail->item->is_batch == "yes")
                        <tr class="headings">
                          <th class="column-title" style="vertical-align: middle" width="15%">Batch Code</th>
                          <th class="column-title" style="vertical-align: middle" width="12%">MFG</th>
                          <th class="column-title" style="vertical-align: middle" width="12%">Expire Date</th>
                          <th class="column-title" style="vertical-align: middle" width="12%">Expire Status</th>
                          <th class="column-title text-center" style="vertical-align: middle" width="10%">Qty</th>
                          <th class="column-title" style="vertical-align: middle">Batch Remarks</th>
                          <th class="column-title" style="vertical-align: middle">Issue Purpose</th>
                        </tr>
                        @php
                        $batches = app('App\Models\Batch')::select('batches.*', 'batch_transactions.batch_qty','batch_transactions.origin_no','batch_transactions.batch_remarks','batch_transactions.assign_to')->join('batch_transactions', 'batches.id', '=', 'batch_transactions.batch_id')->where('batch_transactions.origin_no', $goodissue->gi_doc_num)->where('item_id', $gidetail->item_id)->get();
                        @endphp
                        @foreach ($batches as $batch)
                        <tr>
                          <td>{{ $batch->batch_no }}</td>
                        <td>{{ date('d-M-Y', strtotime($batch->mfg_date)) }}</td>
                        <td>{{ date('d-M-Y', strtotime($batch->mfg_date . "+". $gidetail->item->shelf_life." months")) }}</td>
                        <td>
                          @if (date('Y-m-d') > date('Y-m-d', strtotime($batch->mfg_date . "+". $batch->item->shelf_life." months")))
                          {{ "Expired" }}
                          @else
                          {{ "Non Expired" }}
                          @endif
                        </td>
                        <td class="text-center">{{ $batch->batch_qty }}</td>
                        <td>{{ $batch->batch_remarks }}</td>
                        <td>{{ $batch->assign_to }}</td>
                        </tr>
                        @endforeach
                        @endif --}}
                      </tbody>
                      @endforeach
                    </table>
                    {{-- @dd($batches) --}}
                    <div class="form-group text-right">
                      <label>Total Cost (IDR)</label>
                      <h3>{{ number_format($goodissue->total_cost ?? 0, 2, '.', ',') }}</h3>
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
</div>

<div id="itwoModal" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
        </button>
        <h4 class="modal-title">IT WO Detail</h4>
      </div>
      <div class="modal-body">

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

<script>
  $(document).ready(function() {
    var goodIssue = {
      itWoId: '{{ $goodissue->it_wo_id }}'
      , itWoNo: '{{ $goodissue->it_wo_no }}'
    };

    // Fungsi untuk mengambil data IT WO dari API
    function fetchItwoData(id, callback) {
      // Mengambil data IT WO
      $.ajax({
        url: 'http://192.168.32.37/arka-rest-server/api/it_wo_store/'
        , type: 'GET'
        , dataType: 'json'
        , data: {
          'arka-key': 'arka123'
          , 'id_wo': id
        }
        , success: function(result) {
          if (result.status === true && result.data.length > 0) {
            callback(result.data[0]); // Mengembalikan data IT WO ke callback
          } else {
            console.error('Data IT WO tidak ditemukan.');
          }
        }
        , error: function() {
          console.error('Error saat mengambil data IT WO.');
        }
      });
    }

    // Ketika halaman selesai dimuat, ambil data dan set nilai ke form
    fetchItwoData(goodIssue.itWoId, function(data) {
      $('#it_wo_id').val(data.no_wo);
    });

    // Ketika modal dibuka, tampilkan data yang sesuai
    $('#itwoModal').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget);
      var id = button.data('wo-id');
      var modal = $(this);

      if (id) {
        // Ambil data IT WO dan tampilkan di modal
        fetchItwoData(id, function(data) {
          modal.find('.modal-body').html(`
          <table class="table table-striped">
            <tr>
              <th>No</th>
              <td>:</td>
              <td>${data.no_wo}</td>
            </tr>
            <tr>
              <th>Date</th>
              <td>:</td>
              <td>${data.date}</td>
            </tr>
            <tr>
              <th>NIK</th>
              <td>:</td>
              <td>${data.nik}</td>
            </tr>
            <tr>
              <th>Name</th>
              <td>:</td>
              <td>${data.name}</td>
            </tr>
            <tr>
              <th>Project</th>
              <td>:</td>
              <td>${data.kode_project} - ${data.nama_project}</td>
            </tr>
            <tr>
              <th>Issue</th>
              <td>:</td>
              <td>${data.issue}</td>
            </tr>
            <tr>
              <th>Status</th>
              <td>:</td>
              <td>${data.status}</td>
            </tr>
          </table>
        `);
        });
      } else {
        modal.find('.modal-body').html(`
        <table class="table table-striped">
            <tr>
              <th class="text-center text-danger">IT WO is not available</th>
            </tr>
          </table>
        `);
      }

    });
  });

</script>


@endsection
