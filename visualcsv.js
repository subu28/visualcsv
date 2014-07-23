
//this draws the table based on recieved data
function drawtable(){
    cols=axis.length;
    rows=values.length;
    var row = document.createElement("tr");
    for (var i=0;i<cols;i++) {
        row.innerHTML+="<th>"+axis[i]+"</th>"
    }
    var table=document.createElement("table");
    table.appendChild(row);
    for(var i=0;i<rows;i++){
        row = document.createElement("tr");
        for (var j=0;j<cols;j++) {
            row.innerHTML+="<td>"+diction[j][values[i][j]]+"</td>";
        }
        table.appendChild(row);
    }
    document.getElementById("results").appendChild(table);
}

//draws a new chart with x and y axis from arguments
function drawchart(x,y){
    d3.select("#results").append("svg").style("width","500px").style("height","500px");
    var svg = d3.select( d3.selectAll("svg")[0].pop() );
    var xscale = d3.scale.ordinal()
        .domain(diction[x])
        .rangeBands([50,450]);
    var yscale = d3.scale.ordinal()
        .domain(diction[y])
        .rangeBands([450,50]);
    var xaxis = d3.svg.axis().scale(xscale);
    var yaxis = d3.svg.axis().scale(yscale).orient("left");
    svg.append("svg:g").call(xaxis).attr("transform","translate(0,450)");
    svg.append("svg:g").call(yaxis).attr("transform", "translate(50,0)");
    var circle = svg.selectAll("circle").data(datagen(x,y));
    var circleEnter = circle.enter().append("circle");
    circleEnter.attr("cy",60).attr("cx",function(d){return 50+ ((d.x+0.5)*400/diction[x].length)}).attr("cy",function(d){return 450 - ((d.y+0.5)*400/diction[y].length)}).attr("r",function(d){return Math.sqrt(3000*d.ar/rows)});
}

//manages data required for each chart
function datagen(x,y) {
    var data = new Array;
    for (var i=0;i<diction[x].length;i++) {
        for (var j=0;j<diction[y].length;j++) {
            ar=0;
            for (var k=0;k<rows;k++){
                if (values[k][x]==i && values[k][y]==j) {
                    ar++
                }
            }
            data[data.length] = {"x":i,"y":j,"ar":ar}
        }
    }
    return data;
}

//calls drawchart for each chart required in final results page
function showcharts() {
    for (var i=0;i<charts.length;i++) {
        drawchart(charts[i][0],charts[i][1]);
    }
}

//shows the particular chart as preview and also removes older charts durinng preview
function preview(e) {
    var menu=document.getElementById("menu");
    d3.select("svg").remove();
    drawchart(menu.xAxis.value,menu.yAxis.value);
    return false;
}

//shows console to generate charts after uploading csv file
function showconsole() {
    urlstring="?source="+source;
    no_of_charts=0;
    var menu = document.createElement("form");
    document.getElementById("results").appendChild(menu);
    menu.id="menu";
    menu.innerHTML="<table><tr><td>X Axis</td><td><select name='xAxis'></select></td></tr><tr><td>Y Axis</td><td><select name='yAxis'></select></td></tr><tr><td></td><td><input type='button' onclick='preview()' value='compare'></td></tr><tr><td></td><td><input type='button' onclick='urlbuild()' value='save this chart'></td></tr><tr><td></td><td><input type='button' onclick='go()' value='view all charts'></td></tr></table>"
    for(var i=0;i<axis.length;i++){
        opt=document.createElement("option")
        opt.value=i+"";
        opt.innerHTML=axis[i];
        menu.xAxis.appendChild(opt);
    }
    for(var i=0;i<axis.length;i++){
        opt=document.createElement("option")
        opt.value=i+"";
        opt.innerHTML=axis[i];
        menu.yAxis.appendChild(opt);
    }
}

//builds url and manages it for final result page
function urlbuild() {
    var menu=document.getElementById("menu");
    d3.select("svg").remove();
    var x = menu.xAxis.value;
    var y = menu.yAxis.value;
    urlstring+="&c"+no_of_charts+"="+x+","+y;
    no_of_charts++;
}

//sends user to final result page
function go() {
    if (no_of_charts>0) {
        alert("You should note down the result page url to access these results later")
        window.location=urlstring;
    }
}
