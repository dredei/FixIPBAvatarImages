<?php

class db_e
{

    function ExecQuery( $query )
    {
        $res = mysql_query( $query );
        $i = 0;
        if ( is_bool( $res ) )
        {
            $result[ 'count' ] = 0;
        }
        else
        {
            $result[ 'count' ] = mysql_num_rows( $res );
            while ( $row = mysql_fetch_array( $res ) )
            {
                $rowArr[ $i++ ] = $row;
            }
            $result[ 'rows' ] = $rowArr;
            mysql_free_result( $res );
        }
        return $result;
    }

    function GenWhere( $wr, $orAnd = 'AND' )
    {
        $where = '';
        foreach ( $wr as $field => $value )
        {
            if ( strlen( $where ) > 0 )
                $where .= ' ' . $orAnd . ' ';
            if ( (!is_array( $value )) and ( preg_match( '/^(NOT\s)?NULL$/', $value )) )
            {
                $where .= ' `' . $field . '` IS ' . mysql_real_escape_string( $value ) . '';
            }
            else
            {
                if ( is_array( $value ) )
                {
                    $where2 = '';
                    for ( $i = 0; $i < count( $value ); $i++ )
                    {
                        if ( strlen( $where2 ) > 0 )
                            $where2 .= ' OR ';
                        $where2 .= " `" . $field . "`='" . $value[ $i ] . "'";
                    }
                    $where .= $where2;
                } else
                {
                    if ( is_string( $value ) )
                    {
                        $where .= " `" . $field . "`='" . mysql_real_escape_string( $value ) . "'";
                    }
                    else
                    {
                        $where .= " `" . $field . "`=" . mysql_real_escape_string( $value );
                    }
                }
            }
        }
        $result = ' WHERE ' . $where;
        return $result;
    }

    function escapeDBFunction( $value )
    {
        switch ( $value )
        {
            case "NOW()":
                return $value;

            default:
                return "'" . mysql_real_escape_string( $value ) . "'";
        }
    }

    function GenInsert( $ins )
    {
        foreach ( $ins as $field => $value )
        {
            if ( strlen( $fields ) > 0 )
                $fields .= ', ';
            if ( strlen( $values ) > 0 )
                $values .= ', ';
            $fields .= '`' . $field . '`';
            if ( preg_match( '/^(NOT\s)?NULL$/', $value ) )
            {
                $values .= mysql_real_escape_string( $value );
            }
            else
            {
                if ( is_string( $value ) )
                {
                    $values .= $this->escapeDBFunction( $value );
                }
                else
                {
                    $values .= mysql_real_escape_string( $value );
                }
            }
        }
        $result = ' (' . $fields . ') VALUES (' . $values . ')';
        return $result;
    }

    function GenUpdate( $upd )
    {
        foreach ( $upd as $field => $value )
        {
            if ( strlen( $set ) > 0 )
                $set .= ', ';
            if ( preg_match( '/^(NOT\s)?NULL$/', $value ) )
            {
                $set .= ' `' . $field . '`=' . mysql_real_escape_string( $value );
            }
            else
            {
                if ( is_string( $value ) )
                {
                    $set .= ' `' . $field . '`=\'' . mysql_real_escape_string( $value ) . '\'';
                }
                else
                {
                    $set .= ' `' . $field . '`=' . mysql_real_escape_string( $value ) . '';
                }
            }
        }
        return $set;
    }

}

?>