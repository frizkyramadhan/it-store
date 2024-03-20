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

                <div class="col-md-2 col-sm-12 col-xs-12 form-group">
                  <label>From Warehouse</label>
                  <select id="from_warehouse" class="select2 form-control" name="from_warehouse" style="width: 100%">
                    <option value="">Select Warehouse</option>
                    @foreach ($from_warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}" {{ request('from_warehouse') == $warehouse->id ? 'selected' : ('') }}>{{ $warehouse->warehouse_name }} ({{ $warehouse->bouwheer->bouwheer_name }})</option>
                    @endforeach
                  </select>
                </div>

                <div class="col-md-2 col-sm-12 col-xs-12 form-group">
                  <label>To Warehouse</label>
                  <select id="to_warehouse" class="select2 form-control" name="to_warehouse" style="width: 100%">
                    <option value="">Select Warehouse</option>
                    @foreach ($to_warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}" {{ request('to_warehouse') == $warehouse->id ? 'selected' : ('') }}>{{ $warehouse->warehouse_name }} ({{ $warehouse->bouwheer->bouwheer_name }})</option>
                    @endforeach
                  </select>
                </div>

                <div class="col-md-2 col-sm-12 col-xs-12 form-group">
                  <label>Remarks</label>
                  <input type="text" class="form-control" name="remarks" value="{{ request('remarks') }}">
                </div>

                <div class="col-md-2 col-sm-12 col-xs-12 form-group">
                  @if (isset($results) && count($results) > 0)
                  <a href="{{ route('report.transfer') }}" class="btn btn-warning form-control"><i class="fa fa-undo"></i> Reset</a>
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
                <table class="table table-striped table-bordered">
                  @endif
                  <thead>
                    <tr>
                      <th style="vertical-align: middle">No</th>
                      <th style="vertical-align: middle">Document No.</th>
                      <th style="vertical-align: middle">Posting Date</th>
                      {{-- <th style="vertical-align: middle">Type</th> --}}
                      <th style="vertical-align: middle">From Warehouse</th>
                      <th style="vertical-align: middle">To Warehouse</th>
                      <th style="vertical-align: middle">Bouwheer</th>
                      <th style="vertical-align: middle">Remarks</th>
                      <th style="vertical-align: middle">Item Code</th>
                      <th style="vertical-align: middle">Description</th>
                      <th style="vertical-align: middle">Qty</th>
                      {{-- <th style="vertical-align: middle">Type</th> --}}
                      <th style="vertical-align: middle">Group</th>
                      <th style="vertical-align: middle">Line Remarks</th>
                      <th style="vertical-align: middle">Created By</th>
                    </tr>
                  </thead>
                  <tbody>
                    @if (isset($results) && count($results) > 0)
                    @foreach ($results as $result)
                    <tr>
                      <td>{{ $loop->iteration }}</td>
                      <td>{{ $result->trf_doc_num }}</td>
                      <td class="text-right">{{ date('d M Y', strtotime($result->trf_posting_date)) }}</td>
                      {{-- <td class="text-center">{{ $result->trf_type }}</td> --}}
                      <td>{{ $result->from_warehouse }}</td>
                      <td>{{ $result->to_warehouse }}</td>
                      <td>{{ $result->bouwheer_name }}</td>
                      <td>{{ $result->trf_remarks }}</td>
                      <td>{{ $result->item_code }}</td>
                      <td>{{ $result->description }}</td>
                      <td class="text-right">{{ $result->trf_qty }}</td>
                      {{-- <td>{{ $result->type_name }}</td> --}}
                      <td>{{ $result->group_name }}</td>
                      <td>{{ $result->trf_line_remarks }}</td>
                      <td>{{ $result->name }}</td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                      <td colspan="15" class="text-center">No data available</td>
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
@endsection
