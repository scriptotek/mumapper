<!DOCTYPE html>
<html lang="nb">
<head>
  <title>μmapper {{ isset($subtitle) ? ' : ' . $subtitle : '' }}</title>

  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
 
  <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
  <!--[if lt IE 9]>
  <script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.6/html5shiv.min.js"></script>
  <![endif]-->
 
  <!-- jQuery -->
  <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
  <script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>

  <!-- Bootstrap -->
  <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
  <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">

  <!-- Bootstrap-select -->
  <link rel="stylesheet" type="text/css" href="/components/bootstrap-select/bootstrap-select.css">
  <script src="/components/bootstrap-select/bootstrap-select.min.js"></script>

  <!-- Typeahead-->
  <script src="/typeahead.js/dist/typeahead.bundle.min.js"></script>

  <!-- Open Sans -->
  <link href='//fonts.googleapis.com/css?family=Open+Sans&amp;subset=latin,latin-ext' rel='stylesheet' type='text/css'>

  @yield('header')

  <!-- Code highlighting-->
<!--  <link href="/components/prism/themes/prism.css" rel="stylesheet" />
  <link href="/components/prism/themes/prism-coy.css" rel="stylesheet" />
  <script src="/components/prism/prism.js"></script>
-->

  <!-- Local stylesheet -->
  <link rel="stylesheet" type="text/css" href="{{ URL::to('site.css') }}">

</head>
<body>
  <div class="container">

  <header class="row">

    <div class="col-sm-2">
      <h1>
        <a href="{{ URL::to('/') }}">
          μmapper
        </a>
      </h1>
    </div>

    <div class="col-sm-10" style="text-align:right;">

      @if (Auth::check())

        Logget inn som {{ Auth::user()->name }}
        <span style="color:#bbb;">|</span>
        <a href="{{ URL::action('ActivityController@getIndex', [ Auth::user()->id ]) }}">Min aktivitet</a>
        <span style="color:#bbb;">|</span>
        <a href="{{ URL::action('UsersController@getLogout') }}">Logg ut</a>
      
      @else 

        <a href="{{ URL::action('UsersController@getLogin') }}">Logg inn</a>

      @endif

      <p style="margin-top:6px;">
        <a href="{{ URL::to('/relationships') }}" style="margin:1px 6px;">
          <span class="glyphicon glyphicon-resize-horizontal"></span>
          Relasjoner
        </a>
        <a href="{{ URL::to('/tags') }}" style="margin:1px 6px;">
          <span class="glyphicon glyphicon-list"></span>
          Lister
        </a>
        <a href="{{ URL::to('/activity') }}" style="margin:1px 6px;">
          <span class="glyphicon glyphicon-dashboard"></span>
          Aktivitet
        </a>
      </p>

    </div>

  </header>

    <div>
      @section('sidebar')

      @if (!empty($status))
        <div class="alert alert-info">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          {{$status}}
        </div>
      @endif

      @if ($e = $errors->all('<li>:message</li>'))
        <div class="alert alert-danger">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          Oi!
          <ul>
          @foreach ($e as $msg)
            {{$msg}}
          @endforeach
          </ul>
        </div>
      @endif

      @show
    </div>

    @yield('content')

    <footer style="margin-top:3em; margin-bottom: 2em; font-size: 85%;padding:1em 10%; text-align: center; border-top: 1px solid #ccc;">
      μmapper er et verktøy for å utarbeide og vedlikeholde
      <em class="help" data-toggle="tooltip" title="crosswalks: table of mappings between the concepts in two or more structured vocabularies">overganger</em>
      mellom begreper i Realfagstermer (RT) og andre strukturerte vokabularer som
      Tekord (TEK) og Dewey (DDK23).
    </footer>

  </div>

  @yield('scripts')

  <script type="text/javascript">

    $(document).ready(function() {

    });
  </script>

</body>
</html>
