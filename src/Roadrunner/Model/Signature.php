<?php

namespace Roadrunner\Model;

class Signature
{

	/**
	 * Returns the Signature Image URL for the specified $itemId 
	 * @param string $itemId
	 */
	static public function getImageUrl($itemId)
	{
		return 'http://' + 'roadrunner:roadrunner@' + $app['db.server'] + ':' + 
			$app['db.port'] + '/_utils/roadrunner/' + $itemId + '/signature.png';
	}
	
}