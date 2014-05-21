<?php

function checknull()
{
	$args = func_get_args();

	foreach( $args as $arg )
	{
		if( $arg == null )
		{
			return false;
		}
	}

	return true;
}

