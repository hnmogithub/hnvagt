<?php
/**
 * Basic ArrayAccess class
 */
abstract class baseArray extends base implements ArrayAccess
{
	/* ArrayAccess */
	public function offsetExists ( $offset )
	{ return (bool) $this->get ( $offset ); }

	public function offsetGet ( $offset )
	{	return $this->get ( $offset ); }

	public function offsetSet ( $offset, $value )
	{	return $this->set ( $offset, $value ); }

	public function offsetUnset ( $offset )
	{	throw new Exception ( 'Unable to unset offset in object' ); }
}
