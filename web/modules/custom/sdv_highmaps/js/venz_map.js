var maps = [];
var mqMapMobile = matchMedia('all and (min-width: 0px) and (max-width: 767px)');

(function ($, Drupal) {
  "use strict";
  Drupal.behaviors._mapModule = function (options) {
    var constants = {
      transparent: 'rgba(0,0,0,0)',
      nullColor: 'white',
      highlight: {
        border: 'orange',
        fill: 'orange'
      },
      hover: {
        border: 'rgba(255,153,0, 0.5)', //'orange',
        fill: 'orange'
      }
    };
    var mapTypes = {
      choropleth: 'map',
      locationmap: 'mappoint'
    };

    var view = {
      db: Drupal.behaviors,
      defaults: {
        container: $("<div class='venz-hc-container'></div>"),
        // legendContainer: $("<div class='venz-hc-legend'></div>"),
        // legendItem: {
        //   checkbox: $("<input type='checkbox'>"),
        //   label: $("<label class='option'><span></span></label>")
        // },
        themes: {
          def: ['#8FCAE7', '#F092CD', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#CB2326', '#6AF9C4'],
          provincies: ['#8FCAE7', '#F092CD', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#CB2326', '#6AF9C4'],
          gemeenten_2013: ['#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#CB2326', '#6AF9C4', '#8FCAE7', '#F092CD'],
          ggd: ['#ffCAE7', '#F092CD', '#DDDF00'],
          colorAxis: {
            blue: ['#f1eef6', '#d7b5d8', '#df65b0', '#ce1256']
          },
          locaties: ['darkblue'],
          borderColor: ['silver', '#8FCAE7'],
          shapeBorderColors: {
            backgroundLayer: 'black',
            shapesLayer: '#6e6e6e'
          }
        },
        mapConfig: {
          chart: {
            plotBorderWidth: 0,
            // Explicitly tell the width and height of a chart
            // Add margin to left and top: 79 - 48
//            width: 495,
//            height: 526,
            // Edit chart spacing
            marginLeft: 79,
            marginTop: 49,
            marginRight: 5,
            marginBottom: 5,
            spacingBottom: 20,
            spacingTop: 0,
            spacingLeft: 0,
            spacingRight: 0
          }
        }
      },
      // Public functions
      init: function (options) {
        // assigning all variables
        view.el = options.container.find("article.map");
        if(view.el.length == 0 && options.container.hasClass('map')) {
          view.el = options.container
        }
        view.content = view.el.find("div.content");
        view.table = view.el.find("table.tablefield");
        view.mapActions = view.el.find("div.chart-actions");
        view.id = view.el.attr("id");
        view.exportList = view.mapActions.find("ul.exports");
        view.data = [];
        view.exporting = false; // Only setup Menu if not in 'exporting mode'
        view.popupInformation = view.el.find('div.field-name-field-map-popup-information').clone();

        // Get map settings set in Drupal
        view.settings = Drupal.settings.hc_map_settings[view.id];
        //Set useBackGroundImage boolean
        /*
          if useBackrgoundImage:
            - transparant regions
            - no legend
            - no zoom buttons
            - no dataClasses
            - on mobile only image

        */
        view.settings.map.useBackgroundImage = !(view.settings.map.backgroundImage == undefined);

        //Call private functions to render highmaps depending on option interactive
        if (view.settings.map.interactive && ( !mqMapMobile.matches || !view.settings.map.useBackgroundImage )) {
          view._setup();
          view._renderChart();

          if (view.settings.map.no_popup == undefined || view.settings.map.no_popup != 1) {
            view._bindPopupEvent();
          }
        } else {

          view.el.find('a.htmltable').remove();
          view.el.find('div.field-name-field-map-popup-information').remove();

          //add container class
          view.content.find('.field-name-field-map-image').addClass('venz-hc-container');
          //buttons before image
          view.content.prepend(view.content.find('.chart-actions'));
          // Hide the table
          view.table.parent().hide();

          //bind hyperlink to related maps {a.related-map}
          view.el.closest('.venz_paragraph').find(".related-map").bind("click", view._showRelatedMap);

          view._setupMenu();
        }

      },
      getHeight: function () {
        return view.defaults.container.height();
      },
      getExportList: function () {
        return view.exportList;
      },
      getNodeId: function () {
        return view.el.attr("id");
      },
      // Private functions
      _setup: function () {
        //Hide map titles and image
        view.el.find('h3:first-child', 'h3.field-name-field-map-subtitle').remove();
        view.el.find('.field-name-field-map-subtitle').remove();
        view.el.find('.field-name-field-map-image').remove();
        view.el.find('a.htmltable').remove();

        //bind hyperlink to related maps {a.related-map}
        view.el.closest('.venz_paragraph').find(".related-map").bind("click", view._showRelatedMap);

        // Possibly set specific map theme
        Highcharts.setOptions(Highcharts.mapTheme);

        // Hide the table
        view.table.parent().hide();
        view.el.find('div.field-name-field-map-popup-information').hide();
        // There may be more types in the future that need different renderings of the data, then we should use a switch statement.

        //var geoProvincie = Highcharts.geojson(maps['provincies'], 'map');

        //map key and data key
        view.defaults.dataKey = 'id';

        // renderings map data from table
        view.data = view._getMapData();
        view.mapSettings = view.defaults.mapConfig;
        var creditsContainer = view.el.find('.field-name-field-map-sources div p:first-child').clone();
        creditsContainer.find('span.tip').remove();
        view.settings.credits = creditsContainer.text();

        // Set map settings (only settings dependent on Drupal code)
        $.extend(true, view.mapSettings, {
          chart: {
            renderTo: view.defaults.container.get(0),
            events: {
              load: view._mapLoaded
            },
            plotBackgroundImage: view.settings.map.useBackgroundImage ? view.settings.map.backgroundImage : null,
            width: view.settings.map.useBackgroundImage ? 495 : null,
            height: view.settings.map.useBackgroundImage ? 526 : 526,
          },
          title: {
            text: 'ladida'
            // text: view.settings.map.title
          },
          subtitle: {
            text: view.settings.map.subtitle
          },
          credits: {
            text: view.settings.credits
          },
          legend: {
            enabled: !view.settings.map.useBackgroundImage && !view.settings.map.hide_legend,
            title: {
              text: $.trim(view.table.find('thead tr th:last-child').text())
            }
          },
          exporting: {
            // options when export is triggerd from our custom buttons
            chartOptions: {
              legend: {
                enabled: !view.settings.map.useBackgroundImage && !view.settings.map.hide_legend
              }
            }
          },
          mapNavigation: {
            enabled: !view.settings.map.useBackgroundImage,
          },
          series: view._getSeries()
        });

        // Set Color Axis if no background image is selected and choroplete
        if (view.settings.map.type == 'choropleth' && !view.settings.map.useBackgroundImage) {
          var dataClassColors = ['yellow'];

          if (view.settings.map.data_class_colors != undefined) {
            dataClassColors = view.settings.map.data_class_colors;
          }

          $.extend(true, view.mapSettings, {
            colorAxis: view._getColorAxis(),
            colors: dataClassColors
          });
        }
      },
      _showMessage: function (msg) {
        view.content.prepend('<p style="color: red; border: 1px solid red; padding: 2px; margin-top: 5px;"><em>' + msg + '</em></p>');

      },
      _getColorAxis: function () {
        //Get dataclasses from map settings if available
        // default values
        var colorAxis = {
          dataClassColor: 'category',
          dataClasses: [{name: 'default: >0', from: 0}],
          marker: {
            color: 'black'
          }
          };

        if (view.settings.map.data_classes != undefined) {
          //Process classes to parse from string to float and create names if not provided
          var dataClasses = $.map(view.settings.map.data_classes, function (item, index) {
            var dataClass = {};
            dataClass.name =  ( item.name != undefined ) ? item.name : item.from + ' - ' + item.to ;
            if( item.from != undefined ) {
                dataClass.from = parseFloat(item.from);
            }
            if( item.to != undefined ) {
                dataClass.to = parseFloat(item.to);
            }
            return dataClass;
          });

          colorAxis.dataClasses = dataClasses;
        }

        return colorAxis

      },
      _getMapData: function () {
        var data = [], item = {};

        // loop through each row
        // tr.data-regio -> region id
        // tr.data-value -> data value
        // td[2] -> data value of data label
        view.table.find("tbody tr ").each(function (x, row) {
          var $dataValue = $(row).attr('data-value');
          var $dataLabelValue = null;
          var $dataLabel = null;

          if (view.settings.tooltip.value_label) {
            //Use label from table column
            $dataLabel = $.trim($(row).find('td:nth-of-type(2)').text());
            $dataLabelValue = parseFloat($dataLabel.replace(',', '#').replace('.', ',').replace('#', '.'));

            $dataLabel = ($dataLabelValue.toString() == 'NaN') ? $dataLabel :
              (!(view.settings.tooltip.decimals == undefined)
                ? Highcharts.numberFormat($dataLabelValue, view.settings.tooltip.decimals)
                : $dataLabelValue.toString().replace('.', '#').replace(',', '.').replace('#', ','))
              + (!(view.settings.tooltip.value_unit == undefined) ? view.settings.tooltip.value_unit : '');
          } else {
            $dataLabel = ($dataValue === 'null') ? '-' :
              ( view.settings.tooltip.decimals != undefined
                ? Highcharts.numberFormat($dataValue, view.settings.tooltip.decimals)
                : Highcharts.numberFormat($dataValue, 1) )
              + ( view.settings.tooltip.value_unit != undefined ? view.settings.tooltip.value_unit : '' );
          }

          item = {'id': parseFloat($(row).attr('data-regio')),
            name: $.trim($(row).find('td:nth-of-type(1)').text()),
            value: $dataValue,
            label: $dataLabel
          };
          data.push(item);
        });
        return data;
      },
      _setupMenu: function () {
        var viewDetaildata = Drupal.t("View detail data");
        //var downloadDetaildata = Drupal.t("Download detail data as CSV");
        var print = Drupal.t("Print map");
        var pdf = Drupal.t("Download PDF document");
        var png = Drupal.t("Download PNG image");
        //var jpg = Drupal.t("Download JPG image");
        var svg = Drupal.t("Download SVG vector image");

        view.downloadMenu = view.getExportList();

        view.tableLink = view.mapActions.find('a.show-table');
        var href = view.tableLink.attr("href");
        var mapNum = href.substr(href.lastIndexOf("-") + 1, href.length);

        view.mapActions.children('ul').prepend("<li><a class='map active' href='#show-map-" + mapNum + "'>" + Drupal.t('Show map') + "</a></li>");

        view.mapLink = view.mapActions.find('a.map');

/*
 * Disabled downloads.
        //Only show download link for detail csv data if no_popup != true
        if ((view.settings.map.no_popup == undefined || view.settings.map.no_popup != 1) && (view.settings.map.hide_csv_download == undefined || view.settings.map.hide_csv_download != 1)) {
          view.downloadMenu.append("<li><a class='viewDetaildata' href='/node/" + view.id.replace('node-', '') + "/tabel' title='" + viewDetaildata + "'>" + viewDetaildata + "</a></li>");
        } else {
          //remove download link from module
          view.downloadMenu.find('a.csv-export.detail-data').remove();
        }
        if (view.settings.map.interactive && !mqMapMobile.matches) {
          view.downloadMenu.append("<li><a class='print' href='#print' title='" + print + "'>" + print + "</a></li>");

          //PDF en PNG download only if no backgroundImage is used OR dataclasses are defined for choropleth OR regions layer
          view.downloadMenu.append("<li><a class='pdf-export' href='#pdf-export' title='" + pdf + "'>" + pdf + "</a></li>");
          view.downloadMenu.append("<li><a class='png-export' href='#png-export' title='" + png + "'>" + png + "</a></li>");
          view.downloadMenu.append("<li><a class='svg-export' href='#svg-export' title='" + svg + "'>" + svg + "</a></li>");
        }
*/
        // Download button
        view.downloadLink = $("<a href='#download-map' class='download'>" + Drupal.t("Download options") + "</a>");
        view.downloadMenu.before(view.downloadLink);

        // bind events
        view.downloadLink.bind("click", function (event) {
          event.preventDefault();
        });
        view.tableLink.bind('click', view._toggleTable);
        view.mapLink.bind('click', view._toggleTable);

        if (view.db._keyboardUtil) {
          view.db._keyboardUtil.giveAccess({parent: view.downloadMenu.parent(),
            trigger: view.downloadLink,
            children: view.downloadMenu.find("a")
          });
        }

        view.downloadMenu.find("li a:not(.csv-export, .viewDetaildata)").bind("click", view._exportMenuItemClick);
      },
      _getSeries: function () {
        var geoMap = null;
        var geoLocations = null;
        var arrSeries = [];

        // Add background contours if available
        // ALSO if backgroundimage is used, since otherwise for locationmap the extent will be adjusted
        $.each(view.settings.map.contours, function (index) {
          if(maps[view.settings.map.contours[index]] != undefined) {
            arrSeries.push( view._getBackgroundLayers(view.settings.map.contours[index], index) );
          }
        });

        // Add user selected region if no bgImage is used
        if( !view.settings.map.useBackgroundImage ) {

          if (view.settings.map.type == 'locationmap' && view.settings.map.regions != undefined) {
            // Add regions as backgroundlayer (mapline)
            $.each(view.settings.map.regions, function (index) {

              geoMap = Highcharts.geojson(maps[this.name_uri], 'map');

              arrSeries.push(
                {
                  name: this.name,
                  type: 'mapline',
                  mapData: geoMap,
                  showInLegend: true,
                  color: view.defaults.themes.shapeBorderColors.shapesLayer,
                  lineWidth: 0.8
                }
              );
            });
          }
        }

        //Add all regions to arrSeries
        if (view.settings.map.type == 'choropleth' && view.settings.map.regions != undefined) {

          $.each(view.settings.map.regions, function (index) {

            geoMap = Highcharts.geojson(maps[this.name_uri], 'map');

            arrSeries.push(
              {
                name: this.name,
                type: 'map',
                data: view.data,
                mapData: geoMap,
                joinBy: [this.key, view.defaults.dataKey],
                legendIndex: 100,

                // color settings
                color: constants.transparent, // if bgImage no colorAxis is defined and this color is used
                nullColor: view.settings.map.useBackgroundImage ? constants.transparent : constants.nullColor,
                borderColor: view.settings.map.useBackgroundImage ? constants.transparent : view.defaults.themes.shapeBorderColors.shapesLayer,
                // mouse interaction settings
                enableMouseTracking: view.settings.map.type == 'choropleth' ? true : false,
                // tooltip settings
                tooltip: {//'{point.dataClass}' +
                  pointFormat: (view.settings.tooltip.show_values) ? '{point.name}: <strong>{point.label}</strong>' : '{point.name}',
                },
                // popup event settings
                cursor: (view.settings.map.no_popup == undefined || view.settings.map.no_popup != 1) ? 'pointer' : null,
                point: {
                  events: {
                    click: (view.settings.map.no_popup == undefined) || (view.settings.map.no_popup != 1) ? function () {
                      view._showPopup(this);
                    } : null

                  }
                }
              }
            );

            //Check for existing map key
            if (maps[this.name_uri].features[0].properties[this.key] == undefined) {
              view._showMessage('Error map key "' + this.key + '" not found in shapefile');
            }
          }); // each map.regions
        } // if map.regions

        // ****************** Add Mappoint series ******************************
        if (view.settings.map.locations != undefined) {
          $.each(view.settings.map.locations, function (index) {
            var mapKey = this.key;

            // Read data from table
            var tableData = view._getMapData();
            var dataItem = {};
            var geoFeatures = [];
            var noDataLabel = ( view.settings.tooltip.show_no_data_label ) ? ': geen gegevens' : '';

            // Mapping mappoints to datapoints
            geoFeatures = $.map(maps[this.name_uri].features, function (item, index) {
              var id = item.properties[mapKey];
              var props = {};

              // find corresponding dataItem
              dataItem = $.map(tableData, function (item, index) {
                if( item.id == id ) {
                  return item;
                } else {
                  return null;
                }
              });

              // Only return item if corresponding datarow is found
              if( dataItem.length ) {
                props = {
                    _name: dataItem[0].name,
                    _value: dataItem[0].value,
                    _tooltip: ( ( dataItem[0].value == null || dataItem[0].value == 'null' ) ? dataItem[0].name + noDataLabel : dataItem[0].name + ': ' + dataItem[0].value)
                  };
                props[mapKey]= id;

                return $.extend(false, item,
                  {
                    properties: props
                  });
              } else {
                return null;
              }
            });

            // set filtered features to geoLocations
            geoLocations = {
              title: maps[this.name_uri].title,
              type: maps[this.name_uri].type,
              features: geoFeatures
            };

            //load locations and convert to Highmaps mappoints
            geoLocations = Highcharts.geojson(geoLocations, 'mappoint');

            // set color
            var thisColor = view.settings.map.useBackgroundImage ? constants.transparent : this.color;

            // Add location layer serie
            arrSeries.push(
              {name: this.name,
                type: 'mappoint',
                mapData: null,
                data: geoLocations,
                legendIndex: index,
                // symbol settings
                marker: {
                  symbol: this.symbol,
                  radius: 2.5,
                  lineWidth: 1,
                  lineColor: view.settings.map.useBackgroundImage ? constants.transparent : '#2c3539',
                  fillColor: thisColor
                },
                // tooltip settings
                tooltip: {
                  headerFormat: '',
                  pointFormat: (view.settings.tooltip.show_values) ? '{point.properties._tooltip} ' : '{point.properties._name}'
                },
                // popup event settings
                cursor: (view.settings.map.no_popup == undefined || view.settings.map.no_popup != 1) ? 'pointer' : null,
                point: {
                  events: {
                    click: (view.settings.map.no_popup == undefined) || (view.settings.map.no_popup != 1) ? function () {
                      view._showPopup($.extend(true, this, {id: this.properties[mapKey]}));
                    } : null,
                  }
                }
              }
            );  //arrSeries.push

          }); // each map.locations
        } // if map.locations

        return arrSeries;
      },
      _getBackgroundLayers: function (layerID, index) {
        var objLayers = {};
        // Get geometryType from first feature and set mapType
        var mapType = ( maps[layerID].features[0].geometry.type == 'Polygon' ) ? 'map' : 'mapline';

        if(index == undefined) index = 0;
        // set color to transparent if background image is used
          var layerColor = view.settings.map.useBackgroundImage ? constants.transparent : null;

          // Nederland is used as background layer if not useBackgroundImage.
          objLayers = {
            name: layerID.substr(0,1).toUpperCase() + layerID.substr(1),
            mapData: Highcharts.geojson(maps[layerID], mapType),  // mapline for multiline features,
            type: 'mapline',
            showInLegend: layerID != 'nederland',
            lineWidth: (layerID != 'nederland') ? 0.8 : Highcharts.defaultOptions.plotOptions.mapline.lineWidth,
            color: layerColor
          }
          return objLayers;

      },
      _exportMenuItemClick: function (event) {
        event.preventDefault();
        var docType = $(event.currentTarget).attr("href").substring(1);
        // CSV button is a drupal link, rendered in template.php
        var mimetypes = {
          'pdf-export': 'application/pdf',
          'png-export': 'image/png',
          'jpg-export': 'image/jpg',
          'svg-export': 'image/svg+xml'
        };

        var filename = (view.el.attr("data-title") != undefined) ? (view.el.attr("data-title").replace(/["',]/g, '')).replace(/[^a-zA-Z0-9]/g, '-') : 'Kaart';

        if (docType == 'print') {
          view.chart.print();
        } else {
          view.exporting = true;
          view.chart.exportChart({
            type: mimetypes[docType],
            filename: filename,
            scale: 2
          });
        }
      },
      _renderChart: function () {
        view.content.prepend(view.defaults.container);
        view.chart = new Highcharts.Map(view.mapSettings);
        view.content.find('.venz-hc-container').before(view.content.find('.chart-actions'));
      },
      _mapLoaded: function () {
        // Setup Menu when not in 'exporting mode'
        if (!view.exporting) {
          view._setupMenu();
        } else {
          view.exporting = false;
        }
      },
      _toggleTable: function (event) {
        event.preventDefault();
        var item = $(event.currentTarget);
        if (item.hasClass('map')) {
          view.table.parent().hide();
          view.content.find('.venz-hc-container').show();
          view.mapLink.removeClass('active inactive').addClass('active');
          view.tableLink.removeClass('active inactive').addClass('inactive');
        } else {
          view.table.parent().show();
          view.content.find('.venz-hc-container').hide();
          if (view.popup != undefined) {
            view.popup.remove();
          }
          view.mapLink.removeClass('active inactive').addClass('inactive');
          view.tableLink.removeClass('active inactive').addClass('active');
        }
      },
      // This function shows a popup with details for the selected region/point
      _showPopup: function (point) {
        var closeButton = '<div style="float: right; margin-right: -20px; margin-top: -5px; font-size: 70%;" tabindex="1"><button style="margin-left: -40px; display: none" class="closePopup">X</button></div>';
        //view.el.closest('.venz_paragraph').css('position:relative;');
        if (view.el.closest('.venz_paragraph').length > 0) {
          view.popupContainer = view.el.closest('.venz_paragraph');
        } else {
          view.popupContainer = view.el.closest('article.map');
        }

        if (!view.popupContainer.find('.map_popup').length) {
          view.popupContainer.append('<div id="map_popup-' +
            view.id + '" class="map_popup" style="display:none; "></div>');
        }
        view.popup = view.popupContainer.find('.map_popup');

        //view.popupInformation = view.el.find('div.field-name-field-map-popup-information').clone();
        view.el.find('div.field-name-field-map-popup-information').hide();

        view.popup.hide();
        view.popup[0].innerHTML = closeButton;
        view.popup.find('.closePopup').bind("click", view._hidePopup);

        $.ajax({
          url: Drupal.settings.basePath + 'node/' + view.id.replace('node-', '') + '/ajax/' + point.id,
          success: function (result) {
            var table = $(result);

            table.find('td:contains("http")').each(function () {
              var strUrl = $(this).text() + '|';
              var arrUrl = strUrl.split('|');
              var link = '<a href="' + $.trim(arrUrl[0]) + '"">' + $.trim(arrUrl[arrUrl.length - 2]) + '</a>';
              $(this).html(link);

            });

            view.popup.append(table);

            view.popup.append(view.popupInformation.show());
            view.popup.show();
            view.popup.find('.closePopup').show();
            view.popup.find('.closePopup').focus();
          },
          error: function (result, message) {
            view.popup.append('Er is een fout opgetreden:<br/>' + message);
            view.popup.show();
          }
        });
        if (view.popup.draggable != undefined)
          view.popup.draggable({containment: 'body>div.wrapper', scroll: false, cursor: 'move'});

        // console.log(point.name + '[' + point.id + ']: ' + point.value);
      },
      _hidePopup: function () {
        view.popup = view.popupContainer.find('.map_popup');
        view.popup.hide();
      },
      _bindPopupEvent: function () {
        //bind popup event to region/location name in data table
        view.table.find("tbody tr").each(function (x, row) {
          var point = {id: $(this).attr('data-regio')};
          var cell = $(this).find('td:nth-of-type(1)');

          cell.wrapInner('<a href="#' + '"></a>');
          // bind events
          cell.bind("click", function (event) {
            event.preventDefault();

            view._showPopup(point);
            $('.map_popup').css({
              left: '50%',
              top: $(this).position().top
            });

          });
        });
      },
      _showRelatedMap: function (event) {
        //event handler
        event.preventDefault();

        //Create popup element Drupal.behaviors.fortytwoMain.constants.THEMEPATH="/sites/all/themes/rivm/static/"
        var closeButton = '<div style="float: right; margin-right: -20px; margin-top: -25px; font-size: 70%;"><button style="margin-left: -36px;" class="closeMapPopup">X</button></div>',
          loadingMessage = '<p id="msg-loading" class="loading"><img src="' + Drupal.settings.basePath + 'sites/all/themes/rivm/loading.gif" alt="Laden van de kaart"/></p>';

        // append related_map_popup to VenZ paragraph
        view.relatedMapContainer = view.el.closest('.venz_paragraph');

        if (!view.relatedMapContainer.find('.related_map_popup').length) {
          view.relatedMapContainer.append('<div id="related_map_popup-' +
            view.id + '" class="related_map_popup">' +
            closeButton + '<div class="related-map-container">' + loadingMessage + '</div></div>');
          view.relatedMap = view.relatedMapContainer.find('.related_map_popup');
        } else {
          view.relatedMap = view.relatedMapContainer.find('.related_map_popup');
          view.relatedMap.find('.related-map-container').html(loadingMessage);
          view.relatedMap.show();
        }

        //bind close event
        view.relatedMap.find('.closeMapPopup').bind("click", view._hideRelatedMap);
        if (view.relatedMap.draggable != undefined)
          view.relatedMap.draggable({containment: 'body>div.wrapper', scroll: false, cursor: 'move'});

        var relatedMapNode = $(this).attr('data-node'), relatedMapUri = '/node/' + relatedMapNode, relatedMapSettings = {};

        //Get relatedMapSettings if not jet loaded from ajax call or get them from hc_mapSettings
        if (Drupal.settings.hc_map_settings['node-' + relatedMapNode] == undefined) {
          $.ajax({
            url: Drupal.settings.basePath + 'node/' + relatedMapNode + '/settings',
            async: false,
            success: function (resultSettings) {
              Drupal.settings.hc_map_settings['node-' + relatedMapNode] = resultSettings;
              relatedMapSettings = Drupal.settings.hc_map_settings['node-' + relatedMapNode];
            },
            error: function (message) {
              view.relatedMap.append('Er is een fout opgetreden:<br/>' + message);
              view.relatedMap.show();
            }
          });
        } else {
          relatedMapSettings = Drupal.settings.hc_map_settings['node-' + relatedMapNode];
        }

        //Add title to popup
        view.relatedMap.find('#msg-loading').prepend('<h3 class="field-name-field-map-title">' + relatedMapSettings.map.title + '</h3>');

        // Add regions and/or locations shapes files for interactive maps only
        if (relatedMapSettings.map.interactive) {

          //Add regions and locations of RelatedMap if not yet loaded loaded when page is rendered
          if (relatedMapSettings.map.regions != undefined) {
            $.each(relatedMapSettings.map.regions, function () {
              if (maps[this.name_uri] == undefined) {
                $(window.document.body).append('<script src="' + this.uri + '"></script>');
              }
            });
          }
          if (relatedMapSettings.map.locations != undefined) {
            $.each(relatedMapSettings.map.locations, function () {
              if (maps[this.name_uri] == undefined) {
                $(window.document.body).append('<script src="' + this.uri + '"></script>');
              }
            });
          }
        }

        // Load related map article in the container by using embed=1
        view.relatedMap.find('.related-map-container').load(relatedMapUri + '?embed=1 article',
          function (response, status, xhr) {

            if (status == "error") {
              var msg = "Sorry but there was an error: ";
              $("#error").html(msg + xhr.status + " " + xhr.statusText);
            } else {
              //Verberg bron in popup
              view.relatedMap.find('a.biblio-ref').contents().unwrap().wrap('<span/>')
              view.relatedMap.find('article').prepend('<h3 class="field-name-field-map-title">' + relatedMapSettings.map.title + '</h3>');
              view.relatedMap.find('#msg-loading').remove();
              view.relatedMap.show();
              view.relatedMap.find('.closeMapPopup').focus();
              view.relatedMap.find('a.map_paragraph').bind("click", function () {
                var targetBlind = view.db._blindsCollection.getItemById(this.href.substring(this.href.indexOf('#') + 1));

                //Open blind if on this page
                if (targetBlind != undefined) {
                  view.relatedMap.hide();
                  targetBlind.open();
                }

              });
              //view.db.behaviors._blindsCollection.getItemById('node-bmr-para-hm').open()
              var mapObject = new Drupal.behaviors._mapModule({container: view.relatedMap});
            }
          }
        );
      },
      _hideRelatedMap: function () {
        view.relatedMap = view.relatedMapContainer.find('.related_map_popup');
        view.relatedMap.hide();
      }

    }; //End var view

    // Initialize
    view.init(options);
    return view;
  }; //Drupal.behaviors._mapModule
}(jQuery, Drupal));
