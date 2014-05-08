@extends('layouts.master')


@section('content')


<div class="panel panel-default">
  <div class="panel-heading">
	<h3 class="panel-title">

		<a href="{{ URL::action('UsersController@getLoginUsingGoogle') }}">
			Logg inn med Google
		</a>

	</h3>
  </div>
</div>


<div class="panel panel-default">
  <div class="panel-heading">
	<h3 class="panel-title">
		<a href="#" onclick="$('.panel-body').toggle();">
			Logg inn med lokal innlogging
		</a>
	</h3>
  </div>
  <div class="panel-body" style="display:none;">

	<form class="form-horizontal" role="form" method="POST" action="{{ URL::action('UsersController@postLogin') }}">
	  <div class="form-group">
		<label for="inputEmail3" class="col-sm-2 control-label">Epost</label>
		<div class="col-sm-10">
		  <input type="email" class="form-control" id="inputEmail3" name="user" placeholder="Epost">
		</div>
	  </div>
	  <div class="form-group">
		<label for="inputPassword3" class="col-sm-2 control-label">Passord</label>
		<div class="col-sm-10">
		  <input type="password" class="form-control" id="inputPassword3" name="pass" placeholder="Passord">
		</div>
	  </div>
	  <!--<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
		  <div class="checkbox">
			<label>
			  <input type="checkbox"> Remember me
			</label>
		  </div>
		</div>
	  </div>-->
	  <div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
		  <button type="submit" class="btn btn-default">Logg inn</button>
		</div>
	  </div>
	</form>

  </div>
</div>


@stop