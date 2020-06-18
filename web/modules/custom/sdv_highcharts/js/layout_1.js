/**
 * Theme VTV Highcharts JS
 * @author Laurens Zwakhals
 * @version 1.1
 * @date 05-10-2016
 * @versionhistory: 
 * 0.1: 05-04-2016
 * 1.0: 12-09-2016
 ***/

Highcharts.setOptions({
	lang: {
		months: ['januari', 'februari', 'maart', 'april', 'mei', 'juni',  'juli', 'augustus', 'september', 'oktober', 'november', 'december'],
		weekdays: ['zondag', 'maandag', 'dinsdag', 'woensdag', 'donderdag', 'vrijdag', 'zaterdag'],
		numericSymbols: null,
		decimalPoint: ',',
		thousandsSep: '.',
		loading: 'laden...',
		noData: 'Geen gegevens geselecteerd',
		contextButtonTitle: 'Exporteer grafiek',
		printChart: 'Print grafiek',
		downloadPNG: 'Download als PNG',
		downloadPDF: 'Download als PDF',
		downloadSVG: 'Download als SVG',
		downloadCSV: 'Download data in CSV-formaat',
		downloadXLS: 'Download data in XLS-formaat',
		viewData: 'Toon de data in tabelvorm'
	},
	chart: {
        style: {
			fontFamily: 'RO Sans'
        },
		backgroundColor: 'rgba(255, 255, 255, 0.0)',
		plotBackgroundColor: '#ffffff'
    }
});
 
Highcharts.theme = {
	colors: ['#ca005d','#007bc7','#42145f','#e17000','#ffb612','#76d2b6','#a90061','#8fcae7','#d52b1e','#39870c','#f9e11e','#01689b'],
	// 1)robijnrood, 2)hemelblauw, 3)paars, 4)oranje, 5)donkergeel, 6)mintgroen, 7)violet, 8)lichtblauw, 9)rood, 10)groen, 11)geel, 12)donkerblauw
	title: {
		align: 'center',
		style: {
			fontSize: '26px',
			fontWeight: 'bold',
			color: '#01689B'
			},
		margin: 30, //Deze instelling heeft een bug
		x: -8
	},
	credits: {
		text: '',
		style: {
			fontSize: '14px',
			color: '#505050'
			},
		position: {
            verticalAlign: 'bottom',
			align: 'left',
			x: 0
        }
	},
	xAxis: {
		lineWidth: 2,
		lineColor: '#535353',
		tickLength: 4,
		tickWidth: 2,
		tickColor: '#535353',
		
		labels: {
			style: {
				fontSize: '10pt',
				color: '#000000'
			},
			staggerLines: 1
		},
		title: {
			align: 'high'
		},
		crosshair: {
            width: 3,
            color: '#535353',
			dashStyle: 'shortDash'
        }
	},
	yAxis: {
        title: {
			text: '',
			align: 'high',
			style: {
				fontSize: '11pt',
				color: '#01689b'
			},
			rotation: 0,
			y: -9
        },
		labels: {
			style: {
				fontSize: '10pt',
				color: '#000000'
			}
		},
		lineWidth: 0,
		gridLineDashStyle: 'solid',
		gridLineColor: '#535353',
		gridlineWidth: '0.75'
    },
	legend: {
		margin: 0,
		symbolWidth: 24,
		symbolHeight: 12,
		y: -10,
		itemStyle: {
			fontWeight: 'normal',
			fontSize: '11pt',
			color: '#000000'
		},
		itemHoverStyle: {
			color: '#007bc7'
		},
		itemHiddenStyle: {
			color: '#c8c8c8'
		}
	},
	tooltip: {
		headerFormat: '<strong><large>{point.key}</large></strong><br>',
		style: {
			fontSize: '11pt',
			color: '#000000'
		},
		//pointFormat: '<span style="color:{point.color}; font-size: 24px">\u25CF</span> {series.name}: <b>{point.y}</b><br/>',
		valueDecimals: 1,
		hideDelay: 0.2,
		backgroundColor: 'rgba(255,255,255,0.95)',
		borderWidth: 2,
		borderColor: '#000000',
		shadow: false
	},
	plotOptions: {
		line: {
			marker: {
				enabled: false
			}
		},
		area: {
			marker: {
				enabled: false
			},
			lineWidth: 0
		},
		column: {
			pointPadding: 0,
			groupPadding: 0.08,
			borderWidth: 0
		},
		bar: {
			pointPadding: 0,
			groupPadding: 0.08,
			borderWidth: 0
		},
		series: {
			stickyTracking: false,
			lineWidth: 4,
			states: {
				hover: {
					lineWidthPlus: 2
				}
			},
			marker: {
                symbol: 'circle',
				radius: 0,
                //lineWidth: 4,
                lineColor: null, // inherit from series
                states: {
                    hover: {
						fillColor: '#ffffff',
						lineWidth: 4,
						radius: 7
					}
				}
			},
			animation: {
                duration: 2000
			},
			dataLabels: {
				enabled: true,
				align: 'left',
				verticalAlign: 'middle',
				color: '#000000',
				style: {
					width: '150px',
					fontSize: '14px',
					fontWeight: 'normal'
				},
				y: -2,
				x: 6,
				allowOverlap: true,
				formatter: function() {
					if (this.point.x == this.series.data.length - 1) {	
						return this.series.name;
						//Andere voorbeelden:
						//return this.y + "% " + this.series.name;
						//return (Math.round( this.y * 10 ) / 10) + '%';
					} else {
						return null;
					}
				},
				crop: false,
				overflow: 'none'
			}
		}
	},
	labels: {
		style: {
			color: '#707070'
		}
	},
	navigation: {
		menuStyle: {
                border: '0px',
                background: '#01689B',
                //boxShadow: 'none'
				padding: '5px 0',
        },
		menuItemStyle: {
            padding: '0 10px',
            color: '#ffffff',
            fontSize: '13px',
			fontWeight: 'bold'
            },
        menuItemHoverStyle: {
            background: '#ecf5fb',
            color: '#000000'
            },
        buttonOptions: {
			//symbolStroke: "#c0c0c0" //Niet meer nodig want de hamburger is vervangen door iconen
			height: 38,
			width: 38
        }
    },
	exporting: {
        buttons: {
            contextButton: {
				//symbol: 'url(./js/themes/images/download.png)',
				symbol: 'url(/modules/custom/sdv_highcharts/images/download.png)',
				verticalAlign: 'bottom',
				y: 10,
				symbolX: 19,
				symbolY: 20,
				menuItems: [{
					textKey: 'downloadPNG',
					onclick: function () {this.exportChart({ type: 'image/png', filename: this.options.title.text.replace(/["',]/g,'').replace(/[^a-zA-Z0-9]/g,'-') });}
				},{
					textKey: 'downloadPDF',
					onclick: function () {this.exportChart({type: 'application/pdf', filename: this.options.title.text.replace(/["',]/g,'').replace(/[^a-zA-Z0-9]/g,'-') });}
				},{
					textKey: 'downloadSVG',
					onclick: function () {this.exportChart({type: 'image/svg+xml', filename: this.options.title.text.replace(/["',]/g,'').replace(/[^a-zA-Z0-9]/g,'-') });}
				},{
					separator: true
				},{
					textKey: 'downloadCSV',
					onclick: function () { this.downloadCSV(); }
				},{
					textKey: 'downloadXLS',
					onclick: function () { this.downloadXLS(); }
				},{
					textKey: 'viewData',
					onclick: function () { this.viewData(); }
				}]
            },
			printButton: {
                //symbol: 'url(./js/themes/images/printer.png)',
				symbol: 'url(/modules/custom/sdv_highcharts/images/printer.png)',
				verticalAlign: 'bottom',
				x: -40,
				y: 10,
                symbolX: 19,
                symbolY: 20,
                _titleKey: 'printChart',
                onclick: function () {
					this.print();
                }
            }
        }
    }
};

// Toepassen van het thema
var highchartsOptions = Highcharts.setOptions(Highcharts.theme);
