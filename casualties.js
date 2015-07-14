
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

function aerial_casualties_highlight_year(year_i, show) {
    var p = casualties.e[year_i];
    var column = p.column;

    column.stop().animate(
        show ? styles.mouseover : styles.c,
        animation_length, "<>"
    );

    // on load, first label is shown
    if( casualties.first_hover == true ) {
        casualties.first_hover == false;

        years[0].justHide(casualties.e[0].text_label);
    }

    if( show == true ) {
        p.text_label.attr({ fill: styles.mouseover.fill } );
        years[0].justShow(p.text_label, 1);
    } else {
        years[0].justHide(p.text_label);
    }
}


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
    var number_label = paper.text( x + 5 , y + (h - height) + 15, write_number(value) );

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
        aerial_casualties_highlight_year(i, true);
    }).mouseout(function() {
        aerial_casualties_highlight_year(i, false);
    });

    casualties.e.push({
        column: column,
        number_label: number_label,
        text_label: text_label,
        event_trigger: event_trigger
    });
}
