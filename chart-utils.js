// ========== CHART UTILITY FUNCTIONS ==========

// Safely destroy an existing chart
function destroyChart(chartId) {
  if (window[chartId] && typeof window[chartId].destroy === 'function') {
    window[chartId].destroy();
    window[chartId] = null;
  }
}

// Validate that a chart element exists
function validateChartElement(elementId) {
  const element = document.querySelector("#" + elementId);
  if (!element) {
    console.error(`Chart element #${elementId} not found in DOM`);
    return false;
  }
  return true;
}

// Show error message in chart container
function showChartError(elementId, message) {
  const element = document.querySelector("#" + elementId);
  if (element) {
    element.innerHTML = `<div style="padding: 20px; text-align: center; color: #ff4560;">
      <p><strong>Chart Error:</strong> ${message}</p>
    </div>`;
  }
}

// ========== CHART CREATION FUNCTIONS ==========

// Create a bar chart
function createBarChart(elementId, data, options = {}) {
  const chartVarName = 'chart_' + elementId;
  
  // Validate element exists
  if (!validateChartElement(elementId)) {
    showChartError(elementId, 'Chart container not found');
    return;
  }
  
  // Validate data
  if (!data || !data.labels || !data.values) {
    showChartError(elementId, 'Invalid chart data');
    console.error('Bar chart requires data.labels and data.values');
    return;
  }
  
  // Destroy existing chart
  destroyChart(chartVarName);
  
  // Prepare data with colors
  const chartData = data.labels.map((label, index) => ({
    x: label,
    y: Number(data.values[index]) || 0,
    fillColor: data.colors && data.colors[index] ? data.colors[index] : '#008FFB'
  }));
  
  const chartOptions = {
    series: [{
      name: options.seriesName || 'Count',
      data: chartData
    }],
    chart: {
      type: 'bar',
      height: options.height || 350
    },
    plotOptions: {
      bar: {
        horizontal: options.horizontal || false,
        columnWidth: options.columnWidth || '55%',
        endingShape: 'rounded',
        distributed: true
      },
    },
    dataLabels: {
      enabled: options.dataLabels !== false
    },
    stroke: {
      show: true,
      width: 2,
      colors: ['transparent']
    },
    xaxis: {
      categories: data.labels,
      title: {
        text: options.xAxisTitle || ''
      }
    },
    yaxis: {
      title: {
        text: options.yAxisTitle || ''
      }
    },
    fill: {
      opacity: 1
    },
    colors: data.colors || ['#008FFB'],
    legend: {
      show: false
    },
    tooltip: {
      y: {
        formatter: options.tooltipFormatter || function (val) {
          return val;
        }
      }
    }
  };
  
  try {
    window[chartVarName] = new ApexCharts(document.querySelector("#" + elementId), chartOptions);
    window[chartVarName].render();
  } catch (error) {
    console.error('Error creating bar chart:', error);
    showChartError(elementId, 'Failed to render chart');
  }
}

// Create a stacked bar chart
function createStackedBarChart(elementId, data, options = {}) {
  const chartVarName = 'chart_' + elementId;
  
  // Validate element exists
  if (!validateChartElement(elementId)) {
    showChartError(elementId, 'Chart container not found');
    return;
  }
  
  // Validate data
  if (!data || !data.categories || !data.series || !Array.isArray(data.series)) {
    showChartError(elementId, 'Invalid chart data');
    console.error('Stacked bar chart requires data.categories and data.series array');
    return;
  }
  
  // Destroy existing chart
  destroyChart(chartVarName);
  
  // Process series data
  const processedSeries = data.series.map(s => ({
    name: s.name,
    data: s.data.map(v => Number(v) || 0)
  }));
  
  const chartOptions = {
    series: processedSeries,
    chart: {
      type: 'bar',
      height: options.height || 350,
      stacked: true,
    },
    plotOptions: {
      bar: {
        horizontal: options.horizontal || false,
        columnWidth: options.columnWidth || '55%',
      },
    },
    dataLabels: {
      enabled: options.dataLabels !== false
    },
    stroke: {
      show: true,
      width: 2,
      colors: ['transparent']
    },
    xaxis: {
      categories: data.categories,
      title: {
        text: options.xAxisTitle || ''
      }
    },
    yaxis: {
      title: {
        text: options.yAxisTitle || ''
      }
    },
    fill: {
      opacity: 1
    },
    colors: options.colors || ['#008FFB', '#00E396', '#FEB019', '#FF4560', '#775DD0'],
    legend: {
      show: true,
      position: options.legendPosition || 'top',
      horizontalAlign: 'left'
    },
    tooltip: {
      y: {
        formatter: options.tooltipFormatter || function (val) {
          return val;
        }
      }
    }
  };
  
  try {
    window[chartVarName] = new ApexCharts(document.querySelector("#" + elementId), chartOptions);
    window[chartVarName].render();
  } catch (error) {
    console.error('Error creating stacked bar chart:', error);
    showChartError(elementId, 'Failed to render chart');
  }
}

// Create a monochrome pie chart
function createMonochromePieChart(elementId, data, options = {}) {
  const chartVarName = 'chart_' + elementId;
  
  // Validate element exists
  if (!validateChartElement(elementId)) {
    showChartError(elementId, 'Chart container not found');
    return;
  }
  
  // Validate data
  if (!data || !data.labels || !data.values) {
    showChartError(elementId, 'Invalid chart data');
    console.error('Pie chart requires data.labels and data.values');
    return;
  }
  
  // Destroy existing chart
  destroyChart(chartVarName);
  
  // Convert values to numbers
  const values = data.values.map(v => Number(v) || 0);
  
  // Generate colors if not provided
  const defaultColors = ['#008FFB', '#00E396', '#FEB019', '#FF4560', '#775DD0', '#546E7A', '#26a69a', '#FF6178'];
  const colors = data.colors || defaultColors;
  
  const chartOptions = {
    series: values,
    chart: {
      width: '100%',
      height: options.height || 350,
      type: 'pie',
    },
    labels: data.labels,
    colors: colors,
    theme: {
      monochrome: {
        enabled: true,
        color: options.monochromeColor || colors[0],
        shadeTo: 'light',
        shadeIntensity: 0.65
      }
    },
    plotOptions: {
      pie: {
        dataLabels: {
          offset: -5,
        },
      },
    },
    dataLabels: {
      formatter: options.dataLabelsFormatter || function(val, opts) {
        const name = opts.w.globals.labels[opts.seriesIndex];
        return [name, val.toFixed(1) + '%'];
      },
    },
    legend: {
      show: options.showLegend !== false,
      position: options.legendPosition || 'bottom',
    },
  };
  
  try {
    window[chartVarName] = new ApexCharts(document.querySelector("#" + elementId), chartOptions);
    window[chartVarName].render();
  } catch (error) {
    console.error('Error creating monochrome pie chart:', error);
    showChartError(elementId, 'Failed to render chart');
  }
}

// Create a colorful pie chart
function createColorfulPieChart(elementId, data, options = {}) {
  const chartVarName = 'chart_' + elementId;
  
  // Validate element exists
  if (!validateChartElement(elementId)) {
    showChartError(elementId, 'Chart container not found');
    return;
  }
  
  // Validate data
  if (!data || !data.labels || !data.values) {
    showChartError(elementId, 'Invalid chart data');
    console.error('Pie chart requires data.labels and data.values');
    return;
  }
  
  // Destroy existing chart
  destroyChart(chartVarName);
  
  // Convert values to numbers
  const values = data.values.map(v => Number(v) || 0);
  
  // Colorful palette
  const defaultColors = ['#008FFB', '#00E396', '#FEB019', '#FF4560', '#775DD0', '#546E7A', '#26a69a', '#FF6178'];
  const colors = data.colors || defaultColors;
  
  const chartOptions = {
    series: values,
    chart: {
      width: '100%',
      height: options.height || 350,
      type: 'pie',
    },
    labels: data.labels,
    colors: colors,
    plotOptions: {
      pie: {
        dataLabels: {
          offset: -5,
        },
      },
    },
    dataLabels: {
      formatter: options.dataLabelsFormatter || function(val, opts) {
        const name = opts.w.globals.labels[opts.seriesIndex];
        return [name, val.toFixed(1) + '%'];
      },
    },
    legend: {
      show: options.showLegend !== false,
      position: options.legendPosition || 'bottom',
    },
  };
  
  try {
    window[chartVarName] = new ApexCharts(document.querySelector("#" + elementId), chartOptions);
    window[chartVarName].render();
  } catch (error) {
    console.error('Error creating colorful pie chart:', error);
    showChartError(elementId, 'Failed to render chart');
  }
}

// Create a pie chart (wrapper that uses monochrome option)
function createPieChart(elementId, data, options = {}) {
  if (options.monochrome) {
    createMonochromePieChart(elementId, data, options);
  } else {
    createColorfulPieChart(elementId, data, options);
  }
}


// Create a line chart
function createLineChart(elementId, data, options = {}) {
  const chartVarName = 'chart_' + elementId;
  
  // Validate element exists
  if (!validateChartElement(elementId)) {
    showChartError(elementId, 'Chart container not found');
    return;
  }
  
  // Validate data
  if (!data || !data.labels || !data.values) {
    showChartError(elementId, 'Invalid chart data');
    console.error('Line chart requires data.labels and data.values');
    return;
  }
  
  // Destroy existing chart
  destroyChart(chartVarName);
  
  // Convert values to numbers
  const values = data.values.map(v => Number(v) || 0);
  
  const chartOptions = {
    series: [{
      name: options.seriesName || 'Value',
      data: values
    }],
    chart: {
      height: options.height || 350,
      type: 'line',
      zoom: {
        enabled: options.zoom !== false
      }
    },
    dataLabels: {
      enabled: options.dataLabels || false
    },
    stroke: {
      curve: options.curve || 'straight',
      colors: [options.lineColor || '#008FFB']
    },
    grid: {
      row: {
        colors: ['#f3f3f3', 'transparent'],
        opacity: 0.5
      },
    },
    xaxis: {
      categories: data.labels,
      title: {
        text: options.xAxisTitle || ''
      }
    },
    yaxis: {
      title: {
        text: options.yAxisTitle || ''
      }
    }
  };
  
  try {
    window[chartVarName] = new ApexCharts(document.querySelector("#" + elementId), chartOptions);
    window[chartVarName].render();
  } catch (error) {
    console.error('Error creating line chart:', error);
    showChartError(elementId, 'Failed to render chart');
  }
}
