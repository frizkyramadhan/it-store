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
              <div class="col-md-4 col-xs-12 left-margin">
                <div class="form-group">
                  <label>Item Code <span class="required">*</span></label>
                  <input type="text" class="form-control" name="item_code" value="{{ $item->item_code }}" required>
                </div>
                <div class="form-group">
                  <label>Description <span class="required">*</span></label>
                  <input type="text" class="form-control" name="description" value="{{ $item->description }}" required>
                </div>
                <div class="form-group">
                  <label>Type <span class="required">*</span></label>
                  <select id="type" class="select2 form-control" name="type_id" style="width: 100%" required>
                    <option value="">Select Type</option>
                    @foreach ($types as $type)
                    <option value="{{ $type->id }}" {{ $item->type_id == $type->id ? 'selected' : '' }}>{{ $type->type_name }}</option>
                    @endforeach
                  </select>
                </div>
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
              <div class="col-md-8 col-xs-12 left-margin">
                <div class="" role="tabpanel" data-example-id="togglable-tabs">
                  <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#tab_content1" id="dimension-tab" role="tab" data-toggle="tab" aria-expanded="true">Dimension & Weight</a>
                    </li>
                    <li role="presentation" class=""><a href="#tab_content3" role="tab" id="category-tab" data-toggle="tab" aria-expanded="false">Category & Manufacture</a>
                    </li>
                  </ul>
                  <div id="myTabContent" class="tab-content">
                    <div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="dimension-tab">
                      <div class="col-md-6 col-xs-12 left-margin">
                        <div class="form-group">
                          <label>Length</label>
                          <input type="number" step="0.01" class="form-control" name="dims_l" value="{{ $item->dims_l }}">
                        </div>
                        <div class="form-group">
                          <label>Width</label>
                          <input type="number" step="0.01" class="form-control" name="dims_w" value="{{ $item->dims_w }}">
                        </div>
                        <div class="form-group">
                          <label>Height</label>
                          <input type="number" step="0.01" class="form-control" name="dims_h" value="{{ $item->dims_h }}">
                        </div>
                        <div class="form-group">
                          <label>Weight / Each</label>
                          <input type="number" step="0.0001" class="form-control" name="weight_ea" value="{{ $item->weight_ea }}">
                        </div>
                      </div>
                      <div class="col-md-6 col-xs-12 left-margin">
                        <div class="form-group">
                          <label>NEC / Each</label>
                          <input type="text" class="form-control" name="nec_ea" value="{{ $item->nec_ea }}">
                        </div>
                        <div class="form-group">
                          <label>NEC / Box</label>
                          <input type="text" class="form-control" name="nec_box" value="{{ $item->nec_box }}">
                        </div>
                        <div class="form-group">
                          <label>GW / Box</label>
                          <input type="number" step="0.0001" class="form-control" name="gw_box" value="{{ $item->gw_box }}">
                        </div>
                        <div class="form-group">
                          <label>NW / Box</label>
                          <input type="number" step="0.0001" class="form-control" name="nw_box" value="{{ $item->nw_box }}">
                        </div>
                      </div>
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="tab_content3" aria-labelledby="category-tab">
                      <div class="col-md-6 col-xs-12 left-margin">
                        <div class="form-group">
                          <label>UN Number</label>
                          <input type="text" class="form-control" name="un_no" value="{{ $item->un_no }}">
                        </div>
                        <div class="form-group">
                          <label>Classification</label>
                          <input type="text" class="form-control" name="classification" value="{{ $item->classification }}">
                        </div>
                        <div class="form-group">
                          <label>Ex</label>
                          <input type="text" class="form-control" name="ex" value="{{ $item->ex }}">
                        </div>
                      </div>
                      <div class="col-md-6 col-xs-12 left-margin">
                        <div class="form-group">
                          <label>Manufacture From</label>
                          <input type="text" class="form-control" name="manu_from" value="{{ $item->manu_from }}">
                        </div>
                        <div class="form-group">
                          <label>Shelf Life</label>
                          <input type="text" class="form-control" name="shelf_life" value="{{ $item->shelf_life }}">
                        </div>
                      </div>
                    </div>
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
<script>
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

</script>

@endsection
