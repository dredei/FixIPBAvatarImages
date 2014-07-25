<?php

/*
 * by dredei
 * http://www.softez.pp.ua/
 */

require_once '../conf_global.php';
require_once 'db.class.php';
global $INFO;
$dbPrefix = $INFO[ 'sql_tbl_prefix' ];
$path = "../uploads/";

/* start connect */
$db_conn = mysql_connect( $INFO[ 'sql_host' ], $INFO[ 'sql_user' ], $INFO[ 'sql_pass' ] );
if ( !$db_conn )
{
    die( 'DB connection error!' );
}
mysql_select_db( $INFO[ 'sql_database' ], $db_conn );
/* end connect */

$db = new db_e();

$query = "SELECT pp_member_id, pp_main_photo, pp_thumb_photo FROM " . $dbPrefix . "profile_portal WHERE pp_main_photo <> ''";
$res = $db->ExecQuery( $query );
$rows = $res[ 'rows' ];
$wr = array();

for ( $i = 0; $i < $res[ 'count' ]; $i++ )
{
    $row = $rows[ $i ];

    $fileName = $path . $row[ 'pp_main_photo' ];
    $fileNameThumb = $path . $row[ 'pp_thumb_photo' ];
    if ( !file_exists( $fileName ) || !file_exists( $fileNameThumb ) )
    {
        $wr[ 'pp_member_id' ][] = $row[ 'pp_member_id' ];
        print 'User with id = ' . $row[ 'pp_member_id' ] . ' has problem with avatar! Fixed!<br />';
    }
}
if ( count( $wr[ 'pp_member_id' ] ) > 0 )
{
    $upd[ 'pp_main_photo' ] = "";
    $upd[ 'pp_thumb_photo' ] = "";
    $query = "UPDATE " . $dbPrefix . "profile_portal SET " . $db->GenUpdate( $upd ) . " " . $db->GenWhere( $wr,
              "OR" );
    $db->ExecQuery( $query );
}
print 'Done!';
