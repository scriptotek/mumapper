@extends('layouts.master')


@section('content')

<div class="panel panel-default">
  <div class="panel-body">

  	<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>

  </div>
</div>


@stop

@section('scripts')

<script type="text/javascript">



$(function () {
	$.getJSON('stats-json').done(function(data) {
	    $('#container').highcharts({
	        chart: {
	            type: 'area'
	        },
	        title: {
	            text: 'Stats'
	        },
	        legend: {
			    layout: 'vertical',
			    align: 'right',
			    verticalAlign: 'top',
			    backgroundColor: '#FFFFFF'
			},
	        xAxis: {
	            categories: data.x,
	            tickmarkPlacement: 'on',
	            title: {
	                enabled: false
	            }
	        },
	        yAxis: {
	            title: {
	                text: 'Antall'
	            }
	        },
	        tooltip: {
	            shared: true,
	            // valueSuffix: ' millions'
	        },
	        plotOptions: {
	            area: {
	                stacking: 'normal',
	                lineColor: '#666666',
	                lineWidth: 1,
	                marker: {
	                    lineWidth: 1,
	                    lineColor: '#666666'
	                }
	            }
	        },
	        series: [{
	            name: 'Venter på godkjenning (Humord)',
	            data: data.y[0]
	        },{
	            name: 'Godkjent (Humord)',
	            data: data.y[1]
	        },{
	            name: 'Avvist (Humord)',
	            data: data.y[2],
	            visible: false
	        },{
	            name: 'Venter på godkjenning (Realfagstermer)',
	            data: data.y[3]
	        },{
	            name: 'Godkjent (Realfagstermer)',
	            data: data.y[4]
	        },{
	            name: 'Avvist (Realfagstermer)',
	            data: data.y[5],
	            visible: false
	        }]
	    });
    });
});


</script>

@stop

