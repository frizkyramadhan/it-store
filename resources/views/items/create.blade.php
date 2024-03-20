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
              <a href="{{ url('items') }}" class="btn btn-success"><i class="fa fa-arrow-circle-left"></i> Back</a>
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
            <form data-parsley-validate class="form-horizontal form-label-left" action="{{ route('items.store') }}" method="POST">
              @csrf
              <div class="col-md-6 col-xs-12 center-margin">
                <div class="form-group">
                  <label>Item Code <span class="required">*</span></label>
                  <input type="text" class="form-control" name="item_code" required>
                </div>
                <div class="form-group">
                  <label>Description <span class="required">*</span></label>
                  <input type="text" class="form-control" name="description" required>
                </div>
                {{-- <div class="form-group">
                  <label>Type <span class="required">*</span></label>
                  <select id="type" class="select2 form-control" name="type_id" style="width: 100%" required>
                    <option value="">Select Type</option>
                    @foreach ($types as $type)
                    <option value="{{ $type->id }}">{{ $type->type_name }}</option>
                @endforeach
                </select>
              </div> --}}
              <div class="form-group">
                <label>Group <span class="required">*</span></label>
                <select id="group" class="select2 form-control" name="group_id" style="width: 100%">
                  <option value="">Select Group</option>
                  @foreach ($groups as $group)
                  <option value="{{ $group->id }}">{{ $group->group_name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group">
                <label>Status</label>
                <div data-toggle="buttons">
                  <label class="btn btn-default active">
                    <input type="radio" name="item_status" value="active" checked> Active
                  </label>
                  <label class="btn btn-default">
                    <input type="radio" name="item_status" value="inactive"> Inactive
                  </label>
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
