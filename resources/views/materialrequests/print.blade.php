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
                          <img src="{{ asset('assets/images/logo3.png') }}" alt="Logo" width="30%"> Commissioning Checklist
                        </h3>
                      </div>
                      <div class="col-xs-4 invoice-header">
                        <h1><small class="pull-right">{{ $goodissue->gi_doc_num }}</small></h1><br>
                        <h4><small class="pull-right text-danger"><b>*{{ $goodissue->is_cancelled == 'yes' ? 'Canceled' : '' }}</b></small></h4>
                      </div>
                      <!-- /.col -->
                    </div>
                    <br>
                    <!-- info row -->
                    <div class="row invoice-info">
                      <div class="col-sm-6 invoice-col">
                        <b>Document Number {{ $goodissue->gi_doc_num }}</b>
                        <br>
                        <b>Posting Date:</b> {{ $goodissue->gi_posting_date }}
                        <br>
                        <b>Warehouse:</b> {{ $goodissue->warehouse->warehouse_name }}
                        <br>
                        <b>Project:</b> {{ $goodissue->project->project_code ?? "" }} - {{ $goodissue->project->project_name ?? "" }}
                        <br>
                        <b>Purpose:</b> {{ $goodissue->issuepurpose->purpose_name ?? "" }}

                      </div>
                      <!-- /.col -->
                      <div class="col-sm-6 invoice-col">
                        <b>IT WO Detail:</b>
                        @if($goodissue->it_wo_id)
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
                      </div>
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
                              <th></th>
                            </tr>
                          </thead>
                          @foreach ($goodissue->gidetails->sortBy('item.group.group_name') as $gidetail)
                          <tbody>
                            <tr>
                              <td>{{ $gidetail->gi_qty }}</td>
                              <td>{{ $gidetail->item->group->group_name }}</td>
                              <td>{{ $gidetail->item->item_code }}</td>
                              <td>{{ $gidetail->item->description }}</td>
                              <td><input type="checkbox" name="" id=""></td>
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
                          {{ $goodissue->gi_remarks }}
                        </p>
                      </div>
                      <!-- /.col -->
                      <div class="col-xs-8 text-center">
                        <div class="col-xs-4">
                          <h4 class="lead">Issued By</h4>
                          <br><br><br>
                          <p>({{ $goodissue->user->name }})</p>
                        </div>
                        <div class="col-xs-4">
                          <h4 class="lead">Checked By</h4>
                          <br><br><br>
                          <p>(_______________)</p>
                        </div>
                        <div class="col-xs-4">
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
</body>
</html>
