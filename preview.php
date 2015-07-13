<style type="text/css">
.awr_graphic { font-family: Arial,Helvetica,sans-serif }
#d_paper { width: 974px; height:550px; }
.casualties { }
.year { width: 179px; float: left; }
.label { font-size: 24px; }
.count { font-size: 36px; color: gray; }
</style>

<script type="text/javascript" src="http://www.voanews.com/MediaAssets2/projects/voadigital/shared/js/raphael-2.1.2.min.js"></script>
<script type="text/javascript" src="http://www.voanews.com/MediaAssets2/projects/voadigital/shared/js/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="http://www.voanews.com/MediaAssets2/projects/voadigital/shared/js/rainbowvis.min.js"></script>

<div class="awr_graphic">

<h1>Aircraft Weapons Released in Afghanistan 2010-2015</h1>

<p>Top chart shows number of munitions expended over the course of five years in Afghanistan, by both piloted and remotely piloted aircraft.  Bottom chart shows number of casualties (killed and injured) resulting from aerial operations, as cataloged by UNAMA.</p>

<div id="d_paper"></div>

<script type="text/javascript">
var language = "en";    // en, da, pa
var by_month = [156,154,175,197,300,457,325,416,739,1043,866,272,405,341,337,339,426,610,695,516,597,663,308,174,170,116,229,252,406,521,504,588,385,414,297,202,193,297,248,284,368,337,256,158,232,189,118,76,92,114,93,115,164,272,205,437,441,217,87,126,40,36,52,30,41];
var by_year = [[156,154,175,197,300,457,325,416,739,1043,866,272],[405,341,337,339,426,610,695,516,597,663,308,174],[170,116,229,252,406,521,504,588,385,414,297,202],[193,297,248,284,368,337,256,158,232,189,118,76],[92,114,93,115,164,272,205,437,441,217,87,126],[40,36,52,30,41]];
var max = 1043;

// sadly designed globals
var main_tooltip = { }

function tooltip_fit(node_tooltip, node_text, padding, text, animate, extra) {
    // update label
    node_text.attr({ text: text });
    
    // recompute label size
    var geo = node_text.getBBox();
    
    // update background layer position
    if( animate === false ) {
        node_tooltip.attr({
            x: geo.x - padding,
            y: geo.y - padding,
            width: geo.width + (2 * padding),
            height: geo.height + (2 * padding)
        });
    } else {
        
        // move the background node and text node
        node_tooltip.attr({
            x: extra.x - (geo.width / 2) - padding,
            y: extra.y - padding,
            width: geo.width + (2 * padding),
            height: geo.height + (2 * padding)
        });
        
        var center_y = (geo.height / 2);
        
        node_text.attr({
            x: extra.x,
            y: extra.y + center_y
        });
    }
}


// data source, UNAMA report
var casualties = {
    e: [ ],  // stores raphaeljs objects
    first_hover: true,  // used to reset labels during the first mouseover action
    max: 415,
    data: [
        306, // 2010
        415, // 2011 * note: This number has also been stated as 305 and 353 by
             // UNAMA in 2011 and 2012. By 2014, the number appears to have been
             // revised to 415. UNAMA has not returned comments on the discrepancy
             // and their 2015 report is due sometime this month.
        202, // 2012
        186, // 2013
        162, // 2014
        15   // 2015 * note: This number covers first quarter, Jan - Mar
    ]
};

function plot_aerial_casualties(x, y, w, h, value, max_value, i, block_width, parent) {
    var height = (h * value) / max_value;

    var column = paper.rect(
        x,// - (w / 2),
        y + (h - height),   // y
        w,
        height
    ).attr(styles.c);

    //paper.text( x, y + height + 20, value); 
    // var number_label = paper.text( x + 5 , y + 15, value);
    var number_label = paper.text( x + 5 , y + (h - height) + 15, value);
    
    number_label.attr(styles.ct); 
    
    var text_label = paper.text(
        x + w + 5, y + (h - height) + 15, localizations[language].casualties
    ).attr( styles.ct).attr(styles.ctl);

    // last column adjustments
    if( i == 5 ) {
        number_label.attr(styles.ct_last);
        number_label.translate(0,-30);
        var geo = text_label.getBBox();
        text_label.translate(-geo.width+10,-30);
        text_label.attr(styles.ct_tl_last);
    }
    
    // hide everything but the first one to start
    if( i != 0 ) {
        text_label.attr({opacity:0});
    }
    
    var event_trigger = paper.rect(
       x, y, block_width, h
    ).attr({fill: "red", opacity: 0});
    
    event_trigger.mouseover(function() {
    
        if( casualties.first_hover == true ) {
            casualties.first_hover == false;
            parent.justHide(casualties.e[0].text_label);
        }
    
        parent.justShow(text_label, 1);
    }).mouseout(function() {
        parent.justHide(text_label);
    });
    
    casualties.e.push({
        column: column,
        number_label: number_label,
        text_label: text_label,
        event_trigger: event_trigger
    });
}



// for easier translations to dari and pashto
var localizations = {
    "en": {
        "title": {
            "top": "Weapons Released",
            "bottom": "Aerial Operations Casualties"
        },
        "months": [
            "January", "February", "March",
            "April", "May", "June",
            "July", "August", "September",
            "October", "November", "December"
        ],
        "casualties": "Casualties"
    }
}

var paper;
var W = 974;
var H = 550;


var styles = {
    up: {
        "stroke-width": 0.5,
        "stroke": "white"
    },
    up_active: {
        "fill": "green",
        "opacity": 0
    },
    year: {
        "font-size": 14,
        "font-family": "Arial,Helvetica,sans-serif",
        "text-anchor": "start",
        "fill": "gray",
        "font-weight": "bold"
    },
    yearline: {
        "stroke-width": 1,
        "stroke": "black",
        "stroke-dasharray": ". "
    },
    tooltip: {
        "font-size": 14,
        "font-family": "Arial,Helvetica,sans-serif",
        "text-anchor": "middle",
        "fill": "white"
    },
    tooltip_bgr: {
        "opacity": 0.5,
        "fill": "black",
        "stroke-width": 0
    },
    mouseover: {
        "fill": "#374ec5",
        "stroke": "white"
    },
    c: {
        "fill": "black"
    },
    ct: {
        "fill": "white",
        "font-weight": "bold",
        "font-size": 14,
        "font-family": "Arial,Helvetica,sans-serif",
        "text-anchor": "start"
    },
    ct_last: {
        "fill": "black"
    },
    ct_tl_last: {
        "text-anchor": "end"
    },
    ctl: {
        "fill": "black",
        "font-size": 14
    },
    graph_label: {
        "font-size": 28,
        "font-family": "Arial,Helvetica,sans-serif",
        "text-anchor": "end",
        "fill": "black",
        "font-weight": "bold",
        "opacity": 0.1,
        "glow": "white"
    }
}


function plotYear() {
    this.e = {
        bars: []
    }
    this.year = 0;
    this.max = 0;
    this.paper = "";
    this.x = 0;
    this.y = 0;
    this.w = 0;
    this.h = 0;
    this.data = [];
}

plotYear.prototype.setYear = function(year) {
    this.year = year;
    return( this );
}

plotYear.prototype.setData = function(data) {
    this.data = data;
    return( this );
}

plotYear.prototype.setMax = function(max) {
    this.max = max;
    return( this );
}

plotYear.prototype.setPaper = function(paper) {
    this.paper = paper;
    return( this );
}

plotYear.prototype.setCoordinates = function(o) {
    this.x = o.x;
    this.y = o.y;
    this.w = o.w;
    this.h = o.h;
    return( this );
}

plotYear.prototype.getGeometry = function(i) {
    var w = this.w / this.data.length;
    var x = this.x + (i * w);
    var h = (this.h * this.data[i]) / this.max;
    var y = this.y + (this.h - h);
    
    return({ x: x, y: y, w: w - 1, h: h });
}

plotYear.prototype.getColor = function(i) {
    var rainbow = new Rainbow();
    rainbow.setSpectrum( "black", "#990000", "#ffa500" );
    rainbow.setNumberRange(0, this.max);
    
    return( "#" + rainbow.colourAt(this.data[i]) );
}

plotYear.prototype.draw = function() {
    for( var i = 0; i < this.data.length; i++ )(function(that, i) {
        
        var geometry = that.getGeometry(i);
        var color = that.getColor(i);
        
        // actual visible item
        var x = paper.rect(
            geometry.x, geometry.y,
            geometry.w, geometry.h
        ).attr(styles.up).attr({
            fill: color
        });
        
        // create tooltip containers, generic function does the rest
        var tooltip = paper.text(
            geometry.x + (geometry.w/2), geometry.y - 20,
            ""//that.data[i]
        ).attr( styles.tooltip );
        var tooltip_bgr = paper.rect(0,0,1,1).attr(styles.tooltip_bgr);
     
        // update tooltip initially
        tooltip_fit(tooltip_bgr, tooltip, 5, that.data[i], false);

        // hidden item used for interactions
        var y = paper.rect(
            geometry.x, that.y,
            geometry.w, that.h
        ).attr(styles.up_active);
        
        y.mouseover(function() {
            x.stop().animate( styles.mouseover, 100, "<>" );
            that.showTooltip(true, i);
        }).mouseout(function() {
            x.stop().animate({ fill: x.__data.original }, 100, "<>" );
            that.showTooltip(false, i);
        });

        // give raphael object some data to work with
        x.__data = {
            original: color,
            value: that.data[i],
            year: that.year,
            month: localizations[language].months[i],
            tooltip: tooltip,
            tooltip_bgr: tooltip_bgr,
            y: y
        }
        
        that.e.bars.push( x );
    })(this, i);
    
    // ensure that labels aren't covered up by bars
    this.initialReorder();
    return( this );
}

plotYear.prototype.drawYearMarker = function(i) {
    
    // don't draw the very first divider line
    if( i > 0 ) {
        this.e.line = paper.path(
            // "M" + (this.x - 5) + ",10 l0," + (this.h + 20)
            "M" + (this.x - 5) + ",10 l0," + (H - 5)
        ).attr(styles.yearline);
    }
    
    this.e.text = paper.text(
        this.x, this.h + 50, this.year
        // this.x, this.y, this.year
    ).attr(styles.year);
    
    return( this );
}

plotYear.prototype.justHide = function(node) {
    node.stop().animate({opacity:0}, 100, "<>");
}

plotYear.prototype.justShow = function(node, o) {
    node.stop().animate({opacity:o}, 100, "<>");
}

plotYear.prototype.showTooltip = function(show, i) {
    var bar = this.e.bars[i];

    if( show === false ) {
        this.justHide(bar.__data.tooltip);
        this.justHide(bar.__data.tooltip_bgr);
        this.justHide(main_tooltip.tooltip);
        this.justHide(main_tooltip.tooltip_bgr);
        
        return(false);
    }
    
    bar.__data.tooltip.attr({ text: bar.__data.value });

    // bar.__data.tooltip.stop().animate({opacity: 1}, 100, "<>");
    //bar.__data.tooltip_bgr.stop().animate({opacity: 0.5}, 100, "<>");
    this.justShow(bar.__data.tooltip, 1);
    this.justShow(bar.__data.tooltip_bgr, 0.5);
    
    //main_tooltip.tooltip.stop().animate({opacity: 1}, 100, "<>");
    //main_tooltip.tooltip_bgr.stop().animate({opacity: 0.5}, 100, "<>");
    this.justShow(main_tooltip.tooltip, 1);
    this.justShow(main_tooltip.tooltip_bgr, 0.5);
    
    // update month-year tooltip
    tooltip_fit(
        main_tooltip.tooltip_bgr,
        main_tooltip.tooltip,
        5, 
        bar.__data.month + " " + bar.__data.year,
        true, // animate
        // hints for x,y
        { x: bar.attr("x") + (bar.attr("width")/2), y: this.h + 80 }
        // { x: bar.attr("x"), y: bar.attr("y") }
    );
}

// ensure that labels aren't covered up by bars
// also hides initial tooltips
plotYear.prototype.initialReorder = function() {
    for( var i = 0; i < this.e.bars.length; i++ )(function(bar) {
        bar.toBack();
        
        bar.__data.tooltip.toFront();
        bar.__data.tooltip.attr({opacity: 0});
        bar.__data.tooltip_bgr.attr({opacity: 0});
        
        bar.__data.y.toFront();
    })(this.e.bars[i]);
}

$(document).ready(function() {
    paper = Raphael("d_paper", W, H);
    
    paper.text(W, 30, localizations[language].title.top).attr(styles.graph_label);
    paper.text(W, H- 15, localizations[language].title.bottom).attr(styles.graph_label);
    
    main_tooltip.tooltip_bgr = paper.rect(0,0,30,30).attr(styles.tooltip_bgr);
    main_tooltip.tooltip = paper.text(0,0,"tooltip").attr(styles.tooltip);
    
    main_tooltip.tooltip_bgr.attr({ opacity: 0 });
    main_tooltip.tooltip.attr({ opacity: 0 });
    
    var years = [];
    var relative_block_position = 0;
    
    for( var year in by_year )(function(year, data, i) {
        var temp = new plotYear();

        // layout geometry
        var columns = by_month.length;
        var year_divider = {
            count: 5,
            width: 10
        };
        year_divider.total = year_divider.count * year_divider.width;
        
        var cols = (i < 5) ? 12 : 5; // data stops in middle of a year
        var block_width = cols * ((W - year_divider.total) / columns);

        // ready to draw with this information
        temp
            .setYear( year )
            .setData( data )
            .setMax( max )
            .setPaper(paper)
            .setCoordinates({
                x: relative_block_position,
                y: 30, // allows for a high tooltip
                w: block_width,
                h: 200
            })
            .draw()
            .drawYearMarker(i)
        ;

        // plot casualty numbers
        // 40 px wide
        plot_aerial_casualties(
            //relative_block_position + (block_width/2),
            relative_block_position,
            320,
            40,
            200,
            casualties.data[i],
            casualties.max,
            i,
            block_width,
            temp
        );
        
        // this is a running position on a flat graph so need more information
        relative_block_position += block_width + year_divider.width;
        
        // store in case we conveniently need it later...
        years.push( temp );
    })(2010 + parseInt(year), by_year[year], year);

});

</script>

<p>Data Sources:</p>

<ul>
    <li><a href="http://www.unama.unmissions.org/Portals/UNAMA/human%20rights/2015/2014-Annual-Report-on-Protection-of-Civilians-Final.pdf">UNAMA 2014 Annual Report on Protection of Civilians</a></li>
    <li><a href="http://unama.unmissions.org/Default.aspx?ctl=Details&tabid=12254&mid=15756&ItemID=38675">Latest UNAMA figures, April 2015</a></li>
    <li><a href="http://www.afcent.af.mil/Portals/1/Documents/Airpower%20summary/31%20May%202015%20Airpower%20Summary.pdf">United States Air Forces Central Command</a></li>
</ul>

</div>

