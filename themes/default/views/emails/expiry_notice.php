<?php 
echo __("Hi :recipient_name!

Your :river_name river has shutdown and is no longer receiving new drops 
from your channels.

Click on the link below to extend the expiration date of your 
river by another :duration days.\n :activation_url",
array(
	":recipient_name" => $recipient_name,
	":river_name" => $river_name,
	":duration" => $duration,
	":activation_url" => $activation_url
));

?>