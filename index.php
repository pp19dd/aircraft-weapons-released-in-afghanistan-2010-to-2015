<?php
$language = "en"; // en prs pus
if( isset( $_GET['language'] ) ) $language = $_GET['language'];
$localizations = json_decode(file_get_contents("localization.js"));
?>
<!doctype html>
<html>
<head>
<title><?php echo $localizations->$language->title->article_title ?></title>
<meta charset="utf-8" />
<meta name="robots" content="noindex" />
<style type="text/css">
#d_paper { background-color: white }
</style>
</head>
<body>

<?php include( "graph.php" ); ?>

</body>
</html>
