<?php
$language = "prs"; // en prs pus
$localizations = json_decode(file_get_contents("localization.js"));
?>
<!doctype html>
<html>
<head>
<title><?php echo $localizations->$language->title->article_title ?></title>
<meta charset="utf-8" />
<style type="text/css">
#d_paper { background-color: white }
.awr_graphic { padding:10px; background-color: ghostwhite }
</style>
</head>
<body>

<?php include( "graph.php" ); ?>

</body>
</html>
