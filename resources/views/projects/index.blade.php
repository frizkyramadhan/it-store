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
              @can('admin')
              <a class="btn btn-primary" data-toggle="modal" data-target="#modal-import"><i class="fa fa-upload"></i>
                Import</a>
              @endcan
              <a href="{{ url()->previous() }}" class="btn btn-success"><i class="fa fa-arrow-circle-left"></i> Back</a>
              <a class="btn btn-warning" data-toggle="modal" data-target=".bs-example-modal-md"><i class="fa fa-plus-circle"></i> Add</a>
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
            <div class="table-responsive">
              <table id="datatable-serverside" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Project Code</th>
                    <th>Project Name</th>
                    <th>Status</th>
                    <th class="text-center">Action</th>
                  </tr>
                </thead>
              </table>
            </div>

            {{-- add project modal --}}
            <div id="myModal" class="modal fade bs-example-modal-md" tabindex="-1" role="dialog" aria-hidden="true">
              <div class="modal-dialog modal-md">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">Add Project</h4>
                  </div>
                  <form data-parsley-validate class="form-horizontal form-label-left" action="{{ route('projects.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Project Code <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="text" name="project_code" required="required" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Project Name <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="text" name="project_name" required="required" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Status</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default active">
                              <input type="radio" name="project_status" value="active" checked> Active
                            </label>
                            <label class="btn btn-default">
                              <input type="radio" name="project_status" value="inactive"> Inactive
                            </label>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            {{-- add edit modal --}}
            @foreach ($projects as $project)
            <div id="myModal{{ $project->id }}" class="modal fade bs-example-modal-md-{{ $project->id }}" tabindex="-1" role="dialog" aria-hidden="true">
              <div class="modal-dialog modal-md">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">Edit Project</h4>
                  </div>
                  <form id="demo-form2" data-parsley-validate class="form-horizontal form-label-left" action="{{ route('projects.update', ['project'=> $project->id]) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Project Code <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="text" name="project_code" required="required" class="form-control col-md-7 col-xs-12" value="{{ $project->project_code }}">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Project Name <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="text" name="project_name" required="required" class="form-control col-md-7 col-xs-12" value="{{ $project->project_name }}">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Status</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default {{ $project->project_status == 'active' ? 'active' : '' }}">
                              <input type="radio" name="project_status" value="active" {{ $project->project_status == 'active' ? 'checked' : '' }}> Active
                            </label>
                            <label class="btn btn-default {{ $project->project_status == 'inactive' ? 'active' : '' }}">
                              <input type="radio" name="project_status" value="inactive" {{ $project->project_status == 'inactive' ? 'checked' : '' }}> Inactive
                            </label>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-import">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
        </button>
        <h4 class="modal-title">Import Data</h4>
      </div>
      <form action="{{ url('projects/import') }}" method="POST" enctype="multipart/form-data">
        <div class="modal-body">
          @csrf
          @method('POST')
          <div class="card-body">
            <div class="tab-content p-0">
              <div class="form-group row">
                <label class="col-sm-4 col-form-label">Import Projects</label>
                <div class="col-sm-8">
                  <input type="file" name="project">
                </div>
              </div>
            </div>
          </div><!-- /.card-body -->
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
@endsection

@section('styles')
<!-- iCheck -->
<link href="{{ asset('assets/vendors/iCheck/skins/flat/green.css') }}" rel="stylesheet">
<!-- Datatables -->
<link href="{{ asset('assets/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css') }}" rel="stylesheet">
<!-- Select2 -->
<link href="{{ asset('assets/vendors/select2/dist/css/select2.min.css') }}" rel="stylesheet">
@endsection

@section('scripts')
<!-- iCheck -->
<script src="{{ asset('assets/vendors/iCheck/icheck.min.js') }}"></script>
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
<!-- Parsley -->
<script src="{{ asset('assets/vendors/parsleyjs/dist/parsley.min.js') }}"></script>
<!-- Select2 -->
<script src="{{ asset('assets/vendors/select2/dist/js/select2.full.min.js') }}"></script>
<script>
  $(document).ready(function() {
    $('.select2').select2({
      dropdownParent: $('#myModal')
    });

    $(document).on('select2:open', () => {
      document.querySelector('.select2-search__field').focus();
    })

    @foreach($projects as $project)
    $('.select2{{ $project->id }}').select2({
      dropdownParent: $('#myModal{{ $project->id }}')
    });

    $(document).on('select2:open', () => {
      document.querySelector('.select2-search__field').focus();
    })
    @endforeach
  });

</script>
<!-- Custom Theme Scripts -->
{{-- script datatable serverside --}}
<script>
  $(function() {
    var table = $("#datatable-serverside").DataTable({
      responsive: true
      , autoWidth: true
      , lengthChange: true
      , lengthMenu: [
        [10, 25, 50, 100, -1]
        , ['10', '25', '50', '100', 'Show all']
      ]
      , dom: 'lBfrtpi'
        // , dom: 'frtpi'
      , buttons: ["copy", "csv", "print"]
      , processing: true
      , serverSide: true
      , ajax: {
        url: "{{ route('projects.data') }}"
        , data: function(d) {
          d.search = $("input[type=search][aria-controls=datatable-serverside]").val()
          // console.log(d);
        }
      }
      , columns: [{
        data: 'DT_RowIndex'
        , orderable: false
        , searchable: false
        , className: 'text-center'
      }, {
        data: "project_code"
        , name: "project_code"
        , orderable: false
      , }, {
        data: "project_name"
        , name: "project_name"
        , orderable: false
      , }, {
        data: "project_status"
        , name: "project_status"
        , orderable: false
        , className: "text-center"
      , }, {
        data: "action"
        , name: "action"
        , orderable: false
        , searchable: false
        , className: "text-center"
      }]
      , fixedColumns: true
    , })
  });

</script>


@endsection
