@extends('layouts.master')


@section('content')


<div class="panel panel-default">
  <div class="panel-heading">
	<h3 class="panel-title">
		Hei!
	</h3>
  </div>
  <div class="panel-body">

	<form class="form-horizontal" role="form" method="POST" action="{{ URL::action('UsersController@postActivateNewAccount') }}">

		<p>
			Hei, deg har jeg ikke sett før. Velkommen skal du være, men har du fått aktiveringskode? Hvis ikke, spør Dan Michael.
		</p>
	  <div class="form-group">
		<label for="inputAct" class="col-sm-2 control-label">Aktiveringskode</label>
		<div class="col-sm-10">
		  <input type="text" class="form-control" id="inputAct" name="activationcode" placeholder="Kode">
		</div>
	  </div>

	  <div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
		  <button type="submit" class="btn btn-default">Fortsett</button>
		</div>
	  </div>

	</form>

  </div>
</div>


@stop