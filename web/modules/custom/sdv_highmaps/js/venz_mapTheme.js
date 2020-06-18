/*
 | Highcharts theme for maps
 | @author Martin Kosterman
 | @date May 2017
 |
 | This is a sample theme, which may be uploaded as term for taxonomy 'highhcarts_themes' 
 | Only then it will be loaded for map nodes
 */

// General theming for Highmaps
Highcharts.mapTheme = {
    lang: {
      decimalPoint: ',',
      thousandsSep: '.',
      // Specific text labels for drill down map

      zoomIn: 'Inzoomen',
      zoomOut: 'Uitzoomen',
      downloadCSV : 'CSV downloaden',
      downloadXLS : 'XLS downloaden',
      viewData : 'Tabel tonen'
    },
    chart: {
      reflow: false, // ???
      backgroundColor: '#ffffff',
      style: {
        color: '#ff0000',
        fontFamily: 'RijksoverheidSans, Verdana',
        fontSize: '14px',
        fontWeight: 'normal'
      }
    },
    //colors: ['silver'],
    title: {
      style: {
        color: 'black',
        fontSize: '18px',
        fontWeight: 'bold'
      },
      align: 'left',
      x: 0,
      y: 25
    },
    subtitle: {
      style: {
        color: 'black',
        fontSize: '12px',
        fontWeight: 'normal'
      },
      align: 'left',
      x: 2,
      y: 40
    },
    yAxis: {
      title: {
        margin: 10,
        style: {
          fontSize: '16px',
          fontWeight: 'bold'
        }
      }
    },
    legend: {
      title: {
        style: {
          fontSize: '12px',
          fontWeight: 'normal',
          color: 'black'
        }
      },
      itemStyle: {
        fontSize: '12px',
        fontWeight: 'normal',
        color: 'black'
      },
      itemHoverStyle: {
        fontWeight: 'bold',
        color: 'gray'
      },
      itemBorderColor: '#6e6e6e', // Custom props for VZinfo maps
      itemBorderWidth: .5,        // Custom props for VZinfo maps
      maplineMarginTop: 10,       // Custom props for VZinfo maps

      align: 'left',
      verticalAlign: 'top',
      floating: true,
      layout: 'vertical',
      backgroundColor: 'rgba(255,255,255,0.9)',
      borderWidth: 0,
      // De legendablokken zijn nu: 25,683px x 12,48px (bxh)
      // De afstand tussen de blokjes is ongeveer 3 px
      itemMarginTop: 0,
      itemMarginBottom: 3,
      // itemBorderColor: '#6e6e6e',
      // itemBorderWidth: 1,
      symbolRadius: 0,
      symbolHeight: 12.5,
      symbolWidth: 25.7,
      squareSymbol: false,
      y: 50,
    },
    credits: {
      style: {
        cursor: 'pointer',
        color: '#3E3E3E',
        fontSize: '12px'
      },
      position: {
        align: 'left',
        x: -10,
        verticalAlign: 'bottom',
        y: 0
      }
    },
    exporting: {
      scale: 1,   // Set to 1 to preserve good resolution for background image 
      enabled: false, // we handle exporting ourselves through custom menu for accessibility
      // options when export is triggerd from our custom buttons
      chartOptions: {
        title: {
          style: {
            fontSize: '14px'
          },
          x: 2,
          y: 15
        },
        subtitle: {
          style: {
            fontSize: '10px'
          },
          x: 2,
          y: 30
        },
        legend: {
          enabled: false
        },
        credits: {
          //text: view.settings.credits,
          style: {
            color: '#4E4E4E',
            fontSize: '10px'
          },
          position: {
            align: 'left',
            x: 2,
            verticalAlign: 'bottom',
            y: -5
          }
        }
      },
      url: 'https://export.highcharts.com'
    },
    mapNavigation: {
      buttonOptions: {
        align: 'right',
        verticalAlign: 'bottom',
        width: 10
      },
      enableDoubleClickZoomTo: false
    },
    plotOptions: {
      map: {
        borderWidth: '1px',
        states: {
          hover: {
            color: null, // Setting to null will keep original fill color
            brightness: 0,
            borderColor: 'rgba(255,153,0, 0.5)', //'orange'
            borderWidth: '3px'
          },
          select: {
            color: null, // Setting to null will keep original fill color
            brightness: 0,
            borderColor: 'rgba(255,153,0, 0.5)', //'orange'
            borderWidth: '3px'
          }
        },
        point: {                    
            events: {
                mouseOver: function () { // To show all borders on hover
                    this.graphic.toFront();
                }
            }                  
        },
        tooltip: {
          headerFormat: ''
        },
      },
      mapline: {
        zIndex: 100,
        legendIndex: 100,
        allowPointSelect: false,
        enableMouseTracking: false,
        showInLegend: false,
        color: '#686868',
        lineWidth: 1.5,
        colorAxis: false, // Set to false to create own legend item in stead of connecting up to colorAxis
        events: {
            legendItemClick: function () {
                return false;
            }
        }
      }
    }
};
