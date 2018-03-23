<?php
class users
{
    /**
     * Gets a user by name
     * 
     * @param string $name
     * 
     * @return user $user
     */
    static public function byName ( string $name )
    {
        $result = database (DB)->cache ('users', 'id', '
            SELECT
                *
            FROM
                `users`
            WHERE
                `name` = ?
        ', [ $name ] );

        if ( $result->length () > 0 )
        {

        }
        else
        {

        }
    }

    static public function byId ( int $id )
    {

    }

    /**
     * Attempts to login a user
     * 
     * @param string $username
     * @param string $password
     * 
     * @return user $user
     */
    static public function login ( string $username, string $password )
    {

    }
}