@extends('layouts.admin.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">添加权限</div>

                    <div class="panel-body">
                        <form class="form-horizontal" method="POST" action="{{ route('admin/roleAdd') }}">
                            {{ csrf_field() }}

                            <div class="form-group{{$errors->has('name') ? ' has-error' : '' }}">
                                <label for="name" class="col-md-4 control-label">角色名</label>

                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>

                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('detail') ? ' has-error' : '' }}">
                                <label for="detail" class="col-md-4 control-label">备注</label>

                                <div class="col-md-6">
                                    <input id="detail" type="detail" class="form-control" name="detail" required>

                                    @if ($errors->has('detail'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('detail') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>


                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        添加
                                    </button>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
