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
