<?php
$m  = new  Memcached ();
 $m -> addServer ( 'localhost' ,  11211 );

 $m -> set ( 'foo' ,  100 );
 var_dump ( $m -> get ( 'foo' ));
require_once dirname( __FILE__ ) . '/common.inc.php';
Master::get_lock(1,"user_1000001");