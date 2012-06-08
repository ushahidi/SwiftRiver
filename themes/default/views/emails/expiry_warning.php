<?php
echo __("Hi :recipient_name!

Your :river_name river will shutdown in :days_to_expiry day(s). This means that in :days_to_expiry day(s),
your river will stop being updated with new drops.

Click on the link below to view your river and extend its lifetime
by another :active_duration days.

:river_url",
array(
	":recipient_name" => $recipient_name,
	":river_name" => $river_name,
	":days_to_expiry" => $days_to_expiry,
	":active_duration" => $active_duration,
	":river_url" => $river_url
));
?>
