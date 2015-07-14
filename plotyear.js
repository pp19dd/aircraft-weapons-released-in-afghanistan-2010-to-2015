
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
            x.stop().animate( styles.mouseover, animation_length, "<>" );
            that.showTooltip(true, i);
            aerial_casualties_highlight_year(x.__data.year - 2010, true);
        }).mouseout(function() {
            x.stop().animate({ fill: x.__data.original }, animation_length, "<>" );
            that.showTooltip(false, i);
            aerial_casualties_highlight_year(x.__data.year - 2010, false);
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
        this.e.line.toBack();
    }

    this.e.text = paper.text(
        this.x, this.h + 50, write_number(this.year)
        // this.x, this.y, this.year
    ).attr(styles.year);

    return( this );
}

plotYear.prototype.justHide = function(node) {
    node.stop().animate({opacity:0}, animation_length, "<>");
}

plotYear.prototype.justShow = function(node, o) {
    node.stop().animate({opacity:o}, animation_length, "<>");
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

    bar.__data.tooltip.attr({ text: write_number(bar.__data.value) });

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
        bar.__data.month + " " + write_number(bar.__data.year),
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
