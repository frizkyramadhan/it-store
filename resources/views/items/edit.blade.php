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
            <form data-parsley-validate class="form-horizontal form-label-left" action="{{ route('items.update', ['item'=> $item->id]) }}" method="POST">
              @method('PATCH')
              @csrf
              <div class="col-md-6 col-xs-12 center-margin">
                <div class="form-group">
                  <label>Item Code <span class="required">*</span></label>
                  <input type="text" class="form-control" name="item_code" value="{{ $item->item_code }}" required>
                </div>
                <div class="form-group">
                  <label>Description <span class="required">*</span></label>
                  <input type="text" class="form-control" name="description" value="{{ $item->description }}" required>
                </div>
                {{-- <div class="form-group">
                  <label>Type <span class="required">*</span></label>
                  <select id="type" class="select2 form-control" name="type_id" style="width: 100%" required>
                    <option value="">Select Type</option>
                    @foreach ($types as $type)
                    <option value="{{ $type->id }}" {{ $item->type_id == $type->id ? 'selected' : '' }}>{{ $type->type_name }}</option>
                @endforeach
                </select>
              </div> --}}
              <div class="form-group">
                <label>Group <span class="required">*</span></label>
                <select id="group" class="select2 form-control" name="group_id" style="width: 100%">
                  <option value="">Select Group</option>
                  @foreach ($groups as $group)
                  <option value="{{ $group->id }}" {{ $item->group_id == $group->id ? 'selected' : '' }}>{{ $group->group_name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group">
                <label>Status</label>
                <div data-toggle="buttons">
                  <label class="btn btn-default {{ $item->item_status == 'active' ? 'active' : '' }}">
                    <input type="radio" name="item_status" value="active" {{ $item->item_status == 'active' ? 'checked' : '' }}> Active
                  </label>
                  <label class="btn btn-default {{ $item->item_status == 'inactive' ? 'active' : '' }}">
                    <input type="radio" name="item_status" value="inactive" {{ $item->item_status == 'inactive' ? 'checked' : '' }}> Inactive
                  </label>
                </div>
              </div>
          </div>
          <div class="col-md-12 col-xs-12 left-margin">
            <div class="form-group pull-right">
              <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Submit</button>
            </div>
          </div>
          <input type="hidden" name="url" value="{{ url()->previous() }}">
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
{{-- <script>
  $(document).ready(function() {
    // Populate type dropdown
    $('#type').on('change', function() {
      var type_id = $(this).val();
      if (type_id) {
        $.ajax({
          url: "{{ route('groups.getGroupsFromTypes') }}"
, type: 'GET'
, data: {
id: type_id
}
, dataType: 'json'
, success: function(data) {
console.log(data);
$('#group').empty();
$('#group').append('<option value="">Select Group</option>');
$.each(data, function(key, value) {
$('#group').append('<option value="' + value.id + '">' + value.group_name + '</option>');
});
}
});
} else {
$('#group').empty();
$('#group').append('<option value="">Select Group</option>');
}
});
});

</script> --}}

@endsection
