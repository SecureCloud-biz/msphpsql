--TEST--
For output string parameter crash when output variable is set initially to null
--SKIPIF--
<?php require('skipif.inc'); ?>
--FILE--
<?php
    $sql = 'CREATE PROCEDURE #GetAGuid73
        (@NewValue varchar(50) OUTPUT)
        AS
        BEGIN
            set @NewValue = NEWID()
            select 1
            select 2
            select 3
        END';

    require( 'MsCommon.inc' );
    $conn = Connect();
    
    if( $conn === false ) {
        die( print_r( sqlsrv_errors(), true ));
    }

    $stmt = sqlsrv_query($conn, $sql);
    if( $stmt === false ) {
        die( print_r( sqlsrv_errors(), true ));
    }
     
    $sql = '{CALL #GetAGuid73 (?)}';
    $guid = null;
    $params = array(
                array( &$guid,
                       SQLSRV_PARAM_OUT,
                       SQLSRV_PHPTYPE_STRING(SQLSRV_ENC_CHAR),
                       SQLSRV_SQLTYPE_VARCHAR(50)
                )
              );

    $stmt = sqlsrv_query($conn, $sql, $params);
    if( $stmt === false ) {
        die( print_r( sqlsrv_errors(), true ));
    }

    echo 'New Guid: >'.$guid."<\n";

    while( sqlsrv_next_result( $stmt ) != NULL ) {
    }

    echo 'New Guid: >'.$guid."<\n";

?>
--EXPECTREGEX--
New Guid: \>.+\<
New Guid: \>[0-9A-F]{8}\-[0-9A-F]{4}\-[0-9A-F]{4}\-[0-9A-F]{4}\-[0-9A-F]{12}\<
