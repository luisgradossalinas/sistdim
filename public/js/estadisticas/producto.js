$(document).ready(function() {
    var chart = new Highcharts.Chart(
            {"credits": {"enabled": false}, 
        "chart": {"renderTo": "jqchart"}, 
        "series": [{"name": "Browser share", "data": [["Firefox", 45], ["IE", 26.8], {"name": "Chrome", "y": 12.8, "sliced": true, "selected": true}, ["Safari", 8.5], ["Opera", 6.2], ["Others", 0.7]], "type": "pie"}], 
        "title": {"text": "Ejemplo de estadísticas con Zend y jqChart"}, 
        "tooltip": {"formatter": function() {
                return '<b>' + this.point.name + '</b>: ' + this.y + ' %';
            }}, "plotOptions": {"pie": {"allowPointSelect": true, "cursor": "pointer", 
                        "dataLabels": {"enabled": true, "color": "#000000", "connectorColor": "#000000", "formatter": function() {
                        return '<b>' + this.point.name + '</b>: ' + this.y + ' %'
                    }}}}});
    

});