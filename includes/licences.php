<?php
# -----------------------------------------------------------------
# Creative Commons Licensing
# -----------------------------------------------------------------

function truwriter_get_licences() {
	// return as an array the types of licenses available

	return ( array (
				'u' => 'Rights Status Unknown',
				'pd'	=> 'Public Domain',
				'cc0'	=> 'CC0 No Rights Reserved',
				'by' => 'CC-BY Attribution',
				'by-sa' => 'CC-BY-SA Attribution-ShareAlike',
				'by-nd' => 'CC-BY=ND Attribution-NoDerivs',
				'by-nc' => 'CC-BY-NC Attribution-NonCommercial',
				'by-nc-sa' => 'CC-BY-NC-SA Attribution-NonCommercial-ShareAlike',
				'by-nc-nd' => 'CC-BY-NC-ND Attribution-NonCommercial-NoDerivs',
				'copyright' => 'All Rights Reserved (copyrighted)',

			)
		);
}

function truwriter_the_license( $lcode ) {
	// output the ttitle of a license
	$all_licenses = truwriter_get_licences();

	echo $all_licenses[$lcode];
}

function truwriter_get_the_license( $lcode ) {
	// return the ttitle of a license
	$all_licenses = truwriter_get_licences();

	return ($all_licenses[$lcode]);
}

function truwriter_license_html( $license, $author='', $yr='') {

	if ( !isset( $license ) or $license == '' ) return '';

	$all_licenses = truwriter_get_licences();

	// do we have an author?
	$work_str_html = ($author == '') ? 'This work' : 'This work by ' . $author;

	switch ( $license ) {

		case 'copyright':
			return $work_str_html . ' is &copy;' . $yr . ' All Rights Reserved';
			break;


		case 'u':
			return 'The rights of ' . lcfirst($work_str_html) . ' is unknown or not specified.';
			break;

		case 'cc0':

		return '<a rel="license" href="http://creativecommons.org/publicdomain/zero/1.0/"><img src="https://i.creativecommons.org/p/zero/1.0/88x31.png" style="border-style: none;" alt="CC0" /></a><br />To the extent possible under law, all copyright and related or neighboring rights have been waived for ' . lcfirst($work_str_html) .  ' and is shared under a <a href="https://creativecommons.org/publicdomain/zero/1.0/">Creative Commons CC0 1.0 Universal Public Domain Dedication</a>.';

			break;

		case 'pd':
			return $work_str_html . ' has been explicitly released into the public domain.';
			break;

		default:
			return '<a rel="license" href="http://creativecommons.org/licenses/' . $license . '/4.0/"><img alt="Creative Commons ' . $all_licenses[$license] . ' License" style="border-width:0" src="https://i.creativecommons.org/l/' . $license . '/4.0/88x31.png" /></a><br />' . $work_str_html  . ' is licensed under a <a rel="license" href="http://creativecommons.org/licenses/' . $license . '/4.0/">Creative Commons ' . $all_licenses[$license] . ' 4.0 International License</a>.';
	}
}


function cc_license_select_options ($curr) {
	// output for select form options for use in forms

	$str = '';

	// to restrict the list of options, comment out lines you do not want
	// to make available (HACK HACK HACK)
	$licenses = truwriter_get_licences();

	foreach ($licenses as $key => $value) {
		// build the striing of select options
		$selected = ( $key == $curr ) ? ' selected' : '';
		$str .= '<option value="' . $key . '"' . $selected  . '>' . $value . '</option>';
	}

	return ($str);
}
?>
