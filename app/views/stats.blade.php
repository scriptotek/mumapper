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
                text: 'Processed mapping candidates',
                align: 'left'
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
                    tickInterval: 14,
	            title: {
	                enabled: false
	            }
	        },
	        yAxis: {
	            title: {
	                text: 'Number of mapping candidates processed'
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
                name: 'Humord: Waiting for review',
	            data: data.y[0]
	        },{
                name: 'Humord: Completed',
	            data: data.y[1]
	        },{
                name: 'Realfagstermer: Waiting for review',
	            data: data.y[3]
	        },{
                name: 'Realfagstermer: Completed',
	            data: data.y[4]
	        }]
	    });
    });
});


</script>

@stop

