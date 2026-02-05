<?php
add_filter('pre_option_bs_age_gate_err_msg', function () {
	return 'We can not allow someone to use this site if they are not 19+.';
});
add_filter('gettext', 'dk_change_delivery_heading', 10, 3);
function dk_change_delivery_heading($translated_text, $text, $domain)
{

	if ($translated_text === 'PICKUP YOUR LOCATION FOR DELIVERY' && $domain === 'deepknead') {
		return 'PICK YOUR LOCATION FOR DELIVERY';
	}

	return $translated_text;
}
