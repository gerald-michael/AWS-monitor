@extends('main')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-lg-10">
            <div class="panel panel-default">
                <div class="panel-heading">Edit User</div>
                    <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="{{ action('UserController@update', $id) }}">
                        {{ csrf_field() }}
                        <input name="_method" type="hidden" value="PATCH">
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Name</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{ $users->name }}" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail Address</label>
                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ $users->email }}" required>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">Phone Number</label>
                            <div class="col-md-6">
                                <input id="phone" type="tel" class="form-control" name="phone" value="{{ $users->phone }}" required>
                                @if ($errors->has('phone'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
<!-- 
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Password</label>
                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password">
                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div> -->
<!-- 
                        <div class="form-group">
                            <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>
                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation">
                            </div>
                        </div> -->

                            {{-- <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label for="name" class="col-md-4 control-label">Attarch location</label>

                                <div class="col-md-6">
                                    <select id="locate" type="text" class="form-control" name="admin"   autofocus>
                                    <option name = "locat" value=" " >Make Admin</option>

                                    @foreach ($stationAttarched as $SAT)        
                                    

                                    <option name = "admin" value={{ $SAT['station_id'] }}  " >{{ $SAT['Location'] }}</option>
                                    
                                    @endforeach

                                    </select>
                                </div>
                            </div> --}}

<!-- 
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <input type="checkbox" name="admin" value=" " id="admin">
                                <label for="admin">Make Admin</label>
                            </div>
                        </div> -->

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection