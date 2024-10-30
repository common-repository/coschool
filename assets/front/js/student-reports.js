jQuery(function($) {
	
	const labels = COSCHOOL.progress.student_progress.intervals;
	const data = {
	  labels: labels,
	  datasets: [
	    {
		    label: 'Daily Progress',
		    data: COSCHOOL.progress.student_progress,
		    borderColor: '#0088FF',
		    backgroundColor: '#0088FF',
		    yAxisID: 'progress',
	    },
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
	      progress: {
	        type: 'linear',
	        display: true,
	        position: 'left',
	        beginAtZero :true
	      },
	    }
	  },
	};
	// </block:config>


	// init


	new Chart($('#coschool-student-report'), config);


});