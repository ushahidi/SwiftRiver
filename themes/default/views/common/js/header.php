// Loading image
window.loading_image_url = "<?php echo URL::site().'themes/default/media/img/loading.gif'; ?>";
window.loading_message = $('<div><img src="' + window.loading_image_url + '" /></div>');
// Preload loading_image
(new Image()).src = window.loading_image_url;