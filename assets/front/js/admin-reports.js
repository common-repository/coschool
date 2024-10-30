
jQuery(function($) {

	$('#coschool-periodic-group-by').change(function(e) {
		var $by = $(this).val();
		var $select = $('#coschool-periodic-item');
		if( $by != '' ) {
			$.ajax({
				url: COSCHOOL.ajaxurl,
				type: 'POST',
				data: { action: 'periodic-item', by: $by, _wpnonce: COSCHOOL.nonce },
				success: function(resp) {
					$select.html('');

					$.each(resp.items, function(index, value){
					    $("<option/>", {
					        value: index,
					        text: value
					    }).appendTo($select);
					});

					// keep it selected
					var vars = [], hash;
				    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
				    for(var i = 0; i < hashes.length; i++) {
				        hash = hashes[i].split('=');
				        if( 'item' == hash[0] ) {
				        	$($select).val(hash[1]);
				        }
				    }

					$($select).attr('disabled',false).show();
				},
				error: function(err) {
					console.log(err);
				}
			});
		}
		else {
			$($select).attr('disabled',true).hide();
		}
	}).change();

	$('.coschool-date-range').change(function(e) {
		if( 'custom' == $(this).val() ) {
			$('.coschool-date',$(this).parent()).attr('disabled',false).show();
		}
		else {
			$('.coschool-date',$(this).parent()).attr('disabled',true).hide();
		}
	}).change();

	// <block:setup:1>

	const labels = COSCHOOL.reports.enrollments.intervals;
	const data = {
	  labels: labels,
	  datasets: [
	    {
		    label: 'Enrollments',
		    data: COSCHOOL.reports.enrollments.sales,
		    borderColor: '#0088FF',
		    backgroundColor: '#0088FF',
		    yAxisID: 'enrollments',
	    },
	    {
	      label: 'Earnings',
	      data: COSCHOOL.reports.enrollments.earnings,
	      borderColor: '#FF7F00',
	      backgroundColor: '#FF7F00',
	      yAxisID: 'earnings',
	    }
	  ]
	};
	// </block:setup>

	// <block:config:0>
	const config = {
	  type: 'line',
	  data: data,
	  options: {
	    responsive: true,
	    interaction: {
	      mode: 'index',
	      intersect: false,
	    },
	    stacked: false,
	    plugins: {
	      // title: {
	      //   display: true,
	      //   text: 'Chart.js Line Chart - Multi Axis'
	      // }
	    },
	    scales: {
	      enrollments: {
	        type: 'linear',
	        display: true,
	        position: 'left',
	      },
	      earnings: {
	        type: 'linear',
	        display: true,
	        position: 'right',

	        // grid line settings
	        grid: {
	          drawOnChartArea: true, // only want the grid lines for one axis to show up
	        },
	      },
	    }
	  },
	};
	// </block:config>


	// init


	new Chart($('#coschool-periodic-report'), config);

	const top_sales_sales_data = {
	  	labels: COSCHOOL.reports.top_sales.sales.labels,
	  	datasets: [
	    	{
	      		label: 'Enrollments',
	      		data: COSCHOOL.reports.top_sales.sales.data,
	      		backgroundColor: [ '#FFDE17', '#F7941D', '#F36523', '#EF4136', '#D22334', '#9E1F64', '#92278F', '#662E91', '#21409A', '#008870', '#01A14B', '#8EC63F', '#8EC63F'],
	    	}
	  	]
	};
	const top_sales_sales_config = {
	  type: 'doughnut',
	  data: top_sales_sales_data,
	  options: {
	    responsive: true,
	    plugins: {
	      legend: {
	        position: 'left', // 'top'
	      },
	      title: {
	        display: true,
	        text: 'Top Seller by Count'
	      }
	    }
	  },
	};
	new Chart($('#coschool-share-report-sales'), top_sales_sales_config);

	const top_sales_earnings_data = {
	  labels: COSCHOOL.reports.top_sales.earnings.labels,
	  datasets: [
	    {
	      label: 'Earnings',
	      data: COSCHOOL.reports.top_sales.earnings.data,
	      backgroundColor: [ '#FFDE17', '#F7941D', '#F36523', '#EF4136', '#D22334', '#9E1F64', '#92278F', '#662E91', '#21409A', '#008870', '#01A14B', '#8EC63F', '#8EC63F'],
	    }
	  ]
	};
	const top_sales_earnings_config = {
	  type: 'doughnut',
	  data: top_sales_earnings_data,
	  options: {
	    responsive: true,
	    plugins: {
	      legend: {
	        position: 'right', // 'top'
	      },
	      title: {
	        display: true,
	        text: 'Top Seller by Earnings'
	      }
	    }
	  },
	};
	new Chart($('#coschool-share-report-earning'), top_sales_earnings_config);
})