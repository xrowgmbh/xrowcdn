<?php

class xrowCloundFront implements xrowCDNInterface
{
    private $vars = array();
    private $ini;
    private $s3;

    /**
     * Constructor
     */
    function __construct( $ini )
    {
        $awskey = $ini->variable( 'Settings', 'AWSKey' );
        $secretkey = $ini->variable( 'Settings', 'SecretKey' );
        $this->vars = array( 
            "aws" => $awskey , 
            "secret" => $secretkey 
        );
        $this->ini = $ini;
        /* try force loading of zend framework */
        if ( class_exists( 'Zend_Service_Amazon_S3' ) )
        {
            $this->s3 = new Zend_Service_Amazon_S3( $awskey, $secretkey );
        }
        else
        {
            require_once ( 'Zend/Loader.php' );
            spl_autoload_register( array( 
                'Zend_Loader' , 
                'autoload' 
            ) );
            $this->s3 = new Zend_Service_Amazon_S3( $awskey, $secretkey );
        }
    
    }

    /**
     * Gets all files stored in the namespace / bucket in an array.
     *
     * @param $namespace Defines the name of the namespace / bucket.
     * @throws Exception When an error occured
     */
    function getAllDistributedFiles( $namespace )
    {
        return $this->s3->getObjectsByBucket( $namespace );
    }

    /**
     * Clears all files out of the namespace / bucket
     *
     * @param $namespace Defines the name of the namespace / bucket.
     * @throws Exception When an error occured
     */
    function clean( $bucketName )
    {
    	if( $bucketName )
    	{
            $this->s3->cleanBucket( $bucketName );
    	}
    }

    /**
     * Uploads a file into the bucket
     *
     * @param $bucket Defines the name of the namespace / bucket.
     * @param $file Defines the file (full path) to put into the namespace / bucket.
     * @param $remotepath Defines the remote location in the bucket / namespace to put the file into (without leading bucket).
     * @throws Exception When an error occured
     */
    function put( $file, $remotepath, $bucket, $isgzip = false )
    {
    	if( $isgzip )
    	{
    		$this->s3->putFile( $file, $bucket . "/" . $remotepath, array( 
                Zend_Service_Amazon_S3::S3_ACL_HEADER => Zend_Service_Amazon_S3::S3_ACL_PUBLIC_READ,
                'Cache-Control' => 'max-age=290304000, public', 'Content-Encoding' => 'gzip'
            ) );
    	}
    	else
    	{
	        $this->s3->putFile( $file, $bucket . "/" . $remotepath, array( 
	            Zend_Service_Amazon_S3::S3_ACL_HEADER => Zend_Service_Amazon_S3::S3_ACL_PUBLIC_READ,
	            'Cache-Control' => 'max-age=290304000, public'
	        ) );
    	}
    }

}

?>