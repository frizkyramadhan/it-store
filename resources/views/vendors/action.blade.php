<form action="{{ url('vendors/' . $model->id) }}" method="post" onsubmit="return confirm('Are you sure want to delete this data?')">
  <a class="btn btn-xs btn-warning" data-toggle="modal" data-target=".bs-example-modal-md-{{ $model->id }}"><i class="fa fa-pencil"></i> &nbsp;Edit&nbsp;</a>
  @method('delete')
  @csrf
  <button class="btn btn-xs btn-danger"><i class="fa fa-times"></i> Delete</button>
</form>
