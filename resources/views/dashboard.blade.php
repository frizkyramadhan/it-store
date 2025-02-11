@extends('layouts.main')

@section('content')
<!-- page content -->
<div class="right_col" role="main">
  <div class="row">
    <div class="row top_tiles">
      <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="tile-stats">
          <div class="icon"><i class="fa fa-keyboard-o"></i></div>
          <div class="count">{{ $totalItem }}
          </div>
          <h3>Items</h3>
          <p>Total items</p>
        </div>
      </div>
      <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="tile-stats">
          <div class="icon"><i class="fa fa-hdd-o"></i></div>
          <div class="count">{{ $totalGroup }}
          </div>
          <h3>Groups</h3>
          <p>Total groups</p>
        </div>
      </div>
      <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="tile-stats">
          <div class="icon"><i class="fa fa-cubes"></i></div>
          <div class="count">{{ $totalStock }}
          </div>
          <h3>Stock</h3>
          <p>Total stock of goods</p>
        </div>
      </div>
      <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="tile-stats">
          <div class="icon"><i class="fa fa-users"></i></div>
          <div class="count">{{ $totalUser }}</div>
          <h3>Users</h3>
          <p>Total users</p>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Search Item</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <article class="media event">
              <div class="media-body">
                <form action="{{ route('dashboard.search') }}" method="POST">
                  @csrf
                  <div class="input-group">
                    <input type="text" class="form-control item-code" name="search" required>
                    <span class="input-group-btn">
                      <button type="button" class="btn btn-success search-item"><i class="fa fa-ellipsis-h"></i></button>
                    </span>
                  </div>
                  <button type="submit" class="btn btn-block btn-info"><i class="fa fa-search"></i> Search</button>
                </form>
              </div>
              <div class="clearfix"></div>
            </article>
          </div>
        </div>
      </div>

      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Material Request Open</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <table id="datatable-buttons" class="table table-striped table-bordered jambo_table" width="100%">
              <thead>
                <tr class="headings">
                  <th>#</th>
                  <th>MR No</th>
                  <th>Date</th>
                  <th>Project</th>
                  <th>Remarks</th>
                  <th class="text-center">Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($materialRequests as $mr)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>{{ $mr->mr_doc_num }}</td>
                  <td>{{ $mr->mr_posting_date }}</td>
                  <td>{{ $mr->project->project_code }}</td>
                  <td>{{ $mr->mr_remarks }}</td>
                  <td class="text-center"><a href="{{ url('materialrequests/'.$mr->id) }}" class="btn btn-sm btn-info"><i class="fa fa-info-circle"></i> Detail</a></td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Out of Stock <small class="text-danger">(under 5 ea)</small></h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <table id="datatable" class="table table-striped table-bordered jambo_table" width="100%">
              <thead>
                <tr class="headings">
                  <th>#</th>
                  <th>Item Code</th>
                  <th>Description</th>
                  <th>Warehouse</th>
                  <th>Stock</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($outOfStocks as $out)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>{{ $out->item->item_code }}</td>
                  <td>{{ $out->item->description }}</td>
                  <td>{{ $out->warehouse->warehouse_name }}</td>
                  <td class="text-right">{{ $out->stock }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Last 10 Transactions for Incoming Goods</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <table id="datatable-fixed-header" class="table table-striped table-bordered jambo_table" width="100%">
              <thead>
                <tr class="headings">
                  <th>#</th>
                  <th>Date</th>
                  <th>Item</th>
                  <th>Qty</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($lastIncoming as $li)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td><a href="{{ url('goodreceive/'.$li->good_receive_id) }}">{{ date('d-M-Y', strtotime($li->created_at)) }}</a></td>
                  <td>{{ $li->item->description }}</td>
                  <td class="text-right">{{ $li->gr_qty }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
      {{-- <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Expiring Batches <small class="text-danger">(under 30 days)</small></h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <table id="datatable-fixed-header" class="table table-striped table-bordered jambo_table" width="100%">
              <thead>
                <tr class="headings">
                  <th>#</th>
                  <th>Item Code</th>
                  <th>Batch No</th>
                  <th>Exp Date</th>
                  <th>RMNG Days</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($expiringBatches as $exp)
                @php
                $now = date('d-M-Y');
                $expireDate = date('d-M-Y', strtotime($exp->mfg_date . "+". $exp->item->shelf_life." months"));
                $nowTimestamp = strtotime($now);
                $expireDateTimestamp = strtotime($expireDate);
                $remainingDays = ($expireDateTimestamp - $nowTimestamp) / (60 * 60 * 24);
                @endphp
                <tr>
                  <td>{{ $loop->iteration }}</td>
      <td>{{ $exp->item->item_code }}</td>
      <td><a href="{{ url('batches/'.$exp->id.'/edit') }}">{{ $exp->batch_no }}</a></td>
      <td class="text-right">{{ $expireDate }}</td>
      <td class="text-right {{ $remainingDays < 0 ? 'text-danger' : '' }}"><strong>{{ $remainingDays }} Days</strong></td>
      </tr>
      @endforeach
      </tbody>
      </table>
    </div>
  </div>
</div> --}}

<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
  <div class="x_panel">
    <div class="x_title">
      <h2>Last 10 Transactions for Outgoing Goods</h2>
      <div class="clearfix"></div>
    </div>
    <div class="x_content">
      <table id="datatable-responsive" class="table table-striped table-bordered jambo_table" width="100%">
        <thead>
          <tr class="headings">
            <th>#</th>
            <th>Date</th>
            <th>Item</th>
            <th>Qty</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($lastOutcoming as $lo)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td><a href="{{ url('goodissues/'.$lo->good_issue_id) }}">{{ date('d-M-Y', strtotime($lo->created_at)) }}</a></td>
            <td>{{ $lo->item->description }}</td>
            <td class="text-right">{{ $lo->gi_qty }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
{{-- <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
  <div class="x_panel">
    <div class="x_title">
      <h2>Expiring Permits <small class="text-danger">(under 30 days to 1 year)</small></h2>
      <div class="clearfix"></div>
    </div>
    <div class="x_content">
      <table id="datatable-responsive" class="table table-striped table-bordered jambo_table" width="100%">
        <thead>
          <tr class="headings">
            <th>#</th>
            <th>Permit No</th>
            <th>Permit Date</th>
            <th>Valid Until</th>
            <th>RMNG Days</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($expiringPermits as $exp)
          @php
          $now = date('d-M-Y');
          $expireDate = date('d-M-Y', strtotime($exp->permit_date . "+". $exp->valid_month." months"));
          $nowTimestamp = strtotime($now);
          $expireDateTimestamp = strtotime($expireDate);
          $remainingDays = ($expireDateTimestamp - $nowTimestamp) / (60 * 60 * 24);
          @endphp
          <tr>
            <td>{{ $loop->iteration }}</td>
<td><a href="{{ url('permits/'.$exp->id) }}">{{ $exp->permit_no }}</a></td>
<td>{{ date('d-M-Y', strtotime($exp->permit_date)) }}</td>
<td class="text-right">{{ $expireDate }}</td>
<td class="text-right {{ $remainingDays < 0 ? 'text-danger' : '' }}"><strong>{{ $remainingDays }} Days</strong></td>
</tr>
@endforeach
</tbody>
</table>
</div>
</div>
</div> --}}
</div>
</div>
</div>
<!-- /page content -->

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
<!-- bootstrap-daterangepicker -->
<link href="{{ asset('assets/vendors/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">
<!-- Datatables -->
<link href="{{ asset('assets/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css') }}" rel="stylesheet">
@endsection

@section('scripts')
<!-- Chart.js -->
{{-- <script src="{{ asset('assets/vendors/Chart.js/dist/Chart.min.js') }}"></script> --}}
<!-- Flot -->
<script src="{{ asset('assets/vendors/Flot/jquery.flot.js') }}"></script>
<script src="{{ asset('assets/vendors/Flot/jquery.flot.pie.js') }}"></script>
<script src="{{ asset('assets/vendors/Flot/jquery.flot.time.js') }}"></script>
<script src="{{ asset('assets/vendors/Flot/jquery.flot.stack.js') }}"></script>
<script src="{{ asset('assets/vendors/Flot/jquery.flot.resize.js') }}"></script>
<!-- Flot plugins -->
<script src="{{ asset('assets/vendors/flot.orderbars/js/jquery.flot.orderBars.js') }}"></script>
<script src="{{ asset('assets/vendors/flot-spline/js/jquery.flot.spline.min.js') }}"></script>
<script src="{{ asset('assets/vendors/flot.curvedlines/curvedLines.js') }}"></script>
<!-- DateJS -->
<script src="{{ asset('assets/vendors/DateJS/build/date.js') }}"></script>
<!-- bootstrap-daterangepicker -->
<script src="{{ asset('assets/vendors/moment/min/moment.min.js') }}"></script>
<script src="{{ asset('assets/vendors/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
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
  $(document).on("click", `.search-item`, function() {
    $("#itemModal").modal("show");
    var itemCode = $('.item-code').val();
    listItem(itemCode);
  });

  $("#datatable-serverside").on(
    "click"
    , `button.pick-item`
    , function() {
      var itemCode = $(this).data("item-code");

      // Update nilai item-id, item-code, dan description
      $(`.item-code`).val(itemCode);

      // Sembunyikan modal setelah memilih item
      $("#itemModal").modal("hide");
      $(`.item-code`).focus();
    }
  );

  function listItem(itemCode) {
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
        url: "{{ route('dashboard.searchitem') }}"
          // url: "http://localhost/bh-inventory/items/dataForTransaction"
        , data: function(d) {
          d.itemCode = itemCode;
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
            var groupName = row.group_name;

            return `<button class="btn btn-sm btn-info pick-item" data-item-id="${itemId}" data-item-code="${itemCode}" data-description="${description}" data-group-name="${groupName}"><i class="fa fa-check-square-o"></i> Pick!</button>`;
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

</script>
@endsection
