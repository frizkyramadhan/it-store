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
              <a href="{{ url('batches') }}" class="btn btn-success"><i class="fa fa-arrow-circle-left"></i> Back</a>
              <a href="{{ url('batches/' . $batch->id . '/edit') }}" class="btn btn-warning"><i class="fa fa-pencil"></i> Edit</a>
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

            <div class="col-md-6 col-xs-12 left-margin">
              <div class="x_panel">
                <div class="x_title">
                  <h2>Item Detail</h2>
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">
                  <div class="form-group">
                    <label>Item Code</label>
                    <input type="text" class="form-control" value="{{ $batch->item->item_code }}" readonly>
                  </div>
                  <div class="form-group">
                    <label>Description</label>
                    <input type="text" class="form-control" value="{{ $batch->item->description }}" readonly>
                  </div>
                  <div class="form-group">
                    <label>Type</label>
                    <input type="text" class="form-control" value="{{ $batch->item->type->type_name }}" readonly>
                  </div>
                  <div class="form-group">
                    <label>Group</label>
                    <input type="text" class="form-control" value="{{ $batch->item->group->group_name }}" readonly>
                  </div>
                  <div class="form-group">
                    <label>Shelf Life (month)</label>
                    <input type="text" class="form-control" value="{{ $batch->item->shelf_life }}" readonly>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-xs-12 left-margin">
              <div class="x_panel">
                <div class="x_title">
                  <h2>Batch Detail</h2>
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">
                  <div class="form-group">
                    <label>Batch No.</label>
                    <input type="text" class="form-control" value="{{ $batch->batch_no }}" readonly>
                  </div>
                  <div class="form-group">
                    <label>Mfg Date</label>
                    <input type="text" class="form-control" value="{{ $batch->mfg_date }}" readonly>
                  </div>
                  <div class="form-group">
                    <label>Batch Status</label>
                    <input type="text" class="form-control" value="{{ $batch->batch_status }}" readonly>
                  </div>
                  @php
                  $now = date('Y-m-d');
                  $expireDate = date('Y-m-d', strtotime($batch->mfg_date . "+". $batch->item->shelf_life." months"));
                  if($now > $expireDate) {
                  $expireStatus = "Expired";
                  } else {
                  $expireStatus = "Non Expired";
                  }
                  @endphp
                  <div class="form-group">
                    <label>Expire Date</label>
                    <input type="text" class="form-control" value="{{ $expireDate }}" readonly>
                  </div>
                  <div class="form-group">
                    <label>Expire Status</label>
                    <input type="text" class="form-control" value="{{ $expireStatus }}" readonly>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-12 col-xs-12 left-margin">
              <h3>Batch Transaction: </h3>
              <div class="x-content">
                <table class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Date</th>
                      <th>Document No.</th>
                      <th>Whse</th>
                      <th class="text-right">Qty</th>
                      <th>Remarks</th>
                      <th class="text-center">Type</th>
                    </tr>
                  </thead>
                  @foreach($batch->batch_transactions as $transaction)
                  <tbody>
                    <tr>
                      <th scope="row">{{ $loop->iteration }}</th>
                      <td>{{ $transaction->transaction_date }}</td>
                      <td>{{ $transaction->origin_no }}</td>
                      <td>{{ $transaction->warehouse->warehouse_name }}</td>
                      <td class="text-right">{{ $transaction->batch_qty != 0 ? $transaction->batch_qty : "" }}</td>
                      <td>{{ $transaction->batch_remarks }}</td>
                      <td class="text-center">
                        @if ($transaction->transaction_type == "in")
                        <span class="label label-success">In</span>
                        @else
                        <span class="label label-danger">Out</span>
                        @endif
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
@endsection

@section('styles')
<!-- iCheck -->
<link href="{{ asset('assets/vendors/iCheck/skins/flat/green.css') }}" rel="stylesheet">
<!-- Select2 -->
<link href="{{ asset('assets/vendors/select2/dist/css/select2.min.css') }}" rel="stylesheet">
@endsection

@section('scripts')
<!-- iCheck -->
<script src="{{ asset('assets/vendors/iCheck/icheck.min.js') }}"></script>
<!-- Parsley -->
<script src="{{ asset('assets/vendors/parsleyjs/dist/parsley.min.js') }}"></script>
<!-- Select2 -->
<script src="{{ asset('assets/vendors/select2/dist/js/select2.full.min.js') }}"></script>

<script>
  $(document).ready(function() {
    $('.select2').select2();

    $(document).on('select2:open', () => {
      document.querySelector('.select2-search__field').focus();
    })
  });

</script>


@endsection
