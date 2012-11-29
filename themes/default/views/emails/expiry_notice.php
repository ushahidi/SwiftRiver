<?php 
echo __('Hello,

Your ":river_name" river has expired and is no longer receiving new drops 
from your channels.

Click on the link below to reactivate your river for another :active_duration days.

:activation_url',
array(
	":recipient_name" => $recipient_name,
	":river_name" => $river_name,
	":active_duration" => $active_duration,
	":activation_url" => $activation_url
));

?>