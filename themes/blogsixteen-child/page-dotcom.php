<?php

$P = print_r( $_POST, true);
$R = print_r( $_REQUEST, true);
file_put_contents( WP_CONTENT_DIR . '/log.txt', [$P,$R] );
