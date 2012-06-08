<?php 
echo __("Hi :recipient_name!

Your :river_name river has shutdown and is no longer receiving new drops 
from your channels.

Click on the link below to extend the expiration date of your 
river for another :active_duration days.

:activation_url",
array(
	":recipient_name" => $recipient_name,
	":river_name" => $river_name,
	":active_duration" => $active_duration,
	":activation_url" => $activation_url
));

?>