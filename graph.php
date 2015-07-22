<?php
if( !isset($language) ) {
    $language = "en";
}
if( !isset($localizations) ) {
    $localizations = json_decode(file_get_contents("localization.js"));
}

if( isset( $_GET['language'] ) ) $language = $_GET['language'];

define( "W", 974 );
define( "H", 550 );

// this is a very fragile data representation
$data = <<< EOF
 Jan. Feb. Mar. Apr. May. Jun. Jul. Aug. Sep. Oct. Nov. Dec.
 156. 154. 175. 197. 300. 457. 325. 416. 739.1043. 866. 272.
 405. 341. 337. 339. 426. 610. 695. 516. 597. 663. 308. 174.
 170. 116. 229. 252. 406. 521. 504. 588. 385. 414. 297. 202.
 193. 297. 248. 284. 368. 337. 256. 158. 232. 189. 118. 076.
  92. 114.  93. 115. 164. 272. 205. 437. 441. 217. 087. 126.
  40.  36.  52.  30.  41
EOF;

// mind the spaces after the numbers, needed as delim
$data = explode(".", trim($data));
$data = array_map(function($e) {
   return( intval( trim($e) ) );
}, $data);
$by_month = array_slice($data, 12);
$by_year = array_chunk($data, 12 );
$key = array_shift($by_year);
?>
<style type="text/css">
.awr_graphic { font-family: Arial,Helvetica,sans-serif }
#d_paper { width: <?php echo W ?>px; height:<?php echo H ?>px; }
</style>

<script type="text/javascript" src="http://www.voanews.com/MediaAssets2/projects/voadigital/shared/js/raphael-2.1.2.min.js"></script>
<script type="text/javascript" src="http://www.voanews.com/MediaAssets2/projects/voadigital/shared/js/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="http://www.voanews.com/MediaAssets2/projects/voadigital/shared/js/rainbowvis.min.js"></script>

<div class="awr_graphic">

<div dir="<?php echo $localizations->$language->dir ?>">
<h1><?php echo $localizations->$language->title->article_title ?></h1>
<p><?php echo $localizations->$language->title->introduction ?></p>
</div>

<div id="d_paper"></div>

<script type="text/javascript">
var language = "<?php echo $language ?>";    // en, prs, pus
var by_month = <?php echo json_encode($by_month) ?>;
var by_year = <?php echo json_encode($by_year) ?>;
var max = <?php echo max($by_month) ?>;
var animation_length = 200;

// top container for elements
// another one is casualties
var years = [];

<?php readfile("tooltip.js"); ?>

<?php readfile("casualties.js"); ?>

var localizations = <?php readfile("localization.js"); ?>;

var paper;
var W = <?php echo W ?>;
var H = <?php echo H ?>;

function write_number(input) {
    var a = input.toString();
    var r = "";
    for( var i = 0; i < a.length; i++) {
        var c = parseInt(a.substr(i,1));
        r += localizations[language].numerals[c]
    }
    return(r);
}

<?php readfile("styles.js"); ?>

language_style_customizations(language);

<?php readfile("plotyear.js"); ?>

$(document).ready(function() {
    paper = Raphael("d_paper", W, H);

    function graph_title(x, y, text) {
        var t = paper.text(x, y, text).attr(styles.graph_label);
        var g = t.getBBox();
        paper.rect(g.x, g.y, g.width, g.height).attr(styles.graph_label_bgr);
    }

    graph_title(W, 30, localizations[language].title.top);
    graph_title(W, H - 15, localizations[language].title.bottom);

    main_tooltip.tooltip_bgr = paper.rect(0,0,30,30).attr(styles.tooltip_bgr);
    main_tooltip.tooltip = paper.text(0,0,"tooltip").attr(styles.tooltip);

    main_tooltip.tooltip_bgr.attr({ opacity: 0 });
    main_tooltip.tooltip.attr({ opacity: 0 });

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
