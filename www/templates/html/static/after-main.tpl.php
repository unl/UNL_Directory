<?php
$baseUrl = UNL_Peoplefinder::getURL();
$version = UNL_Peoplefinder::$staticFileVersion;
?>
<?php echo $savvy->render(null, 'Peoplefinder/PersonList/AnnotationTemplate.tpl.php'); ?>
<script id="main-entry">
require(['<?php echo $baseUrl ?>js/directory.min.js?v=<?php echo $version ?>'], function(directory) {
    directory.initialize('<?php echo $baseUrl ?>', '<?php echo UNL_Peoplefinder::$annotateUrl ?>');
});
</script>
