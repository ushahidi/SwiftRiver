<?php
echo __("Hello,

Your :river_name river will stop receiving new drops in :days_to_expiry day(s).

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
