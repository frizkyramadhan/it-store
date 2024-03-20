<form action="{{ url('transfers/' . $model->id) }}" method="post" onsubmit="return confirm('Are you sure want to delete this data?')">
  <a class="btn btn-xs btn-info" href="{{ url('transfers/' . $model->id) }}"><i class="fa fa-info-circle"></i> Detail</a>
  {{-- <a class="btn btn-xs btn-warning" href="{{ url('transfers/' . $model->id . '/edit') }}"><i class="fa fa-pencil"></i> &nbsp;Edit&nbsp;</a>
  @method('delete')
  @csrf
  <button class="btn btn-xs btn-danger"><i class="fa fa-times"></i> Delete</button> --}}
</form>
