<?php
echo __("Hi :recipient_name!

Your :river_name river will shutdown in :days_to_expiry days. This means that in :days_to_expiry days,
your river will stop being updated with new drops.

Click on the link below to view your river and extend its lifetime
by another :duration days.\n\n :river_url",
array(
	":recipient_name" => $recipient_name,
	":river_name" => $river_name,
	":days_to_expiry" => $days_to_expiry,
	":duration" => $duration,
	":river_url" => $river_url
));
?>
