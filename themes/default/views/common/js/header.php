// Loading image
window.loading_image_url = "<?php echo URL::site().'themes/default/media/img/loading.gif'; ?>";
window.loading_message = $('<div class="loading"><img src="' + window.loading_image_url + '" /></div>');
// Preload loading_image
(new Image()).src = window.loading_image_url;

//Preload default avatar
window.default_avatar_url = "<?php echo URL::site().'themes/default/media/img/avatar_default.gif'; ?>";
(new Image()).src = window.default_avatar_url;