<?php
class user extends baseArray
{
    protected $data = [];

    /**
     * Constructor for the user class
     * 
     * @param int $id 
     */
    public function __construct ( int $id )
    {
        parent::__construct ( 'users', 'id' );

        $row = cache (DB)->get ('users', $id );
        if ( $row == null )
        {
            $row = database(DB)->cache ('users', 'id', '
                SELECT
                    *
                FROM
                    `users`
                WHERE
                    `id` = ?
                LIMIT 1
            ', [ $id ] )->fetchOne ();
        }

        $this->data = $row;
    }
}