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
            <div class="col-md-12 col-xs-12 left-margin">
              <table class="table jambo_table" width="100%">
                <thead>
                  <tr class="headings">
                    <th>Doc No.</th>
                    <th>Item Code</th>
                    <th>Description</th>
                    <th>Whse Name</th>
                    <th>Quantity</th>
                    <th>Total Needed</th>
                  </tr>
                </thead>
                @if ($items->count() == 0)
                <tbody>
                  <tr>
                    <td colspan="6" class="text-center">No Batch Item Selected</td>
                  </tr>
                </tbody>
                @else
                @foreach ($items as $item)
                <tbody>
                  <tr>
                    <td class="column-title" style="vertical-align: middle">{{ $sessionData['gi']['gi_doc_num'] }}</td>
                    <td class="column-title" style="vertical-align: middle">{{ $item->item_code }}</td>
                    <td class="column-title" style="vertical-align: middle">{{ $item->description }}</td>
                    <td class="column-title" style="vertical-align: middle">{{ $warehouse->warehouse_name }}</td>
                    <td class="column-title" style="vertical-align: middle">{{ $item->stock }}</td>
                    <td class="column-title" style="vertical-align: middle">{{ $sessionData['gi']['gi_qty'][$loop->iteration] }}</td>
                  </tr>
                </tbody>
                @endforeach
                @endif
              </table>
            </div>

            @if ($items->count() != 0)
            <form id="form" data-parsley-validate class="form-horizontal form-label-left" action="{{ route('batches.issue') }}" method="POST">
              @csrf
              <div class="col-md-12 col-xs-12 left-margin">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Available Batches</h2>
                    <ul class="nav navbar-right panel_toolbox"></ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <div class="table-responsive">
                      <table id="inputTable" class="table table-striped jambo_table" width="100%">
                        <thead>
                          <tr class="headings">
                            <th></th>
                            <th class="column-title" style="vertical-align: middle">Item Code</th>
                            <th class="column-title" style="vertical-align: middle">Batch No</th>
                            <th class="column-title" style="vertical-align: middle">MFG</th>
                            <th class="column-title" style="vertical-align: middle">Exp Date</th>
                            <th class="column-title" style="vertical-align: middle">Exp Status</th>
                            <th class="column-title" style="vertical-align: middle">Available Qty</th>
                            <th class="column-title" style="vertical-align: middle">Selected Qty</th>
                            <th class="column-title" style="vertical-align: middle">Remarks</th>
                            <th class="column-title" style="vertical-align: middle">Issue Purpose</th>
                          </tr>
                        </thead>
                        @foreach ($batches as $batch)
                        <tbody>
                          <tr>
                            <td class="a-center ">
                              <input id="id-{{ $batch->batch_id }}" type="checkbox" class="flat" name="id[]" value="{{ $batch->batch_id }}" data-parsley-min="2">
                            </td>
                            <td>{{ $batch->item_code }}</td>
                            <td>{{ $batch->batch_no }}</td>
                            <td>{{ date('d-M-Y', strtotime($batch->mfg_date)) }}</td>
                            <td>{{ date('d-M-Y', strtotime($batch->mfg_date . "+". $batch->shelf_life." months")) }}</td>
                            <td>
                              @if (date('Y-m-d') > date('Y-m-d', strtotime($batch->mfg_date . "+". $batch->shelf_life." months")))
                              <span class="label label-danger">Expired</span>
                              @else
                              <span class="label label-primary">Non Expired</span>
                              @endif
                            </td>
                            <td>{{ $batch->batch_stock }}</td>
                            <td><input id="qty-{{ $batch->batch_id }}" type="number" name="batch_qty[]" class="form-control" data-parsley-min="1" data-parsley-trigger="keyup"></td>
                            <td><input id="batch-remarks-{{ $batch->batch_id }}" type="text" name="batch_remarks[]" class="form-control"></td>
                            <td><input id="assign-to-{{ $batch->batch_id }}" type="text" name="assign_to[]" class="form-control"></td>
                          </tr>
                        </tbody>
                        @endforeach
                      </table>
                    </div>
                  </div>
                </div>
              </div>
              @else
              <form class="form-horizontal form-label-left" action="{{ route('batches.issuenonbatch') }}" method="POST">
                @csrf
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
    @foreach($batches as $batch)
    $('#qty-{{ $batch->batch_id }}').prop('disabled', true);
    $('#batch-remarks-{{ $batch->batch_id }}').prop('disabled', true);
    $('#assign-to-{{ $batch->batch_id }}').prop('disabled', true);
    $('#qty-{{ $batch->batch_id }}').val('');
    $('#batch-remarks-{{ $batch->batch_id }}').val('');
    $('#assign-to-{{ $batch->batch_id }}').val('');

    $('#id-{{ $batch->batch_id }}').on('ifChecked', function(event) {
      $('#qty-{{ $batch->batch_id }}').prop('disabled', false);
      $('#batch-remarks-{{ $batch->batch_id }}').prop('disabled', false);
      $('#assign-to-{{ $batch->batch_id }}').prop('disabled', false);
    })
    $('#id-{{ $batch->batch_id }}').on('ifUnchecked', function(event) {
      $('#qty-{{ $batch->batch_id }}').prop('disabled', true);
      $('#batch-remarks-{{ $batch->batch_id }}').prop('disabled', true);
      $('#assign-to-{{ $batch->batch_id }}').prop('disabled', true);
      $('#qty-{{ $batch->batch_id }}').val('');
      $('#batch-remarks-{{ $batch->batch_id }}').val('');
      $('#assign-to-{{ $batch->batch_id }}').val('');
    })
    @endforeach

  });

</script>
@endsection
