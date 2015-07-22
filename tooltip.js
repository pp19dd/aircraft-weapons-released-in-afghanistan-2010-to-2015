// sadly designed globals
var main_tooltip = { }

// lower tooltip
function tooltip_fit(node_tooltip, node_text, padding, text, animate, extra) {
    // update label
    node_text.attr({ text: text });

    // manual correction, no time for computations :/
    var offset_x = 0;
    if( typeof extra != "undefined" ) {
        if( extra.x <= 45 ) offset_x = 50;
        if( extra.x >= 910 ) offset_x = -50
    }

    // recompute label size
    var geo = node_text.getBBox();

    // update background layer position
    if( animate === false ) {
        node_tooltip.attr({
            x: geo.x - padding + offset_x,
            y: geo.y - padding,
            width: geo.width + (2 * padding),
            height: geo.height + (2 * padding)
        });
    } else {

        // move the background node and text node
        node_tooltip.attr({
            x: extra.x - (geo.width / 2) - padding + offset_x,
            y: extra.y - padding,
            width: geo.width + (2 * padding),
            height: geo.height + (2 * padding)
        });

        var center_y = (geo.height / 2);

        node_text.attr({
            x: extra.x + offset_x,
            y: extra.y + center_y
        });
    }
}
