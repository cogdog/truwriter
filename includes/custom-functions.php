<?php
/*  =------ SPLOTs are US!
add any custom functions for your site here. Keep a copy safely tucked away in case of
future theme updates
                                                                           -------= */


// ----- short code for number of assignments in the bank
add_shortcode('writingcount', 'getWritingCount');

function getWritingCount() {
	return wp_count_posts()->publish;
}


?>