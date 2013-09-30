<?php

interface xrowCDNInterface
{
	/**
     * Clears all files out of the namespace / bucket
     *
     * @param $namespace Defines the name of the namespace / bucket.
     * @throws Exception When an error occured
     */
	public function clean( $bucket );

	
    /**
     * Gets all files stored in the namespace / bucket in an array.
     *
     * @param $namespace Defines the name of the namespace / bucket.
     * @throws Exception When an error occured
     */
	public function getAllDistributedFiles( $bucket );
    

	/**
     * Uploads a file into the bucket
     *
     * @param $bucket Defines the name of the namespace / bucket.
     * @param $file Defines the file (full path) to put into the namespace / bucket.
     * @param $remotepath Defines the remote location in the bucket / namespace to put the file into (without leading bucket).
     * @throws Exception When an error occured
     */
	public function put( $localfile, $remotepath, $bucket );
}

?>