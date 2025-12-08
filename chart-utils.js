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
      enabled: options.dataLabels !== false,
      formatter: function (val, opts) {
        // For bar charts with x/y structure, val is the y value
        const num = Number(val);
        if (num === 0) return '0';
        if (num >= 1000) return num.toFixed(0);
        if (num >= 1) return num.toFixed(2);
        return num.toFixed(3);
      }
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
      },
      labels: {
        rotate: -45,
        rotateAlways: false,
        hideOverlappingLabels: true,
        trim: true,
        style: {
          fontSize: '12px'
        }
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
          const num = Number(val);
          if (num === 0) return '0';
          if (num >= 1000) return num.toFixed(0);
          if (num >= 1) return num.toFixed(2);
          return num.toFixed(3);
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
      enabled: options.dataLabels !== false,
      formatter: function (val) {
        const num = Number(val);
        if (num === 0) return '0';
        if (num >= 1000) return num.toFixed(0);
        if (num >= 1) return num.toFixed(2);
        return num.toFixed(3);
      }
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
      },
      labels: {
        rotate: -45,
        rotateAlways: false,
        hideOverlappingLabels: true,
        trim: true,
        style: {
          fontSize: '12px'
        }
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
          const num = Number(val);
          if (num === 0) return '0';
          if (num >= 1000) return num.toFixed(0);
          if (num >= 1) return num.toFixed(2);
          return num.toFixed(3);
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

// Create a histogram using Plotly with Freedman-Diaconis rule for bin width
function createHistogram(elementId, data, title, xAxisTitle, yAxisTitle) {
  if (!data || data.length === 0) {
    document.getElementById(elementId).innerHTML = '<p style="text-align: center; padding: 40px; color: #6c757d;">No data available.</p>';
    return;
  }
  
  // Convert to numbers and sort
  const values = data.map(v => Number(v)).filter(v => !isNaN(v)).sort((a, b) => a - b);
  
  if (values.length === 0) {
    document.getElementById(elementId).innerHTML = '<p style="text-align: center; padding: 40px; color: #6c757d;">No valid data available.</p>';
    return;
  }
  
  // Calculate bin width using Freedman-Diaconis rule
  function calculateBinWidth(data) {
    const n = data.length;
    if (n < 2) return 1;
    
    // Calculate IQR (Interquartile Range) with linear interpolation
    // Using method: position = p * (n + 1) - 0.5 (0-based)
    const q1Pos = 0.25 * (n + 1) - 0.5;
    const q3Pos = 0.75 * (n + 1) - 0.5;
    
    // Get 0-based indices for interpolation
    const q1Lower = Math.floor(q1Pos);
    const q1Upper = Math.ceil(q1Pos);
    const q1Fraction = q1Pos - q1Lower;
    const q1 = q1Upper >= n
      ? data[n - 1]
      : (q1Fraction === 0 ? data[q1Lower] : data[q1Lower] + q1Fraction * (data[q1Upper] - data[q1Lower]));
    
    // Q3 calculation with interpolation
    const q3Lower = Math.floor(q3Pos);
    const q3Upper = Math.ceil(q3Pos);
    const q3Fraction = q3Pos - q3Lower;
    const q3 = q3Upper >= n
      ? data[n - 1]
      : (q3Fraction === 0 ? data[q3Lower] : data[q3Lower] + q3Fraction * (data[q3Upper] - data[q3Lower]));
    
    const iqr = q3 - q1;
    
    // Freedman-Diaconis rule: bin_width = 2 * IQR / n^(1/3)
    const binWidth = (2 * iqr) / Math.pow(n, 1/3);
    
    return binWidth > 0 ? binWidth : 1;
  }
  
  const binWidth = calculateBinWidth(values);
  
  // Calculate start and end for bins
  const dataMin = Math.min(...values);
  const dataMax = Math.max(...values);
  
  const trace = {
    x: values,
    type: 'histogram',
    xbins: {
      start: dataMin,
      end: dataMax + binWidth,
      size: binWidth
    },
    marker: {
      color: '#008FFB',
      line: {
        color: '#ffffff',
        width: 2
      }
    },
    autobinx: false,
    text: [],
    textposition: 'none',
    hovertemplate: '%{y}<extra></extra>'
  };
  
  const layout = {
    title: title,
    xaxis: {
      title: xAxisTitle
    },
    yaxis: {
      title: yAxisTitle
    },
    bargap: 0,
    autosize: true,
    margin: {
      l: 60,
      r: 40,
      t: 60,
      b: 60
    }
  };
  
  Plotly.newPlot(elementId, [trace], layout, {responsive: true});
}

// Create RRC choropleth map using Plotly
function createRRCMap(elementId, rrcData, countryToISO3, continentCountries) {
  if (!rrcData || rrcData.length === 0) {
    document.getElementById(elementId).innerHTML = '<p style="text-align: center; padding: 40px; color: #6c757d;">No RRC data available for the selected region.</p>';
    return;
  }
  
  let locations = [];
  let values = [];
  let hoverTexts = [];

  rrcData.forEach(item => {
    if (item.country) {
      const iso = countryToISO3[item.country];
      if (iso) {
        locations.push(iso);
        values.push(parseFloat(item["rrc value"]));
        hoverTexts.push(`${item.country}: ${item["rrc value"]}%`);
      }
    } else if (item.continent) {
      const countries = continentCountries[item.continent] || [];
      countries.forEach(c => {
        locations.push(c);
        values.push(parseFloat(item["rrc value"]));
        hoverTexts.push(`${item.continent}: ${item["rrc value"]}%`);
      });
    }
  });
  
  const data = [{
    type: 'choropleth',
    locations: locations,
    z: values,
    locationmode: 'ISO-3',
    colorscale: 'Reds',
    colorbar: {title: 'RRC (%)'},
    text: hoverTexts,
    hoverinfo: 'text'
  }];

  const layout = {
    title: 'Regional Risk Concentration Map',
    geo: {showframe: false, showcoastlines: true, projection: {type: 'natural earth'}},
    margin: {t: 50},
    autosize: true
  };

  Plotly.newPlot(elementId, data, layout, {responsive: true});
}
