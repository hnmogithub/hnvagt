<?php
/**
 * Class used to initlize various things we need
 */
class initialize
{
    static public function prepareSchedule ()
    {
        $files = glob ('modules/*', GLOB_ONLYDIR );

        var_dump ( $files );
    }
}