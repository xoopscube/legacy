<?php

foreach ( $this->v( 'checks' ) as $check ) {

    echo $check . '<br>';

}

if ( is_array( $this->v( 'msgs' ) ) ) {

    foreach ( $this->v( 'msgs' ) as $msg ) {

        echo $msg ;

    }
}
