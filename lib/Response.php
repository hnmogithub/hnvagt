<?php
class Response extends Exception
{
	public function __construct ( string $message, int $code )
	{
		// TODO: Flesh this out
		http_response_code ( $code );

		die ( $message );
	}
}