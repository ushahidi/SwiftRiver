<?php echo(Html::style('media/css/map.css')); ?>
<div id="map" class="map"></div>
<script>
    var geojson_url = "<?php echo $geojson_url ?>";
    var droplet_base_url = "<?php echo $droplet_base_url ?>";
</script>
<?php echo(Html::script('media/js/map.js')); ?>

