<?php
$baseUrl = UNL_Peoplefinder::getURL();
$version = UNL_Peoplefinder::$staticFileVersion;
?>
<script>
require(['<?php echo $baseUrl ?>js/directory.min.js?v=<?php echo $version ?>'], function(directory) {
    directory.initialize('<?php echo $baseUrl ?>', '<?php echo UNL_Peoplefinder::$annotateUrl ?>');
});
</script>
