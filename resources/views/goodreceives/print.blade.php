<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <!-- Meta, title, CSS, favicons, etc. -->
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>{{ $subtitle }} - Arkananta IT Store </title>

  <!-- Bootstrap -->
  <link href="{{ asset("assets/vendors/bootstrap/dist/css/bootstrap.min.css") }}" rel="stylesheet">
  <!-- Font Awesome -->
  <link href="{{ asset("assets/vendors/font-awesome/css/font-awesome.min.css") }}" rel="stylesheet">
  <!-- NProgress -->
  <link href="{{ asset("assets/vendors/nprogress/nprogress.css") }}" rel="stylesheet">

  <!-- Custom styling plus plugins -->
  <link href="{{ asset("assets/build/css/custom.min.css") }}" rel="stylesheet">
</head>

<body class="nav-md">
  <div class="container body">
    <div class="main_container">
      <div role="main">
        <div class="">
          <div class="row">
            <div class="col-md-12">
              <div class="x_panel">
                <div class="x_title">
                  <h2>Arkananta IT Store</h2>

                  <div class="clearfix"></div>
                </div>
                <div class="x_content">

                  <section class="content invoice">
                    <!-- title row -->
                    <div class="row">
                      <div class="col-xs-8 invoice-header">
                        <h3>
                          <img src="{{ asset('assets/images/logo3.png') }}" alt="Logo" width="30%"> Goods Receive
                        </h3>
                      </div>
                      <div class="col-xs-4 invoice-header">
                        <h1><small class="pull-right">{{ $goodreceive->gr_doc_num }}</small></h1><br>
                        <h4><small class="pull-right text-danger"><b>*{{ $goodreceive->is_cancelled == 'yes' ? 'Canceled' : '' }}</b></small></h4>
                      </div>
                      <!-- /.col -->
                    </div>
                    <br>
                    <!-- info row -->
                    <div class="row invoice-info">
                      <div class="col-sm-6 invoice-col">
                        <b>Document Number {{ $goodreceive->gr_doc_num }}</b>
                        <br>
                        <b>Posting Date:</b> {{ $goodreceive->gr_posting_date }}
                        <br>
                        <b>Warehouse:</b> {{ $goodreceive->warehouse->warehouse_name }}
                        <br>
                        <b>Vendor:</b> {{ $goodreceive->vendor->vendor_name ?? "" }}
                      </div>
                      <!-- /.col -->
                      {{-- <div class="col-sm-6 invoice-col">
                        <b>IT WO Detail:</b>
                        @if($goodreceive->it_wo_id)
                        <address>
                          <strong>{{ $data['no_wo'] }}</strong>
                      <br>{{ $data['nik'] }} - {{ $data['name'] }}
                      <br>{{ $data['kode_project'] }} - {{ $data['nama_project'] }}
                      <br>{{ $data['issue'] }}
                      </address>
                      @else
                      <address>
                        <strong>-</strong>
                      </address>
                      @endif
                    </div> --}}
                    <!-- /.col -->
                </div>
                <!-- /.row -->
                <br>
                <!-- Table row -->
                <div class="row">
                  <div class="col-xs-12 table">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>Qty</th>
                          <th>Group</th>
                          <th>Item Code</th>
                          <th style="width: 50%">Description</th>
                        </tr>
                      </thead>
                      @foreach ($goodreceive->grdetails->sortBy('item.group.group_name') as $grdetail)
                      <tbody>
                        <tr>
                          <td>{{ $grdetail->gr_qty }}</td>
                          <td>{{ $grdetail->item->group->group_name }}</td>
                          <td>{{ $grdetail->item->item_code }}</td>
                          <td>{{ $grdetail->item->description }}</td>
                        </tr>
                      </tbody>
                      @endforeach
                    </table>
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->

                <div class="row">
                  <!-- accepted payments column -->
                  <div class="col-xs-4">
                    <p class="lead">Remarks:</p>
                    <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                      {{ $goodreceive->gr_remarks }}
                    </p>
                  </div>
                  <!-- /.col -->
                  <div class="col-xs-8 text-center">
                    <div class="col-xs-6">
                      <h4 class="lead">Received By</h4>
                      <br><br><br>
                      <p>({{ $goodreceive->user->name }})</p>
                    </div>
                    <div class="col-xs-6">
                      <h4 class="lead">Acknowledged By</h4>
                      <br><br><br>
                      <p>(_______________)</p>
                    </div>
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->

                <!-- this row will not appear when printing -->
                {{-- <div class="row no-print">
                      <div class="col-xs-12">
                        <button class="btn btn-default" onclick="window.print();"><i class="fa fa-print"></i> Print</button>
                        <button class="btn btn-success pull-right"><i class="fa fa-credit-card"></i> Submit Payment</button>
                        <button class="btn btn-primary pull-right" style="margin-right: 5px;"><i class="fa fa-download"></i> Generate PDF</button>
                      </div>
                    </div> --}}
                </section>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>

  <!-- jQuery -->
  <script src="{{ asset("assets/vendors/jquery/dist/jquery.min") }}.js"></script>
  <!-- Bootstrap -->
  <script src="{{ asset("assets/vendors/bootstrap/dist/js/bootstrap.min") }}.js"></script>
  <!-- FastClick -->
  <script src="{{ asset("assets/vendors/fastclick/lib/fastclick") }}.js"></script>
  <!-- NProgress -->
  <script src="{{ asset("assets/vendors/nprogress/nprogress") }}.js"></script>

  <!-- Custom Theme Scripts -->
  <script src="{{ asset("assets/build/js/custom.min") }}.js"></script>
</body>
</html>
