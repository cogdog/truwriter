<?php
/*
Template Name: Get Edit Link

Calls function to generate the email that sends an owner the edit link              */

// get the id parameter from URL
$wid = get_query_var( 'wid' , 0 );   // id of post

truwriter_mail_edit_link ($wid);

?>